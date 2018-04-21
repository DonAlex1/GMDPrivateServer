<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$mainLib = new mainLib();
$GJPCheck = new GJPCheck();
$ep = new exploitPatch();
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
$gameVersion = $ep->remove($_POST["gameVersion"]);
if(!empty($_POST["binaryVersion"])){
	$binaryVersion = $ep->remove($_POST["binaryVersion"]);	
}else{
	$binaryVersion = 0;
}
$userName = $ep->remove($_POST["userName"]);
$userName = preg_replace("/[^A-Za-z0-9 ]/", '', $userName);
$levelID = $ep->remove($_POST["levelID"]);
$levelName = $ep->remove($_POST["levelName"]);
$levelName = preg_replace("/[^A-Za-z0-9 ]/", '', $levelName);
$levelDesc = $ep->remove($_POST["levelDesc"]);
$levelVersion = $ep->remove($_POST["levelVersion"]);
$levelLength = $ep->remove($_POST["levelLength"]);
$audioTrack = $ep->remove($_POST["audioTrack"]);
if(!empty($_POST["auto"])){
	$auto = $ep->remove($_POST["auto"]);
}else{
	$auto = 0;
}
if(isset($_POST["password"])){
	$password = $ep->remove($_POST["password"]);
}else{
	$password = 0;
}
if(!empty($_POST["original"])){
	$original = $ep->remove($_POST["original"]);
}else{
	$original = 0;
}
if(!empty($_POST["twoPlayer"])){
	$twoPlayer = $ep->remove($_POST["twoPlayer"]);
}else{
	$twoPlayer = 0;
}
if(!empty($_POST["songID"])){
	$songID = $ep->remove($_POST["songID"]);
}else{
	$songID = 0;
}
if(!empty($_POST["objects"])){
	$objects = $ep->remove($_POST["objects"]);
}else{
	$objects = 0;
}
if(!empty($_POST["coins"])){
	$coins = $ep->remove($_POST["coins"]);
}else{
	$coins = 0;
}
if(!empty($_POST["requestedStars"])){
	$requestedStars = $ep->remove($_POST["requestedStars"]);
}else{
	$requestedStars = 0;
}
if(!empty($_POST["extraString"])){
	$extraString = $ep->remove($_POST["extraString"]);
}else{
	$extraString = "29_29_29_40_29_29_29_29_29_29_29_29_29_29_29_29";
}
$levelString = $ep->remove($_POST["levelString"]);
if(!empty($_POST["levelInfo"])){
	$levelInfo = $ep->remove($_POST["levelInfo"]);
}else{
	$levelInfo = 0;
}
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	//Error
	exit("-1");
}
if(!empty($_POST["unlisted"])){
	$unlisted = $ep->remove($_POST["unlisted"]);
}else{
	$unlisted = 0;
}
if(!empty($_POST["ldm"])){
	$ldm = $ep->remove($_POST["ldm"]);
}else{
	$ldm = 0;
}
$accountID = "";
if(!empty($_POST["udid"])){
	$id = $ep->remove($_POST["udid"]);
	//Checking if is numeric
	if(is_numeric($id)){
		//Error
		exit("-1");
	}
}
if(!empty($_POST["accountID"]) && $_POST["accountID"] != "0"){
	$id = $ep->remove($_POST["accountID"]);
	//Checking GJP
	$gjpresult = $GJPCheck->check($gjp,$id);
	if($gjpresult != 1){
		//Error
		exit("-1");
	}
}
//Checking if banned
$query3 = $db->prepare("SELECT isLevelBanned FROM users WHERE extID = :accountID");
$query3->execute([':accountID' => $id]);
$result2 = $query3->fetchColumn();
if($result2 == 1){
	//Banned
	exit("-1");
}
//Getting user ID
$userID = $mainLib->getUserID($id, $userName);
$uploadDate = time();
$query = $db->prepare("SELECT count(*) FROM levels WHERE uploadDate > :time AND (userID = :userID OR hostname = :ip)");
$query->execute([':time' => $uploadDate - 60, ':userID' => $userID, ':ip' => $hostname]);
if($query->fetchColumn() > 0){
	//Error
	exit("-1");
}
//Uploading level
$query = $db->prepare("INSERT INTO levels (levelName, gameVersion, binaryVersion, userName, levelDesc, levelVersion, levelLength, audioTrack, auto, password, original, twoPlayer, songID, objects, coins, requestedStars, extraString, levelString, levelInfo, secret, uploadDate, userID, extID, updateDate, unlisted, hostname, isLDM)
VALUES (:levelName, :gameVersion, :binaryVersion, :userName, :levelDesc, :levelVersion, :levelLength, :audioTrack, :auto, :password, :original, :twoPlayer, :songID, :objects, :coins, :requestedStars, :extraString, :levelString, :levelInfo, :secret, :uploadDate, :userID, :id, :uploadDate, :unlisted, :hostname, :ldm)");
//Checking nothing's empty
if($levelString != "" AND $levelName != ""){
	//Checking if uploaded
	$querye = $db->prepare("SELECT levelID FROM levels WHERE levelName = :levelName AND userID = :userID");
	$querye->execute([':levelName' => $levelName, ':userID' => $userID]);
	$levelID = $querye->fetchColumn();
	$lvls = $querye->rowCount();
	if($lvls == 1){
		//Updating level
		$query = $db->prepare("UPDATE levels SET levelName=:levelName, gameVersion=:gameVersion,  binaryVersion=:binaryVersion, userName=:userName, levelDesc=:levelDesc, levelVersion=:levelVersion, levelLength=:levelLength, audioTrack=:audioTrack, auto=:auto, password=:password, original=:original, twoPlayer=:twoPlayer, songID=:songID, objects=:objects, coins=:coins, requestedStars=:requestedStars, extraString=:extraString, levelString=:levelString, levelInfo=:levelInfo, secret=:secret, updateDate=:uploadDate, unlisted=:unlisted, hostname=:hostname, isLDM=:ldm WHERE levelName=:levelName AND extID=:id");	
		$query->execute([':levelName' => $levelName, ':gameVersion' => $gameVersion, ':binaryVersion' => $binaryVersion, ':userName' => $userName, ':levelDesc' => $levelDesc, ':levelVersion' => $levelVersion, ':levelLength' => $levelLength, ':audioTrack' => $audioTrack, ':auto' => $auto, ':password' => $password, ':original' => $original, ':twoPlayer' => $twoPlayer, ':songID' => $songID, ':objects' => $objects, ':coins' => $coins, ':requestedStars' => $requestedStars, ':extraString' => $extraString, ':levelString' => "", ':levelInfo' => $levelInfo, ':secret' => $secret, ':levelName' => $levelName, ':id' => $id, ':uploadDate' => $uploadDate, ':unlisted' => $unlisted, ':hostname' => $hostname, ':ldm' => $ldm]);
		file_put_contents("../../data/levels/$levelID",$levelString);
		echo $levelID;
	}else{
		//Saving level
		$query->execute([':levelName' => $levelName, ':gameVersion' => $gameVersion, ':binaryVersion' => $binaryVersion, ':userName' => $userName, ':levelDesc' => $levelDesc, ':levelVersion' => $levelVersion, ':levelLength' => $levelLength, ':audioTrack' => $audioTrack, ':auto' => $auto, ':password' => $password, ':original' => $original, ':twoPlayer' => $twoPlayer, ':songID' => $songID, ':objects' => $objects, ':coins' => $coins, ':requestedStars' => $requestedStars, ':extraString' => $extraString, ':levelString' => "", ':levelInfo' => $levelInfo, ':secret' => $secret, ':uploadDate' => $uploadDate, ':userID' => $userID, ':id' => $id, ':unlisted' => $unlisted, ':hostname' => $hostname, ':ldm' => $ldm]);
		$levelID = $db->lastInsertId();
		file_put_contents("../../data/levels/$levelID",$levelString);
		echo $levelID;
	}
}else{
	//Failure
	exit("-1");
}
?>
