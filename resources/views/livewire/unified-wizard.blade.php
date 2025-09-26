<div class="min-h-screen flex items-center justify-center bg-gradient-to-br {{ $type === 'donation' ? 'from-blue-50 to-indigo-100' : 'from-green-50 to-emerald-100' }} py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full {{ $type === 'donation' ? 'bg-indigo-100' : 'bg-green-100' }} mb-4">
                    @if($step === 1)
                        <svg class="h-8 w-8 {{ $type === 'donation' ? 'text-indigo-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    @elseif($step === 2)
                        <svg class="h-8 w-8 {{ $type === 'donation' ? 'text-indigo-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="h-8 w-8 {{ $type === 'donation' ? 'text-indigo-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    @endif
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    @if($step === 1)
                        {{ $type === 'donation' ? 'Make a Donation' : 'Request Assistance' }}
                    @elseif($step === 2)
                        Verify Phone Number
                    @else
                        Complete Registration
                    @endif
                </h2>
                <p class="text-sm text-gray-600">
                    @if($step === 1)
                        Enter your phone number to get started
                    @elseif($step === 2)
                        We've sent a 6-digit code to <strong>{{ $phone }}</strong>
                    @else
                        Please provide your details to complete the registration
                    @endif
                </p>
            </div>

            <!-- Progress Steps -->
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center space-x-4">
                    @for($i = 1; $i <= 3; $i++)
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium {{ $i <= $step ? ($type === 'donation' ? 'bg-indigo-600 text-white' : 'bg-green-600 text-white') : 'bg-gray-200 text-gray-600' }}">
                                {{ $i }}
                            </div>
                            @if($i < 3)
                                <div class="w-8 h-0.5 {{ $i < $step ? ($type === 'donation' ? 'bg-indigo-600' : 'bg-green-600') : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
                    {{ session('message') }}
                </div>
            @endif

            @if($error)
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                    {{ $error }}
                </div>
            @endif

            <!-- Step 1: Phone Entry -->
            @if($step === 1)
                <form class="space-y-6" wire:submit.prevent="sendOtp">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div class="flex">
                            <div class="w-1/3">
                                <select wire:model="phone_country_code" class="appearance-none rounded-none rounded-l-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                                    <option value="+91">+91 (India)</option>
                                    <option value="+1">+1 (USA)</option>
                                    <option value="+44">+44 (UK)</option>
                                    <option value="+971">+971 (UAE)</option>
                                </select>
                            </div>
                            <div class="w-2/3">
                                <input wire:model="phone" type="tel" required class="appearance-none rounded-none rounded-r-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Enter your phone number">
                            </div>
                        </div>
                    </div>

                    @error('phone')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                    <div>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white {{ $type === 'donation' ? 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="sendOtp">Send OTP</span>
                            <span wire:loading wire:target="sendOtp" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending OTP...
                            </span>
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 text-sm">
                            Already have an account? Login here
                        </a>
                    </div>
                </form>
            @endif

            <!-- Step 2: OTP Verification -->
            @if($step === 2)
                <form class="space-y-6" wire:submit.prevent="verifyOtp">
                    <div>
                        <label for="otp" class="sr-only">OTP</label>
                        <input wire:model="otp"
                               type="text"
                               maxlength="6"
                               required
                               class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 text-center text-2xl tracking-widest focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="000000">
                    </div>

                    @error('otp')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                    <div>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white {{ $type === 'donation' ? 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="verifyOtp">Verify OTP</span>
                            <span wire:loading wire:target="verifyOtp" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Verifying...
                            </span>
                        </button>
                    </div>

                    <div class="text-center">
                        <button type="button"
                                wire:click="resendOtp"
                                wire:loading.attr="disabled"
                                class="text-indigo-600 hover:text-indigo-500 text-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="resendOtp">Resend OTP</span>
                            <span wire:loading wire:target="resendOtp">Sending...</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <button type="button" wire:click="goBack" class="text-gray-600 hover:text-gray-500 text-sm">
                            Change phone number
                        </button>
                    </div>
                </form>
            @endif

            <!-- Step 3: Registration -->
            @if($step === 3)
                <form class="space-y-6" wire:submit.prevent="register">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                            <input wire:model="first_name" type="text" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                            @error('first_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                            <input wire:model="last_name" type="text" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                            @error('last_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input wire:model="email" type="email" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                            @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                            <select wire:model="gender" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                            <input wire:model="dob" type="date" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                            @error('dob') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country_id" class="block text-sm font-medium text-gray-700">Country</label>
                            <select wire:model.live="country_id" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state_id" class="block text-sm font-medium text-gray-700">State</label>
                            <select wire:model.live="state_id" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city_id" class="block text-sm font-medium text-gray-700">City</label>
                            <select wire:model="city_id" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <!-- Pincode -->
                        <div>
                            <label for="pincode" class="block text-sm font-medium text-gray-700">Pincode *</label>
                            <input wire:model="pincode" type="text" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                            @error('pincode') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address_line" class="block text-sm font-medium text-gray-700">Address *</label>
                        <textarea wire:model="address_line" required rows="3" class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"></textarea>
                        @error('address_line') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white {{ $type === 'donation' ? 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="register">Complete Registration</span>
                            <span wire:loading wire:target="register" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Registering...
                            </span>
                        </button>
                    </div>

                    <div class="text-center">
                        <button type="button" wire:click="goBack" class="text-indigo-600 hover:text-indigo-500 text-sm">
                            Back to OTP verification
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
