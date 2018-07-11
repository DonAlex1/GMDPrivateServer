<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
//Getting data
$page = $ep->remove($_POST["page"]);
$artistsString;
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$artpagea = $page * 10;
//Getting artists
$query = $db->prepare("SELECT * FROM artists ORDER BY author ASC LIMIT 20 OFFSET $artpagea");
$query->execute();
$artists = $query->fetchAll();
if($query->rowCount() == 0) exit("-1");
//Count
$countquery = $db->prepare("SELECT count(*) FROM artists");
$countquery->execute();
$artistcount = $countquery->fetchColumn();
foreach($artists as &$artist){
	//Getting artists data
	$artistsString .= "4:".$artist["author"].":7:".$artist["YouTube"]."|";
}
//Printing artists
$artistsString = substr($artistsString, 0, -1);
echo $artistsString."#".$artistcount.":".$artpagea.":20";
?>