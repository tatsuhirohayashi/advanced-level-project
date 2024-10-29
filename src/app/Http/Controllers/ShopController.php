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
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        $genres = Genre::all();
        $restaurants = Restaurant::with('area', 'genre')->get();

        // 各 restaurant_id ごとのレビュー数をカウントして $reviews に格納
        $reviews = Review::selectRaw('restaurant_id, COUNT(*) as review_count')
            ->groupBy('restaurant_id')
            ->pluck('review_count', 'restaurant_id'); // restaurant_id をキーにした連想配列で取得

        // 各レストランに口コミ数（review_count）を追加
        foreach ($restaurants as $restaurant) {
            $restaurant->review_count = $reviews[$restaurant->id] ?? 0; // review_countカラムに口コミ数を設定
        }

        // ログインしているかどうかで異なるビューを返す
        if (Auth::check()) {
            return view('index', compact('areas', 'genres', 'restaurants'))->with('loggedIn', true);
        } else {
            return view('index', compact('areas', 'genres', 'restaurants'))->with('loggedIn', false);
        }
    }

    public function show($id)
    {
        if (Auth::check()) {
            $restaurant = Restaurant::findOrFail($id);
            $comments = Comment::where('restaurant_id', $id)->get();

            $user_id = Auth::id();
            $hasReservation = Reservation::where('user_id', $user_id)->where('restaurant_id', $id)->exists();
            $hasReviewed = Review::where('user_id', $user_id)->where('restaurant_id', $id)->exists();
            $reviews = Review::with('restaurant')->where('restaurant_id', $id)->get();

            return view('detail', compact('restaurant', 'comments', 'hasReservation', 'hasReviewed', 'reviews'));
        } else {
            $restaurant = Restaurant::findOrFail($id);
            $comments = Comment::where('restaurant_id', $id)->get();
            $reviews = Review::with('restaurant')->where('restaurant_id', $id)->get();

            return view('detail', compact('restaurant', 'comments', 'reviews'));
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

        // 各 restaurant_id ごとのレビュー数をカウントして $reviewCounts に格納
        $reviewCounts = Review::selectRaw('restaurant_id, COUNT(*) as review_count')
            ->groupBy('restaurant_id')
            ->pluck('review_count', 'restaurant_id'); // restaurant_id をキーにした連想配列で取得

        $restaurants = $query->get();

        // 各レストランに口コミ数（review_count）を追加
        foreach ($restaurants as $restaurant) {
            $restaurant->review_count = $reviewCounts[$restaurant->id] ?? 0; // review_countカラムに口コミ数を設定
        }

        $areas = Area::all();
        $genres = Genre::all();

        // ログインしているかどうかで異なるビューを返す
        if (Auth::check()) {
            return view('index', compact('restaurants', 'areas', 'genres'))->with('loggedIn', true);
        } else {
            return view('index', compact('restaurants', 'areas', 'genres'))->with('loggedIn', false);
        }
    }

    public function sortBy(Request $request)
    {
        $sortOption = $request->input('sort');

        // 各 restaurant_id ごとのレビュー数をカウントして $reviews に格納
        $reviews = Review::selectRaw('restaurant_id, COUNT(*) as review_count')
            ->groupBy('restaurant_id')
            ->pluck('review_count', 'restaurant_id'); // restaurant_id をキーにした連想配列で取得

        switch ($sortOption) {
            case '1': // ランダム
                $restaurants = Restaurant::inRandomOrder()->get();
                break;
            case '2': // 評価が高い順
                $restaurants = Restaurant::leftJoin('reviews', 'restaurants.id', '=', 'reviews.restaurant_id')
                    ->selectRaw('restaurants.*, AVG(reviews.rate) as avg_rate') // 店舗ごとの平均評価を計算
                    ->groupBy('restaurants.id') // 各店舗ごとにグループ化
                    ->orderByDesc('avg_rate') // 評価がある店舗を高い順で並び替え
                    ->orderByRaw('avg_rate IS NULL, restaurants.id ASC') // NULLは最後にしてidの昇順で並べる
                    ->get();
                break;
            case '3': // 評価が低い順
                $restaurants = Restaurant::leftJoin('reviews', 'restaurants.id', '=', 'reviews.restaurant_id')
                    ->selectRaw('restaurants.*, AVG(reviews.rate) as avg_rate') // 店舗ごとの平均評価を計算
                    ->groupBy('restaurants.id')
                    ->orderByRaw('avg_rate IS NULL') // 評価がある店舗を優先
                    ->orderBy('avg_rate', 'asc') // 評価がある店舗を低い順で並び替え
                    ->orderBy('restaurants.id', 'ASC') // 評価がない店舗はID順で並べる
                    ->get();
                break;
            default:
                $restaurants = Restaurant::all();
                break;
        }

        // 各レストランに口コミ数を追加
        foreach ($restaurants as $restaurant) {
            $restaurant->review_count = $reviews[$restaurant->id] ?? 0; // review_countカラムに口コミ数を設定
        }

        $areas = Area::all();
        $genres = Genre::all();

        // ログインしているかどうかで異なるビューを返す
        if (Auth::check()) {
            return view('index', compact('restaurants', 'areas', 'genres'))->with('loggedIn', true);
        } else {
            return view('index', compact('restaurants', 'areas', 'genres'))->with('loggedIn', false);
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

    public function showCreateForm($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        return view('review-post', compact('restaurant'));
    }

    public function postReview(ReviewRequest $request, $id)
    {
        $user_id = Auth::id();

        $review = new Review();
        $review->user_id = $user_id;
        $review->restaurant_id = $id;
        $review->rate = $request->input('rate');
        $review->review = $request->input('review');

        // 画像がアップロードされた場合
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $filename = time() . '_' . $file->getClientOriginalName(); // タイムスタンプを追加してファイル名をユニークにする
            $path = 'public/reviews/' . $filename;

            // ストレージに画像を保存し、URLパスを設定
            Storage::put($path, file_get_contents($file));
            $review->image_url = 'storage/reviews/' . $filename;
        } else {
            // 画像がアップロードされなかった場合の処理
            $review->image_url = null;
        }

        $review->save();

        return redirect()->route('restaurants.show', ['id' => $id]);
    }

    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('restaurants.show', ['id' => $review->restaurant_id]);
    }

    public function adminDeleteReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.shop.register');
    }

    public function showEditForm($id)
    {
        $review = Review::findOrFail($id);
        $restaurant = $review->restaurant;

        return view('review-update', compact('review', 'restaurant'));
    }

    public function updateReview(ReviewRequest $request, $id)
    {
        $review = Review::findOrFail($id);

        $review->rate = $request->input('rate');
        $review->review = $request->input('review');

        // 画像がアップロードされた場合
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $filename = time() . '_' . $file->getClientOriginalName(); // タイムスタンプを追加してファイル名をユニークにする
            $path = 'public/reviews/' . $filename;

            // ストレージに画像を保存し、URLパスを設定
            Storage::put($path, file_get_contents($file));
            $review->image_url = 'storage/reviews/' . $filename;
        }

        $review->save();

        return redirect()->route('restaurants.show', ['id' => $review->restaurant_id]);
    }

    public function adminShowShopRegistrationForm()
    {
        $restaurants = Restaurant::all();
        $reviews = Review::with('user', 'restaurant')->get();

        return view('admin-store-register', compact('restaurants', 'reviews'));
    }

    public function adminShopRegistration(OwnerRegisterRequest $request)
    {
        try {
            $restaurant = new Restaurant();
            $restaurant->restaurant_name = $request->input('restaurant_name');
            $restaurant->name = $request->input('name');
            $restaurant->email = $request->input('email');
            $restaurant->password = bcrypt($request->input('password'));
            $restaurant->save();

            return redirect('/admin/shop-register');
        } catch (\Exception $e) {
            // エラーメッセージを 'register' キーで渡す
            return redirect()->back()->withErrors(['register' => '登録処理に失敗しました'])->withInput();
        }
    }

    public function import(Request $request)
    {
        $areaMapping = [
            '東京' => 1,
            '大阪' => 2,
            '福岡' => 3,
        ];

        $genreMapping = [
            '寿司' => 1,
            '焼肉' => 2,
            '居酒屋' => 3,
            'イタリアン' => 4,
            'ラーメン' => 5,
        ];

        // アップロードされたファイルを取得
        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // CSVファイルの内容を読み取る
        $data = array_map('str_getcsv', file($filePath));
        $header = array_shift($data);

        $errors = [];
        foreach ($data as $index => $row) {
            $row = array_combine($header, $row);

            // `area_id` と `genre_id` の文字を数値に変換
            $row['area_id'] = $areaMapping[$row['area_id']] ?? null;
            $row['genre_id'] = $genreMapping[$row['genre_id']] ?? null;

            // image_url のみバリデーションを適用
            $validator = Validator::make($row, [
                'image_url' => ['required', 'regex:/\.(jpeg|jpg|png)$/i'],
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:254', 'unique:restaurants,email'],
                'password' => ['required', 'min:8', 'max:60'],
                'restaurant_name' => ['required', 'max:50'],
                'area_id' => ['required'],
                'genre_id' => ['required'],
                'description' => ['required', 'max:400'],
            ], [
                'name.required' => '店舗代表者を入力してください',
                'name.string' => '店舗代表者は文字列で入力してください',
                'name.max' => '店舗代表者は50文字以内で入力してください',
                'email.required' => 'メールアドレスを入力してください',
                'email.string' => 'メールアドレスは文字列で入力してください',
                'email.email' => 'メールアドレスに@を入力してください',
                'email.max' => 'メールアドレスは254文字以内で入力してください',
                'email.unique' => 'このメールアドレスは既に登録されています',
                'password.required' => 'パスワードを入力してください',
                'password.min' => 'パスワードは8文字以上で入力してください',
                'password.max' => 'パスワードは60文字以内で入力してください',
                'restaurant_name.required' => '店名を入力してください',
                'restaurant_name.max' => '店名は50文字以内で入力してください',
                'area_id.required' => '地域を入力してください',
                'genre_id.required' => 'ジャンルを入力してください',
                'description.required' => '店舗概要を入力してください',
                'description.max' => '店舗概要は400文字以内で入力してください',
                'image_url.required' => '画像URLを入力してください',
                'image_url.regex' => '画像URLにはjpeg、jpg、pngの拡張子が必要です',
            ]);

            if ($validator->fails()) {
                $errors = array_merge($errors, $validator->errors()->all()); // 行番号なしでメッセージを追加
                continue;
            }

            // データベースに保存
            Restaurant::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
                'restaurant_name' => $row['restaurant_name'],
                'area_id' => $row['area_id'],
                'genre_id' => $row['genre_id'],
                'description' => $row['description'],
                'image_url' => $row['image_url'],
            ]);
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors(['csv_errors' => $errors]);
        }

        return redirect()->route('admin.shop.register');
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
