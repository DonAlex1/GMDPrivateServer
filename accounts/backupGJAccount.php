<?php
chdir(dirname(__FILE__));
//Setting limits
ini_set("memory_limit", "128M");
ini_set("post_max_size", "50M");
ini_set("upload_max_filesize", "50M");
//Requesting files
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/mainLib.php";
require_once "../incl/lib/exploitPatch.php";
$gs = new mainLib();
$ep = new exploitPatch();
$generatePass = new generatePass();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfv3899gc9") exit("-1");
$hostname = $gs->getIP();
$password = $_POST["password"];
$username = $ep->remove($_POST["userName"]);
$saveData = $ep->remove($_POST["saveData"]);
//Checking if banned
if($gs->isBanned($username, "account")) exit("-1");
if($gs->isBanned($hostname, "IP")) exit("-1");
//Checking username
if ($generatePass->isValidUsrname($username, $password)) {
	//Splitting CCGameManager and CCLocalLevels
	$saveDataArr = explode(";", $saveData);
	//Decoding
	$saveData = str_replace("-","+", $saveDataArr[0]);
	$saveData = str_replace("_","/", $saveData);
	$saveData = base64_decode($saveData);
	$saveData = gzdecode($saveData);
	//Replacing pass
	$saveData = str_replace("<k>GJA_002</k><s>".$password."</s>", "<k>GJA_002</k><s>ERROR_404</s>", $saveData);
	//Encoding back
	$saveData = gzencode($saveData);
	$saveData = base64_encode($saveData);
	$saveData = str_replace("+","-",$saveData);
	$saveData = str_replace("/","_",$saveData);
	//Merging CCGameManager and CCLocalLevels
	$saveData = $saveData . ";" . $saveDataArr[1];
	//Getting account data
	$query = $db->prepare("SELECT accountID FROM accounts WHERE username = :username");
	$query->execute([':username' => $username]);
	$accountID = $query->fetchColumn();
	//Checking if is numeric
	if(!is_numeric($accountID)) exit("-1");
	//Saving data
	file_put_contents("../data/accounts/$accountID", $saveData);
	echo "1";
}else{
	exit("-5");
}
?>