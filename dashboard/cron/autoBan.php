<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
$gs = new mainLib();
$dl = new dashboardLib();
//Checking permissions
$perms = $gs->checkPermission($_SESSION["accountID"], "dashboardModTools");
if(!$perms){
	//Printing error
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
//Initializing autoban
$query = $db->prepare("SELECT starStars, coins, starDemon, starCoins FROM levels");
$query->execute();
$levelstuff = $query->fetchAll();
//Counting levels
$stars = 0;
$demons = 0;
$banned = 0;
foreach($levelstuff as $level){
	$stars = $stars + $level["starStars"];
	if($level["starCoins"] != 0){
		$coins += $level["coins"];
	}
	if($level["starDemon"] != 0){
		$demons++;
	}
}
$query = $db->prepare("SELECT stars FROM mappacks");
$query->execute();
$result = $query->fetchAll();
//Counting stars
foreach($result as $pack){
	$stars += $pack["stars"];
}
$quarter = floor($stars / 4);
$stars = $stars + 252 + $quarter;
$query = $db->prepare("SELECT userID, extID, userName FROM users WHERE stars > :stars AND isBanned = 0");
$query->execute([':stars' => $stars]);
$result = $query->fetchAll();
//Banning users
foreach($result as $user){
	if($user["extID"] != 71){
		$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE userID = :id");
		$query->execute([':id' => $user["userID"]]);
		$banned++;
	}
}
//Counting coins
$coins = 39;
$query = $db->prepare("SELECT userID, extID, userName FROM users WHERE coins > :coins AND isBanned = 0");
$query->execute([':coins' => $coins]);
$result = $query->fetchAll();
//Banning users
foreach($result as $user){
	if($user["extID"] != 71){
		$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE userID = :id");
		$query->execute([':id' => $user["userID"]]);
		$banned++;
	}
}
//Counting user coins
$quarter = floor($coins / 4);
$coins = $coins + 10 + $quarter;
$query = $db->prepare("SELECT userID, extID, userName FROM users WHERE userCoins > :coins AND isBanned = 0");
$query->execute([':coins' => $coins]);
$result = $query->fetchAll();
//Banning users
foreach($result as $user){
	if($user["extID"] != 71){
		$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE userID = :id");
		$query->execute([':id' => $user["userID"]]);
		$banned++;
	}
}
//Counting demons
$quarter = floor($demons / 16);
$demons = $demons + 3 + $quarter;
$query = $db->prepare("SELECT userID, extID, userName FROM users WHERE demons > :demons AND isBanned = 0");
$query->execute([':demons' => $demons]);
$result = $query->fetchAll();
//Banning users
foreach($result as $user){
	if($user["extID"] != 71){
		$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE userID = :id");
		$query->execute([':id' => $user["userID"]]);
		$banned++;
	}
}
//Banning IPs
$query = $db->prepare("SELECT IP FROM bannedips");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$ip){
	$query = $dl->prepare("UPDATE accounts SET isBanned = '1' WHERE IP LIKE CONCAT(:ip, '%')");
	$query->execute([':ip' => $ip["IP"]]);
	$banned++;
}
if($banned > 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("autoBan")."</h1>
					<p>".sprintf($dl->getLocalizedString("bannedP"), $banned)."</p>","cron");
}elseif($banned == 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("autoBan")."</h1>
					<p>".$dl->getLocalizedString("bannedS")."</p>","cron");
}elseif($banned == 0){
	$dl->printBox("<h1>".$dl->getLocalizedString("autoBan")."</h1>
					<p>".$dl->getLocalizedString("noBanned")."</p>","cron");
}
?>