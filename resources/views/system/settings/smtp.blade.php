@extends('layouts.system')

@section('content')
    <div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">SMTP Settings</h1>
                <p class="text-sm text-gray-500">Configure outgoing email and send a test message.</p>
            </div>
            <a href="{{ route('system.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to dashboard</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('system.settings.smtp.save') }}" class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 lg:col-span-2 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mailer</label>
                        <select name="MAIL_MAILER" class="w-full border rounded px-3 py-2">
                            @php($mailers = ['smtp','sendmail','log','array','ses','mailgun','postmark'])
                            @foreach($mailers as $mailer)
                                <option value="{{ $mailer }}" {{ old('MAIL_MAILER', $env['MAIL_MAILER']) == $mailer ? 'selected' : '' }}>{{ strtoupper($mailer) }}</option>
                            @endforeach
                        </select>
                        @error('MAIL_MAILER')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                        <select name="MAIL_ENCRYPTION" class="w-full border rounded px-3 py-2">
                            @foreach(['tls','ssl','none'] as $enc)
                                <option value="{{ $enc == 'none' ? '' : $enc }}" {{ old('MAIL_ENCRYPTION', $env['MAIL_ENCRYPTION']) == ($enc == 'none' ? '' : $enc) ? 'selected' : '' }}>{{ strtoupper($enc) }}</option>
                            @endforeach
                        </select>
                        @error('MAIL_ENCRYPTION')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Host</label>
                        <input name="MAIL_HOST" value="{{ old('MAIL_HOST', $env['MAIL_HOST']) }}" class="w-full border rounded px-3 py-2" placeholder="smtp.mailtrap.io">
                        @error('MAIL_HOST')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                        <select name="MAIL_PORT" class="w-full border rounded px-3 py-2">
                            @foreach([587, 465, 2525] as $port)
                                <option value="{{ $port }}" {{ (int) old('MAIL_PORT', $env['MAIL_PORT']) === $port ? 'selected' : '' }}>{{ $port }}</option>
                            @endforeach
                        </select>
                        @error('MAIL_PORT')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input name="MAIL_USERNAME" value="{{ old('MAIL_USERNAME', $env['MAIL_USERNAME']) }}" class="w-full border rounded px-3 py-2">
                        @error('MAIL_USERNAME')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="MAIL_PASSWORD" value="{{ old('MAIL_PASSWORD', $env['MAIL_PASSWORD']) }}" class="w-full border rounded px-3 py-2">
                        @error('MAIL_PASSWORD')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                        <input name="MAIL_FROM_ADDRESS" value="{{ old('MAIL_FROM_ADDRESS', $env['MAIL_FROM_ADDRESS']) }}" class="w-full border rounded px-3 py-2" placeholder="noreply@example.org">
                        @error('MAIL_FROM_ADDRESS')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input name="MAIL_FROM_NAME" value="{{ old('MAIL_FROM_NAME', $env['MAIL_FROM_NAME']) }}" class="w-full border rounded px-3 py-2" placeholder="Foundation CRM">
                        @error('MAIL_FROM_NAME')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('system.dashboard') }}" class="text-gray-600">Cancel</a>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save settings</button>
                </div>
            </form>

            <form method="POST" action="{{ route('system.settings.smtp.test') }}" class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
                @csrf
                <div>
                    <h2 class="font-semibold mb-1">Send Test Email</h2>
                    <p class="text-xs text-gray-500">We will send a plain text email using the current SMTP settings.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Email</label>
                    <input type="email" name="to" class="w-full border rounded px-3 py-2" placeholder="you@example.com" required>
                    @error('to')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded w-full">Send Test Email</button>
            </form>
        </div>
    </div>
@endsection
