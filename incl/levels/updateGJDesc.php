<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$gs = new mainLib();
$GJPCheck = new GJPCheck();
$ep = new exploitPatch();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$levelDesc = $ep->remove($_POST["levelDesc"]);
$levelID = $ep->remove($_POST["levelID"]);
if($_POST["udid"]){
	$accountID = $ep->remove($_POST["udid"]);
	//Checking if is numeric
	if(is_numeric($accountID)) exit("-1");
}else{
	$accountID = $ep->remove($_POST["accountID"]);
	//Checking GJP
	$gjp = $ep->remove($_POST["gjp"]);
	if(!$GJPCheck->check($gjp, $accountID)) exit("-1");
}
//Getting user ID
$userID = $gs->getUserID($accountID, $userName);
//Updating description
$query = $db->prepare("UPDATE levels SET levelDesc = :levelDesc WHERE levelID = :levelID AND userID = :userID LIMIT 1");
$query->execute([':levelID' => $levelID, ':userID' => $userID, ':levelDesc' => $levelDesc]) or die("-1");
echo 1;
?>