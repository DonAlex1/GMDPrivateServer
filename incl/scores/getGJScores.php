<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$stars = 0;
$count = 0;
$xi = 0;
$lbstring;
$type = $ep->remove($_POST["type"]);
if(empty($_POST["gameVersion"])){
	$sign = "< 20 AND gameVersion <> 0";
}else{
	$sign = "> 19";
}
if(!empty($_POST["accountID"])){
	$accountID = $ep->remove($_POST["accountID"]);
	//Checking GJP
	$gjp = $ep->remove($_POST["gjp"]);
	if(!$GJPCheck->check($gjp, $accountID)) exit("-1");
}else{
	$accountID = $ep->remove($_POST["udid"]);
	//Checking if is numeric
	if(is_numeric($accountID)) exit("-1");
}
//Detecting top type
if($type == "top" || $type == "creators" || $type == "relative"){
	if($type == "top"){
		$query = "SELECT * FROM users WHERE isBanned = '0' AND gameVersion $sign AND stars > 0 ORDER BY stars DESC LIMIT 100";
	}
	if($type == "creators"){
		$query = "SELECT * FROM users WHERE isCreatorBanned = '0' AND creatorPoints > 0 ORDER BY creatorPoints DESC LIMIT 100";
	}
	//Top global
	if($type == "relative"){
		$query = $db->prepare("SELECT * FROM users WHERE extID = :accountID");
		$query->execute([':accountID' => $accountID]);
		$result = $query->fetchAll();
		$user = $result[0];
		$stars = $user["stars"];
		if($_POST["count"]){
			$count = $ep->remove($_POST["count"]);
		}else{
			$count = 50;
		}
		$count = floor($count / 2);
		$query = "SELECT	A.* FROM	(
			(
				SELECT	*	FROM users
				WHERE stars <= :stars
				AND isBanned = 0
				AND gameVersion $sign
				ORDER BY stars DESC
				LIMIT $count
			)
			UNION
			(
				SELECT * FROM users
				WHERE stars >= :stars
				AND isBanned = 0
				AND gameVersion $sign
				ORDER BY stars ASC
				LIMIT $count
			)
		) as A
		ORDER BY A.stars DESC";
	}
	$query = $db->prepare($query);
	$query->execute([':stars' => $stars, ':count' => $count]);
	$result = $query->fetchAll();
	if($type == "relative"){
		$user = $result[0];
		$extid = $user["extID"];
		$e = "SET @rownum := 0;";
		$query = $db->prepare($e);
		$query->execute();
		$f = "SELECT rank, stars FROM (
							SELECT @rownum := @rownum + 1 AS rank, stars, extID, isBanned
							FROM users WHERE isBanned = '0' AND gameVersion $sign ORDER BY stars DESC
							) as result WHERE extID=:extid";
		$query = $db->prepare($f);
		$query->execute([':extid' => $extid]);
		$leaderboard = $query->fetchAll();
		$leaderboard = $leaderboard[0];
		$xi = $leaderboard["rank"] - 1;
	}
	foreach($result as &$user) {
		$extid = 0;
		if(is_numeric($user["extID"])){
			$extid = $user["extID"];
		}
		$xi++;
		$lbstring .= "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":6:".$xi.":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$extid.":3:".$user["stars"].":8:".round($user["creatorPoints"],0,PHP_ROUND_HALF_DOWN).":4:".$user["demons"].":7:".$extid.":46:".$user["diamonds"]."|";
	}
}
//Top friends
if($type == "friends"){
	$query = $db->prepare("SELECT * FROM friendships WHERE person1 = :accountID OR person2 = :accountID");
	$query->execute([':accountID' => $accountID]);
	$result = $query->fetchAll();
	$people = "";
	foreach ($result as &$friendship) {
		$person = $friendship["person1"];
		if($friendship["person1"] == $accountID){
			$person = $friendship["person2"];
		}
		$people .= ",".$person;
	}
	$query = $db->prepare("SELECT * FROM users WHERE extID IN (:accountID $people ) ORDER BY stars DESC");
	$query->execute([':accountID' => $accountID]);
	$result = $query->fetchAll();
	foreach($result as &$user){
		if(is_numeric($user["extID"])){
			$extid = $user["extID"];
		}else{
			$extid = 0;
		}
		$xi++;
		$lbstring .= "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":6:".$xi.":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$extid.":3:".$user["stars"].":8:".round($user["creatorPoints"],0,PHP_ROUND_HALF_DOWN).":4:".$user["demons"].":7:".$extid.":46:".$user["diamonds"]."|";
	}
}
if(!$lbstring) exit("-1");
//Printing top
$lbstring = substr($lbstring, 0, -1);
echo $lbstring;
?>