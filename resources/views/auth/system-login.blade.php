<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white shadow rounded-lg p-6 w-full max-w-md">
        <h1 class="text-xl font-semibold mb-4">System Login</h1>
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('system.login.attempt') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-gray-700 mb-1">Email</label>
                <input name="email" type="email" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-700 mb-1">Password</label>
                <input name="password" type="password" class="w-full border rounded px-3 py-2" required>
            </div>
            <label class="inline-flex items-center mb-4">
                <input type="checkbox" name="remember" class="mr-2"> Remember me
            </label>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Login</button>
        </form>
    </div>
  </div>
</body>
</html>


