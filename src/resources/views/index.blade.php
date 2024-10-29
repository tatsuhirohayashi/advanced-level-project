@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
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
                    <li><a href="/login/admin">管理者はこちら</a></li>
                    <li><a href="/login/owner">店舗代表者はこちら</a></li>
                    @endif
                </ul>
            </div>
            <div class="sort">
                <form class="sort-form" action="/sort" method="get">
                    <div class="sort-form__item">
                        <select class="sort-form__item-select" name="sort" onchange="this.form.submit()">
                            <option value="">並び替え：評価高／低</option>
                            <option value="1" {{ request('sort') == '1' ? 'selected' : '' }}>ランダム</option>
                            <option value="2" {{ request('sort') == '2' ? 'selected' : '' }}>評価が高い順</option>
                            <option value="3" {{ request('sort') == '3' ? 'selected' : '' }}>評価が低い順</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="search">
                <form class="search-form" action="/search" method="get">
                    <div class="search-form__item">
                        <select class="search-form__item-area" name="area_id">
                            <option value="">All area</option>
                            @foreach ($areas as $area)
                            <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                            @endforeach
                        </select>
                        <select class="search-form__item-genre" name="genre_id">
                            <option value="">All genre</option>
                            @foreach ($genres as $genre)
                            <option value="{{ $genre['id'] }}">{{ $genre['name'] }}</option>
                            @endforeach
                        </select>
                        <input class="search-form__item-keyword" type="text" name="keyword" value="{{ old('keyword') }}" placeholder="🔍 Search...">
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>

<div class="index">
    <div class="index__content">
        <div class="index__content-ttl">
            @foreach ($restaurants as $restaurant)
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
                        <p class="restaurant__content-genre">#{{ $restaurant['genre']['name'] }}（{{ $restaurant->review_count }}件の口コミ）</p>
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


@endsection