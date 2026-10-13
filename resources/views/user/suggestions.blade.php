@extends('layouts.app')

@section('title', 'My Suggestions')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Dashboard
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            My suggestions
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-8">
            Track the status of your project suggestions.
        </p>
        <button onclick="document.getElementById('suggestionModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
            <i class="fas fa-plus text-xs"></i>
            New suggestion
        </button>
    </div>

    {{-- Overview stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $suggestions->count() }}</p>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mt-1">Total</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-2xl font-semibold text-amber-600 dark:text-amber-400">{{ $suggestions->where('status', 'pending')->count() }}</p>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mt-1">Pending</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $suggestions->where('status', 'reviewed')->count() }}</p>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mt-1">Reviewed</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ $suggestions->where('status', 'implemented')->count() }}</p>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mt-1">Implemented</p>
        </div>
    </div>

    @if($suggestions->count() > 0)
        {{-- Suggestions list --}}
        <div>
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    All suggestions
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $suggestions->count() }} {{ Str::plural('suggestion', $suggestions->count()) }}
                </h2>
            </div>

            <div class="space-y-3">
                @foreach($suggestions as $suggestion)
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 hover:border-gray-400 dark:hover:border-gray-600 transition">

                        {{-- Title + status --}}
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                            <h3 class="text-base font-medium text-gray-900 dark:text-white break-words min-w-0">
                                {{ $suggestion->title }}
                            </h3>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($suggestion->flagged)
                                    <span class="text-[10px] font-medium uppercase tracking-wider text-red-600 dark:text-red-400">
                                        ⚠ Pending review
                                    </span>
                                @endif
                                <span class="text-[10px] font-medium uppercase tracking-wider
                                    @if($suggestion->status === 'pending') text-amber-600 dark:text-amber-400
                                    @elseif($suggestion->status === 'reviewed') text-blue-600 dark:text-blue-400
                                    @elseif($suggestion->status === 'implemented') text-emerald-600 dark:text-emerald-400
                                    @else text-red-600 dark:text-red-400
                                    @endif">
                                    ● {{ $suggestion->status }}
                                </span>
                            </div>
                        </div>

                        {{-- Description --}}
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                            {{ Str::limit($suggestion->description, 150) }}
                        </p>

                        {{-- Meta --}}
                        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 flex-wrap mb-3">
                            @if($suggestion->difficulty)
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-signal text-[10px]"></i>
                                    {{ $suggestion->difficulty }}
                                </span>
                            @endif
                            @if($suggestion->sensor_type)
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-microchip text-[10px]"></i>
                                    {{ $suggestion->sensor_type }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-comments text-[10px]"></i>
                                {{ $suggestion->comments->count() }}
                            </span>
                            <span>{{ $suggestion->created_at->diffForHumans() }}</span>
                        </div>

                        {{-- Admin notes --}}
                        @if($suggestion->admin_notes)
                            <div class="border-l-2 border-emerald-500 dark:border-emerald-400 pl-3 py-1 mb-3">
                                <p class="text-[10px] font-medium uppercase tracking-[0.15em] text-emerald-600 dark:text-emerald-400 mb-1">
                                    Admin notes
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                    {{ $suggestion->admin_notes }}
                                </p>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-4 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs font-medium">
                            <a href="{{ route('dashboard.suggestions.show', $suggestion) }}"
                               class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                <i class="fas fa-eye text-[10px]"></i>
                                View
                            </a>
                            @if($suggestion->status === 'pending')
                                <a href="{{ route('dashboard.suggestions.edit', $suggestion) }}"
                                   class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                    <i class="fas fa-edit text-[10px]"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('dashboard.suggestions.destroy', $suggestion) }}"
                                    onsubmit="return confirm('Delete this suggestion?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 transition">
                                        <i class="fas fa-trash text-[10px]"></i>
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-lightbulb text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No suggestions yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Share your first project idea with the community.
            </p>
            <button onclick="document.getElementById('suggestionModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-plus text-xs"></i>
                Submit your first suggestion
            </button>
        </div>
    @endif
</div>

{{-- Suggestion Modal --}}
<div id="suggestionModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">

        {{-- Modal header --}}
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-1">
                    New suggestion
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Submit project suggestion
                </h2>
            </div>
            <button onclick="document.getElementById('suggestionModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Modal body --}}
        <div class="p-6">
            <form method="POST" action="{{ route('dashboard.suggestions.store') }}">
                @csrf
                <div class="space-y-5">

                    <div>
                        <label for="title" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Project title
                        </label>
                        <input type="text" name="title" id="title" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('title') !border-red-500 @enderror"
                            placeholder="e.g., Automatic plant watering system">
                        @error('title') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="5" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none @error('description') !border-red-500 @enderror"
                            placeholder="Describe your project idea in detail..."></textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="difficulty" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                Difficulty
                            </label>
                            <select name="difficulty" id="difficulty"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                                <option value="">Select difficulty</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div>
                            <label for="sensor_type" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                Sensor type
                            </label>
                            <input type="text" name="sensor_type" id="sensor_type"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm"
                                placeholder="e.g., DHT11, HC-SR04">
                        </div>
                    </div>
                </div>

                {{-- Modal footer --}}
                <div class="mt-8 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('suggestionModal').classList.add('hidden')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                        <i class="fas fa-paper-plane text-xs"></i>
                        Submit suggestion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection