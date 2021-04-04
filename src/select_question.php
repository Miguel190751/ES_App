<?php

require_once('config.php');

session_start();

//DB接続
$pdo = new PDO(DB_NAME,DB_USER,DB_PASSWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

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
//  $list = array("sentence_id" => $row['sentence_id'], "ensentence" => $row['ensentence'], "jasentence" => $row['jasentence'], "enkeyword1" => $row['enkeyword1'], "jakeyword1" => $row['jakeyword1'], "enkeyword2" => $row['enkeyword2'], "jakeyword2" => $row['jakeyword2'], "enkeyword3" => $row['enkeyword3'], "jakeyword3" => $row['jakeyword3']);
  $list = array("sentence_id" => $row['sentence_id'], "ensentence" => htmlspecialchars_decode($row['ensentence'], ENT_QUOTES), "jasentence" => htmlspecialchars_decode($row['jasentence'], ENT_QUOTES), "enkeyword1" => htmlspecialchars_decode($row['enkeyword1'], ENT_QUOTES), "jakeyword1" => htmlspecialchars_decode($row['jakeyword1'], ENT_QUOTES), "enkeyword2" => htmlspecialchars_decode($row['enkeyword2'], ENT_QUOTES), "jakeyword2" => htmlspecialchars_decode($row['jakeyword2'], ENT_QUOTES), "enkeyword3" => htmlspecialchars_decode($row['enkeyword3'], ENT_QUOTES), "jakeyword3" => htmlspecialchars_decode($row['jakeyword3'], ENT_QUOTES));
}

echo json_encode($list);
exit();

?>