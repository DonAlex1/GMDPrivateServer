<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
$gs = new mainLib();
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$extid = $ep->remove($_POST["targetAccountID"]);
if(!empty($_POST["accountID"])){
	$me = $ep->remove($_POST["accountID"]);
	//Checking GJP
	$gjpresult = $GJPCheck->check($gjp,$me);
	if($gjpresult != 1){
		//Error
		exit("-1");
	}
}else{
	$me = 0;
}
//Checking who blocked it
$query = $db->prepare("SELECT count(*) FROM blocks WHERE (person1 = :extid AND person2 = :me) OR (person2 = :extid AND person1 = :me)");
$query->execute([':extid' => $extid, ':me' => $me]);
if($query->fetchColumn() > 0){
	//Blocked
	exit("-1");
}
$query = $db->prepare("SELECT * FROM users WHERE extID = :extid");
$query->execute([':extid' => $extid]);
$user = $query->fetch();
//Placeholders
$creatorpoints = round($user["creatorPoints"], PHP_ROUND_HALF_DOWN);
//Global rank
$e = "SET @rownum := 0;";
$query = $db->prepare($e);
$query->execute();
$f = "SELECT rank FROM (
                SELECT @rownum := @rownum + 1 AS rank, extID
                FROM users WHERE isBanned = '0' AND gameVersion > 19 AND stars > 25 ORDER BY stars DESC
                ) as result WHERE extID=:extid";
$query = $db->prepare($f);
$query->execute([':extid' => $extid]);
$rank = $query->fetchColumn();
		$query = $db->prepare("SELECT youtubeurl,twitter,twitch, frS, mS, cS FROM accounts WHERE accountID = :extID");
		$query->execute([':extID' => $extid]);
		$accinfo = $query->fetch();
		$reqsstate = $accinfo["frS"];
		$msgstate = $accinfo["mS"];
		$commentstate = $accinfo["cS"];
		$badge = $gs->getMaxValuePermission($extid, "modBadgeLevel");
if($me == $extid){
	//Notifications
		//Friend requests
		$query = $db->prepare("SELECT count(*) FROM friendreqs WHERE toAccountID = :me");
		$query->execute([':me' => $me]);
		$requests = $query->fetchColumn();
		//Messages
		$query = $db->prepare("SELECT count(*) FROM messages WHERE toAccountID = :me AND isNew = 0");
		$query->execute([':me' => $me]);
		$pms = $query->fetchColumn();
		//Friends
		$query = $db->prepare("SELECT count(*) FROM friendships WHERE (person1 = :me AND isNew2 = '1') OR  (person2 = :me AND isNew1 = '1')");
		$query->execute([':me' => $me]);
		$friends = $query->fetchColumn();
		//Sending data
		echo "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":10:".$user["color1"].":11:".$user["color2"].":3:".$user["stars"].":46:".$user["diamonds"].":4:".$user["demons"].":8:".$creatorpoints.":18:".$msgstate.":19:".$reqsstate.":50:".$commentstate.":20:".$accinfo["youtubeurl"].":21:".$user["accIcon"].":22:".$user["accShip"].":23:".$user["accBall"].":24:".$user["accBird"].":25:".$user["accDart"].":26:".$user["accRobot"].":28:".$user["accGlow"].":43:".$user["accSpider"].":47:".$user["accExplosion"].":30:".$rank.":16:".$user["extID"].":31:0:44:".$accinfo["twitter"].":45:".$accinfo["twitch"].":38:".$pms.":39:".$requests.":40:".$friends.":29:1:49:".$badge;
	}else{
		//Friend state
		$friendstate = 0;
		//Check if incoming friend request
		$query = $db->prepare("SELECT * FROM friendreqs WHERE accountID = :extid AND toAccountID = :me");
		$query->execute([':extid' => $extid, ':me' => $me]);
		$INCrequests = $query->rowCount();
		$INCrequestinfo = $query->fetch();
		$uploaddate = $gs->time_elapsed_string(date("Y-m-d H:i:s", $INCrequestinfo["uploadDate"]));
		if($INCrequests > 0){
			$friendstate = 3;
		}
		//Check if outcoming friend request
			$query = $db->prepare("SELECT count(*) FROM friendreqs WHERE toAccountID = :extid AND accountID = :me");
			$query->execute([':extid' => $extid, ':me' => $me]);
			$OUTrequests = $query->fetchColumn();
			if($OUTrequests > 0){
				$friendstate = 4;
			}
		//Check if is already friend
			$query = $db->prepare("SELECT count(*) FROM friendships WHERE (person1 = :me AND person2 = :extID) OR (person2 = :me AND person1 = :extID)");
			$query->execute([':me' => $me, ':extID' => $extid]);
			$frs = $query->fetchColumn();
			if($frs > 0){
				$friendstate = 1;
			}
		//Sending data
		echo "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":10:".$user["color1"].":11:".$user["color2"].":3:".$user["stars"].":46:".$user["diamonds"].":4:".$user["demons"].":8:".$creatorpoints.":18:0:19:".$reqsstate.":50:".$commentstate.":20:".$accinfo["youtubeurl"].":21:".$user["accIcon"].":22:".$user["accShip"].":23:".$user["accBall"].":24:".$user["accBird"].":25:".$user["accDart"].":26:".$user["accRobot"].":28:".$user["accGlow"].":43:".$user["accSpider"].":47:".$user["accExplosion"].":30:".$rank.":16:".$user["extID"].":31:".$friendstate.":44:".$accinfo["twitter"].":45:".$accinfo["twitch"].":29:1:49:".$badge;
		if($INCrequests > 0){
			echo ":32:".$INCrequestinfo["ID"].":35:".$INCrequestinfo["comment"].":37:".$uploaddate;
		}
	}
?>