# アプリケーション名

飲食店予約サービス（Rese（リーズ））<br>

概要説明（どんなアプリか）<br>
飲食店の予約ができるアプリであり、お気に入り登録をしたり、コメントをしたりすることができる。<br>

![alt text](上級模擬案件トップ画面.jpg)

# 作成した目的

概要説明（なんで作成したか）<br>
とある企業から外部の飲食店予約サービスは手数料が取られるので自社で予約サービスを持ちたいと依頼を受けたため。<br>

# アプリケーションURL

http://ec2-54-250-237-105.ap-northeast-1.compute.amazonaws.com/<br>

新規にユーザー登録し、ログインする時はメール認証が必要になるため、以下URLにアクセスし、メール認証を行ってください。上手くいかない場合は、一番下のテストユーザーを使用してください。<br>

http://ec2-54-250-237-105.ap-northeast-1.compute.amazonaws.com:8025/<br>

上記URLにアクセスしても表示されない場合、私にお申し付けください。<br>

# 機能一覧

・会員登録<br>
・ログイン<br>
・ログアウト<br>
・ユーザー情報取得<br>
・ユーザー飲食店お気に入り一覧取得<br>
・ユーザー飲食店予約情報取得<br>
・飲食店一覧取得<br>
・飲食店詳細取得<br>
・飲食店お気に入り追加<br>
・飲食店お気に入り削除<br>
・飲食店予約情報追加<br>
・飲食店予約情報削除<br>
・エリア検索<br>
・ジャンル検索<br>
・店名検索<br>
・予約変更<br>
・評価<br>
・バリデーション<br>
・レスポンシブデザイン<br>
・管理者による店舗代表者情報追加<br>
・店舗代表者による店舗情報の追加・更新<br>
・店舗代表者による予約情報取得<br>
・ストレージ保存<br>
・メールでの本人確認<br>
・店舗代表者によるお知らせメール送信<br>
・予約情報リマインダー<br>
・QRコード情報取得<br>
・決済<br>

## 使用技術（実行環境）

・PHP 7.4.9<br>
・Laravel 8<br>
・MySQL 8.0.26<br>

## テーブル設計

![alt text](上級模擬案件テーブル設計書1.jpg)

![alt text](上級模擬案件テーブル設計書2.jpg)

![alt text](上級模擬案件テーブル設計書3.jpg)

## ER図

![alt text](上級模擬案件ER図.jpg)

# 環境構築

Dockerビルド<br>
1.git clone リンク<br>
2.DockerDesktopアプリを立ち上げる<br>
3.docker-compose up -d --build<br>

MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。

Laravel環境構築

1.docker-compose exec php bash<br>
2.composer install<br>
3.env.exampleファイルから.envを作成し、環境変数を変更<br>
4.php artisan key:generate<br>
5.php artisan migrate<br>
6.php artisan db:seed<br>
+αphp artisan storage:link（※必要に応じてコマンドを打ってください。）<br>

## URL

開発環境:http://localhost/<br>
phpMyAdmin:http://localhost:8080/<br>
MailHog:http://localhost:8025/<br>

管理者ログイン画面：http://localhost/login/admin<br>
店舗代表者ログイン画面：http://localhost/login/owner<br>

## アカウントの種類（テストユーザー）

利用者<br>

メールアドレス：test@example1.com<br>
パスワード：testtesttest<br>

店舗代表者<br>

メールアドレス：restaurant@restaurant1.com<br>
パスワード：restaurant1<br>
※店舗代表者のテストアカウントはなるべく使わず、管理者による管理画面から店舗代表者アカウント作成して使用してください。<br>

管理者<br>

メールアドレス：admin@admin1.com<br>
パスワード：admin1<br>

## 注意事項

・storage/public/images、storage/public/qr_codesにある画像は必要に応じて削除してください。<br>