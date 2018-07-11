<?php
//Requeting files
chdir(dirname(__FILE__));
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
$generatePass = new generatePass();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfv3899gc9") exit("-1");
$username = $ep->remove($_POST["userName"]);
$password = $_POST["password"];
//Checking username
if($generatePass->isValidUsrname($username, $password)){
	//Getting account data
	$query = $db->prepare("SELECT accountID FROM accounts WHERE username = :username");
	$query->execute([':username' => $username]);
	$account = $query->fetch();
	$accountID = $account["accountID"];
	//Checking if is numeric
	if(!is_numeric($accountID)) exit("-1");
	//Getting saved data
	$saveData = file_get_contents("../data/accounts/$accountID");
	//Loading saved data
	echo $saveData.";21;30;a;a";
}else{
	//Failure
	exit("-1");
}
?>