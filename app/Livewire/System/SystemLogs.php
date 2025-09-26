<?php

namespace App\Livewire\System;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

#[Layout('layouts.system')]
class SystemLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $levelFilter = '';
    public $dateFilter = '';
    public $perPage = 50;
    public $selectedLogFile = 'laravel.log';

    protected $queryString = [
        'search' => ['except' => ''],
        'levelFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'selectedLogFile' => ['except' => 'laravel.log'],
    ];

    public function getPage()
    {
        return request()->get('page', 1);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLevelFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingSelectedLogFile()
    {
        $this->resetPage();
    }

    public function getAvailableLogFilesProperty()
    {
        $logPath = storage_path('logs');
        $files = [];

        if (File::exists($logPath)) {
            $logFiles = File::files($logPath);
            foreach ($logFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s')
                    ];
                }
            }
        }

        return collect($files)->sortByDesc('modified');
    }

    public function getLogEntriesProperty()
    {
        $logPath = storage_path('logs/' . $this->selectedLogFile);

        if (!File::exists($logPath)) {
            return collect();
        }

        $content = File::get($logPath);
        $lines = explode("\n", $content);
        $entries = [];
        $currentEntry = '';

        foreach ($lines as $line) {
            // Check if line starts with a date (new log entry)
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line)) {
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }
                $currentEntry = $line;
            } else {
                $currentEntry .= "\n" . $line;
            }
        }

        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        // Filter entries
        $filteredEntries = collect($entries)->filter(function ($entry) {
            // Search filter
            if ($this->search && !str_contains(strtolower($entry), strtolower($this->search))) {
                return false;
            }

            // Level filter
            if ($this->levelFilter) {
                $levelPattern = '/\.' . strtoupper($this->levelFilter) . ':/';
                if (!preg_match($levelPattern, $entry)) {
                    return false;
                }
            }

            // Date filter
            if ($this->dateFilter) {
                $datePattern = '/\[' . $this->dateFilter . '/';
                if (!preg_match($datePattern, $entry)) {
                    return false;
                }
            }

            return true;
        });

        return $filteredEntries->reverse()->values();
    }

    public function getPaginatedLogEntriesProperty()
    {
        $entries = $this->getLogEntriesProperty();
        $currentPage = $this->getPage();
        $offset = ($currentPage - 1) * $this->perPage;
        $paginatedEntries = $entries->slice($offset, $this->perPage);

        // Create a custom paginator
        return new LengthAwarePaginator(
            $paginatedEntries,
            $entries->count(),
            $this->perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    public function getLogLevelsProperty()
    {
        return [
            'emergency' => 'Emergency',
            'alert' => 'Alert',
            'critical' => 'Critical',
            'error' => 'Error',
            'warning' => 'Warning',
            'notice' => 'Notice',
            'info' => 'Info',
            'debug' => 'Debug'
        ];
    }

    public function getLogStatsProperty()
    {
        $entries = $this->getLogEntriesProperty();
        $stats = [
            'total' => $entries->count(),
            'error' => $entries->filter(fn($entry) => str_contains($entry, '.ERROR:') || str_contains($entry, '.CRITICAL:') || str_contains($entry, '.EMERGENCY:'))->count(),
            'warning' => $entries->filter(fn($entry) => str_contains($entry, '.WARNING:') || str_contains($entry, '.ALERT:'))->count(),
            'info' => $entries->filter(fn($entry) => str_contains($entry, '.INFO:'))->count(),
        ];

        return $stats;
    }

    public function clearLogs()
    {
        $logPath = storage_path('logs/' . $this->selectedLogFile);

        if (File::exists($logPath)) {
            File::put($logPath, '');
            session()->flash('success', 'Log file cleared successfully!');
        }
    }

    public function downloadLogs()
    {
        $logPath = storage_path('logs/' . $this->selectedLogFile);

        if (File::exists($logPath)) {
            return response()->download($logPath);
        }
    }

    public function getLogLevelColor($entry)
    {
        if (str_contains($entry, '.ERROR:') || str_contains($entry, '.CRITICAL:') || str_contains($entry, '.EMERGENCY:')) {
            return 'border-red-400';
        } elseif (str_contains($entry, '.WARNING:') || str_contains($entry, '.ALERT:')) {
            return 'border-yellow-400';
        } elseif (str_contains($entry, '.INFO:')) {
            return 'border-blue-400';
        } elseif (str_contains($entry, '.DEBUG:')) {
            return 'border-gray-400';
        } else {
            return 'border-gray-400';
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    public function render()
    {
        return view('livewire.system.system-logs', [
            'logEntries' => $this->getPaginatedLogEntriesProperty(),
            'availableLogFiles' => $this->getAvailableLogFilesProperty(),
            'logLevels' => $this->getLogLevelsProperty(),
            'logStats' => $this->getLogStatsProperty(),
        ]);
    }
}
