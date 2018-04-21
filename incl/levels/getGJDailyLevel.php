<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
//Checking if daily or weekly
if(empty($_POST["weekly"]) OR $_POST["weekly"] == 0){
	$weekly = 0;
	$midnight = strtotime("tomorrow 00:00:00");
}else{
	$weekly = 1;
	$midnight = strtotime("next monday");
}
//Getting daily ID
$current = time();
$query=$db->prepare("SELECT feaID FROM dailyfeatures WHERE timestamp < :current AND type = :type ORDER BY timestamp DESC LIMIT 1");
$query->execute([':current' => $current, ':type' => $weekly]);
$dailyID = $query->fetchColumn();
if($weekly == 1){
	//Getting weekly ID
	$dailyID = $dailyID + 100001;
}
//Time left
$timeleft = $midnight - $current;
//Printing
echo $dailyID ."|". $timeleft;
?>
