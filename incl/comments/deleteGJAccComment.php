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
if($GJPCheck->check($gjp, $accountID)){
	//Checking moderator status
	$permState = $gs->getMaxValuePermission($id, "actionRequestMod");
	if($permState == 2){
		//Deleting
		$query = $db->prepare("DELETE FROM accComments WHERE commentID = :commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		echo "1";
	}
	//Deleting
	$query = $db->prepare("DELETE FROM accComments WHERE commentID = :commentID AND accountID = :accountID LIMIT 1");
	$query->execute([':accountID' => $accountID, ':commentID' => $commentID]);
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>