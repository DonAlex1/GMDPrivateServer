<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/mainLib.php";
$gs = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
//Getting data
$reqstring = "";
if(!empty($_POST["getSent"])){
	$getSent = $ep->remove($_POST["getSent"]);
}else{
	$getSent = 0;
}
//Checking nothing's empty
if(empty($_POST["accountID"]) || (!isset($_POST["page"]) || !is_numeric($_POST["page"])) || empty($_POST["gjp"])) exit("-1");
$accountID = $ep->remove($_POST["accountID"]);
$page = $ep->remove($_POST["page"]);
//Checking GJP
$gjp = $ep->remove($_POST["gjp"]);
if(!$GJPCheck->check($gjp, $accountID)) exit("-1");
$offset = $page * 10;
if(!$getSent){
	$query = "SELECT accountID, toAccountID, uploadDate, ID, comment, isNew FROM friendreqs WHERE toAccountID = :accountID LIMIT 10 OFFSET $offset";
	$countquery = "SELECT count(*) FROM friendreqs WHERE toAccountID = :accountID";
}elseif($getSent){
	$query = "SELECT * FROM friendreqs WHERE accountID = :accountID LIMIT 10 OFFSET $offset";
	$countquery = "SELECT count(*) FROM friendreqs WHERE accountID = :accountID";
}
$query = $db->prepare($query);
$query->execute([':accountID' => $accountID]);
$result = $query->fetchAll();
$countquery = $db->prepare($countquery);
$countquery->execute([':accountID' => $accountID]);
$reqcount = $countquery->fetchColumn();
if(!$reqcount) exit("-2");
foreach($result as &$request) {
	if(!$getSent){
		$requester = $request["accountID"];
	}elseif($getSent){
		$requester = $request["toAccountID"];
	}
	$query = "SELECT userName, userID, icon, color1, color2, iconType, special, extID FROM users WHERE extID = :requester";
	$query = $db->prepare($query);
	$query->execute([':requester' => $requester]);
	$result2 = $query->fetchAll();
	$user = $result2[0];
	$uploadTime = $gs->convertDate(date("Y-m-d H:i:s", $request["uploadDate"]));
	if(is_numeric($user["extID"])){
		$extid = $user["extID"];
	}else{
		$extid = 0;
	}
	$reqstring .= "1:".$user["userName"].":2:".$user["userID"].":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$extid.":32:".$request["ID"].":35:".$request["comment"].":41:".$request["isNew"].":37:".$uploadTime."|";

}
//Printing requests
$reqstring = substr($reqstring, 0, -1);
echo $reqstring;
echo "#".$reqcount.":".$offset.":10";
?>