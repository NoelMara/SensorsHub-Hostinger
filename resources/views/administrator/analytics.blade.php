@extends('layouts.app')

@section('title', 'Platform Analytics')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('administrator.dashboard') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Dashboard
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Analytics
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Platform analytics
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Overview of users, classes, and content across your platform.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Users</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalUsers }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Instructors</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalInstructors }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Classes</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalClasses }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Content</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalContent }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">New · 30d</p>
            <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $newThisMonth }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Banned</p>
            <p class="text-2xl font-semibold {{ $bannedCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $bannedCount }}</p>
        </div>
    </div>

    {{-- User Growth Chart --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 sm:p-6 mb-12">
        <div class="mb-6">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Timeline
            </p>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                User growth · last 30 days
            </h2>
        </div>

        <div class="relative h-48 sm:h-64">
            <canvas id="userGrowthChart"></canvas>
        </div>

        <div class="flex items-center justify-center gap-3 mt-4">
            <button id="prevBtn" type="button"
                class="inline-flex items-center justify-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition disabled:opacity-30 disabled:pointer-events-none">
                <i class="fas fa-arrow-left text-[10px]"></i>
                Prev
            </button>
            <span id="pageLabel" class="text-xs text-gray-500 dark:text-gray-400 tabular-nums"></span>
            <button id="nextBtn" type="button"
                class="inline-flex items-center justify-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition disabled:opacity-30 disabled:pointer-events-none">
                Next
                <i class="fas fa-arrow-right text-[10px]"></i>
            </button>
        </div>
    </section>

    {{-- Top Classes + Content Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Classes --}}
        <section>
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Classes
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Top performers
                </h2>
            </div>

            @if(count($topClasses) > 0)
                <div class="space-y-2">
                    @foreach($topClasses as $index => $class)
                        <div class="flex items-center justify-between gap-4 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <span class="text-xs font-semibold text-gray-400 dark:text-gray-600 tabular-nums w-5 flex-shrink-0">#{{ $index + 1 }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $class['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ $class['instructor'] }}
                                        @if($class['section'])
                                            <span class="text-gray-300 dark:text-gray-700 mx-1">·</span>
                                            Block {{ $class['section'] }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 flex-shrink-0">
                                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums inline-flex items-center gap-1">
                                    <i class="fas fa-users text-[10px]"></i>
                                    {{ $class['students_count'] }}
                                </span>
                                <span class="text-sm font-semibold tabular-nums {{ $class['avg_score'] >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($class['avg_score'] >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                    {{ $class['avg_score'] }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                    <i class="fas fa-chalkboard text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No classes yet.</p>
                </div>
            @endif
        </section>

        {{-- Content Breakdown --}}
        <section>
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Content
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Breakdown
                </h2>
            </div>

            <div class="border border-gray-200 dark:border-gray-800 rounded-lg divide-y divide-gray-100 dark:divide-gray-800">
                <div class="flex items-center justify-between gap-3 p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-microchip text-emerald-500 text-sm w-5 text-center"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Sensors</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ $contentBreakdown['sensors'] }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-project-diagram text-blue-500 text-sm w-5 text-center"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Projects</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ $contentBreakdown['projects'] }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-video text-red-500 text-sm w-5 text-center"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Videos</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ $contentBreakdown['videos'] }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shopping-cart text-purple-500 text-sm w-5 text-center"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Products</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ $contentBreakdown['products'] }}</span>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('userGrowthChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const allData = @json($userGrowth);
    const daysPerPage = 7;
    const totalPages = Math.ceil(allData.length / daysPerPage);
    let currentPage = Math.max(0, totalPages - 1);

    // Theme-aware chart colors
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
    const tickColor = isDark ? '#9ca3af' : '#6b7280';

    function getPageData(page) {
        const start = page * daysPerPage;
        return allData.slice(start, start + daysPerPage);
    }

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'New Users',
                data: [],
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointHoverRadius: 5,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#111827' : '#ffffff',
                    titleColor: isDark ? '#f9fafb' : '#111827',
                    bodyColor: isDark ? '#d1d5db' : '#374151',
                    borderColor: isDark ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: tickColor, font: { size: 11 } },
                    grid: { color: gridColor },
                },
                x: {
                    ticks: { color: tickColor, font: { size: 11 } },
                    grid: { display: false },
                }
            }
        }
    });

    function updateChart(page) {
        const pageData = getPageData(page);
        chart.data.labels = pageData.map(d => d.date.slice(5));
        chart.data.datasets[0].data = pageData.map(d => d.count);
        chart.update();
        document.getElementById('pageLabel').textContent = `Week ${page + 1} of ${totalPages}`;
        document.getElementById('prevBtn').disabled = page === 0;
        document.getElementById('nextBtn').disabled = page === totalPages - 1;
    }

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentPage > 0) { currentPage--; updateChart(currentPage); }
    });
    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentPage < totalPages - 1) { currentPage++; updateChart(currentPage); }
    });

    updateChart(currentPage);
});
</script>
@endpush