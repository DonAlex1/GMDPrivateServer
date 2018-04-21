<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$gs = new mainLib();
$GJPCheck = new GJPCheck();
$ep = new exploitPatch();
//GEtting data
$accountID = $ep->remove($_POST["accountID"]);
$messageID = $ep->remove($_POST["messageID"]);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
$gjpresult = $GJPCheck->check($gjp,$accountID);
if($gjpresult == 1){
	//Getting message
	$query=$db->prepare("SELECT * FROM messages WHERE messageID = :messageID AND (accID = :accID OR toAccountID = :accID) LIMIT 1");
	$query->execute([':messageID' => $messageID, ':accID' => $accountID]);
	$result = $query->fetch();
	if($query->rowCount() == 0){
		//Error
		exit("-1");
	}
	//Checking if is sender
	if(empty($_POST["isSender"])){
		//Setting as viewed
		$query=$db->prepare("UPDATE messages SET isNew=1 WHERE messageID = :messageID AND toAccountID = :accID");
		$query->execute([':messageID' => $messageID, ':accID' =>$accountID]);
		$accountID = $result["accID"];
		$isSender = 0;
	}else{
		$isSender = 1;
		$accountID = $result["toAccountID"];
	}
	//Printing message
	$query=$db->prepare("SELECT userName,userID,extID FROM users WHERE extID = :accountID");
	$query->execute([':accountID' => $accountID]);
	$result12 = $query->fetch();
	$uploadDate = $gs->time_elapsed_string(date("Y-m-d H:i:s", $result["timestamp"]));
	echo "6:".$result12["userName"].":3:".$result12["userID"].":2:".$result12["extID"].":1:".$result["messageID"].":4:".$result["subject"].":8:".$result["isNew"].":9:".$isSender.":5:".$result["body"].":7:".$uploadDate."";
}else{
	//Failure
	exit("-1");
}
?>