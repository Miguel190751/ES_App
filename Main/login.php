<?php

require_once('../src/config.php');

session_start();

//DB接続

$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

if(!empty($_SESSION['login_name'])){
  header('Location: main.php');
  exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){

  //ログイン処理
  if(!empty($_POST['login_email']) && !empty($_POST['login_password'])){
        
    //HTMLSPECIALCHARSでタグの埋め込みを防ぐ。
    //ENT_QUOTES:シングルクオートとダブルクオートを共に普通の文字として変換する。
    $login_email = htmlspecialchars($_POST['login_email'], ENT_QUOTES, 'UTF-8');
    $login_password = htmlspecialchars($_POST['login_password'], ENT_QUOTES, 'UTF-8'); 
    $login_hashed_password = hash('sha256', $login_password);

    //送信情報とデータベース情報を比較する
    $sql = 'SELECT user_id, name FROM user WHERE email=:email AND password=:password';
    $stmt =  $pdo -> prepare($sql);
    $stmt -> bindParam(':email',$login_email,PDO::PARAM_STR);
    $stmt -> bindParam(':password',$login_hashed_password,PDO::PARAM_STR);
    $stmt -> execute();

    $results = $stmt->fetchAll();
    foreach ($results as $row){
      $name = $row['name'];
      $Uid = $row['user_id'];
    }

    if($results){
      //ログイン成功の証
      $_SESSION['login_name'] = $name;
      $_SESSION['login_id'] = $Uid;

      //DB内のログイン日時を書き換える。
      date_default_timezone_set('Asia/Tokyo'); //php.iniの内容の変更と等しい。 *直接変更したほうがよい。
      $date = date('YmdHis');
      $sql = 'UPDATE user SET login_date=:login_date WHERE user_id=:user_id';
      $stmt =  $pdo -> prepare($sql);
      $stmt -> bindParam(':user_id',$Uid,PDO::PARAM_INT);
      $stmt -> bindParam(':login_date',$date,PDO::PARAM_INT);
      $stmt -> execute();

      //メインページに遷移する。
      header('Location: main.php');
      exit();
    }else{
      echo "Error! メールアドレスまたはパスワードが正しくありません！";
    }
}

}
?>

<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ログイン</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>
  <div class="container mt-5">
    <h1>ログイン</h1>
    <form action="" method="POST">
      <label>Email<br>
      <input type="email" name="login_email" size="30">
      </label>

      <br><br>

      <label>パスワード<br>
      <input type="password" name="login_password" size="30">
      </label>

      <input type="submit" name="submit" value="確認">
    </form>

    <br>

    <a href="../Register/Preregister.php"><p>新規会員登録</p></a>
  </div>
</body>
    
</html>