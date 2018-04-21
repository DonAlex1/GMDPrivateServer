<?php
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$mainLib = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
$gjp = $ep->remove($_POST["gjp"]);
$gjpresult = $GJPCheck->check($gjp,$accountID);
if(!is_numeric($levelID)){
	exit("-1");
}
if($gjpresult == 1){
	//Checking if rated
	$stars = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
	$stars->execute([':levelID' => $levelID]);
	$stars = $stars->fetchColumn();
	if($stars > 0){
		//Rated
		exit("-1");
	}
	//Checking if very downloaded
	$downloads = $db->prepare("SELECT downloads FROM levels WHERE levelID = :levelID");
	$downloads->execute([':levelID' => $levelID]);
	$downloads = $downloads->fetchColumn();
	if($downloads > 9999){
		//Very downloaded
		exit("-1");
	}
	//Getting user ID
	$userID = $mainLib->getUserID($accountID);
	//Deleting
	$query = $db->prepare("DELETE FROM levels WHERE levelID=:levelID AND userID=:userID AND starStars = 0 LIMIT 1");
	$query->execute([':levelID' => $levelID, ':userID' => $userID]);
	$query6 = $db->prepare("INSERT INTO actions (type, value, timestamp, value2) VALUES 
												(:type,:itemID, :time, :ip)");
	$query6->execute([':type' => 8, ':itemID' => $levelID, ':time' => time(), ':ip' => $userID]);
	if(file_exists("../../data/levels/$levelID") AND $query->rowCount() != 0){
		rename("../../data/levels/$levelID","../../data/levels/deleted/$levelID");
	}
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>