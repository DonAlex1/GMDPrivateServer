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
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
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
		$query = $db->prepare("DELETE FROM comments WHERE commentID=:commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		echo "1";
	}
	//Getting user ID
	$query = $db->prepare("SELECT userID FROM users WHERE extID = :accountID");
	$query->execute([':accountID' => $accountID]);
	$userID = $query->fetchColumn();
	//Deleting
	$query = $db->prepare("DELETE FROM comments WHERE commentID=:commentID AND userID=:userID AND secret=:secret LIMIT 1");
	$query->execute([':commentID' => $commentID, ':userID' => $userID, ':secret' => "Wmfd2893gb7"]);
	if($query->rowCount() == 0){
		//Getting level ID
		$query = $db->prepare("SELECT levelID FROM comments WHERE commentID = :commentID AND secret = :secret");
		$query->execute([':commentID' => $commentID, ':secret' => "Wmfd2893gb7"]);
		$levelID = $query->fetchColumn();
		//Getting user data
		$query = $db->prepare("SELECT userID FROM levels WHERE levelID = :levelID");
		$query->execute([':levelID' => $levelID]);
		$creatorID = $query->fetchColumn();
		$query = $db->prepare("SELECT extID FROM users WHERE userID = :userID");
		$query->execute([':userID' => $creatorID]);
		$creatorAccID = $query->fetchColumn();
		if($creatorAccID == $accountID){
			//Deleting
			$query = $db->prepare("DELETE FROM comments WHERE commentID=:commentID AND levelID=:levelID AND secret=:secret LIMIT 1");
			$query->execute([':commentID' => $commentID, ':levelID' => $levelID, ':secret' => "Wmfd2893gb7"]);
		}
	}
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>