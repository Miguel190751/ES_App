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
    <link rel="stylesheet" href="../src/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>

    <div class="container">
    <h1 class="mt-5 mb-5">ようこそ！ <?PHP echo $_SESSION['login_name']; ?> さん</h1>

    <div id="contents"> 
    <a href="Ja-En_Quiz.php"><img src="../src/pic/pic_JE.png"></a><br>
    <a href="management.php"><img src="../src/pic/pic_register.png"></a><br>
    </div>

    </div>

</body>
    
</html>