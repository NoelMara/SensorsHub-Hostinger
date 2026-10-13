@extends('layouts.app')

@php($isAdministratorLogin = ($loginMode ?? 'default') === 'administrator')

@section('title', $isAdministratorLogin ? 'Administrator Login' : 'Login')

@push('styles')
<style>
    .auth-shell {
        background:
            radial-gradient(circle at 20% 10%, rgba(16, 185, 129, 0.08), transparent 40%),
            radial-gradient(circle at 85% 90%, rgba(59, 130, 246, 0.08), transparent 40%),
            #fafafa;
    }
    .dark .auth-shell {
        background:
            radial-gradient(circle at 20% 10%, rgba(16, 185, 129, 0.06), transparent 40%),
            radial-gradient(circle at 85% 90%, rgba(59, 130, 246, 0.06), transparent 40%),
            #111827;
    }

    .auth-input {
        transition: border-color 200ms ease, box-shadow 200ms ease;
    }
    .auth-input:focus {
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }

    @keyframes cursor-blink {
        0%, 49% { opacity: 1; }
        50%, 100% { opacity: 0; }
    }
    .cursor-blink {
        display: inline-block;
        width: 0.5ch;
        animation: cursor-blink 1.1s step-end infinite;
    }
    .terminal-line {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    
</style>
@endpush

@section('content')
<div class="auth-shell min-h-screen">
    <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 items-stretch gap-0 lg:grid-cols-2">

        {{-- ==================== LEFT PANEL ==================== --}}
        <section class="hidden lg:flex flex-col justify-center gap-10 bg-gray-950 p-10 xl:p-14 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] rounded-full bg-emerald-500/6 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-[350px] h-[350px] rounded-full bg-blue-500/6 blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-sm font-medium text-gray-400 hover:text-white transition">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    SensorsHub
                </a>
            </div>

            <div class="relative z-10 max-w-lg">
                <p class="terminal-line text-sm text-emerald-400 mb-5">
                    <span class="text-gray-600">$</span> {{ $isAdministratorLogin ? 'admin.access' : 'auth.session' }}<span class="cursor-blink">▌</span>
                </p>

                <h1 class="text-4xl xl:text-5xl font-semibold leading-[1.1] tracking-tight mb-6">
                    {{ $isAdministratorLogin ? 'Manage the platform.' : 'Pick up where you left off.' }}
                </h1>
                <p class="text-base text-gray-400 leading-relaxed mb-8">
                    {{ $isAdministratorLogin ? 'Review accounts, roles, and platform activity from a secure entry point.' : 'Access guides, save project ideas, and continue learning in a workspace built for makers.' }}
                </p>

                <div class="bg-black border border-gray-900/80 rounded-lg overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-900">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-gray-800"></span>
                            <span class="w-2 h-2 rounded-full bg-gray-800"></span>
                            <span class="w-2 h-2 rounded-full bg-gray-800"></span>
                        </div>
                        <span class="terminal-line text-[10px] uppercase tracking-widest text-gray-700">session.sh</span>
                    </div>
                    <div class="p-4 terminal-line text-xs leading-relaxed space-y-0.5">
                        <div class="flex">
                            <span class="text-gray-700 select-none w-5 text-right mr-4 shrink-0">1</span>
                            <span class="whitespace-pre"><span class="text-emerald-400">$</span> <span class="text-gray-300">authenticate --secure</span></span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-700 select-none w-5 text-right mr-4 shrink-0">2</span>
                            <span class="whitespace-pre"><span class="text-gray-500">  verifying credentials...</span></span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-700 select-none w-5 text-right mr-4 shrink-0">3</span>
                            <span class="whitespace-pre"><span class="text-emerald-400">  ✓ session ready</span></span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-700 select-none w-5 text-right mr-4 shrink-0">4</span>
                            <span class="whitespace-pre"><span class="text-emerald-400">$</span> <span class="text-emerald-400">▌</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-6 pt-7 border-t border-gray-900">
                <div>
                    <p class="text-2xl font-semibold text-white">{{ ($stats['projects'] ?? 0) }}+</p>
                    <p class="mt-1 text-xs uppercase tracking-widest text-gray-500">Guides</p>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-white">24/7</p>
                    <p class="mt-1 text-xs uppercase tracking-widest text-gray-500">Access</p>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-white">IoT</p>
                    <p class="mt-1 text-xs uppercase tracking-widest text-gray-500">Focused</p>
                </div>
            </div>
        </section>

        {{-- ==================== RIGHT PANEL — FORM ==================== --}}
        <section class="flex items-center justify-center p-6 sm:p-10 lg:p-16">
            <div class="w-full max-w-md">

                {{-- Mobile brand --}}
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 mb-8 lg:hidden">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    SensorsHub
                </a>

                {{-- ============ FORM CARD ============ --}}
                <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">

                    {{-- Card header bar --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-black/40">
                        <div class="flex items-center gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="terminal-line text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                {{ session('require_verification') ? 'verify' : ($isAdministratorLogin ? 'admin' : 'sign in') }}
                            </span>
                            <span class="cursor-blink text-emerald-500 text-xs">▌</span>
                        </div>
                        <span class="terminal-line text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-600">
                            auth
                        </span>
                    </div>

                    {{-- Card body --}}
                    <div class="p-6 sm:p-8">

                        {{-- Heading --}}
                        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">
                            @if(session('require_verification'))
                                Enter your code
                            @elseif($isAdministratorLogin)
                                Administrator access
                            @else
                                Welcome back
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                            @if(session('require_verification'))
                                We sent a 6-digit code to <span class="font-medium text-gray-900 dark:text-white">{{ session('user_email') }}</span>
                            @elseif($isAdministratorLogin)
                                Sign in with your administrator credentials.
                            @else
                                Sign in to continue building.
                            @endif
                        </p>

                        @if(session('require_verification'))
                            {{-- ============ VERIFY CODE MODE ============ --}}
                            @if(session('message'))
                                <div class="mb-5 flex items-center gap-2.5 border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg px-4 py-3 text-sm text-emerald-800 dark:text-emerald-300">
                                    <i class="fas fa-check-circle text-xs"></i>
                                    {{ session('message') }}
                                </div>
                            @endif

                            @error('email')
                                <div class="mb-5 flex items-center gap-2.5 border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 rounded-lg px-4 py-3 text-sm text-red-700 dark:text-red-300">
                                    <i class="fas fa-circle-exclamation text-xs"></i>
                                    {{ $message }}
                                </div>
                            @enderror

                            <form method="POST" action="{{ route('login.verify') }}" class="space-y-5">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('user_email') }}">

                                <div>
                                    <label for="verification_code" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                                        Verification code
                                    </label>
                                    <input
                                        id="verification_code"
                                        name="verification_code"
                                        type="text"
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        required
                                        class="auth-input w-full px-4 py-4 text-center text-2xl font-semibold tracking-[0.35em] rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition @error('verification_code') border-red-500 dark:border-red-500 @enderror"
                                        placeholder="000000"
                                        autocomplete="one-time-code"
                                    >
                                    @error('verification_code')
                                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                                    Verify and continue
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            </form>

                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                                <form method="POST" action="{{ route('login.resend') }}">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ session('user_email') }}">
                                    <button type="submit" class="font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition">
                                        Resend code
                                    </button>
                                </form>
                                <a href="{{ $isAdministratorLogin ? route('administrator.login') : route('login') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                    Back to login
                                </a>
                            </div>

                        @else
                            {{-- ============ LOGIN MODE ============ --}}
                            <form method="POST" action="{{ $isAdministratorLogin ? route('administrator.login.submit') : route('login') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="email" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                                        Email
                                    </label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        required
                                        value="{{ old('email') }}"
                                        placeholder="you@example.com"
                                        class="auth-input w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition @error('email') border-red-500 dark:border-red-500 @enderror"
                                    >
                                    @error('email')
                                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            required
                                            placeholder="••••••••"
                                            class="auth-input w-full px-4 py-3 pr-12 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition @error('password') border-red-500 dark:border-red-500 @enderror"
                                        >
                                        <button type="button" id="togglePassword" aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <label for="remember" class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                                    <input id="remember" name="remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 text-emerald-600 dark:text-emerald-500 focus:ring-0 focus:ring-offset-0">
                                    Keep me signed in
                                </label>


                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                                    {{ $isAdministratorLogin ? 'Sign in as administrator' : 'Sign in' }}
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Card footer (hidden during 2FA verify) --}}
                    @if(!session('require_verification'))
                    <div class="px-6 sm:px-8 py-4 border-t border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-black/40 text-sm text-gray-500 dark:text-gray-400">
                        @if($isAdministratorLogin)
                            Need a regular account?
                            <a href="{{ route('login') }}" class="font-medium text-gray-900 dark:text-white hover:underline">
                                User login
                            </a>
                        @else
                            New to SensorsHub?
                            <a href="{{ route('register') }}" class="font-medium text-gray-900 dark:text-white hover:underline">
                                Create an account
                            </a>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Trust line --}}
                <p class="mt-6 text-xs text-gray-400 dark:text-gray-600 text-center">
                    <i class="fas fa-shield-halved mr-1"></i>
                    Secured with two-factor authentication
                </p>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const codeInput = document.getElementById('verification_code');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    }

    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    if (passwordInput && togglePassword) {
        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
</script>
@endpush