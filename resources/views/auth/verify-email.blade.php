@extends('layouts.app')

@section('title', 'Verify Your Email Address')

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
<div class="auth-shell min-h-screen flex items-center justify-center p-6 sm:p-10">

    <div class="w-full max-w-md">

        {{-- ============ FORM CARD ============ --}}
        <div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">

            {{-- Card header bar --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-black/40">
                <div class="flex items-center gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="terminal-line text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        verify.email
                    </span>
                    <span class="cursor-blink text-emerald-500 text-xs">▌</span>
                </div>
                <span class="terminal-line text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-600">
                    code
                </span>
            </div>

            {{-- Card body --}}
            <div class="p-6 sm:p-8">

                {{-- Heading --}}
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">
                    Check your email
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                    We sent a 6-digit verification code to
                    <span class="font-medium text-gray-900 dark:text-white break-all">{{ auth()->user()->email }}</span>
                </p>

                {{-- Success message --}}
                @if (session('message'))
                    <div class="mb-5 flex items-center gap-2.5 border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg px-4 py-3 text-sm text-emerald-800 dark:text-emerald-300">
                        <i class="fas fa-check-circle text-xs"></i>
                        {{ session('message') }}
                    </div>
                @endif

                {{-- Error messages --}}
                @if ($errors->any())
                    <div class="mb-5 border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 rounded-lg px-4 py-3 text-sm text-red-700 dark:text-red-300">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle-exclamation text-xs mt-1"></i>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Verify form --}}
                <form method="POST" action="{{ route('verification.verify') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="code" class="block text-xs font-medium uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
                            Verification code
                        </label>
                        <input
                            type="text"
                            name="code"
                            id="code"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            autocomplete="one-time-code"
                            placeholder="000000"
                            class="auth-input w-full px-4 py-4 text-center text-2xl font-semibold tracking-[0.35em] rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-black text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition"
                        >
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-500 flex items-center gap-1.5">
                            <i class="fas fa-clock"></i>
                            Code expires in 10 minutes
                        </p>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                        Verify email
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-900 flex items-center justify-between gap-3">
                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition">
                            Resend code
                        </button>
                    </form>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Trust line --}}
        <p class="mt-6 text-xs text-gray-400 dark:text-gray-600 text-center">
            <i class="fas fa-shield-halved mr-1"></i>
            Secured with two-factor authentication
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    }
</script>
@endpush