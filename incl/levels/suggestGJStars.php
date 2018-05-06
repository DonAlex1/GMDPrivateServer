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
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$hostname = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$hostname = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{	
	$hostname = $_SERVER['REMOTE_ADDR'];
}
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
$feature = $ep->remove($_POST["feature"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking nothing's empty
if($accountID != "" AND $gjp != ""){
	//Checking GJP
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 1){
		//Checking moderator status
		$permState = $gs->getMaxValuePermission($accountID, "actionRequestMod");
		if($permState == 2){
			//Rating
			$query = $db->prepare("SELECT ratedBy FROM levels WHERE levelID = :levelID LIMIT 1");
			$query->execute([':levelID' => $levelID]);
			if($accountID == 71 OR $query->fetch() != 71){
				$difficulty = $gs->getDiffFromStars($stars);
				$gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"]);
				$gs->featureLevel($accountID, $levelID, $feature);
				$gs->verifyCoinsLevel($accountID, $levelID, 1);
				$query = $db->prepare("UPDATE levels SET ratedBy = :ratedBy WHERE levelID = :levelID");
				$query->execute([':levelID' => $levelID, ':ratedBy' => $accountID]);
				$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('1', :value, :value2, :levelID, :timestamp, :id)");
				$query->execute([':value' => $stars, ':timestamp' => time(), ':id' => $accountID, ':value2' => $feature, ':levelID' => $levelID]);
				echo 1;
			}else{
				exit("-1");
			}
		}if($permState == 1){
			//Suggesting
			$query = $db->prepare("INSERT INTO ratesuggestions (levelID, accountID, stars, feature, isMod, IP, suggestionDate) VALUES (:levelID, :accountID, :stars, :feature, :mod, :IP, :timestamp)");
			$query->execute([':levelID' => $levelID, ':accountID' => $accountID, ':stars' => $stars, ':feature' => $feature, ':mod' => '1', ':IP' => $hostname, ':timestamp' => time()]);
			echo 1;
		}elseif($permState == 0){
			//Not moderator
			exit("-1");
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