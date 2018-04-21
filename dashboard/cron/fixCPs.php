<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
chdir(dirname(__FILE__));
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
$dl = new dashboardLib();
$gs = new mainLib();
//Checking permissions
$perms = $gs->checkPermission($_SESSION["accountID"], "dashboardModTools");
if(!$perms){
	//Printing error
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
//Initialazing
$people = array();
$fixed++;
$nocpppl = "";
$query = $db->prepare("SELECT userID, userName FROM users");
$query->execute();
$result = $query->fetchAll();
//Getting users
foreach($result as $user){
	$userID = $user["userID"];
	//Getting star rated levels count
	$query2 = $db->prepare("SELECT count(*) FROM levels WHERE userID = :userID AND starStars != 0 AND isCPShared = 0");
	$query2->execute([':userID' => $userID]);
	$creatorpoints = $query2->fetchColumn();
	//Getting featured levels count
	$query3 = $db->prepare("SELECT count(*) FROM levels WHERE userID = :userID AND starFeatured != 0 AND isCPShared = 0");
	$query3->execute([':userID' => $userID]);
	$cpgain = $query3->fetchColumn();
	$creatorpoints = $creatorpoints + $cpgain;
	//Getting epic levels count
	$query3 = $db->prepare("SELECT count(*) FROM levels WHERE userID = :userID AND starEpic != 0 AND isCPShared = 0");
	$query3->execute([':userID' => $userID]);
	$cpgain = $query3->fetchColumn();
	$creatorpoints = $creatorpoints + $cpgain + $cpgain;
	//Inserting CP value
	if($creatorpoints != 0){
		$people[$userID] = $creatorpoints;
	}else{
		$nocpppl .= $userID.",";
	}
}
//CP sharing
$query = $db->prepare("SELECT levelID, userID, starStars, starFeatured, starEpic FROM levels WHERE isCPShared = 1");
$query->execute();
$result = $query->fetchAll();
foreach($result as $level){
	$deservedcp = 0;
	if($level["starStars"] != 0){
		$deservedcp++;
	}
	if($level["starFeatured"] != 0){
		$deservedcp++;
	}
	if($level["starEpic"] != 0){
		$deservedcp += 2;
	}
	$query = $db->prepare("SELECT userID FROM cpshares WHERE levelID = :levelID");
	$query->execute([':levelID' => $level["levelID"]]);
	$sharecount = $query->rowCount() + 1;
	$addcp = $deservedcp / $sharecount;
	$shares = $query->fetchAll();
	foreach($shares as &$share){
		$people[$share["userID"]] += $addcp;
	}
	$people[$level["userID"]] += $addcp;
}
//Getting gauntlets CPs
$query = $db->prepare("SELECT * FROM gauntlets");
$query->execute();
$result = $query->fetchAll();
//Getting gauntlets
foreach($result as $gauntlet){
	//Getting levels
	for($x = 1; $x < 6; $x++){
		$query = $db->prepare("SELECT userID, levelID FROM levels WHERE levelID = :levelID");
		$query->execute([':levelID' => $gauntlet["level".$x]]);
		$result = $query->fetch();
		//Getting users
		if($result["userID"] != ""){
			$people[$result["userID"]] += 1;
		}
	}
}
//Getting daily CPs
$query = $db->prepare("SELECT levelID FROM dailyfeatures WHERE timestamp < :time");
$query->execute([':time' => time()]);
$result = $query->fetchAll();
//Getting gauntlets
foreach($result as $daily){
	//Getting levels
	$query = $db->prepare("SELECT userID, levelID FROM levels WHERE levelID = :levelID");
	$query->execute([':levelID' => $daily["levelID"]]);
	$result = $query->fetch();
	//Getting users
	if($result["userID"] != ""){
		$people[$result["userID"]] += 1;
	}
}
//Updating CPs
$nocpppl = substr($nocpppl, 0, -1);
$query4 = $db->prepare("UPDATE users SET creatorPoints = 0 WHERE userID IN ($nocpppl)");
$query4->execute();
foreach($people as $user => $cp){
	$query4 = $db->prepare("UPDATE users SET creatorPoints = :creatorpoints WHERE userID=:userID");
	$query4->execute([':userID' => $user, ':creatorpoints' => $cp]);
	$fixed++;
}
if($fixed > 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("fixCPs")."</h1>
					<p>".sprintf($dl->getLocalizedString("fixedCPP"), $fixed)."</p>","cron");
}elseif($fixed == 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("fixCPs")."</h1>
					<p>".$dl->getLocalizedString("fixedCPS")."</p>","cron");
}elseif($fixed == 0){
	$dl->printBox("<h1>".$dl->getLocalizedString("fixCPs")."</h1>
					<p>".$dl->getLocalizedString("noCPFixed")."</p>","cron");
}
?>