@extends('layouts.app')

@section('title', $class->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-8 sm:mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Classes
    </a>

    {{-- Header --}}
    <div class="mb-8 sm:mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor · Class
        </p>
        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1 mb-4">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white break-words">
                {{ $class->name }}
            </h1>
            @if($class->section)
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    · Block {{ $class->section }}
                </span>
            @endif
        </div>
        @if($class->description)
            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
                {{ $class->description }}
            </p>
        @endif
    </div>

    {{-- Tab nav --}}
    <nav class="-mx-4 sm:mx-0 mb-8 sm:mb-12">
        <div class="flex items-center gap-1 px-4 sm:px-0 overflow-x-auto scrollbar-hide">
            <a href="{{ route('instructor.classes.announcements.index', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-bullhorn text-[10px]"></i>
                Announcements
            </a>
            <a href="{{ route('instructor.classes.modules.index', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-book-open text-[10px]"></i>
                Modules
            </a>
            <a href="{{ route('instructor.classes.assessments.index', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-tasks text-[10px]"></i>
                Assessments
            </a>
            <a href="{{ route('instructor.classes.quizzes.index', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-question-circle text-[10px]"></i>
                Quizzes
            </a>
            <a href="{{ route('instructor.classes.leaderboard', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-trophy text-[10px]"></i>
                Leaderboard
            </a>
            <a href="{{ route('instructor.classes.gradebook', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-table text-[10px]"></i>
                Gradebook
            </a>
            <a href="{{ route('instructor.classes.analytics', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-chart-bar text-[10px]"></i>
                Analytics
            </a>
            <a href="{{ route('instructor.classes.resources', $class) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 whitespace-nowrap flex-shrink-0 rounded-lg transition">
                <i class="fas fa-book text-[10px]"></i>
                Resources
            </a>

            <div class="flex items-center gap-2 ml-auto pl-4 flex-shrink-0">
                <a href="{{ route('instructor.classes.edit', $class) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition flex-shrink-0"
                title="Edit class">
                    <i class="fas fa-edit text-xs"></i>
                </a>
                <form action="{{ route('instructor.classes.destroy', $class) }}" method="POST"
                    onsubmit="return confirm('Delete this class? This cannot be undone.');" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-950/20 hover:text-red-500 transition flex-shrink-0"
                        title="Delete class">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Quick stats --}}
    @php
        $approved = $class->students->where('pivot.status', 'approved')->count();
        $pending = $class->students->where('pivot.status', 'pending')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-8 sm:mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-users text-emerald-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Approved</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $approved }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-clock text-amber-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Pending</p>
            </div>
            <p class="text-2xl font-semibold {{ $pending > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }}">{{ $pending }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-book-open text-amber-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Modules</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $class->modules()->count() }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-tasks text-emerald-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Assessments</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $class->assessments()->count() }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 col-span-2 sm:col-span-1">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-question-circle text-blue-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Quizzes</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $class->quizzes()->count() }}</p>
        </div>
    </div>

    {{-- Class code --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 sm:p-6 mb-8 sm:mb-12 bg-gray-50 dark:bg-gray-900/50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Class code</p>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white tracking-[0.25em] font-mono break-all">{{ $class->code }}</p>
            </div>
            <button type="button"
                onclick="navigator.clipboard.writeText('{{ $class->code }}'); this.querySelector('span').textContent='Copied'; setTimeout(() => this.querySelector('span').textContent='Copy', 2000);"
                class="inline-flex items-center justify-center gap-1.5 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition flex-shrink-0">
                <i class="fas fa-copy text-[11px]"></i>
                <span>Copy</span>
            </button>
        </div>
    </section>

    {{-- Students list --}}
    <section>
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Students
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $class->students->count() }} {{ Str::plural('student', $class->students->count()) }}
                </h2>
            </div>
            @if($pending > 0)
                <form action="{{ route('instructor.classes.approve-all', $class) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition text-sm font-medium">
                        <i class="fas fa-check-double text-xs"></i>
                        Approve all ({{ $pending }})
                    </button>
                </form>
            @endif
        </div>

        {{-- Search --}}
        <div class="relative mb-6">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input type="text" id="studentSearch" placeholder="Search students..."
                class="w-full pl-11 pr-11 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            <button type="button" id="clearSearch"
                class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        @if($class->students->count() > 0)
            <div id="studentList" class="space-y-2">
                @foreach($class->students as $student)
                    @php
                        $submissions = $student->submissions()
                            ->whereIn('assessment_id', $class->assessments()->pluck('id'))
                            ->get();
                        $quizSubmissions = \App\Models\QuizSubmission::where('user_id', $student->id)
                            ->whereIn('quiz_id', $class->quizzes()->pluck('id'))
                            ->get();
                        $totalPoints = $submissions->sum('score') + $quizSubmissions->sum('score');
                        $submittedCount = $submissions->whereNotNull('submitted_at')->count() + $quizSubmissions->count();
                        $totalAssessments = $class->assessments()->count() + $class->quizzes()->count();
                    @endphp

                    <div class="student-row border border-gray-200 dark:border-gray-800 rounded-lg p-4"
                         data-name="{{ strtolower($student->name) }}">

                        {{-- Row 1: avatar + name + status --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-sm font-semibold text-white dark:text-gray-900">
                                @if($student->profile_image)
                                    <img src="{{ Str::startsWith($student->profile_image, ['http://', 'https://']) ? $student->profile_image : asset($student->profile_image) }}"
                                         alt="{{ $student->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $student->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $student->email }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($student->pivot->status === 'pending')
                                    <span class="text-[10px] font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400">● Pending</span>
                                @else
                                    <span class="text-[10px] font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-400">● Approved</span>
                                @endif
                            </div>
                        </div>

                        {{-- Row 2: progress --}}
                        @if($student->pivot->status === 'approved' && $totalAssessments > 0)
                            @php
                                $progressPercent = $totalAssessments > 0 ? ($submittedCount / $totalAssessments) * 100 : 0;
                                $progressColor = $progressPercent >= 75 ? 'bg-emerald-500' : ($progressPercent >= 50 ? 'bg-amber-500' : 'bg-red-500');
                            @endphp
                            <div class="flex items-center gap-3 mb-3 text-xs">
                                <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                    <div class="{{ $progressColor }} h-1.5 rounded-full transition-all"
                                        style="width: {{ $progressPercent }}%"></div>
                                </div>
                                <span class="text-gray-500 dark:text-gray-400 tabular-nums flex-shrink-0">{{ $submittedCount }}/{{ $totalAssessments }}</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300 tabular-nums flex-shrink-0">
                                    <i class="fas fa-star text-amber-500 mr-1 text-[10px]"></i>{{ $totalPoints }} pts
                                </span>
                            </div>
                        @elseif($student->pivot->status === 'approved')
                            <p class="text-xs text-gray-400 dark:text-gray-600 mb-3">No submissions yet</p>
                        @endif

                        {{-- Row 3: actions --}}
                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                            @if($student->pivot->status === 'pending')
                                <form action="{{ route('instructor.classes.approve', [$class, $student->id]) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition text-xs font-medium">
                                        <i class="fas fa-check text-[11px]"></i>
                                        Approve
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('instructor.classes.reject', [$class, $student->id]) }}" method="POST"
                                onsubmit="return confirm('Remove this student?');"
                                class="{{ $student->pivot->status === 'pending' ? 'flex-1' : 'ml-auto' }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition text-xs font-medium">
                                    <i class="fas fa-user-minus text-[11px]"></i>
                                    {{ $student->pivot->status === 'pending' ? 'Reject' : 'Remove' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- No search results --}}
            <div id="noResults" class="hidden text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-search text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No students found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">No students match your search.</p>
            </div>
        @else
            <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-users text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No students yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Share the class code above to get started.</p>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('studentSearch');
    const clearBtn = document.getElementById('clearSearch');
    const noResults = document.getElementById('noResults');
    const studentList = document.getElementById('studentList');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let found = false;

            clearBtn?.classList.toggle('hidden', query === '');

            document.querySelectorAll('.student-row').forEach(row => {
                const name = row.getAttribute('data-name');
                const match = name.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) found = true;
            });

            if (noResults && studentList) {
                if (!found && query !== '') {
                    noResults.classList.remove('hidden');
                    studentList.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    studentList.classList.remove('hidden');
                }
            }
        });

        clearBtn?.addEventListener('click', function () {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    }
</script>
@endpush