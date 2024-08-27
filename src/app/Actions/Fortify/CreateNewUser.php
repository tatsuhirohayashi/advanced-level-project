<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input)
    {
        // バリデーションルールをRegisterRequestから取得
        $rules = (new RegisterRequest())->rules();
        $messages = (new RegisterRequest())->messages();

        // Validatorファサードを使ってバリデーション
        $validator = Validator::make($input, $rules, $messages);
        $validated = $validator->validate();

        // ユーザー作成
        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
    }
}
