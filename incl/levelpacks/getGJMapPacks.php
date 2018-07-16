<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require "../lib/generateHash.php";
$hash = new generateHash();
$ep = new exploitPatch();
//Getting data
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$page = $ep->remove($_POST["page"]);
$packPage = $page * 10;
$mapPacksString = "";
$lvlsmultistring = "";
//Getting map packs
$query = $db->prepare("SELECT * FROM mapPacks ORDER BY difficulty ASC LIMIT 10 OFFSET $packPage");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$mapPack){
	//Getting map pack data
	$lvlsmultistring .= $mapPack["ID"] . ",";
	$colors2 = $mapPack["colors2"];
	if($colors2 == "none" || !$colors2) $colors2 = $mapPack["rgbcolors"];
	$mapPacksString .= "1:".$mapPack["ID"].":2:".$mapPack["name"].":3:".$mapPack["levels"].":4:".$mapPack["stars"].":5:".$mapPack["coins"].":6:".$mapPack["difficulty"].":7:".$mapPack["rgbcolors"].":8:".$colors2."|";
}
//Count
$query = $db->prepare("SELECT count(*) FROM mapPacks");
$query->execute();
$totalPackCount = $query->fetchColumn();
if(!$totalPackCount) exit("-1");
//Printing map packs
$mapPacksString = substr($mapPacksString, 0, -1);
$lvlsmultistring = substr($lvlsmultistring, 0, -1);
echo $mapPacksString;
echo "#".$totalPackCount.":".$packPage.":10";
echo "#";
echo $hash->genPack($lvlsmultistring);
?>