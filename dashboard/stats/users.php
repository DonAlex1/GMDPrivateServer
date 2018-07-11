<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) || !$_SESSION["accountID"]) exit(header("Location: ../login/login.php"));
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/exploitPatch.php";
$dl = new dashboardLib();
$ep = new exploitPatch();
//Generating users table
if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0){
	$page = ($ep->remove($_GET["page"]) - 1) * 10;
	$actualpage = $ep->remove($_GET["page"]);
}else{
	$page = 0;
	$actualpage = 1;
}
$usertable;
//Getting data
$query = $db->prepare("SELECT * FROM users WHERE isRegistered = '1' AND isBanned = '0' ORDER BY userID DESC LIMIT 10 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM users WHERE isRegistered = '1' AND isBanned = '0'");
$query->execute();
$usercount = $query->fetchColumn();
$x = $usercount - $page;
//Printing data
foreach($result as &$user){
	//Getting account data
	$usertable .= '<tr>
					<th scope="row">'.$x.'</th>
					<td>'.$user["userID"].'</td>
					<td>'.$user["userName"].'</td>
					<td>'.$user["stars"].'</td>
					<td>'.$user["demons"].'</td>
					<td>'.$user["coins"].'</td>
					<td>'.$user["userCoins"].'</td>
					<td>'.$user["creatorPoints"].'</td>
					<td>'.$user["diamonds"].'</td>
				</tr>';
	$x--;
	echo "</td></tr>";
}
//Bottom row
$pagecount = ceil($usercount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
//Printing page
$dl->printPage('<table class="table table-inverse">
	<thead>
		<tr>
			<th>#</th>
			<th>'.$dl->getLocalizedString("userID").'</th>
			<th>'.$dl->getLocalizedString("userName").'</th>
			<th>'.$dl->getLocalizedString("stars").'</th>
			<th>'.$dl->getLocalizedString("demons").'</th>
			<th>'.$dl->getLocalizedString("coins").'</th>
			<th>'.$dl->getLocalizedString("userCoins").'</th>
			<th>'.$dl->getLocalizedString("creatorPoints").'</th>
			<th>'.$dl->getLocalizedString("diamonds").'</th>
		</tr>
	</thead>
	<tbody>
		'.$usertable.'
	</tbody>
</table>'
.$bottomrow, true, "browse");
?>