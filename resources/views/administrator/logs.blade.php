@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Logs
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Activity logs
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-8">
            {{ $logs->total() }} {{ Str::plural('entry', $logs->total()) }} recorded.
        </p>

        @if($logs->count() > 0)
            <form method="POST" action="{{ route('administrator.logs.clear') }}"
                onsubmit="return confirm('Delete ALL activity logs? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-red-500 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition text-sm font-medium">
                    <i class="fas fa-trash text-xs"></i>
                    Clear all logs
                </button>
            </form>
        @endif
    </div>

    @if($logs->count() > 0)
        @php $currentDate = ''; @endphp
        <div class="space-y-6">
            @foreach($logs as $log)
                @php $logDate = $log->created_at->format('F d, Y'); @endphp

                {{-- Date separator --}}
                @if($currentDate !== $logDate)
                    @php $currentDate = $logDate; @endphp
                    <div class="flex items-center gap-3 pt-2 first:pt-0">
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $logDate }}</span>
                        <div class="flex-1 h-px bg-gray-200 dark:bg-gray-800"></div>
                    </div>
                @endif

                {{-- Log entry --}}
                @php
                    $icon = 'fa-file';
                    $color = 'text-gray-500 dark:text-gray-400';
                    $stripe = 'border-l-gray-300 dark:border-l-gray-700';

                    // User actions
                    if ($log->type === 'user' && $log->action === 'created') {
                        $icon = 'fa-user-plus'; $color = 'text-indigo-500'; $stripe = 'border-l-indigo-500';
                    } elseif ($log->type === 'user' && $log->action === 'deleted') {
                        $icon = 'fa-user-slash'; $color = 'text-red-500'; $stripe = 'border-l-red-500';
                    } elseif ($log->type === 'user' && $log->action === 'changed') {
                        $icon = 'fa-user-edit'; $color = 'text-orange-500'; $stripe = 'border-l-orange-500';
                    } elseif ($log->type === 'user' && $log->action === 'warned') {
                        $icon = 'fa-exclamation-triangle'; $color = 'text-amber-500'; $stripe = 'border-l-amber-500';
                    } elseif ($log->type === 'user' && $log->action === 'banned') {
                        $icon = 'fa-ban'; $color = 'text-red-500'; $stripe = 'border-l-red-500';
                    } elseif ($log->type === 'user' && $log->action === 'unbanned') {
                        $icon = 'fa-unlock'; $color = 'text-emerald-500'; $stripe = 'border-l-emerald-500';

                    // Content types (plural — matches ContentController)
                    } elseif ($log->type === 'sensors') {
                        $icon = 'fa-microchip'; $color = 'text-cyan-500'; $stripe = 'border-l-cyan-500';
                    } elseif ($log->type === 'projects') {
                        $icon = 'fa-project-diagram'; $color = 'text-emerald-500'; $stripe = 'border-l-emerald-500';
                    } elseif ($log->type === 'products') {
                        $icon = 'fa-shopping-cart'; $color = 'text-purple-500'; $stripe = 'border-l-purple-500';
                    } elseif ($log->type === 'videos') {
                        $icon = 'fa-video'; $color = 'text-red-500'; $stripe = 'border-l-red-500';

                    // Other types
                    } elseif ($log->type === 'class') {
                        $icon = 'fa-chalkboard'; $color = 'text-blue-500'; $stripe = 'border-l-blue-500';
                    } elseif ($log->type === 'suggestion') {
                        $icon = 'fa-lightbulb'; $color = 'text-amber-500'; $stripe = 'border-l-amber-500';
                    } elseif ($log->type === 'profile') {
                        $icon = 'fa-user-edit'; $color = 'text-gray-500 dark:text-gray-400'; $stripe = 'border-l-gray-400 dark:border-l-gray-600';
                    } elseif ($log->type === 'password') {
                        $icon = 'fa-key'; $color = 'text-gray-500 dark:text-gray-400'; $stripe = 'border-l-gray-400 dark:border-l-gray-600';
                    }
                @endphp

                <div class="border border-gray-200 dark:border-gray-800 border-l-2 {{ $stripe }} rounded-lg px-5 py-4 flex items-start gap-3">

                    {{-- Type icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fas {{ $icon }} {{ $color }} text-sm"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $log->user_name }}</span>
                            <span class="text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 ml-1.5">{{ $log->user_role === 'administrator' ? 'Admin' : ($log->user_role === 'instructor' ? 'Instructor' : 'Student') }}</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ ' ' . $log->description }}</span>
                        </p>
                    </div>

                    {{-- Time --}}
                    <span class="text-xs text-gray-400 dark:text-gray-600 flex-shrink-0 whitespace-nowrap tabular-nums">{{ $log->created_at->format('h:i A') }}</span>
                </div>
            @endforeach
        </div>

        @if($logs->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $logs->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-history text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No activity yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Logs will appear here once actions are recorded.</p>
        </div>
    @endif
</div>
@endsection