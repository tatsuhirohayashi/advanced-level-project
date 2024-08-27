<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OwnerRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'restaurant_name' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:254', 'unique:restaurants,email'],
            'password' => ['required', 'min:8', 'max:60'],
        ];
    }

    public function messages()
    {
        return [
            'restaurant_name.required' => '店名を入力してください',
            'restaurant_name.string' => '店名は文字列で入力してください',
            'restaurant_name.max' => '店名は100文字以内で入力してください',
            'name.required' => '店舗代表者を入力してください',
            'name.string' => '店舗代表者は文字列で入力してください',
            'name.max' => '店舗代表者は100文字以内で入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.string' => 'メールアドレスは文字列で入力してください',
            'email.email' => 'メールアドレスに@を入力してください',
            'email.max' => 'メールアドレスは254文字以内で入力してください',
            'email.unique' => 'このメールアドレスは既に登録されています',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password.max' => 'パスワードは60文字以内で入力してください',
        ];
    }
}
