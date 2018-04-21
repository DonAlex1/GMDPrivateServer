<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
require "../../incl/lib/connection.php";
//Generating levels table
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 10;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$leveltable = "";
//Getting data
$query = $db->prepare("SELECT * FROM levels ORDER BY uploadDate DESC LIMIT 10 OFFSET $page");
$query->execute([]);
$result = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM levels");
$query->execute([]);
$levelcount = $query->fetchColumn();
$x = $levelcount - $page;
//Printing data
foreach($result as &$level){
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
$pagecount = ceil($levelcount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
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
.$bottomrow, true, "browse");
?>