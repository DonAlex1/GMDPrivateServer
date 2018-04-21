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
//Getting songs
$query = $db->prepare("SELECT ID FROM songs");
$query->execute();
$result = $query->fetchAll();
$songs = $db->prepare("SELECT count(*) FROM songs");
$songs->execute();
$songs = $songs->fetchColumn();
foreach($result as &$songData){
	//Getting song count
	$song = $songData["ID"];
	$query2 = $db->prepare("SELECT count(*) FROM levels WHERE songID = :song");
	$query2->execute([':song' => $song]);
	$count = $query2->fetchColumn();
	//Updating song count
	if($count != 0){
		$query4 = $db->prepare("UPDATE songs SET levelsCount=:count WHERE ID=:songID");
		$query4->execute([':count' => $count, ':songID' => $song]);
	}
}
if($songs > 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("songsCount")."</h1>
					<p>".sprintf($dl->getLocalizedString("songsP"), $songs)."</p>","cron");
}elseif($songs == 1){
	$dl->printBox("<h1>".$dl->getLocalizedString("songsCount")."</h1>
					<p>".$dl->getLocalizedString("songsS")."</p>","cron");
}elseif($songs == 0){
	$dl->printBox("<h1>".$dl->getLocalizedString("songsCount")."</h1>
					<p>".$dl->getLocalizedString("noSongs")."</p>","cron");
}
?>