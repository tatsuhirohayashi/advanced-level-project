<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRegisterRequest extends FormRequest
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
            'area_id' => ['required'],
            'genre_id' => ['required'],
            'description' => ['required', 'string', 'max:500'],
            'image_url' => ['required'],
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
            'area_id.required' => '地域を選択してください',
            'genre_id.required' => 'ジャンルを選択してください',
            'description.required' => '店舗情報を入力してください',
            'description.string' => '店舗情報は文字列で入力してください',
            'description.max' => '店舗情報は500文字以内で入力してください',
            'image_url.required' => '店舗画像を入力してください',
        ];
    }
}
