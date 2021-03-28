<?php

require_once('../src/config.php');

session_start();

//DB接続
$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//変数の未定義を防ぐための定義
$question = "";
$answer = "";
$en_hint1 = "";
$ja_hint1 = "";
$en_hint2 = "";
$ja_hint2 = "";
$en_hint3 = "";
$ja_hint3 = "";
$selected_id = 0;

//セッション情報の確認
if(!($_SESSION['login_name'])){
  header('Location: login.php');
}

//数問(5問)解いたら、メインページへ遷移(*リザルト画面表示できると良)
if($_SESSION['num'] > 5){
  $_SESSION['num'] = 1;
  header('Location: main.php');
}

//問題表示・正解・不正解ボタンが押された場合の処理。
if($_SERVER['REQUEST_METHOD'] === 'POST'){

  //問題表示処理
  if(!empty($_POST['question'])){

    //問題をランダム選択する処理
    $sql = 'SELECT sentence_id FROM exsentence WHERE user_id=:user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $user_id =  $_SESSION['login_id'];
    $stmt->execute();
    $results = $stmt->fetchAll();
    $x = count($results);
    $random = rand(0, $x-1);
    $selected_id = $results[$random][0];

    $sql = 'SELECT * FROM exsentence where sentence_id=:sentence_id AND user_id=:user_id';
    $stmt = $pdo -> prepare($sql);
    $stmt->bindParam(':sentence_id',$selected_id,PDO::PARAM_INT);
    $stmt->bindParam(':user_id',$user_id,PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row){
      $question = $row['ensentence'];
      $answer = $row['jasentence'];
      $en_hint1 = $row['enkeyword1'];
      $ja_hint1 = $row['jakeyword1'];
      $en_hint2 = $row['enkeyword2'];
      $ja_hint2 = $row['jakeyword2'];
      $en_hint3 = $row['enkeyword3'];
      $ja_hint3 = $row['jakeyword3'];
    }
  }
    
  //正解していれば正解数に+1、外れればそのまま。+ 次の問題に遷移する(ページ更新)処理
  if(!empty($_POST['correct']) && $_POST['id']){

      //現在の出題数に+1する処理
      $_SESSION['num'] += 1;

      $sentence_id = $_POST['id'];
      $user_id =  $_SESSION['login_id'];

      //正解数(correct)を取得する処理。
      $sql = 'SELECT correct FROM exsentence WHERE sentence_id=:sentence_id AND user_id=:user_id';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':sentence_id',$sentence_id,PDO::PARAM_INT);
      $stmt->bindParam(':user_id',$user_id,PDO::PARAM_INT);
      $stmt->execute();
      $results = $stmt->fetchAll();
      $correct = ( (int) $results[0]['correct'] ) + 1;

      //正解数/正解時刻を更新する。
      date_default_timezone_set('Asia/Tokyo'); //php.iniの内容の変更と等しい。
      $correct_date = date('YmdHis');

      $sql = 'UPDATE exsentence SET correct=:correct ,correct_date=:correct_date WHERE sentence_id=:sentence_id AND user_id=:user_id';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':correct',$correct,PDO::PARAM_INT);
      $stmt->bindParam(':correct_date',$correct_date,PDO::PARAM_INT);
      $stmt->bindParam(':sentence_id',$sentence_id,PDO::PARAM_INT);
      $stmt->bindParam(':user_id',$user_id,PDO::PARAM_INT);
      $stmt->execute();

      //ページを更新する。(新しい問題に切り替え)
      header("Location: " . $_SERVER['PHP_SELF']);
  }

  if(!empty($_POST['incorrect']) && $_POST['id']){
    
      //現在の出題数に+1する処理
      $_SESSION['num'] += 1;

      //ページを更新する。(新しい問題に切り替え)
      header("Location: " . $_SERVER['PHP_SELF']);
  }   
    
}

?>

<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../src/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  <title>英 -> 日</title>
</head>

<body>
 
  <div id="main" class="container mt-3">

    <h1> <?php echo '第'.$_SESSION['num'].'問' ?></h1>

    <form action="" method="post">
      <input class="quizbtn btn btn-primary mb-2" type="submit" name="question" value="問題を表示">
      <div id="question"><?php echo $question; ?></div>
    </form>

    <br><br> 

    <button id="hintbtn" class="quizbtn btn btn-primary mb-2">ヒント</button><br>
    <div id="hint">
      <table>
        <table border='1' align="center">
          <tr id="hintdisplay1">
            <td><?php echo $en_hint1 ?></td>
            <td><?php echo $ja_hint1 ?></td>
          </tr>

          <tr id="hintdisplay2">
            <td><?php echo $en_hint2 ?></td>
            <td><?php echo $ja_hint2 ?></td>
          </tr>

          <tr id="hintdisplay3">
            <td><?php echo $en_hint3 ?></td>
            <td><?php echo $ja_hint3 ?></td>
          </tr>
      </table>
    </div>

    <br><br>


    <button id="answerbtn" class="quizbtn btn btn-primary mb-2">解答を表示</button>
    <div id="answer"><?php echo $answer; ?></div>

    <br><br>

    <form action="" method="post">
      <input class="quizbtn btn btn-success" id="correct" type="submit" name="correct" value="正解！">
      <input class="quizbtn btn btn-success" type="submit" name="incorrect" value="不正解！">
      <input type="hidden" name="id" value= "<?PHP echo $selected_id; ?>">
    </form>
    <br>

    <a href="main.php">メインページに戻る</a>

  </div>

  <script>
    //解答を表示が押された場合に非表示から表示に変更する。
    document.getElementById('answer').style.display = "none";

    var answerbtn = document.getElementById("answerbtn");

    answerbtn.addEventListener("click",function(){
      document.getElementById('answer').style.display = "";
    });

    //ヒントが押された場合に非表示から表示に変更する。
    document.getElementById('hintdisplay1').style.display = "none";
    document.getElementById('hintdisplay2').style.display = "none";
    document.getElementById('hintdisplay3').style.display = "none";

    var hintbtn = document.getElementById("hintbtn");

      hintbtn.addEventListener("click",function(){
        if(document.getElementById('hintdisplay1').style.display == "none"){
          document.getElementById('hintdisplay1').style.display = "";
        }
        else if(document.getElementById('hintdisplay2').style.display == "none"){
          document.getElementById('hintdisplay2').style.display = "";
        }
        else if(document.getElementById('hintdisplay3').style.display == "none"){
          document.getElementById('hintdisplay3').style.display = "";
        }
        //正解！ボタンを押せないように変更する。
        document.getElementById("correct").setAttribute("disabled", true);
        })
  </script>
</body>
    
</html>