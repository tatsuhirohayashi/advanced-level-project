@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/owner-reservation-confirm.css') }}">
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
                    <li><a href="{{ route('owner.shop-register', ['id' => $restaurant->id]) }}">店舗登録</a></li>
                    <li>
                        <form class="form-logout" action="/logout/owner" method="post">
                            @csrf
                            <button class="header-nav__li-button" type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="body">
    <div class="reservation__content">
        <div class="reservation__content-ttl">
            <div class="reservation__content-header">
                <h2 class="reservation__content-header-h2">予約情報</h2>
            </div>
            <div class="reservation__content-body">
                <form class="form" action="/owner/confirm/{{ $restaurant->id }}" method="post">
                    @csrf
                    <div class="reservation__content-body-item">
                        <div class="reservation__content-body-date">予約日</div>
                        <div class="reservation__content-body-time">時間</div>
                        <div class="reservation__content-body-people">人数</div>
                    </div>
                    @foreach ($reservations as $reservation)
                    <div class="reservation__content-body-item">
                        <div class="reservation__content-body-date">{{ $reservation['reservation_date'] }}</div>
                        <div class="reservation__content-body-time">{{ \Carbon\Carbon::parse($reservation['reservation_time'])->format('H:i') }}</div>
                        <div class="reservation__content-body-people">{{ $reservation['number_of_people'] }}</div>
                    </div>
                    <input type="hidden" name="reservation_id[]" value="{{ $reservation['id'] }}">
                    @endforeach
                    <div class="reservation__content-body-mail">
                        <textarea type="text" name="message" class="reservation__content-body-mail-textarea" placeholder="内容を入力してください" rows="20" value=""></textarea>
                    </div>
                    <div class="form__button">
                        <button class="form__button-submit" type="submit">お知らせメールを送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection