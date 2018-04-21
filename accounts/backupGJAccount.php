<?php
chdir(dirname(__FILE__));
//Setting limits
ini_set("memory_limit","128M");
ini_set("post_max_size","50M");
ini_set("upload_max_filesize","50M");
//Requesting files
include "../config/security.php";
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
include_once "../incl/lib/defuse-crypto.phar";
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
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
$userName = $ep->remove($_POST["userName"]);
$password = $_POST["password"];
$saveData = $ep->remove($_POST["saveData"]);
//Checking if banned
$isBanned = $db->prepare("SELECT isBanned FROM accounts WHERE userName = :userName LIMIT 1");
$isBanned->execute([':userName' => $userName]);
$isBanned = $isBanned->fetchColumn();
if($isBanned == 1){
	echo "-1";
}
$isBanned = $db->prepare("SELECT IP FROM bannedips WHERE IP = :hostname LIMIT 1");
$isBanned->execute([':hostname' => $hostname]);
$isBanned = $isBanned->fetchColumn();
if($isBanned == $hostname){
	echo "-1";
}
//Checking username
$generatePass = new generatePass();
$pass = $generatePass->isValidUsrname($userName, $password);
if ($pass == 1) {
	//Splitting CCGameManager and CCLocalLevels
	$saveDataArr = explode(";",$saveData);
	//Decoding
	$saveData = str_replace("-","+",$saveDataArr[0]);
	$saveData = str_replace("_","/",$saveData);
	$saveData = base64_decode($saveData);
	$saveData = gzdecode($saveData);
	$orbs = explode("</s><k>14</k><s>",$saveData)[1];
	$orbs = explode("</s>",$orbs)[0];
	$lvls = explode("<k>GS_value</k>",$saveData)[1];
	$lvls = explode("</s><k>4</k><s>",$lvls)[1];
	$lvls = explode("</s>",$lvls)[0];
	$protected_key_encoded = "";
	//Checking save encryption
	if($cloudSaveEncryption == 0){
		//Replacing pass
		$saveData = str_replace("<k>GJA_002</k><s>".$password."</s>", "<k>GJA_002</k><s>not the actual password</s>", $saveData);
		//Encoding back
		$saveData = gzencode($saveData);
		$saveData = base64_encode($saveData);
		$saveData = str_replace("+","-",$saveData);
		$saveData = str_replace("/","_",$saveData);
		//Merging CCGameManager and CCLocalLevels
		$saveData = $saveData . ";" . $saveDataArr[1];
	}else if($cloudSaveEncryption == 1){
		//Getting data
		$saveData = $ep->remove($_POST["saveData"]);
		//Creating key
		$protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
		$protected_key_encoded = $protected_key->saveToAsciiSafeString();
		$user_key = $protected_key->unlockKey($password);
		$saveData = Crypto::encrypt($saveData, $user_key);
	}
	//Getting account data
	$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName");
	$query->execute([':userName' => $userName]);
	$accountID = $query->fetchColumn();
	//Checking if is numeric
	if(!is_numeric($accountID)){
		echo "-1";
	}
	//Saving data
	file_put_contents("../data/accounts/$accountID",$saveData);
	file_put_contents("../data/accounts/keys/$accountID",$protected_key_encoded);
	//Getting user data
	$query = $db->prepare("SELECT extID FROM users WHERE userName = :userName LIMIT 1");
	$query->execute([':userName' => $userName]);
	$result = $query->fetchAll();
	$result = $result[0];
	$extID = $result["extID"];
	//Updating orbs and completed levels
	$query = $db->prepare("UPDATE `users` SET `orbs` = :orbs, `completedLvls` = :lvls WHERE extID = :extID");
	$query->execute([':orbs' => $orbs, ':extID' => $extID, ':lvls' => $lvls]);
	echo "1";
}else{
	echo "-5";
}
?>