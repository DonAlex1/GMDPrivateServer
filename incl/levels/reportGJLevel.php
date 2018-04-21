<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$GJPCheck = new GJPCheck();
$ep = new exploitPatch();
//Getting IP
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
}
//Checking nothing's empty
if($_POST["levelID"]){
	//Getting data
	$levelID =  $ep->remove($_POST["levelID"]);
	$query = "SELECT count(*) FROM reports WHERE levelID = :levelID AND hostname = :hostname";
	$query = $db->prepare($query);
	$query->execute([':levelID' => $levelID, ':hostname' => $ip]);
	if($query->fetchColumn() == 0){
		//Checking if registered
		if(!empty($_POST["accountID"]) AND $_POST["accountID"]!="0"){
			$accountID = $ep->remove($_POST["accountID"]);
			$register = 1;
			//Checking GJP
			$gjpresult = $GJPCheck->check($gjp,$accountID);
			if($gjpresult == 0){
				//Error
				exit("-1");
			}
		}else{
			$accountID = $ep->remove($_POST["udid"]);
			$register = 0;
			//Checking if is numeric
			if(is_numeric($accountID)){
				//Error
				exit("-1");
			}
		}
		//Checking if banned
		$query3 = $db->prepare("SELECT isReportingBanned FROM users WHERE extID = :accountID");
		$query3->execute([':accountID' => $accountID]);
		$result2 = $query3->fetchColumn();
		if($result2 == 1){
			//Banned
			exit("-1");
		}
		//Reporting
		$query = $db->prepare("INSERT INTO reports (levelID, hostname) VALUES (:levelID, :hostname)");	
		$query->execute([':levelID' => $levelID, ':hostname' => $ip]);
		echo $db->lastInsertId();
	}else{
		//Failure
		exit("-1");
	}	
}
?>