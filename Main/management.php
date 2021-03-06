<?php

require_once('../src/config.php');
require_once("simple_html_dom.php");

session_start();

//DB接続
$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

if(!($_SESSION['login_name'])){
  header('Location: login.php');
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
  //例文登録処理
  if((!empty($_POST['en_contents']) && !empty($_POST["ja_contents"]))){
        
    //例文テーブル(exsentence)を作成する。
    $sql = "CREATE TABLE IF NOT EXISTS exsentence"
      ." ("
      ."sentence_id INT AUTO_INCREMENT PRIMARY KEY,"
      ."user_id INT NOT NULL,"
      ."ensentence CHAR(255),"
      ."jasentence CHAR(255),"
      ."enkeyword1 CHAR(30) DEFAULT NULL,"
      ."jakeyword1 CHAR(30) DEFAULT NULL,"
      ."enkeyword2 CHAR(30) DEFAULT NULL,"
      ."jakeyword2 CHAR(30) DEFAULT NULL,"
      ."enkeyword3 CHAR(30) DEFAULT NULL,"
      ."jakeyword3 CHAR(30) DEFAULT NULL,"
      ."correct INT NOT NULL DEFAULT 0,"
      ."correct_date DATETIME DEFAULT NULL"
      .");";
    $stmt = $pdo->query($sql);

    //データベースに追記する際に使用する配列を初期化する。
    $en = array_fill(0,3,'');
    $ja = array_fill(0,3,'');

    //タグの埋め込みを防ぐ。
    $en_contents = htmlspecialchars($_POST['en_contents'], ENT_QUOTES, 'UTF-8');
    $ja_contents = htmlspecialchars($_POST['ja_contents'], ENT_QUOTES, 'UTF-8');

    for($i=0;$i<=2;$i++){
      if(!empty($_POST['en_keyword'][$i]) && !empty($_POST['ja_keyword'][$i])){
        //入力部分の配列を書き換える。
        $en[$i] = htmlspecialchars($_POST['en_keyword'][$i], ENT_QUOTES, 'UTF-8');
        $ja[$i] = htmlspecialchars($_POST['ja_keyword'][$i], ENT_QUOTES, 'UTF-8');;
      }
    }

    //例文テーブルにデータを挿入する。
    $sql = "INSERT INTO exsentence (user_id, ensentence, jasentence, enkeyword1, jakeyword1, enkeyword2, jakeyword2, enkeyword3, jakeyword3) VALUES(:user_id, :ensentence, :jasentence, :enkeyword1, :jakeyword1, :enkeyword2, :jakeyword2, :enkeyword3, :jakeyword3)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':ensentence', $en_contents, PDO::PARAM_STR);
    $stmt->bindParam(':jasentence', $ja_contents, PDO::PARAM_STR);
    $stmt->bindParam(':enkeyword1',$en[0],PDO::PARAM_STR);
    $stmt->bindParam(':jakeyword1',$ja[0],PDO::PARAM_STR);
    $stmt->bindParam(':enkeyword2',$en[1],PDO::PARAM_STR);
    $stmt->bindParam(':jakeyword2',$ja[1],PDO::PARAM_STR);
    $stmt->bindParam(':enkeyword3',$en[2],PDO::PARAM_STR);
    $stmt->bindParam(':jakeyword3',$ja[2],PDO::PARAM_STR);
    //ユーザID(user_id)を結び付ける処理。
    $user_id = $_SESSION['login_id'];
    $stmt->execute();
  }

  //検索欄が利用された場合 *加えてsearch_wordも含まってるか。
  if(!empty($_POST['search'])){

    //shimple html docを使用してスクレイピングする。
    $search_word = htmlspecialchars($_POST['search_word'],ENT_QUOTES,'UTF-8');
    $html = file_get_html( 'https://ejje.weblio.jp/content/'.$search_word);

    $sample1 = $html->find(".ej");
    $sample2 = $html->find(".je");
    }


  //例文削除処理
  if(!empty($_POST['delete']) && !empty($_POST['selected_id'])){
    foreach( $_POST['selected_id'] as $value ){
      $sql = "delete from exsentence where sentence_id=:sentence_id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':sentence_id',$value,PDO::PARAM_INT);
      $stmt->execute();
    }
  }
}

?>

<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../src/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  <title>例文登録</title>
</head>

<body>
  <div class="container">
    
    <div class="row mt-5">
      <div id="register_management" class="col-lg-6 mb-3">
        <h1>例文登録</h1>
        <form action="" method="POST">
          <table>
          <table border="0">
            <tr>
              <td width=300px>
              英語例文　：<textarea name="en_contents" rows="2" cols="22"></textarea><br>
              日本語例文：<textarea name="ja_contents" rows="2" cols="22"></textarea><br><br>
              </td>

              <td>
              <input type="submit" name="submit" value="登録">
              </td>
            </tr>

            <tr>
              <td colspan="2">
              英語キーワード　　：1. <input type="text" name="en_keyword[]" size="5"> 2. <input type="text" name="en_keyword[]" size="5"> 3. <input type="text" name="en_keyword[]" size="5"><br>
              日本語キーワード　：1. <input type="text" name="ja_keyword[]" size="5"> 2. <input type="text" name="ja_keyword[]" size="5"> 3. <input type="text" name="ja_keyword[]" size="5"><br>
              </td>
            </tr>
          </table>
        </form>
      </div>

      <div id="search_management" class="col-lg-6">
        <h1>単語検索</h1>
        <form action="" method="POST">
          <input type="text" name="search_word">
          <input type="submit" name="search" value="検索">
        </form>

        <b>
          <?php if(!empty($sample1[0])){ echo $sample1[0]; } ?>
          <?php if(!empty($sample2[0])){ echo $sample2[0]; $html->clear(); } ?>
        </b>
      </div>
    </div>

    <hr>

    <h1>登録リスト</h1>

    <div class="table-responsive" id="register_table">
      <form action="" method="POST">

        <table class="table table-bordered table-hover">

          <tr class="table-info align-middle" >
            <th width="300px">英語文</th>
            <th width="300px">日本語文</th>
            <th width="60px"> <input type="submit" name="delete" value="削除"> </th>
            <th colspan="2" width="200px"> Keyword1 </th>
            <th colspan="2" width="200px"> Keyword2 </th>
            <th colspan="2" width="200px"> Keyword3 </th>
            <th width="70px">正解数</th>
            <th width="170px">正解時刻</th>
          </tr>

          <?PHP
          //自分のユーザidと等しいところだけを表示する。
          $sql='SELECT * FROM exsentence WHERE user_id=:user_id';
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':user_id',$user_id,PDO::PARAM_INT);
          $user_id =  $_SESSION['login_id'];
          $stmt->execute();
          $results = $stmt->fetchAll();
          foreach ($results as $row){
          ?>

          <tr>
            <td> <?php echo $row['ensentence']; ?></td>
            <td> <?php echo $row['jasentence']; ?></td>
            <td align ="center"> <input type="checkbox" name="selected_id[]" value="<?php echo $row['sentence_id']; ?>"></td>
            <td> <?php echo $row['enkeyword1']; ?> </td>
            <td> <?php echo $row['jakeyword1']; ?> </td>
            <td> <?php echo $row['enkeyword2']; ?> </td>
            <td> <?php echo $row['jakeyword2']; ?> </td>
            <td> <?php echo $row['enkeyword3']; ?> </td>
            <td> <?php echo $row['jakeyword3']; ?> </td>
            <td align ="center"> <?php echo $row['correct']; ?></td>
            <td> <?php echo $row['correct_date']; ?></td>
          </tr>

          <?php
          }
          ?>
        </table>
      </form>
    </div>

    <br>
    <a href="main.php">メインページに戻る</a>
    <br>
  </div>
</body>
    
</html>