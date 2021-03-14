# ES_App

## 使用するには、以下操作が必要
### 1.src/config.php を 作成
### 2.config.php内に以下の設定を記述する。

```bash
<?PHP
define("DB_NAME",""); // データベースの情報
define("DB_USER",""); // ユーザ
define("DB_PASSWD","");  // パスワード
define("Register_URL","") //メール認証型会員登録の本登録用のURL場所を指定する。

// メール情報(PHPMailer)
// メールホスト名・gmailでは smtp.gmail.com
define('MAIL_HOST','');
// メールユーザー名・アカウント名・メールアドレスを@込でフル記述
define('MAIL_USERNAME','');
// メールパスワード・上で記述したメールアドレスに即したパスワード
define('MAIL_PASSWORD','');
// SMTPプロトコル(sslまたはtls)
define('MAIL_ENCRPT','');
// 送信ポート(ssl:465, tls:587)
define('SMTP_PORT', );
// メールアドレス・ここではメールユーザー名と同じでOK
define('MAIL_FROM','');
// 表示名
define('MAIL_FROM_NAME','');
// メールタイトル
define('MAIL_SUBJECT','');
?>
```
