<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'rate' => ['required'],
            'review' => ['required', 'max:400'],
            'image_url' => ['mimes:jpeg,jpg,png'],
        ];
    }

    public function messages()
    {
        return [
            'rate.required' => '評価を入力してください',
            'review.required' => '口コミを入力してください',
            'review.max' => '口コミは400字以内で入力してください',
            'image_url.mimes' => '画像の形式はjpegまたはpngにしてください',
        ];
    }
}
