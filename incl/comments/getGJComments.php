<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-2");
$commentsString = "";
$levelID = 0;
$page = $ep->remove($_POST["page"]);
$gameVersion = $ep->remove($_POST["gameVersion"]);
$binaryVersion = $ep->remove($_POST["binaryVersion"]);
//Mode
if(isset($_POST["mode"]) && is_numeric($_POST["mode"])){
	$mode = $ep->remove($_POST["mode"]);
}else{
	$mode = 0;
}
if(isset($_POST["count"]) && is_numeric($_POST["count"])){
	$count = $ep->remove($_POST["count"]);
}else{
	$count = 10;
}
$commentPage = $page * $count;
//Order
if(!$mode){
	$modeColumn = "commentID";
}else{
	$modeColumn = "likes";
}
if(!isset($_POST["levelID"])){
	//Getting comments history 
	$displayLevelID = true;
	$userID = $ep->remove($_POST["userID"]);
	$levelID = $db->prepare("SELECT extID FROM users WHERE userID = :userID LIMIT 1");
	$levelID->execute([':userID' => $userID]);
	$levelID = $levelID->fetchColumn();
	$query = "SELECT * FROM comments WHERE accountID = :levelID ORDER BY $modeColumn DESC LIMIT $count OFFSET $commentPage";
	$countQuery = "SELECT count(*) FROM comments WHERE accountID = :levelID";
}else{
	//Getting comments
	$displayLevelID = false;
	$levelID = $ep->remove($_POST["levelID"]);
	$query = "SELECT * FROM comments WHERE levelID = :levelID ORDER BY $modeColumn DESC LIMIT $count OFFSET $commentPage";
	$countQuery = "SELECT count(*) FROM comments WHERE levelID = :levelID";
}
//Count
$countQuery = $db->prepare($countQuery);
$countQuery->execute([':levelID' => $levelID]);
$commentCount = $countQuery->fetchColumn();
if(!$commentCount) exit("-2");
$query = $db->prepare($query);
$query->execute([':levelID' => $levelID]);
$result = $query->fetchAll();
foreach($result as &$comment){
	if($comment["commentID"]){
		//Getting comment data
		$uploadDate = $gs->convertDate(date("Y-m-d H:i:s", $comment["timestamp"]));
		if($displayLevelID) $commentsString .= "1~".$comment["levelID"]."~";
		$extID = $comment["accountID"];
		$query = $db->prepare("SELECT userID, userName, icon, color1, color2, iconType, special FROM users WHERE extID = :accountID LIMIT 1");
		$query->execute([':accountID' => $extID]);
		$user = $query->fetchAll()[0];
		$commentsString .= "2~".$comment["comment"]."~3~".$user["userID"]."~4~".$comment["likes"]."~7~".$comment["isSpam"]."~10~".$comment["percent"]."~9~".$uploadDate."~6~".$comment["commentID"];
		$commentsString .= "~11~".$gs->getMaxValuePermission($extID, "modBadgeLevel")."~12~".$gs->getAccountCommentColor($extID).":1~".$user["userName"]."~9~".$user["icon"]."~10~".$user["color1"]."~11~".$user["color2"]."~14~".$user["iconType"]."~15~".$user["special"]."~16~".$extID."|";
	}
}
//Printing account comments
$commentsString = substr($commentsString, 0, -1);
echo $commentsString."#".$commentCount.":".$commentPage.":10";
?>