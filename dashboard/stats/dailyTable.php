<?php
//Check if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Request files
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
require "../../incl/lib/connection.php";
//Generating daily table
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 10;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$dailytable = "";
//Getting data
$query = $db->prepare("SELECT feaID, levelID, timestamp FROM dailyfeatures WHERE timestamp < :time ORDER BY feaID DESC LIMIT 10 OFFSET $page");
$query->execute([':time' => time()]);
$result = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE timestamp < :time");
$query->execute([':time' => time()]);
$dailycount = $query->fetchColumn();
$x = $dailycount - $page;
//Printing data
foreach($result as &$daily){
	//Getting level data
	$query = $db->prepare("SELECT levelName,userID,starStars,coins FROM levels WHERE levelID = :levelID");
	$query->execute([':levelID' => $daily["levelID"]]);
	$level = $query->fetch();
	if($query->rowCount() == 0){
		$level["levelName"] = $dl->getLocalizedString("deletedLevel");
		$level["userID"] = 0;
		$level["starStars"] = -1;
		$level["coins"] = -1;
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
$pagecount = ceil($dailycount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
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
.$bottomrow, true, "stats");
?>