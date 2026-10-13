@extends('layouts.app')

@section('title', 'Database Backup')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('administrator.dashboard') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Dashboard
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Backup
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Database backup
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Create and manage database backups.
        </p>
    </div>

    {{-- Create backup --}}
    @php
        $backupPath = storage_path('app/backups');
        $totalBackups = is_dir($backupPath) ? count(glob($backupPath . '/*.sql')) : 0;
        $remaining = 5 - $totalBackups;
    @endphp

    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-12">
        <div class="text-center">
            <i class="fas fa-database text-indigo-500 dark:text-indigo-400 text-3xl mb-4 block"></i>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Create new backup
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Download a complete SQL dump and save it on the server.
            </p>

            @if($remaining <= 2)
                <div class="flex items-start gap-3 border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-950/20 rounded-lg p-4 mb-6 text-left max-w-md mx-auto">
                    <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5 text-sm"></i>
                    <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                        <span class="font-semibold">{{ $remaining }} {{ Str::plural('slot', $remaining) }} remaining.</span>
                        Max 5 backups stored on server. Oldest auto-deletes when full. Always save to your computer.
                    </p>
                </div>
            @endif

            <a href="{{ route('administrator.backup.download') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-download text-xs"></i>
                Download backup (.sql)
            </a>
            <p class="text-xs text-gray-400 dark:text-gray-600 mt-3 tabular-nums">{{ $totalBackups }}/5 backups stored</p>
        </div>
    </section>

    {{-- Previous backups --}}
    @php
        $backups = is_dir($backupPath) ? array_reverse(glob($backupPath . '/*.sql')) : [];
    @endphp

    @if(count($backups) > 0)
        <section>
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    History
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Previous backups ({{ count($backups) }})
                </h2>
            </div>

            <div class="space-y-2">
                @foreach($backups as $backup)
                    @php
                        $name = basename($backup);
                        $size = filesize($backup);
                        $date = filemtime($backup);
                    @endphp
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <i class="fas fa-file-code text-gray-400 dark:text-gray-600 text-sm flex-shrink-0"></i>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ date('M d, Y · h:i A', $date) }}
                                    <span class="text-gray-300 dark:text-gray-700 mx-1">·</span>
                                    {{ $size > 1048576 ? number_format($size / 1048576, 1) . ' MB' : number_format($size / 1024, 1) . ' KB' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <a href="{{ route('administrator.backup.download-file', $name) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition"
                               title="Download">
                                <i class="fas fa-download text-xs"></i>
                            </a>
                            <form action="{{ route('administrator.backup.delete', $name) }}" method="POST"
                                onsubmit="return confirm('Delete this backup?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition"
                                    title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-database text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No backups yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Click "Download backup" above to create your first one.</p>
        </div>
    @endif
</div>
@endsection