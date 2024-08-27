@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
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
                    <li><a href="/register">Registration</a></li>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/login/admin">管理者はこちら</a></li>
                    <li><a href="/login/owner">店舗代表者はこちら</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>



<div class="body">
    <div class="login__content">
        <div class="login__content-ttl">
            <div class="login__content-header">
                <h2 class="login__content-header-h2">Login</h2>
            </div>
            <div class="login__content-body">
                <form class="form" action="/login" method="post">
                    @csrf
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <i class="form__input--text-email-icon"></i>
                                <input type="email" name="email" class="form__input--text-email" placeholder="Email" value="{{ old('email') }}" />
                            </div>
                            <div class="form__error">
                                @error('email')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <i class="form__input--text-password-icon"></i>
                                <input type="password" name="password" class="form__input--text-password" placeholder="Password" value="{{ old('password') }}" />
                            </div>
                            <div class="form__error">
                                @error('password')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__button">
                        <button class="form__button-submit" type="submit">ログイン</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection