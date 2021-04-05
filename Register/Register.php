<?PHP

require_once('../src/config.php');

$errors = 0;

//DB接続
$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//GET送信された場合(メールのURLから来た時)
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    
  //送信されたURLトークンと、データベース内のURLトークンが一致している情報を取り出す！無ければ配列result[][]は空になる。
  $sql = 'SELECT * FROM preuser where urltoken=:urltoken';
  $stmt = $pdo->prepare($sql); 
  $stmt->bindParam(':urltoken',$_GET['urltoken'],PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchAll();

  //二重配列result[][]に値がないか(トークンが一致してない)、フラグが1(すでに登録している)時。強制終了。
  if(!($result && $result[0]['flag'] == 0)){
      exit('Error!!');
  }else{
      //「すでにEメールがuserテーブルに登録されていた場合はError表示する」で使用。
      $email = $result[0]['email'];
  }

  //完了画面から「前のページに戻る」ボタンを押したとき、Error表示させる。
  if(!($result[0]['flag'] == 0)){
    exit('Error!!');
  }
}

//すでにEメールがuserテーブルに登録されていた場合はError表示する！
$sql = 'SELECT email FROM user where email=:email';
$stmt = $pdo->prepare($sql);
$stmt -> bindParam(':email',$email,PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetchAll();

if(count($result)){
  exit('すでに登録されたメールアドレスです！');
}

//POST送信された場合、userテーブルに書き込み。
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  //*大空白を入力するとそのまま反映されてしまうので、大空白を取り除く処理も入れたい
    
  //htmlspecialcharsで<>タグのねじ込みを封じる。
  $name = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');
  $password = htmlspecialchars($_POST["password"], ENT_QUOTES, 'UTF-8');
  $hashed_password = hash('sha256', $password);
    
  //バリデーション *より細かくしたほうが良い。
  if($name == ''){
    echo "nameの入力は必須です!<br>";
    $errors += 1;
  }
    
  if($password == ''){
    echo "passwordの入力は必須です！<br>";
    $errors += 1;
  }

  if(mb_strlen($name) > 16){
    echo "ユーザ名は16文字以下で設定してください。";
    $errors += 1;
  }

  if(mb_strlen($password) < 8){
    echo "パスワードは8文字以上で設定してください。";
    $errors += 1;
  }
    
  //良ければDBのuserテーブルに内容を書き込み！&& 仮登録のflagも1にして、完了ページに遷移。
  if($errors == 0){
        
    //DBのテーブル作成(user)
    $sql = "CREATE TABLE IF NOT EXISTS user"
    ." ("
    ."user_id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name CHAR(16),"
    ."password CHAR(64),"
    ."email CHAR(50),"
    ."login_date DATETIME DEFAULT NULL"
    .");";
    $stmt = $pdo->query($sql);

    //userテーブルへの書き込み準備。$emailをpreuserから取ってくる処理
    $sql = 'SELECT email FROM preuser where urltoken=:urltoken';
    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(':urltoken',$_GET['urltoken'],PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll();
    $email = $result[0][0];

    //userテーブルに書き込み！
    $sql = $pdo -> prepare('INSERT INTO user (name, password, email) VALUES (:name, :password, :email)');
    $sql ->bindParam(':name', $name, PDO::PARAM_STR);
    $sql ->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $sql ->bindParam(':email', $email, PDO::PARAM_STR);
    $sql ->execute();

    //flagを1に変更！
    $flag = 1;
	$sql = 'UPDATE preuser SET flag=:flag where urltoken=:urltoken';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':flag', $flag, PDO::PARAM_STR);
	$stmt->bindParam(':urltoken',$_GET['urltoken'], PDO::PARAM_STR);
    $stmt->execute(); 
        
    header('Location: Complete.php');
    exit();
  }
}

?>

<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>ユーザー登録</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>
  <div class="container mt-5">
    <h1>新規会員　本登録</h1>
    <!--*確認画面を挟んで登録完了にしてもいいかも -->
    <form action="" method="POST">
      ユーザ名：  <input type="text" name="name"><br>
      パスワード：<input type="password" name="password">
      <input type="submit" name="submit" value="送信">
    </form>
  </div>
</body>
    
</html>