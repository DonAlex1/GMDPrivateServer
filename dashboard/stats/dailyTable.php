<?php
//Check if logged in
session_start();
if(!isset($_SESSION["accountID"]) || !$_SESSION["accountID"]) exit(header("Location: ../login/login.php"));
//Request files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/exploitPatch.php";
$gs = new mainLib();
$dl = new dashboardLib();
$ep = new exploitPatch();
//Generating daily table
if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0){
	$page = ($ep->remove($_GET["page"]) - 1) * 10;
	$actualPage = $ep->remove($_GET["page"]);
}else{
	$page = 0;
	$actualPage = 1;
}
$dailytable;
//Getting data
$query = $db->prepare("SELECT feaID, levelID, timestamp FROM dailyFeatures WHERE timestamp < :time ORDER BY feaID DESC LIMIT 10 OFFSET $page");
$query->execute([':time' => time()]);
$dailies = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM dailyFeatures WHERE timestamp < :time");
$query->execute([':time' => time()]);
$dailyCount = $query->fetchColumn();
$x = $dailyCount - $page;
//Printing data
foreach($dailies as &$daily){
	//Getting level data
	$query = $db->prepare("SELECT levelName, userID, starStars, coins FROM levels WHERE levelID = :levelID");
	$query->execute([':levelID' => $daily["levelID"]]);
	$level = $query->fetch();
	if(!$query->rowCount()){
		$level["coins"] = 0;
		$level["userID"] = 0;
		$level["starStars"] = 0;
		$level["levelName"] = $dl->getLocalizedString("deletedLevel");
	}
	$dailytable .= '<tr>
					<th scope="row">'.$x.'</th>
					<td>'.$daily["levelID"].'</th>
					<td>'.$level["levelName"].'</td>
					<td>'.$gs->getUserName($level["userID"]).'</td>
					<td>'.$level["starStars"].'</td>
					<td>'.$level["coins"].'</td>
					<td>'.date("d/m/Y G:i:s", $daily["timestamp"]).'</td>
				</tr>';
	$x--;
	echo "</td></tr>";
}
//Bottom row
$pageCount = ceil($dailyCount / 10);
$bottomRow = $dl->generateBottomRow($pageCount, $actualPage);
//Printing table
$dl->printPage('<table class="table table-inverse">
	<thead>
		<tr>
			<th>#</th>
			<th>'.$dl->getLocalizedString("ID").'</th>
			<th>'.$dl->getLocalizedString("name").'</th>
			<th>'.$dl->getLocalizedString("author").'</th>
			<th>'.$dl->getLocalizedString("stars").'</th>
			<th>'.$dl->getLocalizedString("userCoins").'</th>
			<th>'.$dl->getLocalizedString("time").'</th>
		</tr>
	</thead>
	<tbody>
		'.$dailytable.'
	</tbody>
</table>'
.$bottomRow, true, "stats");
?>