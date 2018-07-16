<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Checking nothing's empty
if(empty($_POST["accountID"]) || empty($_POST["gjp"]) || empty($_POST["requestID"])) exit("-1");
//Getting data 
$accountID = $ep->remove($_POST["accountID"]);
$requestID = $ep->remove($_POST["requestID"]);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
if($GJPCheck->check($gjp,$accountID)){
	//Reading
	$query = $db->prepare("UPDATE friendreqs SET isNew = '0' WHERE ID = :requestID AND toAccountID = :targetAcc");
	$query->execute([':requestID' => $requestID, ':targetAcc' => $accountID]);
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>