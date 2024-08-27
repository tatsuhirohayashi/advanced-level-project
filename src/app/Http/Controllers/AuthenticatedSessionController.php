<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login view.
     *
     * @return \Laravel\Fortify\Contracts\LoginViewResponse
     */
    public function create(): LoginViewResponse
    {
        return app(LoginViewResponse::class);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Laravel\Fortify\Contracts\LoginResponse
     */
    public function store(LoginRequest $request): LoginResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return app(LoginResponse::class);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LogoutResponse
     */
    public function destroy(Request $request): LogoutResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
