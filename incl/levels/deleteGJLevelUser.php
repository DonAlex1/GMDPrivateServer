<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/mainLib.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$mainLib = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
if(!is_numeric($levelID)) exit("-1");
if($GJPCheck->check($gjp, $accountID)){
	//Checking if rated
	$stars = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
	$stars->execute([':levelID' => $levelID]);
	$stars = $stars->fetchColumn();
	if($stars) exit("-1");
	//Checking if very downloaded
	$downloads = $db->prepare("SELECT downloads FROM levels WHERE levelID = :levelID");
	$downloads->execute([':levelID' => $levelID]);
	$downloads = $downloads->fetchColumn();
	if($downloads > 9999) exit("-1");
	//Getting user ID
	$userID = $mainLib->getUserID($accountID);
	//Deleting
	$query = $db->prepare("DELETE FROM levels WHERE levelID = :levelID AND userID = :userID AND starStars = 0 LIMIT 1");
	$query->execute([':levelID' => $levelID, ':userID' => $userID]);
	if(file_exists("../../data/levels/$levelID") && $query->rowCount()) unlink("../../data/levels/$levelID");
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>