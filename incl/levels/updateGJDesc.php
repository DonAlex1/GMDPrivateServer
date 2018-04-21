<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$mainLib = new mainLib();
$GJPCheck = new GJPCheck();
$ep = new exploitPatch();
//Getting data
$levelDesc = $ep->remove($_POST["levelDesc"]);
$levelID = $ep->remove($_POST["levelID"]);
if($_POST["udid"]){
	$id = $ep->remove($_POST["udid"]);
	//Checking if is numeric
	if(is_numeric($id)){
		exit("-1");
	}
}else{
	$id = $ep->remove($_POST["accountID"]);
	//Checking GJP
	$gjp = $ep->remove($_POST["gjp"]);
	$gjpresult = $GJPCheck->check($gjp,$id);
	if($gjpresult != 1){
		exit("-1");
	}
}
//Getting user ID
$userID = $mainLib->getUserID($id, $userName);
//Updating description
$query = $db->prepare("UPDATE levels SET levelDesc=:levelDesc WHERE levelID=:levelID AND userID=:userID");
$query->execute([':levelID' => $levelID, ':userID' => $userID, ':levelDesc' => $levelDesc]);
echo 1;
?>