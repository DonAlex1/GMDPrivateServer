<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Checking nothing's empty
if(empty($_POST["accountID"]) OR empty($_POST["toAccountID"])){
	//Error
	exit("-1");
}
//Getting data
$accountID = $ep->remove($_POST["accountID"]);
$toAccountID = $ep->remove($_POST["toAccountID"]);
$comment = $ep->remove($_POST["comment"]);
$uploadDate = time();
$query = $db->prepare("SELECT count(*) FROM friendreqs WHERE (accountID=:accountID AND toAccountID=:toAccountID) OR (toAccountID=:accountID AND accountID=:toAccountID)");
$query->execute([':accountID' => $accountID, ':toAccountID' => $toAccountID]);
if($query->fetchColumn() == 0){
	//Checking GJP
	$gjp = $ep->remove($_POST["gjp"]);
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 1){
		//Uploading request
		$query = $db->prepare("INSERT INTO friendreqs (accountID, toAccountID, comment, uploadDate)
		VALUES (:accountID, :toAccountID, :comment, :uploadDate)");
		$query->execute([':accountID' => $accountID, ':toAccountID' => $toAccountID, ':comment' => $comment, ':uploadDate' => $uploadDate]);
		echo 1;
	}else{
		//Error
		exit("-1");
	}
}else{
	//Failure
	exit("-1");
}
?>