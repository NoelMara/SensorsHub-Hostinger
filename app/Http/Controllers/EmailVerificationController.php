<?php

namespace App\Http\Controllers;

use App\Notifications\VerifyEmailWithCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->email_verified_at) {
            return $this->redirectToDashboard($request->user());
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $key = 'verify-code:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'code' => "Too many attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect('/dashboard')->with('message', 'Your email is already verified!');
        }

        if ($user->verifyCode($request->code)) {
            RateLimiter::clear($key);
            $user->clearVerificationCode();
            return redirect('/dashboard')->with('message', 'Email verified successfully!');
        }

        RateLimiter::hit($key, 120);

        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }

    public function resend(Request $request)
    {
        $key = 'resend-code:' . $request->ip() . ':' . $request->user()->email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many resend attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        RateLimiter::hit($key, 120);

        $user = $request->user();
        $code = $user->generateVerificationCode();
        $user->notify(new VerifyEmailWithCode($code));

        return back()->with('message', 'A new verification code has been sent to your email!');
    }

    private function redirectToDashboard($user)
    {
        if ($user->isAdministrator()) {
            return redirect()->route('administrator.dashboard');
        }

        if ($user->isInstructor()) {
            return redirect()->route('instructor.dashboard');
        }

        return redirect()->route('dashboard.index');
    }
}