<?php

require_once('../src/config.php');

session_start();

//DB接続
$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

if(!($_SESSION['login_name'])){
  header('Location: login.php');
}

if(!empty($_SESSION['selected_id1']) && !empty($_SESSION['selected_id2']) && !empty($_SESSION['selected_id3']) && !empty($_SESSION['selected_id4']) && !empty($_SESSION['selected_id5']) ){

  //*配列の無駄が多いのでSQLの修正必要。 *SESSIONの無駄も多い。
  for($i=1;$i<=5;$i++){
    $index = 'selected_id'.$i;

    $selected_id = $_SESSION[$index];
    $user_id = $_SESSION['login_id'];
  
    $sql = "SELECT * FROM exsentence WHERE sentence_id=:sentence_id AND user_id=:user_id";
    $stmt = $pdo -> prepare($sql);
    $stmt->bindParam(':sentence_id',$selected_id,PDO::PARAM_INT);
    $stmt->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $stmt->execute();
    $results[$i] = $stmt->fetchAll();

    /*
    echo('<pre>');
    var_dump($results);
    echo('</pre>');
    */

  }
}else{
  exit('Error!!');
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
  <link rel="stylesheet" href="../src/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  <title>Result</title>
</head>

<body>
  <div class="container mt-3">
      <p>
        <?php
        for($i=1;$i<=5;$i++){
          echo $results[$i][0]['ensentence'] . "<br>";
          echo $results[$i][0]['jasentence'] . "<br>";
          echo $_SESSION['result'.$i] . "<br>"; 
        }
        ?>
      </p>

      <a href="main.php">メインページに戻る</a>  
  </div>

</body>
</html>