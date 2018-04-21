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
include "../../incl/lib/connection.php";
//Generating top table
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 10;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$toptable = "";
//Getting data
$starsgain = array();
$time = time() - 86400;
$x = 0;
$query = $db->prepare("SELECT * FROM actions WHERE type = '9' AND timestamp > :time");
$query->execute([':time' => $time]);
$result = $query->fetchAll();
//Printing data
foreach($result as &$gain){
	if(!empty($starsgain[$gain["account"]])){
		$starsgain[$gain["account"]] += $gain["value"];
	}else{
		$starsgain[$gain["account"]] = $gain["value"];
	}
}
arsort($starsgain);
foreach ($starsgain as $userID => $stars){
	$query = $db->prepare("SELECT * FROM users WHERE userID = :userID");
	$query->execute([':userID' => $userID]);
	$userinfo = $query->fetchAll()[0];
	$username = htmlspecialchars($userinfo["userName"], ENT_QUOTES);
	//Checking if banned
	if($userinfo["isBanned"] == 0){
		$x++;
		$toptable .= '<tr>
				<th scope="row">'.$x.'</th>
				<td>'.$username.'</td>
				<td>'.$stars.'</td>
				<td>'.$dl->convertToDate($userinfo["lastPlayed"]).' ago</td>
				</tr>';
		echo "</td></tr>";
	}
} 
//Printing page
$dl->printPage('<table class="table table-inverse">
	<thead>
		<tr>
			<th>#</th>
			<th>'.$dl->getLocalizedString("userName").'</th>
			<th>'.$dl->getLocalizedString("stars").'</th>
			<th>'.$dl->getLocalizedString("lastPlayed").'</th>
		</tr>
	</thead>
	<tbody>
		'.$toptable.'
	</tbody>
</table>'
.$bottomrow, true, "stats");
?>