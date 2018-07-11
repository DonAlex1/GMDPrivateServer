<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
//Getting IP
$hostname = $gs->getIP();
//Checking nothing's empty
if($_POST["levelID"]){
	//Getting data
	$levelID =  $ep->remove($_POST["levelID"]);
	if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
	$query = $db->prepare("SELECT count(*) FROM reports WHERE levelID = :levelID AND hostname = :hostname");
	$query->execute([':levelID' => $levelID, ':hostname' => $hostname]) or die("-1");
	if($query->fetchColumn() == 0){
		//Checking if banned
		$query = $db->prepare("SELECT isReportingBanned FROM users WHERE hostname = :hostname LIMIT 1");
		$query->execute([':hostname' => $hostname]) or die("-1");
		if($query->fetchColumn() == 1) exit("-1");
		//Reporting
		$query = $db->prepare("INSERT INTO reports (levelID, hostname) VALUES (:levelID, :hostname)");	
		$query->execute([':levelID' => $levelID, ':hostname' => $hostname]) or die("-1");
		echo $db->lastInsertId();
	}else{
		//Error
		exit("-1");
	}	
}else{
	//Error
	exit("-1");
}
?>