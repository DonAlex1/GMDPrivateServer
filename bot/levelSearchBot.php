<?php
include "../incl/lib/connection.php";
require_once "../incl/lib/exploitPatch.php";
require_once "../incl/lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
//Getting searched level
$str = $ep->remove($_POST["str"]);
$difficulty;
$original;
//Getting level data
$query = $db->prepare("(SELECT * FROM levels WHERE levelID = :str) UNION (SELECT * FROM levels WHERE levelName LIKE CONCAT('%', :str, '%') ORDER BY likes DESC LIMIT 1)"); //getting level info
$query->execute([':str' => $str]);
//Checking if exists
if($query->rowCount() == 0){
	exit("-1");
}
$levelInfo = $query->fetchAll()[0];
//Getting creator name
$query = $db->prepare("SELECT userName FROM accounts WHERE userID = :userID");
$query->execute([':userID' => $levelInfo["userID"]]);
$creator = $query->fetchColumn();
//getting song name
if($levelInfo["songID"] != 0){
	$query = $db->prepare("SELECT name, authorName, ID FROM songs WHERE ID = :songID");
	$query->execute([':songID' => $levelInfo["songID"]]);
	$songInfo = $query->fetchAll()[0];
	$song = $songInfo["name"] . " by " . $songInfo["authorName"] . " (ID: " . $songInfo["ID"] . ")";
}else{
	$song = $gs->getAudioTrack($levelInfo["audioTrack"]);
}
//getting difficulty
if($levelInfo["starDemon"] == 1){
	$difficulty .= $gs->getDemonDiff($levelInfo["starDemonDiff"]) . " ";
}
$difficulty .= $gs->getDifficulty($levelInfo["starDifficulty"],$levelInfo["starAuto"],$levelInfo["starDemon"]);
$difficulty .= " " . $levelInfo["starStars"] ."* (State: ";
if($levelInfo["starEpic"] == 1){
	$difficulty .= "Featured and Epic)";
}else if($levelInfo["starFeatured"] == 1 && $levelInfo["starEpic"] == 0){
	$difficulty .= "Featured)";
}
//getting length
$length = $gs->getLength($levelInfo["levelLength"]);
//times
$uploadDate = $gs->time_elapsed_string(date("Y-m-d H:i:s", $levelInfo["uploadDate"])) . " ago";
$updateDate = $gs->time_elapsed_string(date("Y-m-d H:i:s", $levelInfo["updateDate"])) . " ago";
//getting original level
if($levelInfo["original"] != 0){
	$original = $levelInfo["original"];
}
if($levelInfo["originalReup"] != 0){
	$original = $levelInfo["originalReup"] . " (Reuploaded)";
}
//whorated
if($levelInfo["ratedBy"] != 0){
	$query = $db->prepare("SELECT userName FROM accounts WHERE accountID = :accountID LIMIT 1");
	$query->execute([':accountID' => $levelInfo["ratedBy"]]);
	$ratedBy = $query->fetchAll();
	$ratedBy = $ratedBy[0];
	$ratedBy = $ratedBy["userName"];
}elseif($levelInfo["ratedBy"] == 0){
	$ratedBy = 0;
}
//Checking coins
$coins = $levelInfo["coins"];
if($levelInfo["starCoins"] != 0){
	$coins .= " (State: Verified)";
}else{
	$coins .= " (State: Unverified)";
}
//gameVersion
$gameVersion = $gs->getGameVersion($levelInfo["gameVersion"]);
$array = array(
	'levelName' => $levelInfo["levelName"],
	'levelID' => $levelInfo["levelID"],
	'author' => $creator,
	'song' => $song,
	'difficulty' => $difficulty,
	'coins' => $coins,
	'length' => $length,
	'uploadTime' => $uploadDate,
	'updateTime' => $updateDate,
	'original' => $original,
	'ratedBy' => $ratedBy,
	'objects' => $levelInfo["objects"],
	'levelVersion' => $levelInfo["levelVersion"],
	'gameVersion' => $gameVersion,
	'downloads' => $levelInfo["downloads"],
	'likes' => $levelInfo["likes"]
);

echo json_encode($array);
?>