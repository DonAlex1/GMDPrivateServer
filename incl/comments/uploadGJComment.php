<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/mainLib.php";
require_once "../lib/XORCipher.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../misc/commands.php";
require_once "../lib/mainLib.php";
$gs = new mainLib();
$ep = new exploitPatch();
$cmds = new Commands();
$mainLib = new mainLib();
$GJPCheck = new GJPCheck();
//Getting data
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	//Error
	exit("-1");
}
$gjp = $ep->remove($_POST["gjp"]);
$userName = $ep->remove($_POST["userName"]);
$comment = $ep->remove($_POST["comment"]);
$gameversion = $_POST["gameVersion"];
$levelID = $ep->remove($_POST["levelID"]);
//Checking percent
if(!empty($_POST["percent"])){
	$percent = $ep->remove($_POST["percent"]);
}else{
	$percent = 0;
}
//Checking if registered
if(!empty($_POST["accountID"]) AND $_POST["accountID"]!="0"){
	$id = $ep->remove($_POST["accountID"]);
	$register = 1;
	//Checking GJP
	$gjpresult = $GJPCheck->check($gjp,$id);
	if($gjpresult == 0){
		//Error
		exit("-1");
	}
}else{
	$id = $ep->remove($_POST["udid"]);
	$register = 0;
	if(is_numeric($id)){
		//Error
		exit("-1");
	}
}
//Getting user ID
$userID = $mainLib->getUserID($id, $userName);
$uploadDate = time();
$decodecomment = base64_decode($comment);
if($cmds->doCommands($id, $decodecomment, $levelID)){
	//Commands
	exit("-1");
}
//Checking if banned
$query3 = $db->prepare("SELECT isCommentBanned FROM users WHERE extID = :accountID");
$query3->execute([':accountID' => $id]);
$result2 = $query3->fetchColumn();
if($result2 == 1){
	//Banned
	exit("-10");
}
if($id != "" AND $comment != ""){
	//Commenting
	$query = $db->prepare("INSERT INTO comments (userName, comment, secret, levelID, userID, timeStamp, percent) VALUES (:userName, :comment, :secret, :levelID, :userID, :uploadDate, :percent)");
	if($register == 1){
		$query->execute([':userName' => $userName, ':comment' => $comment, ':secret' => "Wmfd2893gb7", ':levelID' => $levelID, ':userID' => $userID, ':uploadDate' => $uploadDate, ':percent' => $percent]);
		echo 1;
		if($percent != 0){
			$query2 = $db->prepare("SELECT percent FROM levelscores WHERE accountID = :accountID AND levelID = :levelID");
			$query2->execute([':accountID' => $id, ':levelID' => $levelID]);
			$result = $query2->fetchColumn();
			if($query2->rowCount() == 0){
				//Creting level score
				$query = $db->prepare("INSERT INTO levelscores (accountID, levelID, percent, uploadDate)
				VALUES (:accountID, :levelID, :percent, :uploadDate)");
			}else{
				//Updating level score
				if($result < $percent){
					$query = $db->prepare("UPDATE levelscores SET percent=:percent, uploadDate=:uploadDate WHERE accountID=:accountID AND levelID=:levelID");
					$query->execute([':accountID' => $id, ':levelID' => $levelID, ':percent' => $percent, ':uploadDate' => $uploadDate]);
				}
			}
		}
	}else{
		//Not registered
		exit("-1");
	}
}else{
	//Failure
	exit("-1");
}
?>
