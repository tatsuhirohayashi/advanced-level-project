@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/review-post.css') }}">
@endsection

@section('content')

<header class="header">
    <div class="header__inner">
        <div class="header-utilities">
            <input type="checkbox" id="menu-toggle" class="menu-toggle">
            <label for="menu-toggle" class="header__logo">
                <span>Rese</span>
            </label>
            <div class="drawer-menu">
                <label for="menu-toggle" class="close-btn">
                    <div class="close-btn-wrapper">
                        <div class="close-btn-content">✕</div>
                    </div>
                </label>
                <ul>
                    <li><a href="/">Home</a></li>
                    @if (Auth::check())
                    <li>
                        <form class="form-logout" action="/logout" method="post">
                            @csrf
                            <button class="header-nav__li-button" type="submit">Logout</button>
                        </form>
                    </li>
                    <li><a href="/mypage">Mypage</a></li>
                    @else
                    <li><a href="/register">Registration</a></li>
                    <li><a href="/login">Login</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="body">
    <div class="review__content">
        <div class="review__content-restaurant">
            <h2 class="review__content-restaurant-h2">今回のご利用はいかがでしたか？</h2>
            <div class="restaurant">
                <div class="restaurant__img">
                    <img src="{{ asset($restaurant['image_url']) }}" alt="">
                </div>
                <div class="restaurant__content">
                    <div class="restaurant__content-name">{{ $restaurant['restaurant_name'] }}</div>
                    <div class="restaurant__content-tag">
                        @if(isset($restaurant['area']['name']))
                        <p class="restaurant__content-area">#{{ $restaurant['area']['name'] }}</p>
                        @endif
                        @if(isset($restaurant['genre']['name']))
                        <p class="restaurant__content-genre">#{{ $restaurant['genre']['name'] }}</p>
                        @endif
                    </div>
                    <div class="restaurant__content-button">
                        <a href="{{ route('restaurants.show', $restaurant['id']) }}" class="restaurant__content-button-a">詳しくみる</a>
                        <form class="favorite-form" action="/favorite" method="post">
                            @csrf
                            <input type="hidden" name="restaurant_id" value="{{ $restaurant['id'] }}">
                            <button type="submit" class="favorite-button">
                                @if (Auth::check())
                                @if (Auth::user()->favorites->contains('restaurant_id', $restaurant->id))
                                <span class="favorite-icon-favorite">❤️</span>
                                @else
                                <span class="favorite-icon-unfavorite">❤️</span>
                                @endif
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="review__content-review">
            <div class="review__content-review-rate">
                <form class="review-form" action="{{ route('review.store', $restaurant['id']) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <p class="review__content-review-rate-p">体験を評価してください</p>
                    <div class="rating">
                        <input type="radio" id="star5" name="rate" value="5" {{ old('rate') == 5 ? 'checked' : '' }} />
                        <label for="star5" title="5 stars">★</label>
                        <input type="radio" id="star4" name="rate" value="4" {{ old('rate') == 4 ? 'checked' : '' }} />
                        <label for="star4" title="4 stars">★</label>
                        <input type="radio" id="star3" name="rate" value="3" {{ old('rate') == 3 ? 'checked' : '' }} />
                        <label for="star3" title="3 stars">★</label>
                        <input type="radio" id="star2" name="rate" value="2" {{ old('rate') == 2 ? 'checked' : '' }} />
                        <label for="star2" title="2 stars">★</label>
                        <input type="radio" id="star1" name="rate" value="1" {{ old('rate') == 1 ? 'checked' : '' }} />
                        <label for="star1" title="1 star">★</label>
                    </div>
                    <div class="form__error">
                        @error('rate')
                        {{ $message }}
                        @enderror
                    </div>
            </div>
            @livewire('review-form')
            <div class="review__content-review-img">
                <p class="review__content-review-img-p">画像の追加</p>
                <div class="file-upload">
                    <input type="file" id="imageUpload" class="review__content-review-img-input" name="image_url" value="" placeholder="クリックして">
                    <label for="imageUpload" class="custom-file-upload">
                        クリックして写真を追加<br>またはドラッグアンドドロップ
                    </label>
                </div>
                <div class="form__error">
                    @error('image_url')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="review__content-button">
        <button class="review__content-submit" type="submit">口コミを投稿する</button>
    </div>
    </form>
</div>

@endsection