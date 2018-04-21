<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$gs = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$secret =$ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	//Error
	exit("-1");
}
$commentID = $ep->remove($_POST["commentID"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
$gjpresult = $GJPCheck->check($gjp,$accountID);
if($gjpresult == 1){
	//Checking moderator status
	$permState = $gs->getMaxValuePermission($id, "actionRequestMod");
	if($permState == 2){
		//Deleting
		$query = $db->prepare("DELETE FROM acccomments WHERE commentID=:commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		echo "1";
	}
	//Getting user ID
	$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :accountID");
	$query2->execute([':accountID' => $accountID]);
	if ($query2->rowCount() > 0) {
		$userID = $query2->fetchColumn();
	}
	//Deleting
	$query = $db->prepare("DELETE FROM acccomments WHERE commentID=:commentID AND userID=:userID AND secret=:secret LIMIT 1");
	$query->execute([':userID' => $userID, ':commentID' => $commentID, ':secret' => "Wmfd2893gb7"]);
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>