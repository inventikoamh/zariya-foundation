@extends('layouts.system')

@section('content')
    <div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Environment Manager</h1>
                <p class="text-sm text-gray-500">View and edit your .env configuration.</p>
            </div>
            <a href="{{ route('system.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to dashboard</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('system.env.save') }}" class="space-y-4">
            @csrf
            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold">.env</h2>
                </div>
                <div class="p-6">
                    <textarea name="env_content" rows="24" class="w-full border rounded px-3 py-2 font-mono text-sm">{{ old('env_content', $env_content) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('system.dashboard') }}" class="text-gray-600">Cancel</a>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save .env</button>
            </div>
        </form>
    </div>
@endsection



