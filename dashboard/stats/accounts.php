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
//Checking permissions
if($gs->checkPermission($_SESSION["accountID"], "dashboardModTools")){
	//Generating accounts table
	if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
		$page = ($_GET["page"] - 1) * 10;
		$actualpage = $_GET["page"];
	}else{
		$page = 0;
		$actualpage = 1;
	}
	$dailytable = "";
	//Getting data
	$query = $db->prepare("SELECT * FROM accounts ORDER BY registerDate DESC LIMIT 10 OFFSET $page");
	$query->execute([]);
	$result = $query->fetchAll();
	$query = $db->prepare("SELECT count(*) FROM accounts");
	$query->execute([]);
	$acccount = $query->fetchColumn();
	$x = $acccount - $page;
	//Printing data
	foreach($result as &$account){
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
						<td>'.$account["userName"].'</td>
						<td>'.$account["friendsCount"].'</td>
						<td>'.$isBanned.'</td>
						<td>'.$dl->convertToDate($account["registerDate"]).' ago</td>
					</tr>';
		$x--;
		echo "</td></tr>";
	}
	//Bottom row
	$pagecount = ceil($acccount / 10);
	$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
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
	.$bottomrow, true, "mod");
}else{
	//Printing error
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
?>