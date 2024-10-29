@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-store-register.css') }}">
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
                    <li><a href="">店舗代表者登録</a></li>
                    <li><a href="{{ route('scheduler.setting') }}">リマインドメール設定</a></li>
                    <li>
                        <form class="form-logout" action="/logout/admin" method="post">
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
                <h2 class="register__content-header-h2">店舗代表者情報</h2>
            </div>
            <div class="register__content-body">
                <form class="form" action="" method="post">
                    @csrf
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="text" name="restaurant_name" class="form__input--text-restaurant_name" placeholder="Restaurant Name" value="{{ old('restaurant_name') }}" />
                            </div>
                            <div class="form__error">
                                @error('restaurant_name', 'register')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="text" name="name" class="form__input--text-name" placeholder="Owner Name" value="{{ old('name') }}" />
                            </div>
                            <div class="form__error">
                                @error('name', 'register')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="email" name="email" class="form__input--text-email" placeholder="Email" value="{{ old('email') }}" />
                            </div>
                            <div class="form__error">
                                @error('email', 'register')
                                {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="password" name="password" class="form__input--text-password" placeholder="Password" value="{{ old('password') }}" />
                            </div>
                            <div class="form__error">
                                @error('password', 'register')
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
        <div class="import__content-ttl">
            <div class="import__content-header">
                <h2 class="import__content-header-name">CSVファイルインポート</h2>
            </div>
            <div class="import__content-body">
                <form action="{{ route('admin.restaurants.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="import__content-body-input">
                        <input type="file" name="csv_file" id="csv_file" class="form-control" required>
                    </div>
                    @if ($errors->has('csv_errors'))
                    @foreach ($errors->get('csv_errors') as $message)
                    <div class="form__error">{{ $message }}</div>
                    @endforeach
                    @endif
                    <div class="form__button">
                        <button class="form__button-submit" type="submit">インポート</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="restaurant__content-ttl">
            <div class="restaurant__content-header">
                <h2 class="restaurant__content-header-name">店名</h2>
                <h2 class="restaurant__content-header-owner">店舗代表者</h2>
            </div>
            @foreach( $restaurants as $restaurant )
            <div class="restaurant__content-body">
                <div class="restaurant__content-body-name">{{ $restaurant['restaurant_name'] }}</div>
                <div class="restaurant__content-body-owner">{{ $restaurant['name'] }}</div>
            </div>
            @endforeach
        </div>
        <div class="review__content-ttl">
            <div class="review__content-header">
                <h2 class="review__content-header-name">名前</h2>
                <h2 class="review__content-header-shopname">店名</h2>
                <h2 class="review__content-header-review">口コミ</h2>
                <h2 class="review__content-header-rate">評価</h2>
            </div>
            @foreach( $reviews as $review )
            <div class="review__content-body">
                <div class="review__content-body-name">{{ $review['user']['name'] }}</div>
                <div class="review__content-body-shopname">{{ $review['restaurant']['restaurant_name'] }}</div>
                <div class="review__content-body-review">{{ $review['review'] }}</div>
                <div class="review__content-body-rate">{{ $review['rate'] }}</div>
                <form action="{{ route('review.admin.delete', $review['id']) }}" class="delete-form" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="detail__review-content-button-delete" type=submit>口コミを削除</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection