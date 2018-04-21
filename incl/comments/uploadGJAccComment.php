<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
require_once "../misc/commands.php";
$cmds = new Commands();
$mainLib = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$userName = $ep->remove($_POST["userName"]);
$comment = $ep->remove($_POST["comment"]);
$id = $ep->remove($_POST["accountID"]);
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	//Error
	exit("-1");
}
//Getting user ID
$userID = $mainLib->getUserID($id, $userName);
$uploadDate = time();
//Checking if banned
$query3 = $db->prepare("SELECT isCommentBanned FROM users WHERE extID = :accountID");
$query3->execute([':accountID' => $id]);
$result2 = $query3->fetchColumn();
if($result2 == 1){
	//Banned
	exit("-10");
}
//User check
if($id != "" AND $comment != "" AND $GJPCheck->check($gjp,$id) == 1){
	//Decoding
	$decodecomment = base64_decode($comment);
	if($cmds->doProfileCommands($id, $decodecomment)){
		//Command
		exit("-1");
	}
	//Commenting
	$query = $db->prepare("INSERT INTO acccomments (userName, comment, secret, userID, timeStamp)
										VALUES (:userName, :comment, :secret, :userID, :uploadDate)");
	$query->execute([':userName' => $userName, ':comment' => $comment, ':secret' => $secret, ':userID' => $userID, ':uploadDate' => $uploadDate]);
	echo 1;
}else{
	//Failure
	exit("-1");
}
?>