<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
$GJPCheck = new GJPCheck();
//Getting IP
$hostname = $gs->getIP();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfp3879gc3") exit("-1");
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
$feature = $ep->remove($_POST["feature"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking nothing's empty
if($accountID != "" && $gjp != ""){
	//Checking GJP
	if($GJPCheck->check($gjp, $accountID)){
		//Checking moderator status
		$permState = $gs->getMaxValuePermission($accountID, "actionRequestMod");
		if($accountID == 71){
			//Rating
			$difficulty = $gs->getDiffFromStars($stars);
			$gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"]);
			$gs->featureLevel($accountID, $levelID, $feature);
			$gs->verifyCoinsLevel($accountID, $levelID, 1);
			$query = $db->prepare("UPDATE levels SET ratedBy = :ratedBy WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID, ':ratedBy' => $accountID]) or die("-1");
			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('1', :value, :value2, :levelID, :timestamp, :id)");
			$query->execute([':value' => $stars, ':timestamp' => time(), ':id' => $accountID, ':value2' => $feature, ':levelID' => $levelID]) or die("-1");
			echo 1;
		}else{
			//Suggesting
			$query = $db->prepare("INSERT INTO ratesSuggestions (levelID, accountID, stars, feature, isMod, hostname, suggestionDate) VALUES (:levelID, :accountID, :stars, :feature, :mod, :hostname, :timestamp)");
			switch($permState){
				case 1:
					$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':feature' => $feature, ':mod' => '1', ':hostname' => $hostname, ':timestamp' => time()]) or die("-1");
					echo 1;
					break;
				case 2:
					$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':feature' => $feature, ':mod' => '2', ':hostname' => $hostname, ':timestamp' => time()]) or die("-1");
					echo 1;
					break;
				default:
					exit("-1");
			}
		}
	}else{
		//Error
		exit("-1");
	}
}else{
	//Failure
	exit("-1");
}
?>