@extends('layouts.system')

@section('content')
    <div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Front Page Editor</h1>
                <p class="text-sm text-gray-500">Manage the public landing page content and style.</p>
            </div>
            <a href="{{ route('system.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to dashboard</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('system.frontpage.save') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold">Hero Content</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Document Title</label>
                        <input name="title" value="{{ old('title', $title) }}" class="w-full border rounded px-3 py-2">
                        @error('title')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                            <input name="headline" value="{{ old('headline', $headline) }}" class="w-full border rounded px-3 py-2">
                            @error('headline')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subheadline</label>
                            <textarea name="subheadline" rows="2" class="w-full border rounded px-3 py-2">{{ old('subheadline', $subheadline) }}</textarea>
                            @error('subheadline')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Primary CTA Text</label>
                            <input name="cta_primary_text" value="{{ old('cta_primary_text', $cta_primary_text) }}" class="w-full border rounded px-3 py-2">
                            @error('cta_primary_text')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Primary CTA Link</label>
                            <input name="cta_primary_link" value="{{ old('cta_primary_link', $cta_primary_link) }}" class="w-full border rounded px-3 py-2" placeholder="/wizard/donation">
                            @error('cta_primary_link')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary CTA Text</label>
                            <input name="cta_secondary_text" value="{{ old('cta_secondary_text', $cta_secondary_text) }}" class="w-full border rounded px-3 py-2">
                            @error('cta_secondary_text')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary CTA Link</label>
                            <input name="cta_secondary_link" value="{{ old('cta_secondary_link', $cta_secondary_link) }}" class="w-full border rounded px-3 py-2" placeholder="/wizard/beneficiary">
                            @error('cta_secondary_link')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold">Features</h2>
                    <p class="text-xs text-gray-500">Add up to 6 features.</p>
                </div>
                <div class="p-6 space-y-4">
                    @php($max=6)
                    @for($i=0;$i<$max;$i++)
                        @php($feature = $features[$i] ?? ['title' => '', 'text' => ''])
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title #{{ $i+1 }}</label>
                                <input name="features[{{ $i }}][title]" value="{{ old("features.$i.title", $feature['title']) }}" class="w-full border rounded px-3 py-2">
                                @error("features.$i.title")<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Text #{{ $i+1 }}</label>
                                <textarea name="features[{{ $i }}][text]" rows="2" class="w-full border rounded px-3 py-2">{{ old("features.$i.text", $feature['text']) }}</textarea>
                                @error("features.$i.text")<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold">Background</h2>
                    <p class="text-xs text-gray-500">Gradient colors for the landing page background.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="background_from" value="{{ old('background_from', $background_from) }}" class="h-10 w-14 border rounded"/>
                            <input type="text" value="{{ old('background_from', $background_from) }}" disabled class="flex-1 border rounded px-3 py-2 bg-gray-50 text-gray-700">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="background_to" value="{{ old('background_to', $background_to) }}" class="h-10 w-14 border rounded"/>
                            <input type="text" value="{{ old('background_to', $background_to) }}" disabled class="flex-1 border rounded px-3 py-2 bg-gray-50 text-gray-700">
                        </div>
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


