<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/GJPCheck.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
if(!empty($_POST["accountID"]) && $_POST["accountID"] != 0){
	$gjp = $ep->remove($_POST["gjp"]);
	$accountID = $ep->remove($_POST["accountID"]);
	$register = 1;
	//Checking GJP
	if(!$GJPCheck->check($gjp, $accountID)) exit("-1");
}else{
	$accountID = $ep->remove($_POST["udid"]);
	$register = 0;
	//Checking if is numeric
	if(is_numeric($accountID)) exit("-1");
}
//Checking if banned
$query = $db->prepare("SELECT isLikeBanned FROM users WHERE extID = :accountID LIMIT 1");
$query->execute([':accountID' => $accountID]);
if($query->fetchColumn() == 1) exit("-1");
$itemID = $ep->remove($_POST["itemID"]);
//Detecting type
switch($ep->remove($_POST["type"])){
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
		$table = "accComments";
		$column = "commentID";
		break;
}
//Liking
$query = $db->prepare("SELECT likes FROM $table WHERE $column = :itemID LIMIT 1");
$query->execute([':itemID' => $itemID]);
$likes = $query->fetchColumn();
//Detecting if like or dislike
if($ep->remove($_POST["like"]) == 1){
	$likes++;
}else{
	$likes--;
}
$query = $db->prepare("UPDATE $table SET likes = :likes WHERE $column = :itemID");
$query->execute([':itemID' => $itemID, ':likes' => $likes]);
echo "1";
?>