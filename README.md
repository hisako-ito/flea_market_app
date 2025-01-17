# フリマアプリ

## 環境構築

### Dockerビルド
1. `git clone git@github.com:hisako-ito/flea_market_app.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

＊ MacのM1・M2チップのPCの場合、no matching manifest for linux/arm64/v8 in the manifest list entriesのメッセージが表示されビルドができない場合があります。 エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください

```
mysql:
    platform: linux/x86_64(この文追加)`
    image: mysql:8.0.26
    environment:
```

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。  または、.envファイルを作成します。　　
4. env以下の環境変数を追加
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_FROM_ADDRESS=example@example.com
```
5. アプリケーションキーの作成
```
php artisan key:generate
```
6. マイグレーションの実行
```
php artisan migrate
```
7. シーディングを実行する
```
php artisan db:seed
```
8. シンボリックリンク作成
```
php artisan storage:link
``` 
### ログインテスト
1. ログイン画面([http://localhost/login](http://localhost/login))表示
2. 以下アカウントでログインを確認  
* アカウント情報 (メール認証済み)  
　メールアドレス：taro@example.com  
　パスワード：password123  
> [!NOTE]
> 新規アカウント登録時は、mailhog([http://localhost/8025](http://localhost/8025))で受信するメールにて認証が必要です。

### 決済処理確認
本アプリはStripeによる決算処理機能を実装しています。
アプリで決済機能を利用するためには、StripeのAPIキーを取得し、環境変数に設定する必要があります。以下の手順に従って設定を行ってください。

1. Stripeアカウントの作成  
まず、Stripeにアクセスしてアカウントを作成してください。既にアカウントをお持ちの場合は、ログインしてください。

2. APIキーの取得  
ログイン後、Stripeのダッシュボードにアクセスし、以下の手順でAPIキーを取得します：

    1. ダッシュボード左側のメニューから「開発者」→「APIキー」をクリックします。
    2. 公開可能キー（pk_live_...）および シークレットキー（sk_live_...）を確認してください。
    * 開発環境ではテストモードのキー（pk_test_... と sk_test_...）を使用してください。
3. .envファイルへの設定
取得したAPIキーをアプリケーションの.envファイルに追加してください。以下の例を参考に、適切なキーを設定してください：

4. アプリケーションの再起動
.envファイルを更新後、アプリケーションを再起動してください。
```
php artisan config:cache
``` 

5. 動作確認
アプリケーションでStripe決済が正しく動作するか確認してください。開発環境ではテストモードで動作確認を行い、必要に応じてテスト用のカード番号を使用してください。  
| 項目 | 入力値 |
| --- | --- |
| カード番号 | 4242 4242 4242 4242 |
| 有効期限 | 任意の未来の日付 (例: 12/34) |
| セキュリティコード | 任意の3桁 (例: 123) |

## 使用技術(実行環境)
* PHP 7.4.9
* Laravel 8.83.8
* MySQL 15.1

## ER図
![flea-market_app](https://github.com/user-attachments/assets/a64cac7a-5cea-4824-9104-2f1fc9596ef2)


## URL
* 開発環境： [http://localhost](http://localhost)
* phpMyAdmin： [http://localhost:8080/](http://localhost:8080/)
* mailhog： [http://localhost:8025/](http://localhost:8025/)

