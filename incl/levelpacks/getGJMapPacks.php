<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require "../lib/generateHash.php";
$hash = new generateHash();
$ep = new exploitPatch();
//Getting data
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	exit("-1");
}
$page = $ep->remove($_POST["page"]);
$packpage = $page*10;
$mappackstring = "";
$lvlsmultistring = "";
//Getting map packs
$query = $db->prepare("SELECT * FROM mappacks ORDER BY difficulty ASC LIMIT 10 OFFSET $packpage");
$query->execute();
$result = $query->fetchAll();
$packcount = $query->rowCount();
foreach($result as &$mappack){
	//Getting map pack data
	$lvlsmultistring .= $mappack["ID"] . ",";
	$colors2 = $mappack["colors2"];
	if($colors2 == "none" OR $colors2 == ""){
		$colors2 = $mappack["rgbcolors"];
	}
	$mappackstring .= "1:".$mappack["ID"].":2:".$mappack["name"].":3:".$mappack["levels"].":4:".$mappack["stars"].":5:".$mappack["coins"].":6:".$mappack["difficulty"].":7:".$mappack["rgbcolors"].":8:".$colors2."|";
}
//Count
$query = $db->prepare("SELECT count(*) FROM mappacks");
$query->execute();
$totalpackcount = $query->fetchColumn();
//Printing map packs
$mappackstring = substr($mappackstring, 0, -1);
$lvlsmultistring = substr($lvlsmultistring, 0, -1);
echo $mappackstring;
echo "#".$totalpackcount.":".$packpage.":10";
echo "#";
echo $hash->genPack($lvlsmultistring);
?>