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
//Checking permissions
if($gs->checkPermission($_SESSION["accountID"], "dashboardModTools")){
	//Generating accounts table
	if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0){
		$page = ($ep->remove($_GET["page"]) - 1) * 10;
		$actualPage = $ep->remove($_GET["page"]);
	}else{
		$page = 0;
		$actualPage = 1;
	}
	//Getting data
	$query = $db->prepare("SELECT * FROM accounts ORDER BY registerDate DESC LIMIT 10 OFFSET $page");
	$query->execute();
	$accounts = $query->fetchAll();
	$query = $db->prepare("SELECT count(*) FROM accounts");
	$query->execute();
	$accCount = $query->fetchColumn();
	$x = $accCount - $page;
	$acctable = "";
	//Printing data
	foreach($accounts as &$account){
		//Getting account data
		$query = $db->prepare("SELECT userID FROM users WHERE extID = :extID LIMIT 1");
		$query->execute([':extID' => $account["accountID"]]);
		$userID = $query->fetchColumn();
		switch($account["isBanned"]){
			case 1:
				$isBanned = $dl->getLocalizedString("Yes");
				break;
			case 0:
				$isBanned = $dl->getLocalizedString("No");
				break;
		}
		$acctable .= '<tr>
						<th scope="row">'.$x.'</th>
						<td>'.$userID.'</td>
						<td>'.$account["accountID"].'</td>
						<td>'.$account["username"].'</td>
						<td>'.$account["friendsCount"].'</td>
						<td>'.$isBanned.'</td>
						<td>'.$dl->convertToDate($account["registerDate"]).' ago</td>
					</tr>';
		$x--;
		echo "</td></tr>";
	}
	//Bottom row
	$pageCount = ceil($accCount / 10);
	$bottomRow = $dl->generateBottomRow($pageCount, $actualPage);
	//Printing page
	$dl->printPage('<table class="table table-inverse">
		<thead>
			<tr>
				<th>#</th>
				<th>'.$dl->getLocalizedString("userID").'</th>
				<th>'.$dl->getLocalizedString("accountID").'</th>
				<th>'.$dl->getLocalizedString("userName").'</th>
				<th>'.$dl->getLocalizedString("friends").'</th>
				<th>'.$dl->getLocalizedString("banned?").'</th>
				<th>'.$dl->getLocalizedString("registerDate").'</th>
			</tr>
		</thead>
		<tbody>
			'.$acctable.'
		</tbody>
	</table>'
	.$bottomRow, true, "mod");
}else{
	//Printing error
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
?>