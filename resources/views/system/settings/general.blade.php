@extends('layouts.system')

@section('content')
@php(
    $logoUrl = isset($logo) && $logo ? Storage::url($logo) : null
)
    <div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">General Settings</h1>
                <p class="text-sm text-gray-500">Branding, organization info, locale, theme, contact and more.</p>
            </div>
            <a href="{{ route('system.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to dashboard</a>
        </div>

    @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded">{{ session('success') }}</div>
    @endif

        <form method="POST" action="{{ route('system.settings.general.save') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold">Branding</h2>
                        <p class="text-xs text-gray-500">Name, logo, favicon and theme colors.</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CRM Name</label>
            <input name="crm_name" value="{{ old('crm_name', $crm_name) }}" class="w-full border rounded px-3 py-2" required>
            @error('crm_name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                <input type="file" name="logo" accept="image/*" class="w-full border rounded px-3 py-2">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" class="mt-2 h-12" alt="Logo"/>
                @endif
                @error('logo')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                <input type="file" name="favicon" accept="image/*" class="w-full border rounded px-3 py-2">
                @if($favicon)
                    <img src="{{ Storage::url($favicon) }}" class="mt-2 h-8" alt="Favicon"/>
                @endif
                @error('favicon')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color" value="{{ old('primary_color', $primary_color) }}" class="h-10 w-14 border rounded"/>
                                <input type="text" value="{{ old('primary_color', $primary_color) }}" disabled class="flex-1 border rounded px-3 py-2 bg-gray-50 text-gray-700">
                            </div>
                            @error('primary_color')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="secondary_color" value="{{ old('secondary_color', $secondary_color) }}" class="h-10 w-14 border rounded"/>
                                <input type="text" value="{{ old('secondary_color', $secondary_color) }}" disabled class="flex-1 border rounded px-3 py-2 bg-gray-50 text-gray-700">
                            </div>
                            @error('secondary_color')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold">Locale & Formatting</h2>
                    <p class="text-xs text-gray-500">Default language, timezone and currency.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                        @php($tz = old('timezone', $timezone))
                        @php($timezones = DateTimeZone::listIdentifiers())
                        <select name="timezone" class="w-full border rounded px-3 py-2">
                            @foreach($timezones as $zone)
                                <option value="{{ $zone }}" {{ $tz === $zone ? 'selected' : '' }}>{{ $zone }}</option>
                            @endforeach
                        </select>
                        @error('timezone')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                        @php($cur = old('currency', $currency))
                        @php($currencies = ['INR','USD','EUR'])
                        <select name="currency" class="w-full border rounded px-3 py-2">
                            @foreach($currencies as $code)
                                <option value="{{ $code }}" {{ $cur === $code ? 'selected' : '' }}>{{ $code }}</option>
                            @endforeach
                        </select>
                        @error('currency')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold">Application Behavior</h2>
                    <p class="text-xs text-gray-500">Control maintenance state per role group.</p>
                </div>
                <style>
                    /* Toggle styles for reliable knob movement without JS */
                    input.toggle + .toggle-track { background-color: #e5e7eb; }
                    input.toggle:checked + .toggle-track { background-color: #4f46e5; }
                    input.toggle + .toggle-track .toggle-dot { transform: translateX(0); }
                    input.toggle:checked + .toggle-track .toggle-dot { transform: translateX(16px); }
                </style>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="flex items-center justify-between border rounded px-3 py-3">
                        <div>
                            <div class="font-medium text-sm">User Maintenance</div>
                            <div class="text-xs text-gray-500">Affects regular users.</div>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="maintenance_user" value="0">
                            <input type="checkbox" name="maintenance_user" value="1" class="sr-only toggle" {{ old('maintenance_user', $maintenance_user) ? 'checked' : '' }}>
                            <div class="toggle-track relative w-10 h-6 rounded-full transition-colors">
                                <span class="toggle-dot absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform"></span>
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between border rounded px-3 py-3">
                        <div>
                            <div class="font-medium text-sm">Volunteer Maintenance</div>
                            <div class="text-xs text-gray-500">Affects volunteers.</div>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="maintenance_volunteer" value="0">
                            <input type="checkbox" name="maintenance_volunteer" value="1" class="sr-only toggle" {{ old('maintenance_volunteer', $maintenance_volunteer) ? 'checked' : '' }}>
                            <div class="toggle-track relative w-10 h-6 rounded-full transition-colors">
                                <span class="toggle-dot absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform"></span>
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between border rounded px-3 py-3">
                        <div>
                            <div class="font-medium text-sm">Admin Maintenance</div>
                            <div class="text-xs text-gray-500">Affects admins.</div>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="maintenance_admin" value="0">
                            <input type="checkbox" name="maintenance_admin" value="1" class="sr-only toggle" {{ old('maintenance_admin', $maintenance_admin) ? 'checked' : '' }}>
                            <div class="toggle-track relative w-10 h-6 rounded-full transition-colors">
                                <span class="toggle-dot absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform"></span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('system.dashboard') }}" class="text-gray-600">Cancel</a>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save changes</button>
        </div>
    </form>
</div>
@endsection
