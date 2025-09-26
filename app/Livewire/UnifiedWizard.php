<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class UnifiedWizard extends Component
{
    public $type = ''; // 'donation' or 'beneficiary'
    public $step = 1; // 1: phone entry, 2: OTP verify, 3: register

    // Phone entry
    public $phone = '';
    public $phone_country_code = '+91';
    public $isLoading = false;
    public $error = '';

    // OTP verification
    public $otp = '';

    // Registration
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $gender = '';
    public $dob = '';
    public $country_id = '';
    public $state_id = '';
    public $city_id = '';
    public $pincode = '';
    public $address_line = '';

    public $countries = [];
    public $states = [];
    public $cities = [];

    protected $rules = [
        'phone' => 'required|digits:10',
        'phone_country_code' => 'required|string',
        'otp' => 'required|digits:6',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'gender' => 'required|in:male,female,other',
        'dob' => 'required|date|before:today',
        'country_id' => 'nullable|exists:countries,id',
        'state_id' => 'nullable|exists:states,id',
        'city_id' => 'nullable|exists:cities,id',
        'pincode' => 'required|string|max:10',
        'address_line' => 'required|string|max:500',
    ];

    protected $messages = [
        'phone.required' => 'Phone number is required.',
        'phone.digits' => 'Phone number must be exactly 10 digits.',
        'otp.required' => 'OTP is required.',
        'otp.digits' => 'OTP must be exactly 6 digits.',
        'first_name.required' => 'First name is required.',
        'last_name.required' => 'Last name is required.',
        'email.email' => 'Please enter a valid email address.',
        'gender.required' => 'Please select your gender.',
        'dob.required' => 'Date of birth is required.',
        'dob.before' => 'Date of birth must be before today.',
        'pincode.required' => 'Pincode is required.',
        'address_line.required' => 'Address is required.',
    ];

    public function mount($type)
    {
        $this->type = $type;

        // Check if user is already authenticated
        if (auth()->check()) {
            if ($type === 'donation') {
                return redirect()->route('donate');
            } else {
                return redirect()->route('beneficiary.submit');
            }
        }

        // Check if we're continuing from a previous step
        $sessionKey = $type . '_phone';
        if (session($sessionKey)) {
            $this->phone = session($sessionKey);
            $this->step = 2; // Go to OTP step
        }

        $this->countries = Country::orderBy('name')->get();
    }

    public function sendOtp()
    {
        $this->error = '';
        $this->isLoading = true;

        $this->validate([
            'phone' => 'required|digits:10',
            'phone_country_code' => 'required|string',
        ]);

        try {
            $fullPhone = $this->phone_country_code . $this->phone;

            // Extract phone number without country code for database lookup
            $phoneNumber = preg_replace('/[^0-9]/', '', $this->phone);

            // Check if user exists
            $user = User::where('phone', $phoneNumber)->first();

            if ($user) {
                // User exists, send OTP and redirect to OTP verification
                $otpService = new OtpService();
                $otp = $otpService->generate();
                $otpService->send($fullPhone, $otp);

                session([
                    $this->type . '_phone' => $fullPhone,
                    $this->type . '_user_exists' => true,
                    $this->type . '_user_id' => $user->id,
                    $this->type . '_otp' => $otp
                ]);

                $this->step = 2;
            } else {
                // User doesn't exist, send OTP and redirect to registration
                $otpService = new OtpService();
                $otp = $otpService->generate();
                $otpService->send($fullPhone, $otp);

                session([
                    $this->type . '_phone' => $fullPhone,
                    $this->type . '_user_exists' => false,
                    $this->type . '_otp' => $otp
                ]);

                $this->step = 2;
            }
        } catch (\Exception $e) {
            $this->error = 'Failed to send OTP. Please try again.';
        } finally {
            $this->isLoading = false;
        }
    }

    public function verifyOtp()
    {
        $this->error = '';
        $this->isLoading = true;

        $this->validate([
            'otp' => 'required|digits:6',
        ]);

        try {
            $otpService = new OtpService();
            $fullPhone = session($this->type . '_phone');
            $isValid = $otpService->verify($fullPhone, $this->otp);

            if ($isValid) {
                $userExists = session($this->type . '_user_exists');

                if ($userExists) {
                    // User exists, login and redirect to appropriate page
                    $userId = session($this->type . '_user_id');
                    auth()->loginUsingId($userId);

                    // Clear session data
                    session()->forget([
                        $this->type . '_phone',
                        $this->type . '_user_exists',
                        $this->type . '_user_id',
                        $this->type . '_otp'
                    ]);

                    if ($this->type === 'donation') {
                        return redirect()->route('donate');
                    } else {
                        return redirect()->route('beneficiary.submit');
                    }
                } else {
                    // User doesn't exist, go to registration
                    $this->step = 3;
                }
            } else {
                $this->error = 'Invalid OTP. Please try again.';
            }
        } catch (\Exception $e) {
            $this->error = 'Failed to verify OTP. Please try again.';
        } finally {
            $this->isLoading = false;
        }
    }

    public function resendOtp()
    {
        $this->error = '';
        $this->isLoading = true;

        try {
            $fullPhone = session($this->type . '_phone');
            if (empty($fullPhone)) {
                $fullPhone = $this->phone_country_code . $this->phone;
            }
            $otpService = new OtpService();
            $otp = $otpService->generate();
            $otpService->send($fullPhone, $otp);

            session([$this->type . '_otp' => $otp]);
            session()->flash('message', 'OTP has been resent successfully.');
        } catch (\Exception $e) {
            $this->error = 'Failed to resend OTP. Please try again.';
        } finally {
            $this->isLoading = false;
        }
    }

    public function updatedCountryId()
    {
        $this->state_id = '';
        $this->city_id = '';
        $this->states = State::where('country_id', $this->country_id)->orderBy('name')->get();
        $this->cities = [];
    }

    public function updatedStateId()
    {
        $this->city_id = '';
        $this->cities = City::where('state_id', $this->state_id)->orderBy('name')->get();
    }

    public function register()
    {
        $this->error = '';
        $this->isLoading = true;

        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:today',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'pincode' => 'required|string|max:10',
            'address_line' => 'required|string|max:500',
        ]);

        try {
            // Get the phone number from session (it's stored with country code)
            $fullPhone = session($this->type . '_phone');

            // If session phone is empty, use component phone with country code
            if (empty($fullPhone)) {
                $fullPhone = $this->phone_country_code . $this->phone;
            }

            // Extract phone number without country code
            $phoneNumber = preg_replace('/^\+\d+/', '', $fullPhone);

            // If still empty, try to get from component properties directly
            if (empty($phoneNumber) && !empty($this->phone)) {
                $phoneNumber = $this->phone;
            }

            // Ensure we have a valid phone number
            if (empty($phoneNumber)) {
                throw new \Exception('Phone number is required for registration');
            }

            // Ensure phone number is only digits and not longer than 15 characters
            $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (strlen($phoneNumber) > 15) {
                throw new \Exception('Phone number is too long');
            }

            // Check if user with this phone number already exists
            $existingUser = User::where('phone', $phoneNumber)->first();
            if ($existingUser) {
                throw new \Exception('A user with this phone number already exists');
            }

            // Debug logging
            \Log::info('Phone number extraction debug', [
                'fullPhone' => $fullPhone,
                'phoneNumber' => $phoneNumber,
                'component_phone' => $this->phone,
                'component_country_code' => $this->phone_country_code,
                'session_key' => $this->type . '_phone',
                'session_data' => session()->all()
            ]);

            $user = User::create([
                'name' => $this->first_name . ' ' . $this->last_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $phoneNumber,
                'gender' => $this->gender,
                'dob' => $this->dob,
                'country_id' => $this->country_id,
                'state_id' => $this->state_id,
                'city_id' => $this->city_id,
                'pincode' => $this->pincode,
                'address_line' => $this->address_line,
                'password' => Hash::make('temp_password_' . time()), // Temporary password
                'is_disabled' => false,
            ]);

            // Login the user
            auth()->login($user);

            // Clear session data
            session()->forget([
                $this->type . '_phone',
                $this->type . '_user_exists',
                $this->type . '_otp'
            ]);

            if ($this->type === 'donation') {
                return redirect()->route('donate');
            } else {
                return redirect()->route('beneficiary.submit');
            }
        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage(), [
                'exception' => $e,
                'user_data' => [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $phoneNumber,
                ]
            ]);
            $this->error = 'Registration failed: ' . $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function render()
    {
        return view('livewire.unified-wizard');
    }
}
