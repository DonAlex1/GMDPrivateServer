<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/mainLib.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$gs = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$commentID = $ep->remove($_POST["commentID"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
if($GJPCheck->check($gjp, $accountID)){
	//Checking moderator status
	$permState = $gs->getMaxValuePermission($id, "actionRequestMod");
	if($permState == 2){
		//Deleting
		$query = $db->prepare("DELETE FROM comments WHERE commentID=:commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		echo "1";
	}
	//Deleting
	$query = $db->prepare("DELETE FROM comments WHERE commentID = :commentID AND accountID = :accountID LIMIT 1");
	$query->execute([':commentID' => $commentID, ':accountID' => $accountID]);
	if($query->rowCount() == 0){
		//Getting level ID
		$query = $db->prepare("SELECT levelID FROM comments WHERE commentID = :commentID");
		$query->execute([':commentID' => $commentID]);
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
			$query = $db->prepare("DELETE FROM comments WHERE commentID = :commentID AND levelID = :levelID LIMIT 1");
			$query->execute([':commentID' => $commentID, ':levelID' => $levelID]);
		}
	}
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>