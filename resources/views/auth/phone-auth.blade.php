<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Authentication</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Foundation CRM</h1>
                    <p class="mt-2 text-sm text-gray-600">Connecting communities through compassion</p>
                </div>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div id="auth-container">
                        <!-- Phone Entry Component -->
                        <div id="phone-entry" class="auth-step">
                            <livewire:auth.phone-entry />
                        </div>

                        <!-- OTP Verification Component -->
                        <div id="otp-verification" class="auth-step hidden">
                            <livewire:auth.otp-verification />
                        </div>

                        <!-- Registration Form Component -->
                        <div id="registration-form" class="auth-step hidden">
                            <livewire:auth.registration-form />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                // Handle phone exists event (login flow)
                Livewire.on('phoneExists', (data) => {
                    if (data.exists) {
                        // Redirect to login with phone
                        window.location.href = '/login?phone=' + document.querySelector('input[wire\\:model="phone"]').value;
                    }
                });

                // Handle new phone event (registration flow)
                Livewire.on('newPhone', (data) => {
                    showStep('otp-verification');
                    // Mount OTP component with phone
                    Livewire.dispatch('mount-otp', { phone: data.phone, isForLogin: false });
                });

                // Handle OTP verified for registration
                Livewire.on('otpVerified', (data) => {
                    showStep('registration-form');
                    // Mount registration component with phone
                    Livewire.dispatch('mount-registration', { phone: data.phone });
                });

                // Handle OTP verified for login
                Livewire.on('otpVerifiedForLogin', (data) => {
                    // This will be handled by the login component
                });
            });

            function showStep(stepId) {
                // Hide all steps
                document.querySelectorAll('.auth-step').forEach(step => {
                    step.classList.add('hidden');
                });
                
                // Show the requested step
                document.getElementById(stepId).classList.remove('hidden');
            }
        </script>
    </body>
</html>
