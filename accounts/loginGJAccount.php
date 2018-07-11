<?php
//Requesting files
include "../incl/lib/connection.php";
include "../config/email.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
require_once "../incl/lib/mainLib.php";
$gs = new mainLib();
$ep = new exploitPatch();
$generatePass = new generatePass();
//Getting IP
$hostname = $gs->getIP();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfv3899gc9") exit("-1");
$udid = $ep->remove($_POST["udid"]);
$username = $ep->remove($_POST["userName"]);
$password = $_POST["password"];
//Registering
$query = $db->prepare("SELECT accountID FROM accounts WHERE username LIKE :username");
$query->execute([':username' => $username]);
if(!$query->rowCount()) exit("-1");
$account = $query->fetch();
//Authenticating
if($generatePass->isValidUsrname($username, $password)){
	//Getting account ID
	$accountID = $account["accountID"];
	//Checking if active
	if($emailEnabled == 1){
		$query = $db->prepare("SELECT active FROM accounts WHERE accountID = :accID LIMIT 1");
		$query->execute([':accID' => $accountID]);
		if($query->fetchColumn() == 0) exit("-1");
	}
	//Checking if banned
	if($gs->isBanned($accountID, "account")) exit("-12");
	if($gs->isBanned($hostname, "IP")) exit("-12");
	//Getting user ID
	$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :id LIMIT 1");
	$query2->execute([':id' => $accountID]);
	if($query2->rowCount() > 0){
		$userID = $query2->fetchColumn();
	}else{
		//Registering
		$query = $db->prepare("INSERT INTO users (isRegistered, extID, userName) VALUES (1, :id, :username)");
		$query->execute([':id' => $accountID, ':username' => $username]);
		$userID = $db->lastInsertId();
	}
	//Result
	echo $accountID.",".$userID;
	if(!is_numeric($udid)){
		$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :udid LIMIT 1");
		$query2->execute([':udid' => $udid]);
		$usrid2 = $query2->fetchColumn();
		$query2 = $db->prepare("UPDATE levels SET userID = :userID, extID = :extID WHERE userID = :usrid2");
		$query2->execute([':userID' => $userID, ':extID' => $accountID, ':usrid2' => $usrid2]);	
	}
}else{
	//Failed
	exit("-1");
}
?>