<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/GJPCheck.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting IP
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
}
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
if(!empty($_POST["accountID"]) AND $_POST["accountID"]!="0"){
	$accountID = $ep->remove($_POST["accountID"]);
	$register = 1;
	//Checking GJP
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 0){
		//Error
		exit("-1");
	}
}else{
	$accountID = $ep->remove($_POST["udid"]);
	$register = 0;
	//Checking if is numeric
	if(is_numeric($accountID)){
		//Error
		exit("-1");
	}
}
$type = $_POST["type"] + 2;
//Checking if banned
$query3 = $db->prepare("SELECT isLikeBanned FROM users WHERE extID = :accountID");
$query3->execute([':accountID' => $accountID]);
$result2 = $query3->fetchColumn();
if($result2 == 1){
	//Banned
	exit("-1");
}
$itemID = $ep->remove($_POST["itemID"]);
//Count
$query6 = $db->prepare("SELECT count(*) FROM actions WHERE type=:type AND value=:itemID AND value2=:ip");
$query6->execute([':type' => $type, ':itemID' => $itemID, ':ip' => $ip]);
if($query6->fetchColumn() > 2){
	//Error
	exit("-1");
}
$query6 = $db->prepare("INSERT INTO actions (type, value, timestamp, value2) VALUES 
											(:type,:itemID, :time, :ip)");
$query6->execute([':type' => $type, ':itemID' => $itemID, ':time' => time(), ':ip' => $ip]);
//Detecting object
switch($_POST["type"]){
	//Levels
	case 1:
		$table = "levels";
		$column = "levelID";
		break;
	//Comments
	case 2:
		$table = "comments";
		$column = "commentID";
		break;
	//Account comments
	case 3:
		$table = "acccomments";
		$column = "commentID";
		break;
}
//Liking
$query = $db->prepare("SELECT likes FROM $table WHERE $column = :itemID LIMIT 1");
$query->execute([':itemID' => $itemID]);
$likes = $query->fetchColumn();
//Detecting if like or dislike
if($_POST["like"] == 1){
	$likes++;
}else{
	$likes--;
}
$query2=$db->prepare("UPDATE $table SET likes = :likes WHERE $column = :itemID");
$query2->execute([':itemID' => $itemID, ':likes' => $likes]);
echo "1";
?>