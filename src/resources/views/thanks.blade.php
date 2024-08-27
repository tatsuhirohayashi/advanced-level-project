@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
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
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="body">
    <div class="thanks__content">
        <div class="thanks__content-ttl">
            <div class="thanks__content-header">
                <h2 class="thanks__content-header-h2">会員登録ありがとうございます</h2>
            </div>
            <div class="thanks__button">
                <a href="{{ route('verification.notice') }}" class="thanks__button-submit">ログインする</a>
            </div>
        </div>
    </div>
</div>


@endsection