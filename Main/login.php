<?php

require_once('../Config/Config.php');

session_start();

//DB接続

$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//$_SESSION[---]が無いときにエラーが発生する。
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
            date_default_timezone_set('Asia/Tokyo'); //php.iniの内容の変更と等しい。
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
</head>

<body>
    <h1>ログイン</h1>
    <!--確認を押したとき、再確認ウインドウが出るように設定したい-->
    <form action="" method="POST">
        Email：<input type="email" name="login_email"><br>
        パスワード<input type="password" name="login_password">
        <input type="submit" name="submit" value="確認">
    </form>

    <a href="../Register/Preregister.php"><p>新規会員登録</p></a>
    
    <?PHP

    //デバッグ表示
    echo "DEB:デバッグ表示 preuser"."<br>";
    $sql = 'SELECT * FROM preuser';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
       //$rowの中にはテーブルのカラム名が入る
        echo $row['preuser_id'].' ';
        echo $row['urltoken'].' ';
        echo $row['email'].' ';
        echo $row['flag'].'<br>';
    }

    echo "<br>";

    echo "DEB:デバッグ表示 user"."<br>";
    $sql = 'SELECT * FROM user';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
       //$rowの中にはテーブルのカラム名が入る
       echo $row['user_id'].' ';
       echo $row['name'].' ';
       echo $row['password'].' ';
       echo $row['email'].' ';
       echo $row['login_date'].'<br>';
    }

?>

</body>
    
</html>