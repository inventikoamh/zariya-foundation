<?php

namespace App\Livewire\Auth;

use App\Models\Country;
use App\Models\LoginMethodSetting;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class UnifiedLogin extends Component
{
    // Password login fields
    #[Rule('required|string|min:10|max:15')]
    public string $passwordPhone = '';

    #[Rule('required|string|min:6')]
    public string $password = '';

    // SMS login fields
    #[Rule('required|string')]
    public string $smsPhoneCountryCode = '+91';

    #[Rule('required|string|min:10|max:15')]
    public string $smsPhone = '';

    #[Rule('required|string|min:6|max:6')]
    public string $otp = '';

    public bool $otpSent = false;
    public bool $isLoading = false;
    public bool $remember = false;

    // Method availability flags
    public bool $passwordMethodEnabled = false;
    public bool $smsMethodEnabled = false;
    public string $currentMethod = 'password'; // 'password' or 'sms'

    protected OtpService $otpService;

    public function boot(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function mount()
    {
        // Check which methods are enabled
        $this->passwordMethodEnabled = LoginMethodSetting::isMethodEnabled('password');
        $this->smsMethodEnabled = LoginMethodSetting::isMethodEnabled('sms');

        // Set default method
        if ($this->passwordMethodEnabled && !$this->smsMethodEnabled) {
            $this->currentMethod = 'password';
        } elseif (!$this->passwordMethodEnabled && $this->smsMethodEnabled) {
            $this->currentMethod = 'sms';
        } else {
            // Both enabled, default to password
            $this->currentMethod = 'password';
        }
    }

    public function switchToPassword()
    {
        if ($this->passwordMethodEnabled) {
            $this->currentMethod = 'password';
            $this->reset(['passwordPhone', 'password', 'otpSent']);
        }
    }

    public function switchToSms()
    {
        if ($this->smsMethodEnabled) {
            $this->currentMethod = 'sms';
            $this->reset(['smsPhone', 'smsPhoneCountryCode', 'otp', 'otpSent']);
        }
    }

    public function sendOtp()
    {
        $this->validate([
            'smsPhoneCountryCode' => 'required|string',
            'smsPhone' => 'required|string|min:10|max:15'
        ]);

        $this->isLoading = true;

        try {
            // Generate and send OTP
            $otp = $this->otpService->generate();
            $fullPhone = $this->smsPhoneCountryCode . $this->smsPhone;
            $this->otpService->send($fullPhone, $otp);

            $this->otpSent = true;
            $this->addError('success', 'OTP sent successfully to your phone number.');
        } catch (\Exception $e) {
            $this->addError('smsPhone', 'Failed to send OTP. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function verifyOtp()
    {
        $this->validate([
            'smsPhoneCountryCode' => 'required|string',
            'smsPhone' => 'required|string|min:10|max:15',
            'otp' => 'required|string|min:6|max:6'
        ]);

        $this->isLoading = true;

        try {
            // Verify OTP
            $fullPhone = $this->smsPhoneCountryCode . $this->smsPhone;
            if (!$this->otpService->verify($fullPhone, $this->otp)) {
                throw ValidationException::withMessages([
                    'otp' => 'Invalid OTP. Please try again.',
                ]);
            }

            // Find user by phone number and country code
            $user = \App\Models\User::where('phone', $this->smsPhone)
                ->where('phone_country_code', $this->smsPhoneCountryCode)
                ->first();

            // Fallback: Check if user exists with full phone number in phone field (for legacy data)
            if (!$user) {
                $fullPhone = $this->smsPhoneCountryCode . $this->smsPhone;
                $user = \App\Models\User::where('phone', $fullPhone)->first();
            }

            if (!$user) {
                throw ValidationException::withMessages([
                    'smsPhone' => 'No account found with this phone number. Please register first.',
                ]);
            }

            if ($user->is_disabled) {
                throw ValidationException::withMessages([
                    'smsPhone' => 'Your account has been disabled. Please contact support.',
                ]);
            }

            // Login the user
            Auth::login($user, $this->remember);

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

    public function loginWithPassword()
    {
        $this->validate([
            'passwordPhone' => 'required|string|min:10|max:15',
            'password' => 'required|string|min:6'
        ]);

        $this->isLoading = true;

        try {
            // Find user by phone number
            $user = \App\Models\User::where('phone', $this->passwordPhone)->first();

            // Fallback: Check if user exists with full phone number in phone field (for legacy data)
            if (!$user) {
                $fullPhone = '+91' . $this->passwordPhone; // Default to India for legacy data
                $user = \App\Models\User::where('phone', $fullPhone)->first();
            }

            if (!$user) {
                throw ValidationException::withMessages([
                    'passwordPhone' => 'No account found with this phone number.',
                ]);
            }

            if ($user->is_disabled) {
                throw ValidationException::withMessages([
                    'passwordPhone' => 'Your account has been disabled. Please contact support.',
                ]);
            }

            // Verify password
            if (!\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => 'The provided password is incorrect.',
                ]);
            }

            // Login the user
            Auth::login($user, $this->remember);
            $request = request();
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->addError('passwordPhone', 'An error occurred. Please try again.');
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

        return view('livewire.auth.unified-login', compact('countries'));
    }
}
