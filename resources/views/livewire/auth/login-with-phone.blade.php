<div>
    @if (!$otpSent)
        <!-- Phone Number Input Form -->
        <form wire:submit="sendOtp">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">
                    Phone Number
                </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <!-- Country Code Dropdown -->
                    <div class="relative">
                        <select
                            wire:model="phone_country_code"
                            class="appearance-none rounded-l-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 @error('phone_country_code') border-red-300 @enderror"
                        >
                            @foreach($countries as $country)
                                <option value="{{ $country->phone_code }}">
                                    {{ $country->phone_code }} {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Phone Number Input -->
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        wire:model="phone"
                        class="block w-full appearance-none rounded-r-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror"
                        placeholder="Enter your phone number"
                        required
                    >
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Enter your phone number without the country code (e.g., 9876543210 for +91)
                </p>
                @error('phone_country_code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="sendOtp"
                    class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="sendOtp">Send OTP</span>
                    <span wire:loading wire:target="sendOtp">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </div>
        </form>
    @else
        <!-- OTP Verification Form -->
        <form wire:submit="verifyOtp">
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">
                    Enter OTP
                </label>
                <div class="mt-1">
                    <input
                        id="otp"
                        name="otp"
                        type="text"
                        wire:model="otp"
                        class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm @error('otp') border-red-300 @enderror"
                        placeholder="Enter 6-digit OTP"
                        maxlength="6"
                        required
                    >
                </div>
                @error('otp')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <p class="text-sm text-gray-600">
                    OTP sent to: <span class="font-medium">{{ $phone_country_code }}{{ $phone }}</span>
                </p>
            </div>

            <div class="mt-6 space-y-3">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="verifyOtp"
                    class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="verifyOtp">Verify OTP</span>
                    <span wire:loading wire:target="verifyOtp">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                    wire:target="resendOtp"
                    class="flex w-full justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="resendOtp">Resend OTP</span>
                    <span wire:loading wire:target="resendOtp">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Resending...
                    </span>
                </button>
            </div>
        </form>

        <div class="mt-4">
            <button
                type="button"
                wire:click="$set('otpSent', false)"
                class="text-sm text-indigo-600 hover:text-indigo-500"
            >
                ← Change phone number
            </button>
        </div>
    @endif

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="mt-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
