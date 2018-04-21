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
//Getting users
$userNames = 0;
$query = $db->prepare("SELECT userName, accountID FROM accounts");
$query->execute();
$result = $query->fetchAll();
foreach($result as $account){
	$accountID = $account["accountID"];
	$userName = $account["userName"];
	$query4 = $db->prepare("UPDATE users SET userName = :userName WHERE extID = :accountID");
	$query4->execute([':userName' => $userName, ':accountID' => $accountID]);
	$userNames++;
}
if($userNames > 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("fixNames")."</h1>
					<p>".sprintf($dl->getLocalizedString("fixedUsernamesP"), $userNames)."</p>","cron");
}elseif($userNames == 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("fixNames")."</h1>
					<p>".$dl->getLocalizedString("fixedUsernamesS")."</p>","cron");
}elseif($userNames == 0){
	$dl->printBox("<h1>".$dl->getLocalizedString("fixNames")."</h1>
					<p>".$dl->getLocalizedString("noFixedUsernames")."</p>","cron");
}
?>