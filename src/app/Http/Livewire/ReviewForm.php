<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ReviewForm extends Component
{
    public $review = ''; // textareaの内容を保持
    public $maxLength = 400; // 最大文字数

    // リアルタイムで更新
    public function updatedReview($value)
    {
        // 文字数を制限
        if (strlen($value) > $this->maxLength) {
            $this->review = substr($value, 0, $this->maxLength);
        }
    }
    public function render()
    {
        return view('livewire.review-form');
    }

    public function mount($review = null) // $reviewをオプションにする
    {
        // フォームエラー時はold()関数で値を保持、新規の場合は空文字、編集の場合は$reviewの内容
        $this->review = old('review', $review ? $review->review : '');
    }
}
