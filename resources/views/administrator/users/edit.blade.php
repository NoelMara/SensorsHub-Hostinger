@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('administrator.users.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Users
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Edit user
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Edit user
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Update account details for {{ $user->name }}.
        </p>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="border border-red-200 dark:border-red-900/40 bg-red-50 dark:bg-red-950/20 rounded-lg p-5 mb-8">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-red-600 dark:text-red-400 mb-2">
                Please fix the following
            </p>
            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- User preview --}}
    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 mb-8 bg-gray-50 dark:bg-gray-900/50">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($user->profile_image)
                    <img src="{{ Str::startsWith($user->profile_image, ['http://', 'https://']) ? $user->profile_image : asset($user->profile_image) }}"
                         alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-lg font-semibold text-white dark:text-gray-900">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap mb-0.5">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                    <span class="text-[10px] font-medium uppercase tracking-wider
                        {{ $user->role === 'administrator' ? 'text-purple-600 dark:text-purple-400' : '' }}
                        {{ $user->role === 'instructor' ? 'text-blue-600 dark:text-blue-400' : '' }}
                        {{ $user->role === 'student' ? 'text-emerald-600 dark:text-emerald-400' : '' }}">
                        {{ $user->role === 'administrator' ? 'Administrator' : ($user->role === 'instructor' ? 'Instructor' : 'Student') }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('administrator.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Name + Email --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Full name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('name') !border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Email address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('email') !border-red-500 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Role --}}
            @if(!$user->isAdministrator())
                <div>
                    <label for="role" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm @error('role') !border-red-500 @enderror">
                        <option value="student" @selected(old('role', $user->role) === 'student')>Student</option>
                        <option value="instructor" @selected(old('role', $user->role) === 'instructor')>Instructor</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            @endif

            {{-- Password --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        New password <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(leave blank to keep)</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-3 pr-11 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm @error('password') !border-red-500 @enderror">
                        <button type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
                            onclick="togglePasswordVisibility('password', this)">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Confirm new password
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-3 pr-11 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                        <button type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
                            onclick="togglePasswordVisibility('password_confirmation', this)">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('administrator.users.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-save text-xs"></i>
                Save changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        if (!input || !button) return;
        const icon = button.querySelector('i');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        if (icon) {
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        }
    }
</script>
@endpush