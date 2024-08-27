<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Favorite;
use App\Http\Requests\ReservationRequest;
use App\Models\Comment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ShopRegisterRequest;
use App\Http\Requests\OwnerRegisterRequest;
use App\Models\SchedulerSetting;

class ShopController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $areas = Area::all();
            $genres = Genre::all();
            $restaurants = Restaurant::with('area', 'genre')->get();

            return view('index', compact('areas', 'genres', 'restaurants'));
        } else {
            $areas = Area::all();
            $genres = Genre::all();
            $restaurants = Restaurant::with('area', 'genre')->get();

            return view('index', compact('areas', 'genres', 'restaurants'));
        }
    }

    public function show($id)
    {
        if (Auth::check()) {
            $restaurant = Restaurant::findOrFail($id);
            $comments = Comment::where('restaurant_id', $id)->get();

            $user_id = Auth::id();
            $hasReservation = Reservation::where('user_id', $user_id)->where('restaurant_id', $id)->exists();

            return view('detail', compact('restaurant', 'comments', 'hasReservation'));
        } else {
            $restaurant = Restaurant::findOrFail($id);
            $comments = Comment::where('restaurant_id', $id)->get();

            return view('detail', compact('restaurant', 'comments'));
        }
    }

    public function profile()
    {
        $user = Auth::user();
        $reservations = Reservation::with('restaurant')->where('user_id', $user->id)->orderBy('reservation_date', 'asc')->get();
        $favoriteRestaurants = Restaurant::whereIn('id', $user->favorites->pluck('restaurant_id'))->with('area', 'genre')->get();

        return view('mypage', compact('reservations', 'favoriteRestaurants'));
    }

    public function reserve(ReservationRequest $request, $id)
    {
        $reservation = new Reservation();
        $reservation->user_id = Auth::id();
        $reservation->restaurant_id = $id;
        $reservation->reservation_date = $request->input('reservation_date');
        $reservation->reservation_time = $request->input('reservation_time');
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->qr_code = '';
        $reservation->save();

        // QRコードに埋め込むURLを作成
        $url = route('reservation.confirm', ['id' => $reservation->id]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->build();

        // QRコードを保存するディレクトリのパスを定義
        $filePath = 'public/qr_codes/';

        // ファイル名を予約IDに基づいて設定
        $fileName = 'qr_' . $reservation->id . '.png';

        // フルパスを結合
        $fullPath = $filePath . $fileName;

        // QRコード画像をファイルシステムに保存
        Storage::put($fullPath, $result->getString());

        // データベースにファイルパスを保存
        $reservation->qr_code = $fileName;
        $reservation->save();

        return redirect('/done');
    }

    public function confirmation()
    {
        return view('done');
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return redirect('/mypage');
    }

    public function search(Request $request)
    {
        $query = Restaurant::query()
            ->AreaSearch($request->area_id)
            ->GenreSearch($request->genre_id)
            ->KeywordSearch($request->keyword);

        if (Auth::check()) {
            $restaurants = $query->get();
            $areas = Area::all();
            $genres = Genre::all();

            return view('index', compact('restaurants', 'areas', 'genres'));
        } else {
            $restaurants = $query->get();
            $areas = Area::all();
            $genres = Genre::all();

            return view('index', compact('restaurants', 'areas', 'genres'));
        }
    }

    public function favorite(Request $request)
    {
        $user = Auth::user();
        $restaurantId = $request->input('restaurant_id');

        $favorite = Favorite::where('user_id', $user->id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurantId,
            ]);
        }

        return redirect()->back();
    }

    public function edit($id)
    {
        $reservation = Reservation::with('restaurant')->findOrFail($id);

        return view('edit', compact('reservation'));
    }

    public function update(ReservationRequest $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->reservation_date = $request->input('reservation_date');
        $reservation->reservation_time = $request->input('reservation_time');
        $reservation->number_of_people = $request->input('number_of_people');

        $reservation->save();

        return redirect('/done');
    }

    public function review(Request $request)
    {
        $user_id = Auth::id();

        // 既にレビューが存在するかチェック
        $existingComment = Comment::where('restaurant_id', $request->input('restaurant_id'))
            ->where('user_id', $user_id)
            ->first();

        // 既にレビューがある場合は、リダイレクト（エラーメッセージを追加しても良い）
        if ($existingComment) {
            return redirect()->route('restaurants.show', ['id' => $request->input('restaurant_id')]);
        }

        // 新しいレビューを作成
        $comment = new Comment();
        $comment->restaurant_id = $request->input('restaurant_id');
        $comment->user_id = $user_id;
        $comment->rate = $request->input('rate');
        $comment->comment = $request->input('comment');
        $comment->save();

        return redirect()->route('restaurants.show', ['id' => $request->input('restaurant_id')]);
    }

    public function checkout(Request $request, $id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $restaurant = Restaurant::findOrFail($id);
        $amount = $restaurant->id;

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $restaurant->restaurant_name,
                    ],
                    'unit_amount' => $amount * 1000, // 金額をセンチ単位で指定
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
        ]);

        return Redirect::away($session->url);
    }

    public function success()
    {
        return view('success');
    }

    public function cancel()
    {
        return view('cancel');
    }

    public function adminShowShopRegistrationForm()
    {
        $restaurants = Restaurant::all();

        return view('admin-store-register', compact('restaurants'));
    }

    public function adminShopRegistration(OwnerRegisterRequest $request)
    {
        $restaurant = new Restaurant();
        $restaurant->restaurant_name = $request->input('restaurant_name');
        $restaurant->name = $request->input('name');
        $restaurant->email = $request->input('email');
        $restaurant->password = bcrypt($request->input('password'));
        $restaurant->save();

        return redirect('/admin/shop-register');
    }

    public function ownerShowShopRegistrationForm($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $areas = Area::all();
        $genres = Genre::all();

        return view('owner-store-register', compact('restaurant', 'areas', 'genres'));
    }

    public function ownerShopRegistration(ShopRegisterRequest $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // レストラン情報の更新
        $restaurant->restaurant_name = $request->input('restaurant_name');
        $restaurant->name = $request->input('name');
        $restaurant->area_id = $request->input('area_id');
        $restaurant->genre_id = $request->input('genre_id');
        $restaurant->description = $request->input('description');

        // 画像がアップロードされた場合
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $filename = time() . '_' . $file->getClientOriginalName(); // タイムスタンプを追加してファイル名をユニークにする
            $path = 'public/images/' . $filename;

            Storage::put($path, file_get_contents($file)); // 画像を保存
            $restaurant->image_url = 'storage/images/' . $filename; // パスを保存
        } else {
            $restaurant->image_url = 'null';
        }

        // データベースに保存
        $restaurant->save();

        // 保存後に元のページにリダイレクト
        return redirect()->back();
    }

    public function ownerShowReservationConfirmationForm($id)
    {
        // restaurantテーブルからidが$idと一致するデータを取得
        $restaurant = Restaurant::findOrFail($id);

        // reservationテーブルからrestaurant_idが$idと一致するデータを取得
        $reservations = Reservation::where('restaurant_id', $id)->orderBy('reservation_date', 'asc')->get();

        // 取得したデータをビューに渡す
        return view('owner-reservation-confirm', compact('restaurant', 'reservations'));
    }

    public function ownerReservationConfirmation(Request $request, $id)
    {
        // reservation_idsから対応する予約を取得
        $reservationIds = $request->input('reservation_id');

        if (is_array($reservationIds) && !empty($reservationIds)) {
            $reservations = Reservation::whereIn('id', $reservationIds)
                ->where('restaurant_id', $id)
                ->get();

            // 各予約のユーザーにメールを送信
            foreach ($reservations as $reservation) {
                $user = $reservation->user;

                // メールを直接送信
                Mail::raw($request->input('message'), function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('お知らせ');
                });
            }
        }

        return redirect()->back();
    }

    public function confirm($id)
    {
        $reservation = Reservation::findOrFail($id);

        return view('reservations.confirm', compact('reservation'));
    }

    public function showSettings()
    {
        $setting = SchedulerSetting::first();
        return view('scheduler.settings', compact('setting'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'schedule_time' => 'required|date_format:H:i',
        ]);

        $setting = SchedulerSetting::first();
        if (!$setting) {
            $setting = new SchedulerSetting();
        }

        $setting->schedule_time = $request->input('schedule_time');
        $setting->save();

        return redirect()->back()->with('success', 'スケジュール時間が更新されました。');
    }
}
