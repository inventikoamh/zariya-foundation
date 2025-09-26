@extends('layouts.system')

@section('content')
    <div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Cron / Scheduler</h1>
                <p class="text-sm text-gray-500">Manage scheduled tasks and run them manually.</p>
            </div>
            <a href="{{ route('system.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to dashboard</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold">Scheduler Status</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <span class="font-medium text-gray-700">Scheduler Status:</span>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $schedulerRunning ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $schedulerRunning ? 'Running' : 'Not Running' }}
                        </span>
                    </div>
                </div>

                @if($lastRun)
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Last Run:</span> {{ $lastRun }}
                    </div>
                @endif

                <div class="flex space-x-3">
                    <form method="POST" action="{{ route('system.cron.run') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">
                            Run Schedule Now
                        </button>
                    </form>

                    <button onclick="checkSchedulerStatus()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium">
                        Refresh Status
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold">Server Setup</h2>
            </div>
            <div class="p-6 text-sm text-gray-700 space-y-2">
                <p>On your server, add this cron entry to run the Laravel scheduler every minute:</p>
                <pre class="bg-gray-50 border border-gray-200 rounded p-3 overflow-auto">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</pre>
                <p>Ensure supervisor or a process monitor keeps your queue workers (if any) running.</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold">Available Commands</h2>
            </div>
            <div class="p-6 text-sm text-gray-700 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-medium text-gray-900">Queue Commands</div>
                        <div class="mt-1 space-y-1 text-xs">
                            <div><code>php artisan queue:work</code> - Start queue worker</div>
                            <div><code>php artisan queue:restart</code> - Restart queue workers</div>
                            <div><code>php artisan queue:failed</code> - View failed jobs</div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-medium text-gray-900">Schedule Commands</div>
                        <div class="mt-1 space-y-1 text-xs">
                            <div><code>php artisan schedule:run</code> - Run scheduled tasks</div>
                            <div><code>php artisan schedule:work</code> - Start scheduler daemon</div>
                            <div><code>php artisan schedule:list</code> - List scheduled tasks</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkSchedulerStatus() {
            location.reload();
        }
    </script>
@endsection




