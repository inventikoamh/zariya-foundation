<div>
    <!-- Method Switcher (only show if both methods are enabled) -->
    @if($passwordMethodEnabled && $smsMethodEnabled)
        <div class="mb-8">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <button
                    type="button"
                    wire:click="switchToPassword"
                    class="flex-1 py-3 px-4 text-sm font-medium rounded-md transition-all duration-200 {{ $currentMethod === 'password' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}"
                >
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        <span>Password</span>
                    </div>
                </button>
                <button
                    type="button"
                    wire:click="switchToSms"
                    class="flex-1 py-3 px-4 text-sm font-medium rounded-md transition-all duration-200 {{ $currentMethod === 'sms' ? 'bg-white text-green-600 shadow-sm ring-1 ring-green-500' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}"
                >
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>SMS OTP</span>
                    </div>
                </button>
            </div>
        </div>
    @endif

    <!-- Password Login Form -->
    @if($passwordMethodEnabled && ($currentMethod === 'password' || !$smsMethodEnabled))
        <div class="space-y-6">
            @if($passwordMethodEnabled && $smsMethodEnabled)
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900">Login with Password</h3>
                    <p class="mt-1 text-sm text-gray-600">Enter your phone number and password</p>
                </div>
            @endif

            <form wire:submit.prevent="loginWithPassword">
                <div>
                    <label for="password_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <div class="mt-1">
                        <input
                            id="password_phone"
                            name="password_phone"
                            type="tel"
                            wire:model="passwordPhone"
                            autocomplete="tel"
                            required
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-200 @error('passwordPhone') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Enter your phone number (e.g., 9876543210)"
                        >
                    </div>
                    @error('passwordPhone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="mt-1">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            wire:model="password"
                            autocomplete="current-password"
                            required
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-200 @error('password') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Enter your password"
                        >
                    </div>
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            wire:model="remember"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                    >
                        <span wire:loading.remove wire:target="loginWithPassword">Sign in with Password</span>
                        <span wire:loading wire:target="loginWithPassword">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Signing in...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- SMS Login Form -->
    @if($smsMethodEnabled && ($currentMethod === 'sms' || !$passwordMethodEnabled))
        <div class="space-y-6">
            @if($passwordMethodEnabled && $smsMethodEnabled)
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900">Login with SMS</h3>
                    <p class="mt-1 text-sm text-gray-600">Enter your phone number to receive OTP</p>
                </div>
            @endif

            @if(!$otpSent)
                <!-- Phone Entry -->
                <form wire:submit.prevent="sendOtp">
                    <div>
                        <label for="sms_phone_country_code" class="block text-sm font-medium text-gray-700 mb-1">Country Code</label>
                        <div class="mt-1">
                            <select
                                id="sms_phone_country_code"
                                name="sms_phone_country_code"
                                wire:model="smsPhoneCountryCode"
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm transition-colors duration-200"
                            >
                                @foreach($countries as $country)
                                    <option value="{{ $country->phone_code }}">{{ $country->name }} ({{ $country->phone_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="sms_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="mt-1">
                            <input
                                id="sms_phone"
                                name="sms_phone"
                                type="tel"
                                wire:model="smsPhone"
                                autocomplete="tel"
                                required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm transition-colors duration-200 @error('smsPhone') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                placeholder="Enter your phone number"
                            >
                        </div>
                        @error('smsPhone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                        >
                            <span wire:loading.remove wire:target="sendOtp">Send OTP</span>
                            <span wire:loading wire:target="sendOtp">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </button>
                    </div>
                </form>
            @else
                <!-- OTP Verification -->
                <form wire:submit.prevent="verifyOtp">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Enter Verification Code</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            We sent a 6-digit code to {{ $smsPhoneCountryCode }}{{ $smsPhone }}
                        </p>
                    </div>

                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
                        <div class="mt-1">
                            <input
                                id="otp"
                                name="otp"
                                type="text"
                                wire:model="otp"
                                autocomplete="one-time-code"
                                required
                                maxlength="6"
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm text-center text-lg tracking-widest transition-colors duration-200 @error('otp') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                placeholder="000000"
                            >
                        </div>
                        @error('otp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6 space-y-3">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                        >
                            <span wire:loading.remove wire:target="verifyOtp">Verify & Sign In</span>
                            <span wire:loading wire:target="verifyOtp">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Verifying...
                            </span>
                        </button>

                        <button
                            type="button"
                            wire:click="resendOtp"
                            wire:loading.attr="disabled"
                            class="w-full text-center text-sm text-green-600 hover:text-green-500 disabled:opacity-50 transition-colors duration-200"
                        >
                            <span wire:loading.remove wire:target="resendOtp">Resend Code</span>
                            <span wire:loading wire:target="resendOtp">Resending...</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif
</div>
