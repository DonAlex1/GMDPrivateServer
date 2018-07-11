<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
//Checking if daily or weekly
if(empty($_POST["weekly"]) || $_POST["weekly"] == 0){
	$weekly = 0;
	$midnight = strtotime("tomorrow 00:00:00");
}else{
	$weekly = 1;
	$midnight = strtotime("next monday");
}
//Getting daily ID
$query = $db->prepare("SELECT feaID FROM dailyFeatures WHERE timestamp < :current AND type = :type ORDER BY timestamp DESC LIMIT 1");
$query->execute([':current' => time(), ':type' => $weekly]);
$dailyID = $query->fetchColumn();
//Getting weekly ID
if($weekly == 1) $dailyID = $dailyID + 100001;
//Time left
$timeleft = $midnight - time();
//Printing
echo $dailyID ."|". $timeleft;
?>
