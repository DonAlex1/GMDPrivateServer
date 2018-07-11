<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/mainLib.php";
require_once "../lib/mainLib.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/XORCipher.php";
require_once "../misc/commands.php";
require_once "../lib/exploitPatch.php";
$gs = new mainLib();
$cmds = new Commands();
$mainLib = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$gjp = $ep->remove($_POST["gjp"]);
$comment = $ep->remove($_POST["comment"]);
$levelID = $ep->remove($_POST["levelID"]);
$username = $ep->remove($_POST["userName"]);
$gameVersion = $ep->remove($_POST["gameVersion"]);
$binaryVersion = $ep->remove($_POST["binaryVersion"]);
//Checking percent
if(!empty($_POST["percent"])){
	$percent = $ep->remove($_POST["percent"]);
}else{
	$percent = 0;
}
//Checking if registered
if(!empty($_POST["accountID"]) && $_POST["accountID"] && is_numeric($_POST["accountID"])){
	$accountID = $ep->remove($_POST["accountID"]);
	//Checking GJP
	if(!$GJPCheck->check($gjp, $accountID)) exit("-1");
}else{
	//Not registered
	exit("-1");
}
if($cmds->doCommands($accountID, base64_decode($comment), $levelID)) exit("-1");
//Checking if banned
if($gs->isBanned($accountID, "comment")) exit("-10");
if($accountID && is_numeric($accountID) && $comment){
	//Commenting
	$query = $db->prepare("INSERT INTO comments (username, comment, levelID, accountID, timestamp, percent) VALUES (:username, :comment, :levelID, :accountID, :uploadDate, :percent)");
	$query->execute([':username' => $username, ':comment' => $comment, ':levelID' => $levelID, ':accountID' => $accountID, ':uploadDate' => time(), ':percent' => $percent]);
	echo 1;
}else{
	//Failure
	exit("-1");
}
?>
