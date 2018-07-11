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
//Generating levels table
if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0){
	$page = ($ep->remove($_GET["page"]) - 1) * 10;
	$actualPage = $ep->remove($_GET["page"]);
}else{
	$page = 0;
	$actualPage = 1;
}
$leveltable = "";
//Getting data
$query = $db->prepare("SELECT * FROM levels ORDER BY uploadDate DESC LIMIT 10 OFFSET $page");
$query->execute();
$levels = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM levels");
$query->execute();
$levelCount = $query->fetchColumn();
$x = $levelCount - $page;
//Printing data
foreach($levels as &$level){
	$leveltable .= '<tr>
					<th scope="row">'.$x.'</th>
					<td>'.$level["levelID"].'</th>
					<td>'.$level["levelName"].'</td>
					<td>'.$gs->getUserName($level["userID"]).'</td>
					<td>'.$level["starStars"].'</td>
					<td>'.$level["coins"].'</td>
					<td>'.$dl->convertToDate($level["uploadDate"]).' ago</td>
				</tr>';
	$x--;
	echo "</td></tr>";
}
//Bottom row
$pageCount = ceil($levelCount / 10);
$bottomRow = $dl->generateBottomRow($pageCount, $actualPage);
//Printing page
$dl->printPage('<table class="table table-inverse">
	<thead>
		<tr>
			<th>#</th>
			<th>'.$dl->getLocalizedString("levelID").'</th>
			<th>'.$dl->getLocalizedString("name").'</th>
			<th>'.$dl->getLocalizedString("author").'</th>
			<th>'.$dl->getLocalizedString("stars").'</th>
			<th>'.$dl->getLocalizedString("userCoins").'</th>
			<th>'.$dl->getLocalizedString("uploaded").'</th>
		</tr>
	</thead>
	<tbody>
		'.$leveltable.'
	</tbody>
</table>'
.$bottomRow, true, "browse");
?>