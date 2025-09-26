<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Maintenance' }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body { min-height: 100vh; }
    </style>
    <meta http-equiv="refresh" content="60">
</head>
<body class="bg-gray-50 flex items-center justify-center">
    <div class="max-w-lg mx-auto text-center p-8">
        <h1 class="text-3xl font-bold mb-3">{{ $title ?? 'Maintenance' }}</h1>
        <p class="text-gray-600">{{ $message ?? 'We are performing scheduled maintenance. Please check back soon.' }}</p>
    </div>
</body>
</html>


