@extends('layouts.app')

@section('title', 'Send Feedback')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ auth()->user()->isInstructor() ? route('instructor.dashboard') : route('dashboard.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Dashboard
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Support
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Send feedback
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Found a bug? Have an idea? Let us know — we read every message.
        </p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ auth()->user()->isInstructor() ? route('instructor.feedback.store') : route('dashboard.feedback.store') }}">
        @csrf
        <input type="hidden" name="page_url" id="page_url" value="{{ url()->previous() }}">

        <div class="space-y-6">

            {{-- Type --}}
            <div>
                <label for="type" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    What kind of feedback? <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach([
                        'bug' => ['icon' => 'fa-bug', 'label' => 'Bug', 'desc' => 'Something is broken'],
                        'feature' => ['icon' => 'fa-lightbulb', 'label' => 'Feature', 'desc' => 'Suggest an idea'],
                        'other' => ['icon' => 'fa-comment', 'label' => 'Other', 'desc' => 'Anything else'],
                    ] as $value => $meta)
                        <label class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-gray-400 dark:hover:border-gray-600 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-950/20">
                            <input type="radio" name="type" value="{{ $value }}" required
                                {{ old('type') === $value ? 'checked' : '' }}
                                class="sr-only">
                            <i class="fas {{ $meta['icon'] }} text-lg text-gray-500 dark:text-gray-400 mb-2 block"></i>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $meta['label'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $meta['desc'] }}</p>
                        </label>
                    @endforeach
                </div>
                @error('type') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Message --}}
            <div>
                <label for="message" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Tell us more <span class="text-red-500">*</span>
                </label>
                <textarea name="message" id="message" rows="6" required minlength="10" maxlength="2000"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('message') !border-red-500 @enderror"
                    placeholder="Describe what happened, or what you'd like to see...">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 dark:text-gray-600 mt-2">Be specific so we can fix it faster. Screenshots aren't supported yet.</p>
            </div>

            {{-- Info box --}}
            <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50 flex items-start gap-3">
                <i class="fas fa-info-circle text-gray-400 text-sm mt-0.5 shrink-0"></i>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    We'll also send your current page and account role so we know where it happened.
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ auth()->user()->isInstructor() ? route('instructor.dashboard') : route('dashboard.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-paper-plane text-xs"></i>
                Send feedback
            </button>
        </div>
    </form>
</div>
@endsection