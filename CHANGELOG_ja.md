# 変更履歴
このプロジェクトの主な変更はこのファイルに記録されます。

## [5.0.2] - 2025/11/8

### 変更
- LICENSEの著作権年を2024から2025に更新しました。
- デモとスケルトンの構成を改善しました(コアパッケージ機能への変更はありません)。

## [5.0.1] - 2024/5/14

### 変更
- インストーラープログラムを修正しました。インストール後に`prototypes/`、`__tests__/`、`phpunit-printer.yml`、`phpunit.xml`を削除する処理を追加しました。
- スケルトンに`client/package-lock.json`を追加しました。

## [5.0.0] - 2024/5/13

### 変更
- PHP8サポート。PHP8以上が必要です。
  PHP8をサポートするには、アプリケーション内でcodeigniter-extensionのコアクラスを拡張してください。
  |application/core/|PHP|
  |--|--|
  |AppController.php|`abstract class AppController extends \X\Controller\Controller {}`|
  |AppInput.php|`class AppInput extends \X\Library\Input {}`|
  |AppLoader.php|`class AppLoader extends \X\Core\Loader {}`|
  |AppModel.php|`abstract class AppModel extends \X\Model\Model {}`|
  |AppRouter.php|`class AppRouter extends \X\Core\Router {}`|
  |AppURI.php|`class AppURI extends \X\Core\URI {}`|

  <!-- [https://github.com/bcit-ci/CodeIgniter/pull/6173](https://github.com/bcit-ci/CodeIgniter/pull/6173) が非常に参考になりました。 -->

## [4.2.0] - 2024/5/13

### 変更
- `X\Rekognition\Client`クラスの`generateCollectionId`メソッドから`$baseDir`引数を削除しました。
- `\X\Util\EMail`クラスから非推奨メソッド`message_from_template`、`message_from_xml`、`set_mailtype`、`attachment_cid`を削除しました。
  代わりに`messageFromTemplate`、`messageFromXml`、`setMailType`、`attachmentCid`を使用してください。
- メソッド名をより適切な名前に変更しました。
  |変更前|変更後|
  |--|--|
  |ImageHelper::putBase64|ImageHelper::writeDataURLToFile|
  |ImageHelper::putBlob|ImageHelper::writeBlobToFile|
  |ImageHelper::readAsBase64|ImageHelper::readAsDataURL|
  |ImageHelper::isBase64|ImageHelper::isDataURL|
  |ImageHelper::convertBase64ToBlob|ImageHelper::dataURL2Blob|
  |ImageHelper::read|ImageHelper::readAsBlob|
  |VideoHelper::putBase64|VideoHelper::writeDataURLToFile|
  |VideoHelper::isBase64|VideoHelper::isDataURL|
  |VideoHelper::convertBase64ToBlob|VideoHelper::dataURL2Blob|

## [4.1.9] - 2023/9/15

### 変更
- パス検証関数に先頭スラッシュ拒否オプションを追加しました。デフォルトでは先頭スラッシュを許可します(`\X\Util\Validation#is_path`)。

## [4.1.8] - 2023/9/15

### 変更
- ファイル(ディレクトリ)パス検証関数名を"directory_path"から"is_path"に変更しました。

## [4.1.7] - 2023/8/29

### 変更
- ディレクトリ作成メソッドを、作成に成功した場合はtrueを、失敗した場合はfalseを返すように修正しました。
  また、失敗時のエラーメッセージのログタイプをerrorからinfoに変更しました。(\X\Util\FileHelper::makeDirectory)

## [4.1.6] - 2023/8/9

### 変更
- 顔比較(<code>\X\RekognitionClient#compareFaces()</code>)は、以前は画像に顔がない場合にRuntimeExceptionを返していましたが、類似度として0を返すようになりました。
- 再帰的ディレクトリ削除(<code>\X\Util\FileHelper#delete()</code>)は、自身のディレクトリを削除する前にファイル状態キャッシュ(<code>clearstatcache</code>)をクリアするようになりました。

## [4.1.5] - 2023/5/25

### 追加
- PDFを画像に変換する機能を追加しました(`\X\Util\ImageHelper::pdf2Image`)。

## [4.1.4] - 2023/5/11

### 変更
- 顔認識クラス(\X\Rekognition\Client)のユニットテストを追加しました。
- Util\RestClientのメンバー変数名をリファクタリングしました。
  |変更前|変更後|
  |--|--|
  |public $option|public $options|
  |public $response_source|public $responseRaw|
  |public $headers|public $responseHeaders|
- ユニットテストディレクトリをtestsから__tests__に変更しました。

## [4.1.3] - 2023/2/28

### 追加
- "\X\Util\ImageHelper"クラスにGIFの最初のフレームを抽出するメソッドを追加しました。
- "\X\Util\ImageHelper"クラスにGIFのフレーム数を取得するメソッドを追加しました。


### 変更
- "\X\Util\ImageHelper"クラスのユニットテストを追加しました。

## [4.1.2] - 2023/2/10

### 追加
- 連想配列リストから連想配列、または任意のキーの要素のみの配列を作成するメソッドを追加しました(\X\Util\ArrayHelper#filteringElements())。


### 修正
- RESTクライアントクラス(\X\Util\RestClient)が削除されたロガークラスのメソッドを参照していたバグを修正しました。

## [4.1.1] - 2023/1/20

### 追加
- リクエストデータを読み取るユーティリティクラスを追加しました(\X\Util\HttpInput)。

## [4.1.0] - 2023/1/20

### 変更
- 依存するCodeIgniterフレームワークのバージョンを3.1.11から3.1.13に更新しました。

## [4.0.25] - 2022/12/26

### 修正
- SES送信バリデーションを実行する前に検証ルールなどをリセットするようにしました(\X\Util\AmazonSesClient)。

## [4.0.24] - 2022/12/26

### 変更
- XSSとRFDのリスクを軽減するために、以下のレスポンスヘッダーをJSONレスポンスに追加しました。
  - X-Content-Type-Options: nosniff
  - Content-Disposition: attachment; filename="{リクエストURLのベース名}.json"
    例えば、リクエストURLが"https://example.com/api/users/123"の場合、添付ファイル名は"123.json"になります。

## [4.0.23] - 2022/12/26

### 変更
- 内部リダイレクトレスポンスメソッドが適切なレスポンスコンテンツタイプを設定するようになりました(\X\Controller\Controller#internalRedirect())。

## [4.0.22] - 2022/12/13

### 変更
- コントローラーのエラーレスポンスメソッドに強制JSONレスポンスオプションを追加しました。
  このオプションがtrueの場合、レスポンダーのコンテンツタイプはapplication/jsonとして返されます。
  setメソッドを使用してエラーレスポンスにレスポンスデータを設定することもできます。
  ```php
  class User extends AppController {
    public function deliberateError() {
    $forceJsonResponse = true;
    parent
      ::set('error', 'Deliberate error')
      ::error('Deliberate error', 400, $forceJsonResponse);
    }
  }
  ```

## [4.0.21] - 2022/12/9

### 変更
- メールアドレスの検証ルールを修正しました。
  <table>
    <tr><th>メールアドレス</th><th>変更前</th><th>変更後</th><th>変更</th></tr>
    <tr><td>email@domain.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>firstname.lastname@domain.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@subdomain.domain.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>firstname+lastname@domain.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@123.123.123.123</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@[123.123.123.123]</td><td>無効</td><td>無効</td></tr>
    <tr><td>"email"@domain.com</td><td>無効</td><td>有効</td><td>変更</td></tr>
    <tr><td>1234567890@domain.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@domain-one.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>_______@domain.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@domain.name</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@domain.co.jp</td><td>有効</td><td>有効</td></tr>
    <tr><td>firstname-lastname@domain.com</td><td>有効</td><td>有効</td></tr>
    <tr><td>#@%^%#$@#$@#.com</td><td>無効</td><td>無効</td></tr>
    <tr><td>@domain.com</td><td>無効</td><td>無効</td></tr>
    <tr><td>Joe Smith <email@domain.com></td><td>無効</td><td>無効</td></tr>
    <tr><td>email.domain.com</td><td>無効</td><td>無効</td></tr>
    <tr><td>email@domain@domain.com</td><td>無効</td><td>無効</td></tr>
    <tr><td>.email@domain.com</td><td>有効</td><td>無効</td><td>変更</td></tr>
    <tr><td>email.@domain.com</td><td>有効</td><td>無効</td><td>変更</td></tr>
    <tr><td>email..email@domain.com</td><td>有効</td><td>無効</td><td>変更</td></tr>
    <tr><td>あいうえお@domain.com</td><td>無効</td><td>有効</td><td>変更</td></tr>
    <tr><td>email@domain.com (Joe Smith)</td><td>無効</td><td>無効</td></tr>
    <tr><td>email@domain</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@-domain.com</td><td>無効</td><td>無効</td></tr>
    <tr><td>email@domain.web</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@111.222.333.44444</td><td>有効</td><td>有効</td></tr>
    <tr><td>email@domain..com</td><td>無効</td><td>無効
  </table>

## [4.0.20] - 2022/9/26

### 修正
- "\X\Library\Input"でputデータの読み込みに関する警告を修正しました。

## [4.0.19] - 2022/9/25

### 変更
- "\X\Util\Logger"のログ出力からPIDを削除しました。
- "\X\Util\Logger"からprintWithoutPathメソッドを削除しました。
- "\X\Util\Logger"の"print"メソッド名を"display"に変更しました。
- スケルトンの未使用ファイルを削除しました。
- "\X\Util\Logger"からprintHidepathメソッドを削除しました。代わりに"display"メソッドを使用してください。
- サンプルとスケルトンの$config['log_file_permissions']を0644から0666に変更しました。

## [4.0.18] - 2022/9/24

### 変更
- README.mdを修正しました。

## [4.0.17] - 2022/9/23

### 追加
- サンプルテストコントローラーにform_validation_testアクションを追加しました。

## [4.0.16] - 2022/9/23

### 修正
- インストーラーのバグを修正しました。

## [4.0.15] - 2022/9/23

### 変更
- スケルトンの.gitignoreを更新しました。

## [4.0.14] - 2022/9/23

### 変更
- hostnameとhostname_or_ipaddress検証で文字列"localhost"を許可するようになりました。

## [4.0.13] - 2022/6/6

### 変更
- 長い文字列を省略するメソッド(\X\UtilStringHelper#ellipsis)をUnicodeに対応するように修正しました。

## [4.0.12] - 2021/11/10

### 修正
- ファイル削除機能のバグを修正しました。

## [4.0.11] - 2021/11/10

### 変更
- ファイル削除時にロックを有効/無効にするかを指定できる機能を追加しました。

## [4.0.10] - 2021/10/20

### 変更
- ファイルサイズを取得する前にファイルステータスキャッシュをクリアする処理を追加しました。

## [4.0.9] - 2021/9/27

### 変更
- クエリロギング動作を変更しました。
  ```php
  use \X\Util\Logger;
  $users = $this->UserModel->select('id, name')->get()->result_array();
  $query = $this->UserModel->last_query();
  Logger::print($query);// SELECT `id`, `name` FROM `user`
  ```

## [4.0.8] - 2021/9/22

### 追加
- IPアドレスまたはCIDRの検証ルールを追加しました。

## [4.0.7] - 2021/9/16

### 変更
- ランダム文字生成関数名をキャメルケースに変更しました。

## [4.0.6] - 2021/9/16

### 追加
- ランダム文字列生成関数を追加しました。

## [4.0.5] - 2021/8/10

### 変更
- ファイル移動メソッドで、移動したファイルにグループと所有者を設定できるようになりました。
- ファイルコピーメソッドで、移動したファイルにグループと所有者を設定できるようになりました。

## [4.0.4] - 2021/7/29

### 追加
- ディレクトリパスの検証ルールを追加しました。

## [4.0.3] - 2021/6/30

### 追加
- キーペア生成処理と公開鍵OpenSSHエンコード処理を追加しました。

## [4.0.2] - 2021/6/15

### 修正
- \X\Model\ModelクラスのExists_by_idメソッドのバグを修正しました。

## [4.0.1] - 2021/5/25

### 追加
- モデルで検索クエリ結果をキャッシュする機能を追加しました。
  モデルキャッシングの詳細については<a href="https://www.codeigniter.com/userguide3/database/caching.html" target="_blank">こちら</a>をご覧ください。

[3.3.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v1.0.0...v3.3.8
[3.3.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.3.8...v3.3.9
[3.4.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.3.9...v3.4.2
[3.4.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.2...v3.4.6
[3.4.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.6...v3.4.7
[3.4.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.7...v3.4.8
[3.5.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.4.8...v3.5.0
[3.5.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.0...v3.5.3
[3.5.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.3...v3.5.4
[3.5.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.4...v3.5.5
[3.5.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.5...v3.5.7
[3.5.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.7...v3.5.8
[3.5.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.8...v3.5.9
[3.6.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.5.9...v3.6.0
[3.6.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.0...v3.6.1
[3.6.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.1...v3.6.2
[3.6.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.2...v3.6.3
[3.6.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.3...v3.6.4
[3.6.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.4...v3.6.5
[3.6.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.5...v3.6.6
[3.6.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.6...v3.6.7
[3.6.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.7...v3.6.8
[3.6.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.8...v3.6.9
[3.7.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.6.9...v3.7.0
[3.7.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.0...v3.7.1
[3.7.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.1...v3.7.2
[3.7.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.2...v3.7.3
[3.7.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.3...v3.7.4
[3.7.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.4...v3.7.5
[3.7.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.5...v3.7.6
[3.7.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.6...v3.7.7
[3.7.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.7...v3.7.8
[3.7.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.8...v3.7.9
[3.8.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.7.9...v3.8.0
[3.8.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.0...v3.8.1
[3.8.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.1...v3.8.2
[3.8.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.2...v3.8.3
[3.8.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.3...v3.8.4
[3.8.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.4...v3.8.5
[3.8.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.5...v3.8.6
[3.8.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.6...v3.8.7
[3.8.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.7...v3.8.8
[3.8.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.8...v3.8.9
[3.9.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.8.9...v3.9.0
[3.9.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.0...v3.9.1
[3.9.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.1...v3.9.2
[3.9.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.2...v3.9.3
[3.9.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.3...v3.9.4
[3.9.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.4...v3.9.5
[3.9.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.5...v3.9.6
[3.9.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.6...v3.9.7
[3.9.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.7...v3.9.8
[3.9.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.8...v3.9.9
[4.0.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v3.9.9...v4.0.0
[4.0.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.0...v4.0.1
[4.0.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.1...v4.0.2
[4.0.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.2...v4.0.3
[4.0.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.3...v4.0.4
[4.0.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.4...v4.0.5
[4.0.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.5...v4.0.6
[4.0.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.6...v4.0.7
[4.0.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.7...v4.0.8
[4.0.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.8...v4.0.9
[4.0.10]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.9...v4.0.10
[4.0.11]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.10...v4.0.11
[4.0.12]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.11...v4.0.12
[4.0.13]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.12...v4.0.13
[4.0.14]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.13...v4.0.14
[4.0.15]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.14...v4.0.15
[4.0.16]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.15...v4.0.16
[4.0.17]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.16...v4.0.17
[4.0.18]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.17...v4.0.18
[4.0.19]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.18...v4.0.19
[4.0.20]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.19...v4.0.20
[4.0.21]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.20...v4.0.21
[4.0.22]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.21...v4.0.22
[4.0.23]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.22...v4.0.23
[4.0.24]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.23...v4.0.24
[4.0.25]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.24...v4.0.25
[4.1.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.0.25...v4.1.0
[4.1.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.0...v4.1.1
[4.1.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.1...v4.1.2
[4.1.3]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.2...v4.1.3
[4.1.4]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.3...v4.1.4
[4.1.5]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.4...v4.1.5
[4.1.6]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.5...v4.1.6
[4.1.7]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.6...v4.1.7
[4.1.8]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.7...v4.1.8
[4.1.9]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.8...v4.1.9
[4.2.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.1.9...v4.2.0
[5.0.0]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v4.2.0...v5.0.0
[5.0.1]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v5.0.0...v5.0.1
[5.0.2]: https://github.com/takuya-motoshima/codeigniter-extension/compare/v5.0.1...v5.0.2
