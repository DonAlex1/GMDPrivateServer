<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/generateHash.php";
$generateHash = new generateHash();
//Getting data
$secret = $_POST["secret"];
if($secret != "Wmfd2893gb7"){
	//Error
	exit("-1");
}
$gauntletstring = "";
$string = "";
//Getting gauntlets
$query = $db->prepare("SELECT * FROM gauntlets WHERE level5 != '0' ORDER BY ID ASC");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$gauntlet){
	//Getting gauntlet data
	$lvls = $gauntlet["level1"].",".$gauntlet["level2"].",".$gauntlet["level3"].",".$gauntlet["level4"].",".$gauntlet["level5"];
	$gauntletstring .= "1:".$gauntlet["ID"].":3:".$lvls."|";
	$string .= $gauntlet["ID"].$lvls;
}
//Printing gauntlets
$gauntletstring = substr($gauntletstring, 0, -1);
echo $gauntletstring;
echo "#".$generateHash->genSolo2($string);
?>