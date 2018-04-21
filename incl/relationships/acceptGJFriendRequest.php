<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Checking nothing's empty
if(empty($_POST["gjp"]) OR empty($_POST["requestID"]) OR empty($_POST["accountID"])){
	//Error
	exit("-1");
}
//GEtting data
$accountID = $ep->remove($_POST["accountID"]);
$requestID = $ep->remove($_POST["requestID"]);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
$gjpresult = $GJPCheck->check($gjp,$accountID);
if($gjpresult == 1){
	//Accepting for user 2
	$query = $db->prepare("SELECT accountID, toAccountID FROM friendreqs WHERE ID = :requestID");
	$query->execute([':requestID' => $requestID]);
	$request = $query->fetch();
	$reqAccountID = $request["accountID"];
	$toAccountID = $request["toAccountID"];
	if($toAccountID != $accountID){
		//Error
		exit("-1");
	}
	//Accepting
	$query = $db->prepare("INSERT INTO friendships (person1, person2, isNew1, isNew2)
	VALUES (:accountID, :targetAccountID, 1, 1)");
	$query->execute([':accountID' => $reqAccountID, ':targetAccountID' => $toAccountID]);
	//Removing request
	$query = $db->prepare("DELETE FROM friendreqs WHERE ID=:requestID LIMIT 1");
	$query->execute([':requestID' => $requestID]);
	echo "1";
}else{
	//Failure
	exit("-1");
}
?>