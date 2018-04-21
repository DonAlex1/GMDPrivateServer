<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
require "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
include "../../incl/lib/connection.php";
//Getting form data
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 10;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
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
$query = $db->prepare("SELECT levelID, levelName, starStars, coins FROM levels WHERE extID=:extID AND unlisted=1 ORDER BY levelID DESC LIMIT 10 OFFSET $page");
$query->execute([":extID" => $_SESSION["accountID"]]);
$result = $query->fetchAll();
foreach($result as &$level){
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
$query = $db->prepare("SELECT count(*) FROM levels WHERE extID=:extID AND unlisted=1");
$query->execute([':extID' => $_SESSION["accountID"]]);
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 10);
//Bottom row
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "account");
?>