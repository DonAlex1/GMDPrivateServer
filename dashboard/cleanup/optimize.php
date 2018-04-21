<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
require_once "../../incl/lib/mainLib.php";
require_once "../incl/dashboardLib.php";
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
$issues = 0;
//Deleting inavlid users
$query = $db->prepare("SELECT * FROM users");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$user){
	if($user["extID"] == ""){
		$query = $db->prepare("DELETE FROM users WHERE userID = :userID LIMIT 1");
		$query->execute([':userID' => $user["userID"]]);
		$issues++;
	}
}
//Deleting invalid songs
$query = $db->prepare("SELECT * FROM songs");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$song){
	if($song["download"] == ""){
		$query = $db->prepare("DELETE FROM songs WHERE ID = :ID LIMIT 1");
		$query->execute([':ID' => $song["ID"]]);
		$issues++;
	}
}
//Deleting unused accounts
$query = $db->prepare("SELECT accountID, registerDate, active FROM accounts");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$account){
	$query = $db->prepare("SELECT count(*) FROM users WHERE extID = :accountID");
	$query->execute([':accountID' => $account["accountID"]]);
	if($query->fetchColumn() == 0){
		$time = time() - 2592000;
		if($account["registerDate"] < $time){
			$query = $db->prepare("DELETE FROM accounts WHERE accountID = :accountID LIMIT 1");
			$query->execute([':accountID' => $account["accountID"]]);
			$issues++;
		}
	}
	if($account["active"] == 0){
		$time = time() - 604800;
		if($account["registerDate"] < $time){
			$query = $db->prepare("DELETE FROM accounts WHERE accountID = :accountID LIMIT 1");
			$query->execute([':accountID' => $account["accountID"]]);
			$issues++;
			$query = $db->prepare("SELECT count(*) FROM users WHERE extID = :accountID");
			$query->execute([':accountID' => $account["accountID"]]);
			if($query->fetchColumn() == 1){
				$query = $db->prepare("DELETE FROM users WHERE extID = :accountID LIMIT 1");
				$query->execute([':accountID' => $account["accountID"]]);
			}
		}
	}
}
//Fixing userIDs
$query = $db->prepare("SELECT accountID, userID, userName FROM accounts");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$accounts){
	$query = $db->prepare("SELECT count(*) FROM users WHERE extID = :accountID");
	$query->execute([':accountID' => $accounts["accountID"]]);
	if($query->fetchColumn() == 1){
		$query = $db->prepare("SELECT userID FROM users WHERE extID = :accountID LIMIT 1");
		$query->execute([':accountID' => $accounts["accountID"]]);
		$result2 = $query->fetchColumn();
		if($result2 != $account["userID"]){
			$query = $db->prepare("UPDATE accounts SET userID = :userID WHERE userName = :userName AND accountID = :accountID");
			$query->execute([':userID' => $accounts["userID"], ':userName' => $accounts["userName"], ':accountID' => $accounts["accountID"]]);		
			$issues++;
		}
	}
}
//Printing
if($issues > 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("optimize")."</h1>
					<p>".sprintf($dl->getLocalizedString("optimizedP"), $issues)."</p>","cron");
}elseif($issues == 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("optimize")."</h1>
					<p>".$dl->getLocalizedString("optimizedS")."</p>","cron");
}elseif($issues == 0){
	$dl->printBox("<h1>".$dl->getLocalizedString("optimize")."</h1>
					<p>".$dl->getLocalizedString("noOptimized")."</p>","cron");
}
?>