<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
//Getting data
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	//Error
	exit("-2");
}
$binaryVersion = $ep->remove($_POST["binaryVersion"]);
$gameVersion = $ep->remove($_POST["gameVersion"]);
$commentstring = "";
$userstring = "";
$users = array();
//Mode
if(isset($_POST["mode"])){
	$mode = $ep->remove($_POST["mode"]);
}else{
	$mode = 0;
}
if(isset($_POST["count"]) AND is_numeric($_POST["count"])){
	$count = $ep->remove($_POST["count"]);
}else{
	$count = 10;
}
$page = $ep->remove($_POST["page"]);
$commentpage = $page*$count;
//Order
if($mode == 0){
	$modeColumn = "commentID";
}else{
	$modeColumn = "likes";
}
if(!$_POST["levelID"]){
	//Getting comments history 
	$displayLevelID = true;
	$levelID = $ep->remove($_POST["userID"]);
	$query = "SELECT * FROM comments WHERE userID = :levelID AND secret = :secret ORDER BY $modeColumn DESC LIMIT $count OFFSET $commentpage";
	$countquery = "SELECT count(*) FROM comments WHERE userID = :levelID AND secret = :secret";
}else{
	//Getting comments
	$displayLevelID = false;
	$levelID = $ep->remove($_POST["levelID"]);
	$query = "SELECT * FROM comments WHERE levelID = :levelID AND secret = :secret ORDER BY $modeColumn DESC LIMIT $count OFFSET $commentpage";
	$countquery = "SELECT count(*) FROM comments WHERE levelID = :levelID AND secret = :secret";
}
//Count
$countquery = $db->prepare($countquery);
$countquery->execute([':levelID' => $levelID, ':secret' => "Wmfd2893gb7"]);
$commentcount = $countquery->fetchColumn();
if($commentcount == 0){
	//Nothing
	exit("-2");
}
$query = $db->prepare($query);
$query->execute([':levelID' => $levelID, ':secret' => "Wmfd2893gb7"]);
$result = $query->fetchAll();
foreach($result as &$comment1){
	if($comment1["commentID"]!=""){
		//Getting comment data
		$uploadDate = $gs->time_elapsed_string(date("Y-m-d H:i:s", $comment1["timestamp"]));
		$actualcomment = $comment1["comment"];
		if($displayLevelID){
			$commentstring .= "1~".$comment1["levelID"]."~";
		}
		$commentstring .= "2~".$actualcomment."~3~".$comment1["userID"]."~4~".$comment1["likes"]."~5~0~7~".$comment1["isSpam"]."~9~".$uploadDate."~6~".$comment1["commentID"]."~10~".$comment1["percent"];
		$query12 = $db->prepare("SELECT userID, userName, icon, color1, color2, iconType, special, extID FROM users WHERE userID = :userID");
		$query12->execute([':userID' => $comment1["userID"]]);
		if($query12->rowCount() > 0){
			$user = $query12->fetchAll()[0];
			//Checking if is numeric
			if(is_numeric($user["extID"])){
				$extID = $user["extID"];
			}else{
				$extID = 0;
			}
			if(!in_array($user["userID"], $users)){
				$users[] = $user["userID"];
				$userstring .=  $user["userID"] . ":" . $user["userName"] . ":" . $extID . "|";
			}
			$commentstring .= "~11~".$gs->getMaxValuePermission($extID, "modBadgeLevel")."~12~".$gs->getAccountCommentColor($extID).":1~".$user["userName"]."~7~1~9~".$user["icon"]."~10~".$user["color1"]."~11~".$user["color2"]."~14~".$user["iconType"]."~15~".$user["special"]."~16~".$user["extID"];
			$commentstring .= "|";
		}
	}
}
//Printing account comments
$commentstring = substr($commentstring, 0, -1);
$userstring = substr($userstring, 0, -1);
echo $commentstring;
echo "#".$commentcount.":".$commentpage.":10";
?>