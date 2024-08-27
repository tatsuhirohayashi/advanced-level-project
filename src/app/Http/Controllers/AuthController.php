<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrator;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OwnerLoginRequest;
use App\Http\Requests\AdminLoginRequest;

class AuthController extends Controller
{
    public function thanks()
    {
        return view('thanks');
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        // 管理者情報の確認
        $administrator = Administrator::where('email', $request->email)->first();

        // 管理者が存在し、パスワードが一致するか確認
        if ($administrator && Hash::check($request->password, $administrator->password)) {
            // 認証処理
            Auth::login($administrator);

            // ログイン成功後のリダイレクト
            return redirect('/admin/shop-register');
        }

        // 認証失敗時の処理
        return redirect('/login/admin');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();

        // セッションを無効化する
        $request->session()->invalidate();

        // セッション再生成
        $request->session()->regenerateToken();

        // ログアウト後のリダイレクト
        return redirect('/login/admin');
    }

    public function showOwnerLoginForm()
    {
        return view('auth.owner-login');
    }

    public function ownerLogin(OwnerLoginRequest $request)
    {
        // 管理者情報の確認
        $restaurant = Restaurant::where('email', $request->email)->first();

        // 管理者が存在し、パスワードが一致するか確認
        if ($restaurant && Hash::check($request->password, $restaurant->password)) {
            // 認証処理
            Auth::guard('restaurant')->login($restaurant);

            // ログイン成功後にレストランのIDをURLパラメータとして渡してリダイレクト
            return redirect()->route('owner.shop-register', ['id' => $restaurant->id]);
        }

        // 認証失敗時の処理
        return redirect('/login/owner');
    }

    public function ownerLogout(Request $request)
    {
        // カスタムガードを使用してログアウト
        Auth::guard('restaurant')->logout();

        // セッションを無効化する
        $request->session()->invalidate();

        // セッション再生成
        $request->session()->regenerateToken();

        // ログアウト後のリダイレクト
        return redirect('/login/owner');
    }
}
