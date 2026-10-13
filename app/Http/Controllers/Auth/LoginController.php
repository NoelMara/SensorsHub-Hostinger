<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Notifications\VerifyEmailWithCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', [
            'loginMode' => 'default',
            'stats' => [
                'projects' => Project::count(),
            ],
        ]);
    }

    public function showAdministratorLoginForm()
    {
        return view('auth.login', [
            'loginMode' => 'administrator',
            'stats' => [
                'projects' => Project::count(),
            ],
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();
        if ($user && $user->isBanned()) {
            $banMsg = $user->ban_until 
                ? 'Your account is banned until ' . $user->ban_until->format('M d, Y') . '.'
                : 'Your account has been permanently banned.';
            return back()->withErrors(['email' => $banMsg])->onlyInput('email');
        }

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->isAdministrator()) {
                return back()->withErrors([
                    'email' => 'These credentials do not match our records.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($key);

            if (!$user->email_verified_at) {
                $code = $user->generateVerificationCode();
                $user->notify(new VerifyEmailWithCode($code));

                return redirect()->back()->with([
                    'require_verification' => true,
                    'user_email' => $user->email,
                ]);
            }

            return $this->completeLogin($request, $user);
        }

        RateLimiter::hit($key, 120);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function administratorLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();
        if ($user && $user->isBanned()) {
            $banMsg = $user->ban_until 
                ? 'Your account is banned until ' . $user->ban_until->format('M d, Y') . '.'
                : 'Your account has been permanently banned.';
            return back()->withErrors(['email' => $banMsg])->onlyInput('email');
        }

        if (!$user || !$user->isAdministrator() || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, 120);

            return back()->withErrors([
                'email' => 'The provided Administrator credentials do not match our records.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($key);

        if (!$user->email_verified_at) {
            $code = $user->generateVerificationCode();
            $user->notify(new VerifyEmailWithCode($code));

            return redirect()->back()->with([
                'require_verification' => true,
                'user_email' => $user->email,
            ]);
        }

        return $this->completeLogin($request, $user);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'verification_code' => ['required', 'string', 'size:6'],
        ]);

        $key = 'verify-code:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'verification_code' => "Too many attempts. Please try again in {$seconds} seconds.",
            ])->with([
                'require_verification' => true,
                'user_email' => $request->email,
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'User not found.',
            ])->withInput();
        }

        if ($user->verifyCode($request->verification_code)) {
            RateLimiter::clear($key);

            $user->clearVerificationCode();

            return $this->completeLogin($request, $user);
        }

        RateLimiter::hit($key, 120);

        return back()->withErrors([
            'verification_code' => 'Invalid or expired verification code.',
        ])->with([
            'require_verification' => true,
            'user_email' => $request->email,
        ]);
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $key = 'resend-code:' . $request->ip() . ':' . $request->email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many resend attempts. Please try again in {$seconds} seconds.",
            ])->with([
                'require_verification' => true,
                'user_email' => $request->email,
            ]);
        }

        RateLimiter::hit($key, 120);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $code = $user->generateVerificationCode();
            $user->notify(new VerifyEmailWithCode($code));
        }

        return back()->with([
            'require_verification' => true,
            'user_email' => $request->email,
            'message' => 'A new verification code has been sent to your email!',
        ]);
    }

    public function logout(Request $request)
    {
        $redirectRoute = Auth::user()?->isAdministrator() ? 'administrator.login' : 'login';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirectRoute);
    }

    private function completeLogin(Request $request, User $user)
    {
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        if ($user->isAdministrator()) {
            return redirect()->route('administrator.dashboard');
        }

        if ($user->isInstructor()) {
            return redirect()->route('instructor.dashboard');
        }

        return redirect()->route('dashboard.index');
    }
}