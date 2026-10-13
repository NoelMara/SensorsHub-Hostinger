@extends('layouts.app')

@section('title', 'Analytics - ' . $class->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Class
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Analytics · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Analytics
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Performance overview for {{ $studentCount }} {{ Str::plural('student', $studentCount) }}.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Students</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $studentCount }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Assessments</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $assessmentCount }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Quizzes</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $quizCount }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Assess avg</p>
            <p class="text-2xl font-semibold tabular-nums {{ $assessmentAvg >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($assessmentAvg >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">{{ $assessmentAvg }}%</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Quiz avg</p>
            <p class="text-2xl font-semibold tabular-nums {{ $quizAvg >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($quizAvg >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">{{ $quizAvg }}%</p>
        </div>
    </div>

    {{-- Submission Timeline Chart --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 sm:p-6 mb-12">
        <div class="mb-6">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Timeline
            </p>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Submission activity
            </h2>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-5 mb-4">
            <span class="inline-flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-block w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                Assessments
            </span>
            <span class="inline-flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-block w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
                Quizzes
            </span>
        </div>

        {{-- Chart --}}
        <div class="relative h-48 sm:h-64">
            <canvas id="submissionChart"></canvas>
        </div>

        {{-- Pagination --}}
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

    {{-- Breakdown Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">

        {{-- Assessment Breakdown --}}
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Assessments
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Breakdown
                </h2>
            </div>

            @if(count($assessmentBreakdown) > 0)
                <div class="space-y-5">
                    @foreach($assessmentBreakdown as $item)
                        @php $aColor = $item['average'] >= 75 ? 'bg-emerald-500' : ($item['average'] >= 50 ? 'bg-amber-500' : 'bg-red-500'); @endphp
                        <div>
                            <div class="flex items-baseline justify-between gap-3 mb-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate min-w-0">{{ $item['title'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums flex-shrink-0">
                                    {{ $item['submitted'] }}/{{ $item['total'] }} · {{ $item['average'] }}%
                                </span>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $aColor }} h-1.5 rounded-full transition-all" style="width: {{ $item['submission_rate'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No assessments published yet.</p>
            @endif
        </section>

        {{-- Quiz Breakdown --}}
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Quizzes
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Breakdown
                </h2>
            </div>

            @if(count($quizBreakdown) > 0)
                <div class="space-y-5">
                    @foreach($quizBreakdown as $item)
                        @php $qColor = $item['average'] >= 75 ? 'bg-emerald-500' : ($item['average'] >= 50 ? 'bg-amber-500' : 'bg-red-500'); @endphp
                        <div>
                            <div class="flex items-baseline justify-between gap-3 mb-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate min-w-0">{{ $item['title'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums flex-shrink-0">
                                    {{ $item['submitted'] }}/{{ $item['total'] }} · {{ $item['average'] }}%
                                </span>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $qColor }} h-1.5 rounded-full transition-all" style="width: {{ $item['submission_rate'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No quizzes published yet.</p>
            @endif
        </section>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('submissionChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const allData = @json($submissionTimeline);
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
            datasets: [
                {
                    label: 'Assessments',
                    data: [],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                },
                {
                    label: 'Quizzes',
                    data: [],
                    borderColor: '#A855F7',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                }
            ]
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
        chart.data.datasets[0].data = pageData.map(d => d.assessments);
        chart.data.datasets[1].data = pageData.map(d => d.quizzes);
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