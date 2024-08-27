<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\EmailVerificationNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ShopController::class, 'index']);
Route::get('/detail/{id}', [ShopController::class, 'show'])->name('restaurants.show');
Route::get('/search', [ShopController::class, 'search']);

// メール認証リマインダーページ
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール認証リンクの確認
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

// メール認証リンクの再送信
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ユーザーログインページの表示と処理
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest']);

// ユーザーログアウト処理
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

// ユーザー新規登録ページの表示と処理
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware(['guest'])
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest']);

Route::get('/thanks', [AuthController::class, 'thanks']);


Route::middleware('auth', 'verified')->group(function () {
    Route::get('/mypage', [ShopController::class, 'profile']);
    Route::post('/detail/{id}', [ShopController::class, 'reserve'])->name('restaurants.reserve');
    Route::get('/done', [ShopController::class, 'confirmation']);
    Route::delete('/delete', [ShopController::class, 'destroy']);
    Route::post('/favorite', [ShopController::class, 'favorite']);
    Route::get('/edit/{id}', [ShopController::class, 'edit'])->name('reservations.edit');
    Route::patch('/edit/{id}', [ShopController::class, 'update'])->name('reservations.update');
    Route::post('/review', [ShopController::class, 'review']);
    Route::post('/checkout/{id}', [ShopController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/success', [ShopController::class, 'success'])->name('checkout.success');
    Route::get('/cancel', [ShopController::class, 'cancel'])->name('checkout.cancel');
});


Route::get('/reserve/{id}', [ShopController::class, 'confirm'])->name('reservation.confirm');

//管理者ログインページの表示
Route::get('/login/admin', [AuthController::class, 'showAdminLoginForm']);

//管理者ログイン処理
Route::post('/login/admin', [AuthController::class, 'adminLogin']);

//管理者ログアウト処理
Route::post('/logout/admin', [AuthController::class, 'adminLogout']);

//店舗代表者ログインページの表示
Route::get('/login/owner', [AuthController::class, 'showOwnerLoginForm']);

//店舗代表者ログイン処理
Route::post('/login/owner', [AuthController::class, 'ownerLogin']);

//店舗代表者ログアウト処理
Route::post('/logout/owner', [AuthController::class, 'ownerLogout']);


Route::middleware('auth')->group(function () {
    //管理者用店舗登録ページの表示
    Route::get('/admin/shop-register', [ShopController::class, 'adminShowShopRegistrationForm']);

    //管理者用店舗登録処理
    Route::post('/admin/shop-register', [ShopController::class, 'adminShopRegistration']);

    Route::get('/scheduler/settings', [ShopController::class, 'showSettings'])->name('scheduler.setting');
    Route::post('/scheduler/settings', [ShopController::class, 'saveSettings'])->name('scheduler.saving');
});


Route::middleware('auth:restaurant')->group(function () {
    //店舗代表者用店舗登録ページの表示
    Route::get('/owner/shop-register/{id}', [ShopController::class, 'ownerShowShopRegistrationForm'])->name('owner.shop-register');

    //店舗代表者用店舗登録処理
    Route::post('/owner/shop-register/{id}', [ShopController::class, 'ownerShopRegistration']);

    //店舗代表者用予約確認ページの表示
    Route::get('/owner/confirm/{id}', [ShopController::class, 'ownerShowReservationConfirmationForm'])->name('owner.confirm');

    //お知らせメールの処理
    Route::post('/owner/confirm/{id}', [ShopController::class, 'ownerReservationConfirmation']);
});
