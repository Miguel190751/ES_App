<?php

session_start();

//出題数のリセットを行う。
$_SESSION['num'] = 1;

if(!($_SESSION['login_name'])){
    header('Location: login.php');
}

?>

<!DOCTYPE HTML>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>メインページ</title>
</head>

<body>
    <h1>ようこそ！ <?PHP echo $_SESSION['login_name']; ?> さん</h1>

    <a href="Ja-En_Quiz.php">英->日モード</a>
    <a href="management.php">例文登録</a>
</body>
    
</html>