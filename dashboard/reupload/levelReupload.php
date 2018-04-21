<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
include "../../config/defaults.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/XORCipher.php";
$dl = new dashboardLib();
$xc = new XORCipher();
function chkarray($source){
	if($source == ""){
		$target = "0";
	}else{
		$target = $source;
	}
	return $target;
}
//Checking nothing's empty
if(!empty($_POST["id"])){
	//Getting form data
	$levelID = $_POST["id"];
	$levelID = preg_replace("/[^0-9]/", '', $levelID);
	$url = $_POST["server"];
	//Requesting level
	$post = ['gameVersion' => '21', 'binaryVersion' => '33', 'gdw' => '0', 'levelID' => $levelID, 'secret' => 'Wmfd2893gb7', 'inc' => '1', 'extras' => '0'];
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch);
	curl_close($ch);
	//Cheking result
	if($result == "" OR $result == "-1" OR $result == "No no no"){
		if($result == ""){
			//Printing error
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelToGDError")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}elseif($result == "-1"){
			//Printing error
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelReuploadError-3")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}
	}else{
		//Getting level data
		$level = explode('#', $result)[0];
		$resultarray = explode(':', $level);
		$levelarray = array();
		$x = 1;
		foreach($resultarray as &$value){
			if ($x % 2 == 0) {
				$levelarray["a$arname"] = $value;
			}else{
				$arname = $value;
			}
			$x++;
		}
		if($levelarray["a4"] == ""){
			//Printing error
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
							<p>".htmlspecialchars($result,ENT_QUOTES)."</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}
		$uploadDate = time();
		//Old levelString
		$levelString = chkarray($levelarray["a4"]);
		$gameVersion = chkarray($levelarray["a13"]);
		if(substr($levelString,0,2) == 'eJ'){
			$levelString = str_replace("_","/",$levelString);
			$levelString = str_replace("-","+",$levelString);
			$levelString = gzuncompress(base64_decode($levelString));
			if($gameVersion > 18){
				$gameVersion = 18;
			}
		}
		//Check if exists
		$query = $db->prepare("SELECT count(*) FROM levels WHERE originalReup = :lvl OR original = :lvl");
		$query->execute([':lvl' => $levelarray["a1"]]);
		if($query->fetchColumn() == 0){
			$parsedurl = parse_url($url);
			if($parsedurl["host"] == $_SERVER['SERVER_NAME']){
				//Printing error
				$errorDesc = $dl->getLocalizedString("levelReuploadError-2");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
			}
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$hostname = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$hostname = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$hostname = $_SERVER['REMOTE_ADDR'];
			}
			//Getting values
			$twoPlayer = chkarray($levelarray["a31"]);
			$songID = chkarray($levelarray["a35"]);
			$coins = chkarray($levelarray["a37"]);
			$reqstar = chkarray($levelarray["a39"]);
			$extraString = chkarray($levelarray["a36"]);
			$starStars = chkarray($levelarray["a18"]);
			$isLDM = chkarray($levelarray["a40"]);
			$password = chkarray($xc->cipher(base64_decode($levelarray["a27"]),26364));
			$starCoins = 0;
			$starDiff = 0;
			$starDemon = 0;
			$starAuto = 0;
			if($parsedurl["host"] == "www.boomlings.com"){
				if($starStars != 0){
					$starCoins = chkarray($levelarray["a38"]);
					$starDiff = chkarray($levelarray["a9"]);
					$starDemon = chkarray($levelarray["a17"]);
					$starAuto = chkarray($levelarray["a25"]);
				}
			}else{
				$starStars = 0;
			}
			$targetUserID = chkarray($levelarray["a6"]);
			//Checking if linked account
			$query = $db->prepare("SELECT accountID, userID FROM links WHERE targetUserID=:target");
			$query->execute([':target' => $targetUserID]);
			if($query->rowCount() == 0){
				$userID = $levelReuploadUserID;
				$extID = $levelReuploadExtID;
				$userName = $levelReuploadUsername;
			}else{
				$userInfo = $query->fetchAll()[0];
				$userID = $userInfo["userID"];
				$extID = $userInfo["accountID"];
				$query = $db->prepare("SELECT userName FROM users WHERE userID = :userID AND extID = :extID LIMIT 1");
				$query->execute([':userID' => $userID, ':extID' => $extID]);
				$userName = $query->fetchColumn();
			}
			//Reuploading level
			$query = $db->prepare("INSERT INTO levels (levelName, gameVersion, binaryVersion, userName, levelDesc, levelVersion, levelLength, audioTrack, auto, password, original, twoPlayer, songID, objects, coins, requestedStars, extraString, levelString, levelInfo, secret, uploadDate, updateDate, originalReup, userID, extID, unlisted, hostname, starStars, starCoins, starDifficulty, starDemon, starAuto, isLDM)
												VALUES (:name , :gameVersion, '27', :userName, :desc, :version, :length, :audiotrack, '0', :password, :originalReup, :twoPlayer, :songID, '0', :coins, :reqstar, :extraString, :levelString, '0', 'Wmfd2893gb7', '$uploadDate', '$uploadDate', :originalReup, :userID, :extID, '0', :hostname, :starStars, :starCoins, :starDifficulty, :starDemon, :starAuto, :isLDM)");
			$query->execute([':userName' => $userName, ':password' => $password, ':starDemon' => $starDemon, ':starAuto' => $starAuto, ':gameVersion' => $gameVersion, ':name' => $levelarray["a2"], ':desc' => $levelarray["a3"], ':version' => $levelarray["a5"], ':length' => $levelarray["a15"], ':audiotrack' => $levelarray["a12"], ':twoPlayer' => $twoPlayer, ':songID' => $songID, ':coins' => $coins, ':reqstar' => $reqstar, ':extraString' => $extraString, ':levelString' => "", ':originalReup' => $levelarray["a1"], ':hostname' => $hostname, ':starStars' => $starStars, ':starCoins' => $starCoins, ':starDifficulty' => $starDiff, ':userID' => $userID, ':extID' => $extID, ':isLDM' => $isLDM]);
			$levelID = $db->lastInsertId();
			file_put_contents("../../data/levels/$levelID",$levelString);
			//Printing box
			$dl->printBox("<h1>".$dl->getLocalizedString("levelReupload")."</h1>
							<p>".sprintf($dl->getLocalizedString("levelReuploaded"), $levelID)."</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("levelReuploadAnotherBTN")."</a>","reupload");
		}else{
			//Printing error
			$errorDesc = sprintf($dl->getLocalizedString("levelReuploadError-1"),$levelarray["a2"]);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}
	}
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="urlField" name="id" placeholder="'.$dl->getLocalizedString("levelReuploadIDFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="urlField" name="server" value="http://www.boomlings.com/database/downloadGJLevel22.php" placeholder="'.$dl->getLocalizedString("levelReuploadServerFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("reuploadBTN").'</button>
				</form>',"reupload");
}
?>