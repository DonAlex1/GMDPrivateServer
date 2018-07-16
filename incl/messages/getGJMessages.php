<?php
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$gs = new mainLib();
$GJPCheck = new GJPCheck();
$ep = new exploitPatch();
//Getting data
$msgstring = "";
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$accountID = $ep->remove($_POST["accountID"]);
$page = $ep->remove($_POST["page"]);
$offset = $page * 10;
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
if(!$GJPCheck->check($gjp, $accountID)) exit("-1");
//Checking if sent
if(!isset($_POST["getSent"]) || $_POST["getSent"] != 1){
	$query = "SELECT * FROM messages WHERE toAccountID = :accountID ORDER BY messageID DESC LIMIT 10 OFFSET $offset";
	$countquery = "SELECT count(*) FROM messages WHERE toAccountID = :accountID";
	$getSent = 0;
}else{
	$query = "SELECT * FROM messages WHERE accID = :accountID ORDER BY messageID DESC LIMIT 10 OFFSET $offset";
	$countquery = "SELECT count(*) FROM messages WHERE accID = :accountID";
	$getSent = 1;
}
$query = $db->prepare($query);
$query->execute([':accountID' => $accountID]);
$messages = $query->fetchAll();
$countquery = $db->prepare($countquery);
$countquery->execute([':accountID' => $accountID]);
$msgcount = $countquery->fetchColumn();
if(!$msgcount) exit("-2");
foreach ($messages as &$message) {
	//Getting message data
	if($message["messageID"]){
		$uploadDate = $gs->convertDate(date("Y-m-d H:i:s", $message["timestamp"]));
		if($getSent){
			$accountID = $message["toAccountID"];
		}else{
			$accountID = $message["accID"];
		}
		$query = $db->prepare("SELECT * FROM users WHERE extID = :accountID");
		$query->execute([':accountID' => $accountID]);
		$user = $query->fetchAll()[0];
		$msgstring .= "6:".$user["userName"].":3:".$user["userID"].":2:".$user["extID"].":1:".$message["messageID"].":4:".$message["subject"].":8:".$message["isNew"].":9:".$getSent.":7:".$uploadDate."|";
	}
}
//Printing messages
$msgstring = substr($msgstring, 0, -1);
echo $msgstring ."#".$msgcount.":".$offset.":10";
?>