<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
//Getting data
$type = $ep->remove($_POST["type"]);
$accountID = $ep->remove($_POST["accountID"]);
//Checking if banned
$query = $db->prepare("SELECT * FROM users WHERE isCreatorBanned = '0' AND creatorPoints > 0  ORDER BY creatorPoints DESC LIMIT 100");
$query->execute([':stars' => $stars, ':count' => $count]);
$creators = $query->fetchAll();
$pplstring;
$xi = 0;
foreach($creators as &$creator){
	//Checking if is numeric
	if(is_numeric($creator["extID"])){
		$extid = $creator["extID"];
	}else{
		$extid = 0;
	}
	$xi++;
	$pplstring .= "1:".$creator["userName"].":2:".$creator["userID"].":13:".$creator["coins"].":17:".$creator["userCoins"].":6:".$xi.":9:".$creator["icon"].":10:".$creator["color1"].":11:".$creator["color2"].":14:".$creator["iconType"].":15:".$creator["special"].":16:".$extid.":3:".$creator["stars"].":8:".round($creator["creatorPoints"], 0, PHP_ROUND_HALF_DOWN).":4:".$creator["demons"].":7:".$extid.":46:".$creator["diamonds"]."|";
}
//Printing top creators
$pplstring = substr($pplstring, 0, -1);
echo $pplstring;
?>