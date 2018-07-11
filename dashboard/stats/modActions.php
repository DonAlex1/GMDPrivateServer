<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/exploitPatch.php";
$gs = new mainLib();
$dl = new dashboardLib();
$ep = new exploitPatch();
//Generating mod table
$modtable = "";
$accounts = implode(",",$gs->getAccountsWithPermission("toolModactions"));
if(!$accounts) exit($dl->printBox(sprintf($dl->getLocalizedString("errorNoAccWithPerm"), "toolsModactions")));
//Getting data
$query = $db->prepare("SELECT accountID, username FROM accounts WHERE accountID IN ($accounts) ORDER BY username ASC");
$query->execute();
$result = $query->fetchAll();
$row = 0;
foreach($result as &$mod){
	$row++;
	$query = $db->prepare("SELECT lastPlayed FROM users WHERE extID = :id");
	$query->execute([':id' => $mod["accountID"]]);
	$time = "".$dl->convertToDate($query->fetchColumn())." ago";
	$query = $db->prepare("SELECT count(*) FROM modactions WHERE account = :id");
	$query->execute([':id' => $mod["accountID"]]);
	$actionscount = $query->fetchColumn();
	$query = $db->prepare("SELECT count(*) FROM modactions WHERE account = :id AND type = '1'");
	$query->execute([':id' => $mod["accountID"]]);
	$lvlcount = $query->fetchColumn();
	$modtable .= "<tr>
					<th scope='row'>".$row."</th>
					<td>".$mod["userName"]."</td>
					<td>".$actionscount."</td>
					<td>".$lvlcount."</td>
					<td>".$time."</td>
				</tr>";
}
//Printing page
$dl->printPage('<table class="table table-inverse">
  <thead>
    <tr>
      <th>#</th>
      <th>'.$dl->getLocalizedString("mod").'</th>
      <th>'.$dl->getLocalizedString("count").'</th>
      <th>'.$dl->getLocalizedString("ratedLevels").'</th>
	<th>'.$dl->getLocalizedString("lastSeen").'</th>
    </tr>
  </thead>
  <tbody>
    '.$modtable.'
  </tbody>
</table>', true, "stats");
?>