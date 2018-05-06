<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
require "../lib/generateHash.php";
$hash = new generateHash();
$ep = new exploitPatch();
$gs = new mainLib();
$GJPCheck = new GJPCheck();
//Getting data
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	//Error
	exit("-1");
}
$lvlstring = "";
$userstring = "";
$songsstring  = "";
$lvlsmultistring = "";
$params = array("NOT unlisted = 1");
if(!empty($_POST["gameVersion"])){
	$gameVersion = $ep->remove($_POST["gameVersion"]);
}else{
	$gameVersion = 0;
}
if(!is_numeric($gameVersion)){
	//Error
	exit("-1");
}
if($gameVersion == 20){
	$binaryVersion = $ep->remove($_POST["binaryVersion"]);
	if($binaryVersion > 27){
		$gameVersion++;
	}
}
if(!empty($_POST["type"])){
	$type = $ep->remove($_POST["type"]);
}else{
	$type = 0;
}
$query = "";
if(!empty($_POST["len"])){
	$len = $ep->remove($_POST["len"]);
}else{
	$len = "-";
}
if(!empty($_POST["diff"])){
	$diff = $ep->remove($_POST["diff"]);
}else{
	$diff = "-";
}
//Additional parameters
if($gameVersion == 0){
	$params[] = "gameVersion <= 18";
}else{
	$params[] = " gameVersion <= '$gameVersion'";
}
if(!empty($_POST["featured"]) && $_POST["featured"] == 1){
	$params[] = "starFeatured = 1 OR starEpic = 1";
}
if(!empty($_POST["original"]) && $_POST["original"] == 1){
	$params[] = "original = 0";
}
if(!empty($_POST["coins"]) && $_POST["coins"] == 1){
	$params[] = "starCoins = 1 AND NOT coins = 0";
}
if(!empty($_POST["epic"]) && $_POST["epic"] == 1){
	$params[] = "starEpic = 1";
}
if(!empty($_POST["uncompleted"]) && $_POST["uncompleted"] == 1){
	$completedLevels = $ep->remove($_POST["completedLevels"]);
	$completedLevels = explode("(",$completedLevels)[1];
	$completedLevels = explode(")",$completedLevels)[0];
	$completedLevels = $db->quote($completedLevels);
	$completedLevels = str_replace("'","", $completedLevels);
	$params[] = "NOT levelID IN ($completedLevels)";
}
if(!empty($_POST["onlyCompleted"]) && $_POST["onlyCompleted"] == 1){
	$completedLevels = $ep->remove($_POST["completedLevels"]);
	$completedLevels = explode("(",$completedLevels)[1];
	$completedLevels = explode(")",$completedLevels)[0];
	$completedLevels = $db->quote($completedLevels);
	$completedLevels = str_replace("'","", $completedLevels);
	$params[] = "levelID IN ($completedLevels)";
}
if(!empty($_POST["song"])){
	//Checking if custom song or normal
	if(empty($_POST["customSong"])){
		$song = $ep->remove($_POST["song"]);
		$song = str_replace("'", "", $db->quote($song));
		$song = $song -1;
		$params[] = "audioTrack = '$song' AND songID = 0";
	}else{
		$song = $ep->remove($_POST["song"]);
		$params[] = "songID = '$song'";
	}
}
if(!empty($_POST["twoPlayer"]) && $_POST["twoPlayer"] == 1){
	$params[] = "twoPlayer = 1";
}
if(!empty($_POST["star"])){
	$params[] = "NOT starStars = 0";
}
if(!empty($_POST["noStar"])){
	$params[] = "starStars = 0";
}
if(!empty($_POST["gauntlet"])){
	$order = "starStars ASC";
	$gauntlet = $ep->remove($_POST["gauntlet"]);
	$query=$db->prepare("SELECT * FROM gauntlets WHERE ID = :gauntlet");
	$query->execute([':gauntlet' => $gauntlet]);
	$actualgauntlet = $query->fetch();
	$str = $actualgauntlet["levels"];
	$params[] = "levelID IN ($str)";
	unset($type);
}
//Difficulty filters
$diff = $db->quote($diff);
$diff = str_replace("'","", $diff);
$diff = explode(")",$diff)[0];
switch($diff){
	case -1:
		//NA difficulty
		$params[] = "starDifficulty = '0'";
		break;
	case -3:
		//Auto
		$params[] = "starAuto = '1'";
		break;
	case -2:
		//Demon filter
		if(!empty($_POST["demonFilter"])){
			$demonFilter = $ep->remove($_POST["demonFilter"]);
		}else{
			$demonFilter = 0;
		}
		$params[] = "starDemon = 1";
		switch($demonFilter){
			case 1:
				//Easy
				$params[] = "starDemonDiff = '3'";
				break;
			case 2:
				//Medium
				$params[] = "starDemonDiff = '4'";
				break;
			case 3:
				//Hard
				$params[] = "starDemonDiff = '0'";
				break;
			case 4:
				//Insane
				$params[] = "starDemonDiff = '5'";
				break;
			case 5:
				//Extreme
				$params[] = "starDemonDiff = '6'";
				break;
			default:
				break;
		}
		break;
	case "-";
		break;
	default:
		$diff = str_replace(",", "0,", $diff) . "0";
		$params[] = "starDifficulty IN ($diff) AND starAuto = '0' AND starDemon = '0'";
		break;
}
//Length filters
$len = $db->quote($len);
$len = str_replace("'","", $len);
if($len != "-"){
	$params[] = "levelLength IN ($len)";
}
//Type detection
if(!empty($_POST["str"])){
	$str = $ep->remove($_POST["str"]);
	$str = $db->quote($str);
	$str = str_replace("'","", $str);
}else{
	$str = "";
}
if(isset($_POST["page"]) && is_numeric($_POST["page"])){
	$page = $ep->remove($_POST["page"]);
}else{
	$page = 0;
}
$lvlpagea = $page * 10;
//Most liked
if(isset($type) AND $type == 0 OR $type == 15){
	$order = "likes DESC";
	if($str != ""){
		//Checking if is level ID or level name
		if(is_numeric($str)){
			$params = array("levelID = '$str'");
		}else{
			$params[] = "levelName LIKE '%$str%'";
		}
	}
}
//Downloads
if($type == 1){
	$order = "downloads DESC";
}
//Likes
if($type == 2){
	$order = "likes DESC";
}
//Trending
if($type == 3){
	$uploadDate = time() - (7 * 24 * 60 * 60);
	$params[] = "uploadDate > $uploadDate ";
	$order = "likes DESC";
}
//User levels
if($type == 5){
	$params[] = "userID = '$str'";
	if($_POST["local"] == 1){
		$params = array("userID = '$str'");
	}
}
//Featured
if($type == 6 OR $type == 17){
	$params[] = "NOT starFeatured = 0 OR NOT starEpic = 0";
	$order = "rateDate DESC, uploadDate DESC";
}
//Hall of Fame
if($type == 16){
	$params[] = "NOT starEpic = 0";
	$order = "rateDate DESC, uploadDate DESC";
}
//Magic
if($type == 7){
	$params[] = "objects > 9999";
}
//Map packs
if($type == 10){
	$order = false;
	$params[] = "levelID IN ($str)";
}
//Awarded
if($type == 11){
	$params[] = "NOT starStars = 0";
	$order = "rateDate DESC, uploadDate DESC";
}
//Followed
if($type == 12){
	$followed = $ep->remove($_POST["followed"]);
	$followed = $db->quote($followed);
	$followed = explode(")",$followed)[0];
	$followed = str_replace("'","", $followed);
	$params[] = "extID IN ($followed)";
}
//Friends
if($type == 13){
	$accountID = $ep->remove($_POST["accountID"]);
	//Checking GJP
	$gjp = $ep->remove($_POST["gjp"]);
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 1){
		$peoplearray = $gs->getFriends($accountID);
		$whereor = implode(",", $peoplearray);
		$params[] = "extID in ($whereor)";
	}
}
if(empty($order)){
	$order = "uploadDate DESC";
}
$querybase = "FROM levels";
if(!empty($params)){
	$querybase .= " WHERE (" . implode(" ) AND ( ", $params) . ")";
}
$query = "(SELECT * $querybase ) ";
if($order){
	$query .= "ORDER BY $order";
}
$query .= " LIMIT 10 OFFSET $lvlpagea";
$countquery = "SELECT count(*) $querybase";
//Getting levels
$query = $db->prepare($query);
$query->execute();
$countquery = $db->prepare($countquery);
$countquery->execute();
$totallvlcount = $countquery->fetchColumn();
$result = $query->fetchAll();
$levelcount = $query->rowCount();
foreach($result as &$level1){
	//Getting levels data
	if($level1["levelID"] != ""){
		$lvlsmultistring .= $level1["levelID"].",";
		if(!empty($gauntlet)){
			$lvlstring .= "44:$gauntlet:";
		}
		$lvlstring .= "1:".$level1["levelID"].":2:".$level1["levelName"].":5:".$level1["levelVersion"].":6:".$level1["userID"].":8:10:9:".$level1["starDifficulty"].":10:".$level1["downloads"].":12:".$level1["audioTrack"].":13:".$level1["gameVersion"].":14:".$level1["likes"].":17:".$level1["starDemon"].":43:".$level1["starDemonDiff"].":25:".$level1["starAuto"].":18:".$level1["starStars"].":19:".$level1["starFeatured"].":42:".$level1["starEpic"].":45:".$level1["objects"].":3:".$level1["levelDesc"].":15:".$level1["levelLength"].":30:".$level1["original"].":31:0:37:".$level1["coins"].":38:".$level1["starCoins"].":39:".$level1["requestedStars"].":46:1:47:2:40:".$level1["isLDM"].":35:".$level1["songID"]."|";
		if($level1["songID"] != 0){
			$song = $gs->getSongString($level1["songID"]);
			if($song){
				$songsstring .= $gs->getSongString($level1["songID"]) . "~:~";
			}
		}
		$userstring .= $gs->getUserString($level1["userID"])."|";
	}
}
$lvlstring = substr($lvlstring, 0, -1);
$lvlsmultistring = substr($lvlsmultistring, 0, -1);
$userstring = substr($userstring, 0, -1);
$songsstring = substr($songsstring, 0, -3);
//Printing levels
echo $lvlstring."#".$userstring;
echo "#".$songsstring;
echo "#".$totallvlcount.":".$lvlpagea.":10";
echo "#";
echo $hash->genMulti($lvlsmultistring);
?>