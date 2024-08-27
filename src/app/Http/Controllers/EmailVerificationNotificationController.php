<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(config('fortify.home'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
