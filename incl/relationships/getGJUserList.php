<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Checking nothing's empty
if(empty($_POST["accountID"]) || empty($_POST["gjp"]) || (!isset($_POST["type"]) || !is_numeric($_POST["type"]))) exit("-1");
//Getting data
$accountID = $ep->remove($_POST["accountID"]);
$type = $ep->remove($_POST["type"]);
$people = "";
$peoplestring = "";
$new = array();
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
if(!$GJPCheck->check($gjp,$accountID)) exit("-1");
//Detecting type
if(!$type){
	$query = "SELECT * FROM friendships WHERE person1 = :accountID OR person2 = :accountID";
}elseif($type){
	$query = "SELECT * FROM blocks WHERE person1 = :accountID";
}
$query = $db->prepare($query);
$query->execute([':accountID' => $accountID]);
$result = $query->fetchAll();
if(!$query->rowCount()){
	//Nothing
	exit("-2");
}else{
	foreach ($result as &$friendship) {
		$person = $friendship["person1"];
		$isnew = $friendship["isNew1"];
		if($friendship["person1"] == $accountID){
			$person = $friendship["person2"];
			$isnew = $friendship["isNew2"];
		}
		$new[$person] = $isnew;
		$people .= $person . ",";
	}
	$people = substr($people, 0, -1);
	//Getting users
	$query = $db->prepare("SELECT userName, userID, icon, color1, color2, iconType, special, extID FROM users WHERE extID IN ($people) ORDER BY userName ASC");
	$query->execute();
	$result = $query->fetchAll();
	foreach($result as &$user){
		$peoplestring .= "1:".$user["userName"].":2:".$user["userID"].":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$user["extID"].":18:0:41:".$new[$user["extID"]]."|";
	}
	$peoplestring = substr($peoplestring, 0, -1);
	$query = $db->prepare("UPDATE friendships SET isNew1 = '0' WHERE person2 = :me");
	$query->execute([':me' => $accountID]);
	$query = $db->prepare("UPDATE friendships SET isNew2 = '0' WHERE person1 = :me");
	$query->execute([':me' => $accountID]);
	if(!$peoplestring) exit("-1");
	//Printing users
	echo $peoplestring;
}
?>