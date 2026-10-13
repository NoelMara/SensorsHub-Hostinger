@extends('layouts.app')

@section('title', 'Register')

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

    .recaptcha-center {
        display: flex;
        justify-content: center;
        width: 100%;
    }

</style>
@endpush

@section('content')
<div class="auth-shell min-h-screen">
    <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 items-stretch gap-0 lg:grid-cols-2">

        {{-- ==================== LEFT PANEL — BRANDING ==================== --}}
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
                    <span class="text-gray-600">$</span> create account<span class="cursor-blink">▌</span>
                </p>

                <h1 class="text-4xl xl:text-5xl font-semibold leading-[1.1] tracking-tight mb-6">
                    A focused home<br>for sensor learning.
                </h1>
                <p class="text-base text-gray-400 leading-relaxed mb-8">
                    Organize projects, explore practical guides, and keep the materials you need close when you're ready to build.
                </p>

                <div class="space-y-3">
                    <div class="feature-row rounded-lg p-4 flex items-start gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300">
                            <i class="fas fa-book-open text-sm"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-white">Structured sensor guides</p>
                            <p class="mt-1 text-xs leading-relaxed text-gray-400">Browse clear references for modules, wiring, and project ideas.</p>
                        </div>
                    </div>

                    <div class="feature-row rounded-lg p-4 flex items-start gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-400/15 text-blue-300">
                            <i class="fas fa-folder-open text-sm"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-white">Saved project flow</p>
                            <p class="mt-1 text-xs leading-relaxed text-gray-400">Keep favorite builds and tutorials ready for your next session.</p>
                        </div>
                    </div>

                    <div class="feature-row rounded-lg p-4 flex items-start gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-cyan-400/15 text-cyan-300">
                            <i class="fas fa-lightbulb text-sm"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-white">Ideas that keep moving</p>
                            <p class="mt-1 text-xs leading-relaxed text-gray-400">Share suggestions and discover practical inspiration from the platform.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-6 pt-7 border-t border-gray-900">
                <div>
                    <p class="text-2xl font-semibold text-white">Free</p>
                    <p class="mt-1 text-xs uppercase tracking-widest text-gray-500">Forever</p>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-white">2FA</p>
                    <p class="mt-1 text-xs uppercase tracking-widest text-gray-500">Secured</p>
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
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    SensorsHub
                </a>

                {{-- ============ FORM CARD ============ --}}
                <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">

                    {{-- Card header bar --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-black/40">
                        <div class="flex items-center gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span class="terminal-line text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                register
                            </span>
                            <span class="cursor-blink text-blue-500 text-xs">▌</span>
                        </div>
                        <span class="terminal-line text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-600">
                            new user
                        </span>
                    </div>

                    {{-- Card body --}}
                    <div class="p-6 sm:p-8">

                        {{-- Heading --}}
                        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">
                            Create your account
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                            Start building in a workspace made for makers.
                        </p>

                        {{-- Registration form --}}
                        <form method="POST" action="{{ route('register') }}" class="space-y-5">
                            @csrf

                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                                    Full name
                                </label>
                                <input id="name" name="name" type="text" required
                                    value="{{ old('name') }}"
                                    placeholder="Juan Dela Cruz"
                                    class="auth-input w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition @error('name') border-red-500 dark:border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                                    Email
                                </label>
                                <input id="email" name="email" type="email" required
                                    value="{{ old('email') }}"
                                    placeholder="you@example.com"
                                    class="auth-input w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition @error('email') border-red-500 dark:border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" required
                                        placeholder="Minimum 8 characters"
                                        class="auth-input w-full px-4 py-3 pr-12 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition @error('password') border-red-500 dark:border-red-500 @enderror">
                                    <button type="button" id="togglePassword" aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm password --}}
                            <div>
                                <label for="password_confirmation" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                                    Confirm password
                                </label>
                                <div class="relative">
                                    <input id="password_confirmation" name="password_confirmation" type="password" required
                                        placeholder="Repeat password"
                                        class="auth-input w-full px-4 py-3 pr-12 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition">
                                    <button type="button" id="togglePasswordConfirm" aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Account type notice --}}
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                You're registering as a <span class="font-medium text-gray-900 dark:text-white">User</span>.
                            </p>

                            {{-- reCAPTCHA --}}
                            <div>
                                <div class="recaptcha-center">
                                    <div class="g-recaptcha"
                                        data-sitekey="{{ config('services.recaptcha.site_key') }}">
                                    </div>
                                </div>
                                @error('g-recaptcha-response')
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400 text-center">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                                Create account
                                <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Card footer --}}
                    <div class="px-6 sm:px-8 py-4 border-t border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-black/40 text-sm text-gray-500 dark:text-gray-400">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-gray-900 dark:text-white hover:underline">
                            Sign in
                        </a>
                    </div>
                </div>

                {{-- Trust line --}}
                <p class="mt-6 text-xs text-gray-400 dark:text-gray-600 text-center">
                    <i class="fas fa-shield-halved mr-1"></i>
                    Free forever · No credit card required
                </p>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    const passwordConfirmInput = document.getElementById('password_confirmation');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    if (passwordConfirmInput && togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', function() {
            const isPassword = passwordConfirmInput.type === 'password';
            passwordConfirmInput.type = isPassword ? 'text' : 'password';
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
</script>
@endpush