@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
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
    <div class="detail__content">
        <div class="detail__content-restaurant">
            <div class="restaurant__content-header">
                <button class="back-button" onclick="history.back()">＜</button>
                <div class="restaurant__content-name">{{ $restaurant['restaurant_name'] }}</div>
            </div>
            <div class="restaurant__img">
                <img src="{{ asset($restaurant['image_url']) }}" alt="">
            </div>
            <div class="restaurant__content-tag">
                @if(isset($restaurant['area']['name']))
                <p class="restaurant__content-area">#{{ $restaurant['area']['name'] }}</p>
                @endif
                @if(isset($restaurant['genre']['name']))
                <p class="restaurant__content-genre">#{{ $restaurant['genre']['name'] }}</p>
                @endif
            </div>
            <div class="restaurant__content-description">{{ $restaurant['description'] }}</div>
        </div>
        <div class="detail__content-reserve">
            @livewire('reservation-form', ['restaurant' => $restaurant])
        </div>
    </div>
    <div class="detail__comment">
        @auth
        @if ($hasReservation)
        <div class="detail__comment-input">
            <form class="comment-form" action="/review" method="post">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ $restaurant['id'] }}">
                <div class="detail__comment-input-header">レビュー</div>
                <div class="detail__comment-input-ttl">
                    <div class="detail__comment-input-rate">評価</div>
                    <select class="detail__comment-input-rate-select" name="rate">
                        @for ($i = 1; $i <= 5; $i++) <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
                <div class="detail__comment-input-ttl">
                    <div class="detail__comment-input-review">感想</div>
                    <textarea class="detail__comment-input-textarea" name="comment" rows="4"></textarea>
                </div>
                <div class="form__comment-button">
                    <button class="form__comment-button-submit" type="submit">コメントする</button>
                </div>
            </form>
        </div>
        @endif
        @endauth
        <div class="detail__comment-content">
            <div class="detail__comment-content-ttl">
                <div class="detail__comment-content-rate">評価</div>
                <div class="detail__comment-content-review">感想</div>
            </div>
            @foreach ($comments as $comment)
            <div class="detail__comment-content-ttl">
                <div class="detail__comment-content-rate">{{ $comment['rate'] }}</div>
                <div class="detail__comment-content-review">{{ $comment['comment'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection