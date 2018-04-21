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
//Generating suggestions table
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 10;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$ratetable = "";
//Getting data
$query = $db->prepare("SELECT * FROM ratesuggestions ORDER BY suggestionDate DESC LIMIT 10 OFFSET $page");
$query->execute([]);
$result = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM ratesuggestions");
$query->execute([]);
$ratecount = $query->fetchColumn();
$x = $ratecount - $page;
//Printing data
foreach($result as &$rate){
	//Getting data
	$query2 = $db->prepare("SELECT userID, userName FROM users WHERE extID = :accID");
	$accountID = $rate["accountID"];
	$query2->execute([':accID' => $accountID]);
	$userInfo = $query2->fetch();
	//Checking feature
	switch($rate["feature"]){
		case 1:
			$feature = $dl->getLocalizedString("Yes");
			break;
		case 0:
			$feature = $dl->getLocalizedString("No");
			break;
	}
	//Checking mod
	switch($rate["isMod"]){
		case 1:
			$isMod = $dl->getLocalizedString("Yes");
			break;
		case 0:
			$isMod = $dl->getLocalizedString("No");
			break;
	}
	//Checking stars
	switch($rate["stars"]){
		case 1:
			$diff = $dl->getLocalizedString("Auto");
			break;
		case 2:
			$diff = $dl->getLocalizedString("Easy");
			break;
		case 3:
			$diff = $dl->getLocalizedString("Normal");
			break;
		case 4:
		case 5:
			$diff = $dl->getLocalizedString("Hard");
			break;
		case 6:
		case 7:
			$diff = $dl->getLocalizedString("Harder");
			break;
		case 8:
		case 9:
			$diff = $dl->getLocalizedString("Insane");
			break;
		case 10:
			$diff = $dl->getLocalizedString("Demon");
			break;
	}
	//Checking demon
	$query3 = $db->prepare("SELECT diff FROM demondiffsuggestions WHERE accountID = :accID");
	$query3->execute([':accID' => $accountID]);
	$demon = $query3->fetchColumn();
	switch($demon){
		case 1:
			$demon = $dl->getLocalizedString("Easy");
			break;
		case 2:
			$demon = $dl->getLocalizedString("Medium");
			break;
		case 3:
			$demon = $dl->getLocalizedString("Hard");
			break;
		case 4:
			$demon = $dl->getLocalizedString("Insane");
			break;
		case 5:
			$demon = $dl->getLocalizedString("Extreme");
			break;
		default:
			$demon = $dl->getLocalizedString("Hard");
			break;
	}
	//Switching tables
	switch($diff){
		case $dl->getLocalizedString("Demon"):
			$ratetable .= '<tr>
					<th scope="row">'.$x.'</th>
					<td>'.$userInfo["userID"].'</th>
					<td>'.$userInfo["userName"].'</td>
					<td>'.$rate["stars"].'</td>
					<td>'.$diff.'</td>
					<td>'.$demon.'</td>
					<td>'.$feature.'</td>
					<td>'.$isMod.'</td>
					<td>'.$dl->convertToDate($rate["suggestionDate"]).' ago</td>
				</tr>';
			break;
		default:
			$ratetable .= '<tr>
					<th scope="row">'.$x.'</th>
					<td>'.$userInfo["userID"].'</th>
					<td>'.$userInfo["userName"].'</td>
					<td>'.$rate["stars"].'</td>
					<td>'.$diff.'</td>
					<td>NA</td>
					<td>'.$feature.'</td>
					<td>'.$isMod.'</td>
					<td>'.$dl->convertToDate($rate["suggestionDate"]).' ago</td>
				</tr>';
			break;
	}
	$x--;
	echo "</td></tr>";
}
//Bottom row
$pagecount = ceil($ratecount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
//Printing page
$dl->printPage('<table class="table table-inverse">
<thead>
	<tr>
		<th>#</th>
		<th>'.$dl->getLocalizedString("ID").'</th>
		<th>'.$dl->getLocalizedString("userName").'</th>
		<th>'.$dl->getLocalizedString("stars").'</th>
		<th>'.$dl->getLocalizedString("diff").'</th>
		<th>'.$dl->getLocalizedString("demonDiff").'</th>
		<th>'.$dl->getLocalizedString("feature").'</th>
		<th>'.$dl->getLocalizedString("isMod?").'</th>
		<th>'.$dl->getLocalizedString("suggestionDate").'</th>
	</tr>
	</thead>
	<tbody>
		'.$ratetable.'
	</tbody>
</table>'
.$bottomrow, true, "stats");
?>