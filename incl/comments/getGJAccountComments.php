<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
//Checking secret
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("#0:0:0");
//Getting post data
$commentsString = "";
$page = $ep->remove($_POST["page"]);
$accountID = $ep->remove($_POST["accountID"]);
$commentPage = $page * 10;
//Getting account comments
$query = $db->prepare("SELECT * FROM accComments WHERE accountID = :accountID ORDER BY timestamp DESC LIMIT 10 OFFSET $commentPage");
$query->execute([':accountID' => $accountID]);
$comments = $query->fetchAll();
//Counting account comments
if(!$query->rowCount()) exit("#0:0:0");
$query = $db->prepare("SELECT count(*) FROM accComments WHERE accountID = :accountID");
$query->execute([':accountID' => $accountID]);
$commentCount = $query->fetchColumn();
//Fetching account comments
foreach($comments as &$comment) {
	if($comment["commentID"]){
		$uploadDate = $gs->convertDate(date("Y-m-d H:i:s", $comment["timestamp"]));
		$commentsString .= "2~".$comment["comment"]."~4~".$comment["likes"]."~9~".$uploadDate."~6~".$comment["commentID"]."|";
	}
}
//Printing account comments
$commentsString = substr($commentsString, 0, -1);
echo $commentsString;
echo "#".$commentCount.":".$commentPage.":10";
?>