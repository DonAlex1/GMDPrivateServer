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
//Getting IP
$hostname = $gs->getIP();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfp3879gc3") exit("-1");
if(!isset($_POST["gjp"]) || !isset($_POST["rating"]) || !isset($_POST["levelID"]) || !isset($_POST["accountID"])){
	//Error
	exit("-1");
}
$gjp = $ep->remove($_POST["gjp"]);
$accountID = $ep->remove($_POST["accountID"]);
$rating = $ep->remove($_POST["rating"]);
$levelID = $ep->remove($_POST["levelID"]);
//Ckecking GJP
if(!$GJPCheck->check($gjp, $accountID)) exit("-1");
//Checking if banned
$query = $db->prepare("SELECT isRatingBanned FROM users WHERE extID = :accountID");
$query->execute([':accountID' => $accountID]);
if($query->fetchColumn() == 1) exit("-1");
//Checking moderator status
$permState = $gs->getMaxValuePermission($accountID, "actionRequestMod");
$auto = 0;
$demon = 0;
//Detecting rate
switch($rating){
	case 1:
		$dmn = 3;
		$dmnname = "Easy";
		break;
	case 2:
		$dmn = 4;
		$dmnname = "Medium";
		break;
	case 3:
		$dmn = 0;
		$dmnname = "Hard";
		break;
	case 4:
		$dmn = 5;
		$dmnname = "Insane";
		break;
	case 5:
		$dmn = 6;
		$dmnname = "Extreme";
		break;
}
if($accountID == 71){
	//Rating
	$query = $db->prepare("UPDATE levels SET starDemonDiff = :demon WHERE levelID = :levelID");	
	$query->execute([':demon' => $dmn, ':levelID' => $levelID]);
	echo $levelID;
}else{
	//Suggesting
	$query = $db->prepare("INSERT INTO demonDiffSuggestions (levelID, accountID, diff, isMod, hostname, suggestionDate) VALUES (:levelID, :accountID, :diff, :isMod, :hostname, :date)");
	switch($permState){
		case 1:
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':demon' => $dmn, ':isMod' => '1', ':hostname' => $hostname, ':date' => time()]);
			break;
		case 2:
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':demon' => $dmn, ':isMod' => '2', ':hostname' => $hostname, ':date' => time()]);
			break;
		default:
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':diff' => $dmn, ':isMod' => '0', ':hostname' => $hostname, ':date' => time()]);
			break;
	}
	echo $levelID;
}
?>