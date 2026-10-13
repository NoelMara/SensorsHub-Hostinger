@extends('layouts.app')

@section('title', 'Administrator Profile')

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
            Administrator · Account
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Settings
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Manage your profile and security settings.
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

    <div class="space-y-8">

        {{-- Profile summary --}}
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                <div class="w-20 h-20 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($user->profile_image)
                        <img src="{{ Str::startsWith($user->profile_image, ['http://', 'https://']) ? $user->profile_image : asset($user->profile_image) }}"
                             alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-semibold text-white dark:text-gray-900">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="text-center sm:text-left flex-1 min-w-0">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate mb-2">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-start">
                        <span class="text-[10px] font-medium uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            Administrator
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-600">·</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Joined {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Profile form --}}
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Profile
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Profile information
                </h2>
            </div>

            <form action="{{ route('administrator.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Full name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Email address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Profile picture <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(PNG, JPG, GIF up to 2MB)</span>
                    </label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*"
                        class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 dark:file:bg-gray-800 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700 file:transition file:cursor-pointer">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                        <i class="fas fa-save text-xs"></i>
                        Save changes
                    </button>
                </div>
            </form>
        </section>

        {{-- Password form --}}
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Security
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Change password
                </h2>
            </div>

            <form action="{{ route('administrator.profile.password') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="current_password" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Current password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required
                            class="w-full px-4 py-3 pr-11 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                        <button type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
                            onclick="togglePasswordVisibility('current_password', this)">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            New password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-3 pr-11 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                            <button type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
                                onclick="togglePasswordVisibility('password', this)">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Confirm new password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-3 pr-11 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                            <button type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
                                onclick="togglePasswordVisibility('password_confirmation', this)">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                        <i class="fas fa-key text-xs"></i>
                        Update password
                    </button>
                </div>
            </form>
        </section>
    </div>
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