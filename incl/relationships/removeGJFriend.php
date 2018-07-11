<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$accountID = $ep->remove($_POST["accountID"]);
$targetAccountID = $ep->remove($_POST["targetAccountID"]);
//Removing
$query = "DELETE FROM friendships WHERE person1 = :accountID AND person2 = :targetAccountID";
$query = $db->prepare($query);
$query2 = "DELETE FROM friendships WHERE person2 = :accountID AND person1 = :targetAccountID";
$query2 = $db->prepare($query2);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
if($GJPCheck->check($gjp,$accountID)){
	$query->execute([':accountID' => $accountID, ':targetAccountID' => $targetAccountID]);
	$query2->execute([':accountID' => $accountID, ':targetAccountID' => $targetAccountID]);
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>