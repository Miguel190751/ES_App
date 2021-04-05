<?PHP

require_once('../src/config.php');

session_start();

$errors = 0;

//DB接続
$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

if($_SERVER['REQUEST_METHOD'] === 'POST'){

  //DBのテーブル作成(preuser) ※テーブル名に-は使えない。
  //*登録日時を記録して、１日以内に登録しないとだめなようにしてもいいかも。
  $sql = "CREATE TABLE IF NOT EXISTS preuser"
  ." ("
  ."preuser_id INT AUTO_INCREMENT PRIMARY KEY,"
  ."urltoken CHAR(128),"
  ."email CHAR(50),"
  ."flag TINYINT(1) NOT NULL DEFAULT 0"
  .");";
  $stmt = $pdo->query($sql);
    
  //htmlspecialcharsで<>タグのねじ込みを封じる。
  $email = htmlspecialchars($_POST["email"], ENT_QUOTES, 'UTF-8');
    
  //バリデーション(エラー処理)
  //*大空白を入力するとそのまま反映されてしまうので、大空白を取り除く処理も入れたい
  if($email == ''){
    echo "emailの入力は必須です!<br>";
    $errors += 1;
  }

  //すでにEメールがuserテーブルに登録されていた場合はエラーを出力する！
  $sql = 'SELECT email FROM user where email=:email';
  $stmt = $pdo->prepare($sql);
  $stmt -> bindParam(':email',$email,PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchAll();

  if($result[0][0]){
    echo "すでに登録されたメールアドレスです！<br>";
    $errors += 1;
  }

  //良ければDBのpreuserテーブルに内容を書き込み && メール送信！
  if($errors == 0){
    
      //64文字のトークンを作成し、URLを作る。
      $urltoken = hash('sha256',uniqid(rand(),1));
      $url = Register_URL.$urltoken;

      //send_test.phpで使う情報を入れとく。*使い終わったら削除したほうがいいかも。
      $_SESSION['url'] = $url;
      $_SESSION['email'] = $email;

      //DBのpreuserに書き込み。
      $sql = $pdo -> prepare('INSERT INTO preuser (urltoken, email) VALUES (:urltoken, :email)');
      $sql ->bindParam(':urltoken', $urltoken, PDO::PARAM_STR);
      $sql ->bindParam(':email', $email, PDO::PARAM_STR);
      $sql -> execute();
	
      //メール送信
      header('Location: phpmailer/send_test.php');
      exit();
  }
    
}

?>

<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ユーザー仮登録</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>
  <div class="container mt-5">
    <h1>新規会員　仮登録</h1>
    <form action="" method="POST">
      メールアドレス：<input type="email" name="email">
      <input type="submit" name="submit" value="送信" onClick="confirm()">        
    </form>

    <br>

    <a href="../Main/login.php">ログイン画面へ戻る</a><br>
  </div>
</body>
    
</html>