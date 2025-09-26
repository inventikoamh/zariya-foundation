<div>
    <section>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="updateProfile" class="mt-6 space-y-6">
            <!-- Profile Image Section -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-900">Profile Image</h3>
                
                <div class="flex items-center space-x-6">
                    <!-- Current Profile Image -->
                    <div class="flex-shrink-0">
                        @if($current_avatar_url)
                            <img class="h-20 w-20 rounded-full object-cover" 
                                 src="{{ Storage::url($current_avatar_url) }}" 
                                 alt="Profile Image">
                        @else
                            <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Controls -->
                    <div class="space-y-2">
                        <div>
                            <input type="file" 
                                   wire:model="profile_image" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('profile_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @if($current_avatar_url)
                            <button type="button" 
                                    wire:click="removeProfileImage"
                                    class="text-sm text-red-600 hover:text-red-800">
                                Remove Image
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-900">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                            First Name *
                        </label>
                        <input 
                            type="text" 
                            id="first_name"
                            wire:model="first_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('first_name') border-red-300 @enderror"
                            placeholder="Enter first name"
                        />
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Last Name *
                        </label>
                        <input 
                            type="text" 
                            id="last_name"
                            wire:model="last_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('last_name') border-red-300 @enderror"
                            placeholder="Enter last name"
                        />
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email"
                        wire:model="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror"
                        placeholder="Enter email address"
                    />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Personal Information -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-900">Personal Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                            Gender *
                        </label>
                        <select 
                            id="gender"
                            wire:model="gender"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('gender') border-red-300 @enderror"
                        >
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth *
                        </label>
                        <input 
                            type="date" 
                            id="dob"
                            wire:model="dob"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('dob') border-red-300 @enderror"
                        />
                        @error('dob')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-900">Contact Information</h3>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number *
                    </label>
                    <input 
                        type="text" 
                        id="phone"
                        wire:model="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror"
                        placeholder="Enter phone number"
                        maxlength="10"
                    />
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address Information -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-900">Address Information</h3>
                
                <div>
                    <label for="address_line" class="block text-sm font-medium text-gray-700 mb-1">
                        Address
                    </label>
                    <textarea 
                        id="address_line"
                        wire:model="address_line"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('address_line') border-red-300 @enderror"
                        placeholder="Enter your address"
                    ></textarea>
                    @error('address_line')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pincode" class="block text-sm font-medium text-gray-700 mb-1">
                        Pincode
                    </label>
                    <input 
                        type="text" 
                        id="pincode"
                        wire:model="pincode"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('pincode') border-red-300 @enderror"
                        placeholder="Enter 6-digit pincode"
                        maxlength="6"
                    />
                    @error('pincode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Update Profile') }}
                </button>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Profile Updated.') }}
                </x-action-message>
            </div>
        </form>
    </section>
</div>
