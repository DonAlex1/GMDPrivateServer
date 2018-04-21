<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
//Getting data
$secret = $ep->remove($_POST["secret"]);
if($secret != "Wmfd2893gb7"){
	//Error
	exit("#0:0:0");
}
$commentstring = "";
$accountid = $ep->remove($_POST["accountID"]);
$page = $ep->remove($_POST["page"]);
$commentpage = $page*10;
//Getting user ID
$userID = $gs->getUserID($accountid);
//Getting account comments
$query = $db->prepare("SELECT * FROM acccomments WHERE userID = :userID AND secret = :secret ORDER BY timeStamp DESC LIMIT 10 OFFSET $commentpage");
$query->execute([':userID' => $userID, ':secret' => "Wmfd2893gb7"]);
$result = $query->fetchAll();
if($query->rowCount() == 0){
	//Nothing
	exit("#0:0:0");
}
$countquery = $db->prepare("SELECT count(*) FROM acccomments WHERE userID = :userID AND secret = :secret");
$countquery->execute([':userID' => $userID, ':secret' => "Wmfd2893gb7"]);
$commentcount = $countquery->fetchColumn();
foreach($result as &$comment1) {
	if($comment1["commentID"]!=""){
		$uploadDate = $gs->time_elapsed_string(date("Y-m-d H:i:s", $comment1["timestamp"]));
		$commentstring .= "2~".$comment1["comment"]."~3~".$comment1["userID"]."~4~".$comment1["likes"]."~5~0~7~".$comment1["isSpam"]."~9~".$uploadDate."~6~".$comment1["commentID"]."|";
	}
}
//Printing account comments
$commentstring = substr($commentstring, 0, -1);
echo $commentstring;
echo "#".$commentcount.":".$commentpage.":10";
?>