<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Checking nothng's empty
if(!empty($_POST["accountID"]) AND !empty($_POST["gjp"]) AND !empty($_POST["targetAccountID"])){
	//Getting data
	$accountID = $ep->remove($_POST["accountID"]);
	$targetAccountID = $ep->remove($_POST["targetAccountID"]);
	//Checking GJP
	$gjp = $ep->remove($_POST["gjp"]);
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 1){
		//Blocking
		$query = $db->prepare("INSERT INTO blocks (person1, person2) VALUES (:accountID, :targetAccountID)");
		$query->execute([':accountID' => $accountID, ':targetAccountID' => $targetAccountID]);
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