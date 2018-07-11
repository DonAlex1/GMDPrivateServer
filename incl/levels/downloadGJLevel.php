<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require "../lib/XORCipher.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
require "../lib/generateHash.php";
$gs = new mainLib();
$ep = new exploitPatch();
$xor = new XORCipher();
$hash = new generateHash();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
if(empty($_POST["gameVersion"])){
	$gameVersion = 1;
}else{
	$gameVersion = $ep->remove($_POST["gameVersion"]);
}
if(empty($_POST["levelID"])) exit("-1");
//Getting IP
$ip = $gs->getIP();
$levelID = $ep->remove($_POST["levelID"]);
$feaID = 0;
//Checking if is numeric
if(!is_numeric($levelID)){
	//Error
	exit("-1");
}else{
	//Checking whatever is daily or weekly
	switch($levelID){
		case -1:
			//Daily
			$query = $db->prepare("SELECT * FROM dailyFeatures WHERE timestamp < :time AND type = 0 ORDER BY timestamp DESC LIMIT 1");
			$query->execute([':time' => time()]);
			$result = $query->fetch();
			$levelID = $result["levelID"];
			$feaID = $result["feaID"];
			$daily = 1;
			break;
		case -2:
			//Weekly
			$query = $db->prepare("SELECT * FROM dailyFeatures WHERE timestamp < :time AND type = 1 ORDER BY timestamp DESC LIMIT 1");
			$query->execute([':time' => time()]);
			$result = $query->fetch();
			$levelID = $result["levelID"];
			$feaID = $result["feaID"];
			$feaID = $feaID + 100001;
			$daily = 1;
			break;
		default:
			$daily = 0;
			break;
	}
	//Downloading level
	$query = $db->prepare("SELECT * FROM levels WHERE levelID = :levelID");
	$query->execute([':levelID' => $levelID]);
	$lvls = $query->rowCount();
	if($lvls != 0){
		$result = $query->fetch();
		//Adding download
		$query = $db->prepare("UPDATE levels SET downloads = downloads + 1 WHERE levelID = :levelID");
		$query->execute([':levelID' => $levelID]);
		//Getting time since uploaded
		$uploadDate = $gs->convertDate(date("Y-m-d H:i:s", $result["uploadDate"]));
		$updateDate = $gs->convertDate(date("Y-m-d H:i:s", $result["updateDate"]));
		//Password XOR
		$pass = $result["password"];
		$desc = $result["levelDesc"];
		//Checking if free copy
		if($gs->checkModIPPermission("actionFreeCopy") == 1) $pass = "1";
		$xorPass = base64_encode($xor->cipher($pass, 26364));
		//Checking level file
		if(file_exists("../../data/levels/$levelID")){
			$levelstring = file_get_contents("../../data/levels/$levelID");
		}else{
			$levelstring = $result["levelString"];
		}
		if(substr($levelstring, 0, 3) == 'kS1'){
			$levelstring = base64_encode(gzcompress($levelstring));
			$levelstring = str_replace("/", "_", $levelstring);
			$levelstring = str_replace("+", "-", $levelstring);
		}
		$response = "1:".$result["levelID"].":2:".$result["levelName"].":3:".$desc.":4:".$levelstring.":5:".$result["levelVersion"].":6:".$result["userID"].":8:10:9:".$result["starDifficulty"].":10:".$result["downloads"].":11:1:12:".$result["audioTrack"].":13:".$result["gameVersion"].":14:".$result["likes"].":17:".$result["starDemon"].":43:".$result["starDemonDiff"].":25:".$result["starAuto"].":18:".$result["starStars"].":19:".$result["starFeatured"].":42:".$result["starEpic"].":45:".$result["objects"].":15:".$result["levelLength"].":30:".$result["original"].":31:1:28:".$uploadDate. ":29:".$updateDate. ":35:".$result["songID"].":36:".$result["extraString"].":37:".$result["coins"].":38:".$result["starCoins"].":39:".$result["requestedStars"].":46:1:47:2:48:1:40:".$result["isLDM"].":27:$xorPass";
		//Checking if daily/weekly
		if($daily == 1) $response .= ":41:".$feaID;
		$response .= "#" . $hash->genSolo($levelstring) . "#";
		$somestring = $result["userID"].",".$result["starStars"].",".$result["starDemon"].",".$result["levelID"].",".$result["starCoins"].",".$result["starFeatured"].",".$pass.",".$feaID;
		$response .= $hash->genSolo2($somestring) . "#";
		if($daily == 1){
			$extID = $gs->getExtID($result["userID"]);
			if(!is_numeric($extID)) $extID = 0;
			$response .= $gs->getUserString($result["userID"]);
		}else{
			$response .= $somestring;
		}
		echo $response;
	}else{
		//Failure
		exit("-1");
	}
}
?>