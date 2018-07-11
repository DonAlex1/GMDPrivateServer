<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Checking nothing's empty
if(empty($_POST["gjp"]) OR empty($_POST["accountID"]) OR empty($_POST["targetAccountID"])){
	exit("-1");
}
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$accountID = $ep->remove($_POST["accountID"]);
$targetAccountID = $ep->remove($_POST["targetAccountID"]);
//Removing request
if(!empty($_POST["isSender"]) AND $_POST["isSender"] == 1){
		$query = $db->prepare("DELETE FROM friendreqs WHERE accountID=:accountID AND toAccountID=:targetAccountID LIMIT 1");
}else{
		$query = $db->prepare("DELETE FROM friendreqs WHERE toAccountID=:accountID AND accountID=:targetAccountID LIMIT 1");
}
//Checking GJP
if($GJPCheck->check($gjp,$accountID)){
	$query->execute([':accountID' => $accountID, ':targetAccountID' => $targetAccountID]);
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>