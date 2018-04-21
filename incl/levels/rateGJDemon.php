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
//Getting IP
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
}
//Getting data
if(!isset($_POST["gjp"]) OR !isset($_POST["rating"]) OR !isset($_POST["levelID"]) OR !isset($_POST["accountID"])){
	//Error
	exit("-1");
}
$gjp = $ep->remove($_POST["gjp"]);
$rating = $ep->remove($_POST["rating"]);
$levelID = $ep->remove($_POST["levelID"]);
$id = $ep->remove($_POST["accountID"]);
//Ckecking GJP
$gjpresult = $GJPCheck->check($gjp,$id);
//Checking moderator status
$permState = $gs->getMaxValuePermission($id, "actionRequestMod");
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
$timestamp = time();
if($permState == 2){
	//Rating
	$query = $db->prepare("UPDATE levels SET starDemonDiff=:demon WHERE levelID=:levelID");	
	$query->execute([':demon' => $dmn, ':levelID'=>$levelID]);
	$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('10', :value, :levelID, :timestamp, :id)");
	$query->execute([':value' => $dmnname, ':timestamp' => $timestamp, ':id' => $id, ':levelID' => $levelID]);
	echo $levelID;
}elseif($permState == 1){
	//Checking if banned
	$query3 = $db->prepare("SELECT isRatingBanned FROM users WHERE extID = :accountID");
	$query3->execute([':accountID' => $id]);
	$result2 = $query3->fetchColumn();
	if($result2 == 1){
		//Banned
		exit("-1");
	}
	//Rating
	$query = $db->prepare("INSERT INTO demondiffsuggestions (levelID, accountID, diff, isMod, IP, suggestionDate) VALUES (:levelID, :accountID, :diff, :mod, :IP, :date)");
	$query->execute([':levelID' => $levelID, ':accountID' => $id, ':demon' => $dmnname, ':mod' => '1', ':IP' => $ip, ':date' => time()]);
	echo $levelID;
}else{
	//Checking if banned
	$query3 = $db->prepare("SELECT isRatingBanned FROM users WHERE extID = :accountID");
	$query3->execute([':accountID' => $id]);
	$result2 = $query3->fetchColumn();
	if($result2 == 1){
		//Banned
		exit("-1");
	}
	//Rating
	$query = $db->prepare("INSERT INTO demondiffsuggestions (levelID, accountID, diff, isMod, IP, suggestionDate) VALUES (:levelID, :accountID, :diff, :mod, :IP, :date)");
	$query->execute([':levelID' => $levelID, ':accountID' => $id, ':diff' => $dmnname, ':mod' => '0', ':IP' => $ip, ':date' => time()]);
	echo $levelID;
}
?>