<div>
    <div class="review__content-review-text">
        <p class="review__content-review-text-p">口コミを投稿</p>
        <textarea class="review__content-review-text-textarea" placeholder="カジュアルな夜のお出かけにおすすめのスポット" rows="7" type="text" name="review" value="" wire:model="review">{{ old('review') }}</textarea>
        <p class="review__content-review-text-count">{{ mb_strlen(old('review', $review ?? '')) }}/{{ $maxLength }}（最高文字数）</p>
        <div class="form__error">
            @error('review')
            {{ $message }}
            @enderror
        </div>
    </div>
</div>