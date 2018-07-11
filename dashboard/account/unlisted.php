<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) || !$_SESSION["accountID"]) exit(header("Location: ../login/login.php"));
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/exploitPatch.php";
$gs = new mainLib();
$dl = new dashboardLib();
$ep = new exploitPatch();
//Getting form data
if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0){
	$page = ($ep->remove($_GET["page"]) - 1) * 10;
	$actualPage = $ep->remove($_GET["page"]);
}else{
	$page = 0;
	$actualPage = 1;
}
//Generating unlisted table
$table = '<table class="table table-inverse">
			<thead>
				<tr>
					<th>'.$dl->getLocalizedString("ID").'</th>
					<th>'.$dl->getLocalizedString("name").'</th>
					<th>'.$dl->getLocalizedString("stars").'</th>
					<th>'.$dl->getLocalizedString("userCoins").'</th>
				</tr>
			</thead>
			<tbody>';
//Getting unlisted level
$query = $db->prepare("SELECT levelID, levelName, starStars, coins FROM levels WHERE extID = :extID AND unlisted = 1 ORDER BY levelID DESC LIMIT 10 OFFSET $page");
$query->execute([":extID" => $_SESSION["accountID"]]);
$levels = $query->fetchAll();
foreach($levels as &$level){
	//Getting level data
	$table .= "<tr>
				<td>".$level["levelID"]."</td>
				<td>".$level["levelName"]."</td>
				<td>".$level["starStars"]."</td>
				<td>".$level["coins"]."</td>
			</tr>";
}
$table .= "</tbody></table>";
//Getting count
$query = $db->prepare("SELECT count(*) FROM levels WHERE extID = :extID AND unlisted = 1");
$query->execute([':extID' => $_SESSION["accountID"]]);
$unlistedCount = $query->fetchColumn();
$pageCount = ceil($unlistedCount / 10);
//Bottom row
$bottomRow = $dl->generateBottomRow($pageCount, $actualPage);
$dl->printPage($table . $bottomRow, true, "account");
?>