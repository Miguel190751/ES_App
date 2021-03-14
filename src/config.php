<?PHP
// DB情報
define("DB_NAME","mysql:dbname=vocabulary_app;host=127.0.0.1"); // データベースの情報
define("DB_USER","root");    // ユーザ
define("DB_PASSWD","root");  // パスワード
define("Register_URL","127.0.0.1/Works/ES_App/Register/Register.php?urltoken="); // メール認証会員登録する際に、本登録(Register.php)のパスを指定

// メール情報(PHPMailer)
// メールホスト名・gmailでは smtp.gmail.com
define('MAIL_HOST','smtp.gmail.com');
// メールユーザー名・アカウント名・メールアドレスを@込でフル記述
define('MAIL_USERNAME','mig.techbase@gmail.com');
// メールパスワード・上で記述したメールアドレスに即したパスワード
define('MAIL_PASSWORD','mikaruge1014$');
// SMTPプロトコル(sslまたはtls)
define('MAIL_ENCRPT','ssl');
// 送信ポート(ssl:465, tls:587)
define('SMTP_PORT', 465);
// メールアドレス・ここではメールユーザー名と同じでOK
define('MAIL_FROM','mig.techbase@gmail.com');
// 表示名
define('MAIL_FROM_NAME','MIGUEL');
// メールタイトル
define('MAIL_SUBJECT','お問い合わせいただきありがとうございます');


?>