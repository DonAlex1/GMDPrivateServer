<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
//Getting data
$page = $ep->remove($_POST["page"]);
$artiststring = "";
$artpagea = $page*10;
//Getting artists
$query = "SELECT * FROM artists ORDER BY author ASC LIMIT 10 OFFSET $artpagea";
$query = $db->prepare($query);
$query->execute();
$result = $query->fetchAll();
if(count($result) < 1){
	//Nothing
	exit("-1");
}
//Count
$countquery = "SELECT count(*) FROM artists";
$countquery = $db->prepare($countquery);
$countquery->execute();
$artistcount = $countquery->fetchColumn();
foreach($result as &$artist){
	//Getting artists data
	$artiststring .= "4:".$artist["author"].":7:".$artist["YouTube"]."|";
}
//Printing artists
$artiststring = substr($artiststring, 0, -1);
echo $artiststring;
echo "#".$artistcount.":".$artpagea.":20";
?>