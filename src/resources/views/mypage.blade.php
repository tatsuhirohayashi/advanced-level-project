@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
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
    <div class="mypage__content">
        <div class="mypage__heading">
            <h2>{{ Auth::user()->name }}さん</h2>
        </div>
        <div class="mypage__content-ttl">
            <div class="mypage__content-reservation">
                <div class="mypage__content-reservation-header">予約状況</div>
                @foreach ($reservations as $reservation)
                <div class="reserve__content-confirm">
                    <div class="reserve__content-confirm-restaurant">
                        <div class="reserve__content-confirm-reservation-number">予約{{ $reservation['id'] }}</div>
                        <div class="reserve__content-confirm-reservation-delete">
                            <form class="form" action="/delete" method="post">
                                @method('DELETE')
                                @csrf
                                <input type="hidden" name="id" value="{{ $reservation['id'] }}">
                                <button class="form__button-submit" type="submit">×</button>
                            </form>
                        </div>
                    </div>
                    <div class="reserve__content-confirm-restaurant-ttl">
                        <div class="reserve__content-confirm-restaurant-item">Shop</div>
                        <div class="reserve__content-confirm-restaurant-name">{{ $reservation['restaurant']['restaurant_name'] }}</div>
                    </div>
                    <div class="reserve__content-confirm-restaurant-ttl">
                        <div class="reserve__content-confirm-reservation-item">Date</div>
                        <div class="reserve__content-confirm-reservation-date">{{ $reservation['reservation_date'] }}</div>
                    </div>
                    <div class="reserve__content-confirm-restaurant-ttl">
                        <div class="reserve__content-confirm-reservation-item">Time</div>
                        <div class="reserve__content-confirm-reservation-time">{{ \Carbon\Carbon::parse($reservation['reservation_time'])->format('H:i') }}</div>
                    </div>
                    <div class="reserve__content-confirm-restaurant-ttl">
                        <div class="reserve__content-confirm-reservation-item">Number</div>
                        <div class="reserve__content-confirm-reservation-time">{{ $reservation['number_of_people'] }}人</div>
                    </div>
                    <div class="reserve__content-confirm-qrcode">
                        <img class="reserve__content-confirm-qrcode-img" src="{{ asset('storage/qr_codes/' . $reservation->qr_code) }}" alt="QR Code">
                    </div>
                    <div class="form__button-edit">
                        <input type="hidden" name="id" value="{{ $reservation['id'] }}">
                        <a href="{{ route('reservations.edit', ['id' => $reservation['id']]) }}" class="form__button-edit-submit" type="submit">予約変更</a>
                    </div>
                    <div class="reserve__content-confirm-checkout">
                        <form action="{{ route('stripe.checkout', ['id' => $reservation['restaurant_id']]) }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $reservation['id'] }}">
                            <button type="submit" class="form__button-checkout">決済する</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mypage__content-favorite">
                <div class="mypage__content-reservation-header">お気に入り店舗</div>
                <div class="mypage__content-favorite-ttl">
                    @foreach ($favoriteRestaurants as $restaurant)
                    <div class="restaurant">
                        <div class="restaurant__img">
                            <img src="{{ $restaurant['image_url'] }}" alt="">
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection