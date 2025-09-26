<?php

namespace App\Livewire\Auth;

use App\Models\Country;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class LoginWithPhone extends Component
{
    #[Rule('required|string')]
    public string $phone_country_code = '+91';

    #[Rule('required|string|min:10|max:15')]
    public string $phone = '';

    #[Rule('required|string|min:6|max:6')]
    public string $otp = '';

    public bool $otpSent = false;
    public bool $isLoading = false;

    protected OtpService $otpService;

    public function boot(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function sendOtp()
    {
        $this->validate([
            'phone_country_code' => 'required|string',
            'phone' => 'required|string|min:10|max:15'
        ]);

        $this->isLoading = true;

        try {
            // Generate and send OTP
            $otp = $this->otpService->generate();
            $fullPhone = $this->phone_country_code . $this->phone;
            $this->otpService->send($fullPhone, $otp);

            $this->otpSent = true;
            $this->addError('success', 'OTP sent successfully to your phone number.');
        } catch (\Exception $e) {
            $this->addError('phone', 'Failed to send OTP. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function verifyOtp()
    {
        $this->validate();

        $this->isLoading = true;

        try {
            // Verify OTP
            $fullPhone = $this->phone_country_code . $this->phone;
            if (!$this->otpService->verify($fullPhone, $this->otp)) {
                throw ValidationException::withMessages([
                    'otp' => 'Invalid OTP. Please try again.',
                ]);
            }

            // Find user by phone number and country code
            $user = \App\Models\User::where('phone', $this->phone)
                ->where('phone_country_code', $this->phone_country_code)
                ->first();

            // Fallback: Check if user exists with full phone number in phone field (for legacy data)
            if (!$user) {
                $fullPhone = $this->phone_country_code . $this->phone;
                $user = \App\Models\User::where('phone', $fullPhone)->first();
            }

            if (!$user) {
                throw ValidationException::withMessages([
                    'phone' => 'No account found with this phone number. Please register first.',
                ]);
            }

            if ($user->is_disabled) {
                throw ValidationException::withMessages([
                    'phone' => 'Your account has been disabled. Please contact support.',
                ]);
            }

            // Login the user
            Auth::login($user);

            // Mark phone as verified if not already
            if (!$user->phone_verified_at) {
                $user->update(['phone_verified_at' => now()]);
            }

            return redirect()->intended(route('dashboard'));

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->addError('otp', 'An error occurred. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function resendOtp()
    {
        $this->sendOtp();
    }

    public function render()
    {
        $countries = Country::active()->orderBy('name')->get();
        return view('livewire.auth.login-with-phone', compact('countries'));
    }
}
