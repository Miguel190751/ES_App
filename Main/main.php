<?php

session_start();

//出題数のリセットを行う。
$_SESSION['num'] = 1;

if(!($_SESSION['login_name'])){
  header('Location: login.php');
}

//ログアウト機能 *SESSION情報が残ってしまっているかも。
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(!(empty($_POST['logout']))){
  $_SESSION['login_name'] = '';
  header('Location: login.php');
  }
}

?>

<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>メインページ</title>
  <link rel="stylesheet" href="../src/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>

  <div class="container">

    <div id="header_main">
      <h1 class="mt-5 col-11">ようこそ！ <?PHP echo $_SESSION['login_name']; ?> さん</h1>
      <form action="" method="POST">
        <input type="submit" name="logout" value="ログアウト" class="mt-5 mb-5 col-1">
      </form>
    </div>

    <div id="contents_main"> 
      <a href="Ja-En_Quiz.php"><img src="../src/pic/pic_EJ.png"></a><br>
      <a href="management.php"><img src="../src/pic/pic_register.png"></a><br>
    </div>
    
  </div>

</body>
    
</html>