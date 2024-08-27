<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterViewResponse;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;

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

        // Generate email verification URL
        //$verificationUrl = URL::temporarySignedRoute(
        //'verification.verify',
        //now()->addMinutes(60),
        //['id' => $user->id, 'hash' => sha1($user->email)]
        //);

        // Send verification email
        //Mail::to($user->email)->send(new VerifyEmail($verificationUrl));

        return app(RegisterResponse::class);
    }
}
