<?php
//Requesting data
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../misc/commands.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
$cmds = new Commands();
$gs = new mainLib();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$gjp =  $ep->remove($_POST["gjp"]);
$body =  $ep->remove($_POST["body"]);
$subject =  $ep->remove($_POST["subject"]);
$accountID =  $ep->remove($_POST["accountID"]);
$toAccountID =  $ep->remove($_POST["toAccountID"]);
//Checking if banned
if($gs->isBanned($accountID, "message")) exit("-1");
//Checking GJP
if($GJPCheck->check($gjp, $accountID)){
	//Uploading message
	$query = $db->prepare("INSERT INTO messages (subject, body, accountID, toAccountID, timestamp)
	VALUES (:subject, :body, :accountID, :toAccountID, :uploadDate)");
	$query->execute([':subject' => $subject, ':body' => $body, ':accountID' => $accountID, ':toAccountID' => $toAccountID, ':uploadDate' => time()]);
	echo 1;
}else{
	//Failure
	exit("-1");
}
?>