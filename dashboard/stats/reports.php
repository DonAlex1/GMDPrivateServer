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
if(!$gs->checkPermission($_SESSION["accountID"], "dashboardModTools")){
	//Printing error
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
//Generating reports table
if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0){
	$page = ($ep->remove($_GET["page"]) - 1) * 10;
	$actualPage = $ep->remove($_GET["page"]);
}else{
	$page = 0;
	$actualPage = 1;
}
$array = array();
//Getting data
$query = $db->prepare("SELECT levelID FROM reports ORDER BY levelID DESC LIMIT 10 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM reports");
$query->execute();
$reportCount = $query->fetchColumn();
foreach($result as &$report){
	if(!empty($array[$report["levelID"]])){
		$array[$report["levelID"]]++;
	}else{
		$array[$report["levelID"]] = 1;
	}
}
arsort($array);
foreach($array as $id => $count){
	$reporttable .= '<tr>
						<td>'.$id.'</th>
						<td>'.$count.'</td>
					</tr>';
}
//Bottom row
$pageCount = ceil($reportCount / 10);
$bottomRow = $dl->generateBottomRow($pageCount, $actualPage);
//Printing page
$dl->printPage('<table class="table table-inverse">
<thead>
	<tr>
		<th>'.$dl->getLocalizedString("ID").'</th>
		<th>'.$dl->getLocalizedString("times").'</th>
	</tr>
	</thead>
	<tbody>
		'.$reporttable.'
	</tbody>
</table>'
.$bottomRow, true, "mod");
?>