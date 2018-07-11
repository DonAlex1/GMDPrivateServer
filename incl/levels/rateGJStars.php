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
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking nothing's files
if($accountID != "" && $gjp != ""){
	//Checking GJP
	if($GJPCheck->check($gjp, $accountID)){
		//Checking if banned
		$query = $db->prepare("SELECT isRatingBanned FROM users WHERE extID = :accountID");
		$query->execute([':accountID' => $accountID]);
		if($query->fetchColumn() == 1) exit("-1");
		//Checking moderator status
		$permState = $gs->getMaxValuePermission($accountID, "actionRequestMod");
		if($accountID == 71){
			$difficulty = $gs->getDiffFromStars($stars);
			$query = $db->prepare("UPDATE levels SET starDemon = :demon, starAuto = :auto, starDifficulty = :diff, rateDate = :now WHERE levelID = :levelID");
			$query->execute([':demon' => $difficulty["demon"], ':auto' => $difficulty["auto"], ':diff' => $difficulty["diff"], ':levelID'=>$levelID, ':now' => time()]);
			echo 1;
		}else{
			$query = $db->prepare("INSERT INTO ratesSuggestions (levelID, accountID, stars, isMod, hostname, suggestionDate) VALUES (:levelID, :accountID, :stars, :mod, :hostname, :date)");
			switch($permState){
				case 1:
					$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':mod' => '1', ':hostname' => $hostname, ':date' => time()]);
					break;
				case 2:
					$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':mod' => '2', ':hostname' => $hostname, ':date' => time()]);
					break;
				default:
					$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':mod' => '0', ':hostname' => $hostname, ':date' => time()]);
					break;
			}
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