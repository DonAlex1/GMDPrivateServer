<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfv3899gc9") exit("-1");
$mS = $ep->remove($_POST["mS"]);
$frS = $ep->remove($_POST["frS"]);
$cS = $ep->remove($_POST["cS"]);
$youtubeurl = $ep->remove($_POST["yt"]);
$gjp = $ep->remove($_POST["gjp"]);
$accountID = $ep->remove($_POST["accountID"]);
$twitter = $ep->remove($_POST["twitter"]);
$twitch = $ep->remove($_POST["twitch"]);
if($GJPCheck->check($gjp, $accountID)){
	//Updating
	$query = $db->prepare("UPDATE accounts SET mS = :mS, frS = :frS, cS = :cS, youtubeurl = :youtubeurl, twitter = :twitter, twitch = :twitch WHERE accountID = :accountID");
	$query->execute([':mS' => $mS, ':frS' => $frS, ':cS' => $cS, ':youtubeurl' => $youtubeurl, ':accountID' => $accountID, ':twitch' => $twitch, ':twitter' => $twitter]);
	echo 1;
}else{
	//Failure
	exit("-1");
}
?>