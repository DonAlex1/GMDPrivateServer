<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$messageID = $ep->remove($_POST["messageID"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
$gjpresult = $GJPCheck->check($gjp,$accountID);
if($gjpresult == 1){
	//Deleting
	$query = $db->prepare("DELETE FROM messages WHERE messageID=:messageID AND accID=:accountID LIMIT 1");
	$query->execute([':messageID' => $messageID, ':accountID' => $accountID]);
	$query = $db->prepare("DELETE FROM messages WHERE messageID=:messageID AND toAccountID=:accountID LIMIT 1");
	$query->execute([':messageID' => $messageID, ':accountID' => $accountID]);
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>