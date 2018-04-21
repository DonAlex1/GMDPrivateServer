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
//Getting accounts
$friends = 0;
$query = $db->prepare("SELECT accountID, userName FROM accounts");
$query->execute();
$result = $query->fetchAll();
foreach($result as $account){
	//Getting friends count
	$me = $account["accountID"];
	$query2 = $db->prepare("SELECT count(*) FROM friendships WHERE person1 = :me OR person2 = :me");
	$query2->execute([':me' => $me]);
	$friendscount = $query2->fetchColumn();
	//Updating friends count
	if($friendscount != 0){
		$query4 = $db->prepare("UPDATE accounts SET friendsCount=:friendscount WHERE accountID=:me");
		$query4->execute([':friendscount' => $friendscount, ':me' => $me]);
		$friends++;
	}
}
if($friends > 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("friendsLeaderboard")."</h1>
					<p>".sprintf($dl->getLocalizedString("friendsP"), $friends)."</p>","cron");
}elseif($friends == 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("friendsLeaderboard")."</h1>
					<p>".$dl->getLocalizedString("friendsS")."</p>","cron");
}elseif($friends == 0){
	$dl->printBox("<h1>".$dl->getLocalizedString("friendsLeaderboard")."</h1>
					<p>".$dl->getLocalizedString("noFriends")."</p>","cron");
}
?>