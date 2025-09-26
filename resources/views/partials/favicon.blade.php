@php
    $favicon = \App\Models\SystemSetting::get('crm_favicon');
@endphp

@if($favicon)
    <!-- Custom Favicon from System Settings -->
    <link rel="icon" type="image/png" href="{{ Storage::url($favicon) }}" />
    <link rel="shortcut icon" type="image/png" href="{{ Storage::url($favicon) }}" />
    <link rel="apple-touch-icon" href="{{ Storage::url($favicon) }}" />
@else
    <!-- Default Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
@endif
