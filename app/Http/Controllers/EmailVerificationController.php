<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Notifications\WelcomeNotification;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        // Send welcome email after successful verification
        $request->user()->notify(new WelcomeNotification());

        return redirect()->route('dashboard')->with('success', 'Your email has been verified! Welcome to the marketplace.');
    }

    /**
     * Send a new verification email to the user.
     */
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'A new verification link has been sent to your email address.');
    }
}
