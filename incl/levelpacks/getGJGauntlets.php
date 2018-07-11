<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/generateHash.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
$generateHash = new generateHash();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7"){
	//Error
	exit("-1");
}
$string;
$gauntletsString;
//Getting gauntlets
$query = $db->prepare("SELECT * FROM gauntlets WHERE levels != '' ORDER BY ID ASC");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$gauntlet){
	//Getting gauntlet data
	$lvls = $gauntlet["levels"];
	$gauntletsString .= "1:".$gauntlet["ID"].":3:".$lvls."|";
	$string .= $gauntlet["ID"].$lvls;
}
//Printing gauntlets
$gauntletsString = substr($gauntletsString, 0, -1);
echo $gauntletsString;
echo "#".$generateHash->genSolo2($string);
?>