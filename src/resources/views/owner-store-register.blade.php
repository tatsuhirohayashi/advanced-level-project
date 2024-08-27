@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/owner-store-register.css') }}">
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
                    <li><a href="{{ route('owner.confirm', ['id' => $restaurant->id]) }}">予約確認</a></li>
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
    <div class="register__content">
        <div class="register__content-ttl">
            <div class="register__content-header">
                <h2 class="register__content-header-h2">店舗情報</h2>
            </div>
            <div class="register__content-body">
                <form class="form" action="/owner/shop-register/{{ $restaurant->id }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="text" name="restaurant_name" class="form__input--text-restaurant_name" placeholder="Restaurant Name" value="{{ $restaurant['restaurant_name'] }}" />
                            </div>
                            <div class="form__error">
                                @error('restaurant_name')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="text" name="name" class="form__input--text-name" placeholder="Owner Name" value="{{ $restaurant['name'] }}" />
                            </div>
                            <div class="form__error">
                                @error('name')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <select class="select-form__item-area" name="area_id">
                                <option value="">Select Area</option>
                                @foreach ($areas as $area)
                                <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="form__error">
                                @error('area_id')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <select class="select-form__item-genre" name="genre_id">
                                <option value="">Select Genre</option>
                                @foreach ($genres as $genre)
                                <option value="{{ $genre['id'] }}">{{ $genre['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="form__error">
                                @error('genre_id')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <textarea type="text" name="description" class="form__input--text-description" placeholder="Restaurant Information" rows="4" value="{{ old('description') }}"></textarea>
                            </div>
                            <div class="form__error">
                                @error('description')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="file" name="image_url" class="form__input--text-image_url" placeholder="Restaurant Picture" value="{{ old('image_url') }}" />
                            </div>
                            <div class="form__error">
                                @error('image_url')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__button">
                        <button class="form__button-submit" type="submit">登録</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection