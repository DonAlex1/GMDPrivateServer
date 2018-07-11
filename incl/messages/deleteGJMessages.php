<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$messageID = $ep->remove($_POST["messageID"]);
$accountID = $ep->remove($_POST["accountID"]);
$gjp = $ep->remove($_POST["gjp"]);
//Checking GJP
if($GJPCheck->check($gjp, $accountID)){
	//Deleting
	$query = $db->prepare("DELETE FROM messages WHERE messageID = :messageID AND accID=:accountID LIMIT 1");
	$query->execute([':messageID' => $messageID, ':accountID' => $accountID]) or die("-1");
	$query = $db->prepare("DELETE FROM messages WHERE messageID = :messageID AND toAccountID=:accountID LIMIT 1");
	$query->execute([':messageID' => $messageID, ':accountID' => $accountID]) or die("-1");
	echo 1;
}else{
	//Failure
	exit("-1");
}
?>