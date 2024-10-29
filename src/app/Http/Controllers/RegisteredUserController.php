<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterViewResponse;
use App\Http\Requests\RegisterRequest;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration view.
     *
     * @return \Laravel\Fortify\Contracts\RegisterViewResponse
     */
    public function create(): RegisterViewResponse
    {
        return app(RegisterViewResponse::class);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Laravel\Fortify\Contracts\RegisterResponse
     */
    public function store(RegisterRequest $request): RegisterResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return app(RegisterResponse::class);
    }
}
