<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/mainLib.php";
require_once "../lib/GJPCheck.php";
require_once "../misc/commands.php";
require_once "../lib/exploitPatch.php";
$gs = new mainLib();
$cmds = new Commands();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$comment = $ep->remove($_POST["comment"]);
$username = $ep->remove($_POST["userName"]);
$accountID = $ep->remove($_POST["accountID"]);
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
//Checking if banned
if($gs->isBanned($accountID, "comment")) exit("-10");
//User check
if($accountID != "" && is_numeric($accountID) && $comment != "" && $GJPCheck->check($gjp, $accountID)){
	//Command
	if($cmds->doProfileCommands($accountID, base64_decode($comment))) exit("-1");
	//Commenting
	$query = $db->prepare("INSERT INTO accComments (username, comment, accountID, timestamp)
										VALUES (:username, :comment, :accountID, :uploadDate)");
	$query->execute([':username' => $username, ':comment' => $comment, ':accountID' => $accountID, ':uploadDate' => time()]);
	echo 1;
}else{
	//Failure
	exit("-1");
}
?>