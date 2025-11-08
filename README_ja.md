# CodeIgniter Extension

CodeIgniter 3の拡張パッケージで、拡張されたコアクラス(コントローラー、モデル、ビュー)とユーティリティクラスを提供します。

## 目次

- [機能](#機能)
- [要件](#要件)
- [インストール](#インストール)
- [クイックスタート](#クイックスタート)
- [設定](#設定)
- [使用例](#使用例)
- [テスト](#テスト)
- [ドキュメント](#ドキュメント)
- [ライセンス](#ライセンス)

## 機能

### コア拡張
- **拡張コントローラー** - JSONレスポンス、テンプレートレンダリング、アクセス制御
- **拡張モデル** - クエリキャッシュ、バッチ操作、ヘルパーメソッド
- **拡張ルーター** - アノテーションベースのアクセス制御

### ユーティリティクラス
- **画像処理** - リサイズ、クロップ、フォーマット変換、GIFフレーム抽出、PDFから画像へ変換
- **動画処理** - 動画ファイルの操作と変換
- **ファイル操作** - ロック機能付き高度なファイルとディレクトリ操作
- **CSV処理** - インポート/エクスポートユーティリティ
- **メール** - テンプレートベースのメール、Amazon SES統合
- **RESTクライアント** - API統合用HTTPクライアント
- **セキュリティ** - 暗号化/復号化、IP検証
- **バリデーション** - カスタムルール(ホスト名、IP、CIDR、日時、パス)
- **セッション管理** - カスタムカラム付きデータベースバックセッション
- **ロギング** - コンテキスト付き拡張ロギング
- **テンプレートエンジン** - セッション変数統合Twig

### AWS統合
- **Amazon Rekognition** - 顔検出、比較、分析
- **Amazon SES** - 信頼性の高いメール配信サービス

## 要件

- **PHP** 7.3.0以上
- **Composer**
- **PHP拡張:**
  - php-gd
  - php-mbstring
  - php-xml
  - php-imagick (オプション、GIF操作用)

### オプション: ImageMagickインストール

`\X\Util\ImageHelper`の`extractFirstFrameOfGif`メソッドに必要です。

**Amazon Linux 2:**
```sh
sudo yum -y install ImageMagick php-imagick
```

**Amazon Linux 2023:**
```sh
# ImageMagickとPECLをインストール
sudo dnf -y install ImageMagick ImageMagick-devel php-pear.noarch

# imagick拡張をインストール
sudo pecl install imagick
echo "extension=imagick.so" | sudo tee -a /etc/php.ini

# サービスを再起動
sudo systemctl restart nginx php-fpm
```

## インストール

Composerを使用して新しいプロジェクトを作成:

```sh
composer create-project takuya-motoshima/codeigniter-extension myapp
cd myapp
```

## クイックスタート

### 1. パーミッション設定

```sh
sudo chmod -R 755 public/upload application/{logs,cache,session}
sudo chown -R nginx:nginx public/upload application/{logs,cache,session}
```

### 2. Webサーバー設定

Nginx設定をコピー:

```sh
sudo cp nginx.sample.conf /etc/nginx/conf.d/myapp.conf
sudo systemctl restart nginx
```

### 3. データベースセットアップ

データベーススキーマをインポート:

```sh
mysql -u root -p your_database < skeleton/init.sql
```

### 4. フロントエンドアセットのビルド

```sh
cd client
npm install
npm run build
```

### 5. アプリケーションへアクセス

ブラウザで`http://{your-server-ip}:3000/`を開きます。

**デフォルト認証情報:**
- メール: `robin@example.com`
- パスワード: `password`

### スクリーンショット

<p align="left">
  <img alt="サインイン" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/sign-in.png" width="45%">
  <img alt="ユーザーリスト" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/list-of-users.png" width="45%">
</p>

## 設定

### 基本設定 (`application/config/config.php`)

<table>
  <thead>
    <tr>
      <th>設定項目</th>
      <th>デフォルト</th>
      <th>推奨</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>base_url</td>
      <td><em>空</em></td>
      <td>if (!empty($_SERVER['HTTP_HOST'])) $config['base_url'] = '//' . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);</td>
    </tr>
    <tr>
      <td>enable_hooks</td>
      <td>FALSE</td>
      <td>TRUE</td>
    </tr>
    <tr>
      <td>permitted_uri_chars</td>
      <td>a-z 0-9~%.:_\-</td>
      <td>a-z 0-9~%.:_\-,</td>
    </tr>
    <tr>
      <td>sess_save_path</td>
      <td>NULL</td>
      <td>APPPATH . 'session';</td>
    </tr>
    <tr>
      <td>cookie_httponly</td>
      <td>FALSE</td>
      <td>TRUE</td>
    </tr>
    <tr>
      <td>composer_autoload</td>
      <td>FALSE</td>
      <td>realpath(APPPATH . '../vendor/autoload.php');</td>
    </tr>
    <tr>
      <td>index_page</td>
      <td>index.php</td>
      <td><em>空</em></td>
    </tr>
  </tbody>
</table>

### アクセス制御のセットアップ

#### 1. デフォルトルートの定義

`application/config/routes.php`:

```php
$route['default_controller'] = 'users/login';
```

#### 2. セッション定数の設定

`application/config/constants.php`:

```php
const SESSION_NAME = 'session';
```

#### 3. フックの設定

`application/config/hooks.php`:

```php
use \X\Annotation\AnnotationReader;
use \X\Util\Logger;

$hook['post_controller_constructor'] = function() {
  if (is_cli()) return;

  $CI =& get_instance();
  $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
  $loggedin = !empty($_SESSION[SESSION_NAME]);

  if (!$meta->allow_http)
    throw new \RuntimeException('HTTP access is not allowed');
  else if ($loggedin && !$meta->allow_login)
    redirect('/users/index');
  else if (!$loggedin && !$meta->allow_logoff)
    redirect('/users/login');
};

$hook['pre_system'] = function () {
  $dotenv = Dotenv\Dotenv::createImmutable(ENV_DIR);
  $dotenv->load();
  set_exception_handler(function ($e) {
    Logger::error($e);
    show_error($e->getMessage(), 500);
  });
};
```

## 使用例

### コントローラー

```php
use \X\Annotation\Access;

class Users extends AppController {
  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function index() {
    $users = $this->UserModel->get()->result_array();
    parent::set('users', $users)->view('users/index');
  }

  /**
   * @Access(allow_http=true)
   */
  public function api() {
    $data = ['message' => 'Success'];
    parent::set($data)->json();
  }
}
```

### モデル

```php
class UserModel extends AppModel {
  const TABLE = 'user';

  public function getActiveUsers() {
    return $this
      ->where('active', 1)
      ->order_by('name', 'ASC')
      ->get()
      ->result_array();
  }
}
```

### Twigテンプレート

セッション変数は自動的に利用可能です:

```php
// PHP
$_SESSION['user'] = ['name' => 'John Smith', 'role' => 'admin'];
```

```twig
{# テンプレート #}
{% if session.user is defined %}
  <p>ようこそ、{{ session.user.name }}さん！</p>
  {% if session.user.role == 'admin' %}
    <a href="/admin">管理パネル</a>
  {% endif %}
{% endif %}
```

### ユーティリティの使用

```php
// 画像処理
use \X\Util\ImageHelper;
ImageHelper::resize('/path/to/image.jpg', '/path/to/output.jpg', 800, 600);

// ファイル操作
use \X\Util\FileHelper;
FileHelper::makeDirectory('/path/to/dir', 0755);

// 暗号化
use \X\Util\Cipher;
$encrypted = Cipher::encrypt('secret data', 'encryption-key');

// RESTクライアント
use \X\Util\RestClient;
$client = new RestClient(['base_url' => 'https://api.example.com']);
$response = $client->get('/users');
```

## テスト

ユニットテストの実行:

```sh
composer test
```

テストファイルの場所:
- `__tests__/*.php` - テストケース
- `phpunit.xml` - 設定
- `phpunit-printer.yml` - 出力フォーマット

## ドキュメント

- **[APIドキュメント](https://takuya-motoshima.github.io/codeigniter-extension/)** - 完全なAPIリファレンス
- **[デモアプリケーション](demo/)** - 完全な動作例
- **[変更履歴](CHANGELOG_ja.md)** - バージョン履歴と変更内容
- **[CodeIgniter 3ガイド](https://codeigniter.com/userguide3/)** - 公式フレームワークドキュメント

### PHPDocの生成

```sh
# phpDocumentorをダウンロード(初回のみ)
wget https://phpdoc.org/phpDocumentor.phar
chmod +x phpDocumentor.phar

# ドキュメント生成
php phpDocumentor.phar run -d src/ --ignore vendor --ignore src/X/Database/Driver/ -t docs/
```

## 貢献

プルリクエストを歓迎します！お気軽にご投稿ください。

## 著者

**Takuya Motoshima**
- GitHub: [@takuya-motoshima](https://github.com/takuya-motoshima)
- Twitter: [@TakuyaMotoshima](https://x.com/takuya_motech)
- Facebook: [takuya.motoshima.7](https://www.facebook.com/takuya.motoshima.7)

## ライセンス

[MIT License](LICENSE)
