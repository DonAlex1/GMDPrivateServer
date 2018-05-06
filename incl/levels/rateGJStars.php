<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
$gs = new mainLib();
//Getting IP
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
}
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking nothing's files
if($accountID != "" AND $gjp != ""){
	//Checking GJP
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 1){
		//Checking moderator status
		$permState = $gs->getMaxValuePermission($accountID, "actionRequestMod");
		if($permState == 2){
			//Rating
			$difficulty = $gs->getDiffFromStars($stars);
			$query = $db->prepare("UPDATE levels SET starDemon = :demon, starAuto = :auto, starDifficulty = :diff, rateDate = :now WHERE levelID = :levelID");
			$query->execute([':demon' => $difficulty["demon"], ':auto' => $difficulty["auto"], ':diff' => $difficulty["diff"], ':levelID'=>$levelID, ':now' => time()]);
			echo 1;
		}if($permState == 1){
			//Checking if banned
			$query3 = $db->prepare("SELECT isRatingBanned FROM users WHERE extID = :accountID");
			$query3->execute([':accountID' => $accountID]);
			$result2 = $query3->fetchColumn();
			if($result2 == 1){
				//Banned
				exit("-1");
			}
			//Rating
			$query = $db->prepare("INSERT INTO ratesuggestions (levelID, accountID, stars, feature, isMod, IP, suggestionDate) VALUES (:levelID, :accountID, :stars, :feature, :mod, :IP, :date)");
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':feature' => $feature, ':mod' => '1', ':IP' => $ip, ':date' => time()]);
			echo 1;
		}elseif($permState == 0){
			//Checking if banned
			$query3 = $db->prepare("SELECT isRatingBanned FROM users WHERE extID = :accountID");
			$query3->execute([':accountID' => $accountID]);
			$result2 = $query3->fetchColumn();
			if($result2 == 1){
				//Banned
				exit("-1");
			}
			//Rating
			$query = $db->prepare("INSERT INTO ratesuggestions (levelID, accountID, stars, isMod, IP, suggestionDate) VALUES (:levelID, :accountID, :stars, :mod, :IP, :date)");
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':mod' => '0', ':IP' => $ip, ':date' => time()]);
			echo 1;
		}
	}else{
		//Error
		exit("-1");
	}
}else{
	//Failure
	exit("-1");
}