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
$gjp =  $ep->remove($_POST["gjp"]);
$gameVersion =  $ep->remove($_POST["gameVersion"]);
$binaryVersion =  $ep->remove($_POST["binaryVersion"]);
$secret =  $ep->remove($_POST["secret"]);
$subject =  $ep->remove($_POST["subject"]);
$toAccountID =  $ep->remove($_POST["toAccountID"]);
$body =  $ep->remove($_POST["body"]);
$accID =  $ep->remove($_POST["accountID"]);
//Getting username
$query3 = $db->prepare("SELECT userName FROM users WHERE extID = :accID ORDER BY userName DESC");
$query3->execute([':accID' => $accID]);
$userName = $query3->fetchColumn();
//Checking if banned
$query3 = $db->prepare("SELECT isMessageBanned FROM users WHERE extID = :accountID");
$query3->execute([':accountID' => $accID]);
$result2 = $query3->fetchColumn();
if($result2 == 1){
	//Banned
	exit("-1");
}
if($cmds->doMessageCommands($accID, $toAccountID, $body)){
	//Commands
	exit("-1");
}
//Getting account data
$id = $ep->remove($_POST["accountID"]);
$register = 1;
$userID = $gs->getUserID($id);
$uploadDate = time();
//Uploading message
$query = $db->prepare("INSERT INTO messages (subject, body, accID, userID, userName, toAccountID, secret, timestamp)
VALUES (:subject, :body, :accID, :userID, :userName, :toAccountID, :secret, :uploadDate)");
//Checking GJP
$gjpresult = $GJPCheck->check($gjp,$id);
if($gjpresult == 1){
	$query->execute([':subject' => $subject, ':body' => $body, ':accID' => $id, ':userID' => $userID, ':userName' => $userName, ':toAccountID' => $toAccountID, ':secret' => $secret, ':uploadDate' => $uploadDate]);
	echo 1;
}else{
	//Failure
	exit("-1");
}
?>