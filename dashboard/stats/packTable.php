<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) || !$_SESSION["accountID"]) exit(header("Location: ../login/login.php"));
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/exploitPatch.php";
$gs = new mainLib();
$dl = new dashboardLib();
$ep = new exploitPatch();
//Generating pack table
if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0){
	$page = ($ep->remove($_GET["page"]) - 1) * 10;
	$actualPage = $ep->remove($_GET["page"]);
}else{
	$page = 0;
	$actualPage = 1;
}
$packtable = "";
$x = $page + 1;
//Getting map packs
$query = $db->prepare("SELECT levels, name, stars, coins FROM mapPacks ORDER BY ID ASC LIMIT 10 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$pack){
	//Getting data
	$lvltable;
	$lvlarray = explode(",", $pack["levels"]);
	foreach($lvlarray as &$lvl){
		$query = $db->prepare("SELECT levelID, levelName, starStars, userID, coins FROM levels WHERE levelID = :levelID");
		$query->execute([':levelID' => $lvl]);
		$level = $query->fetch();
		$lvltable .= "<tr>
						<td>".$level["levelID"]."</td>
						<td>".$level["levelName"]."</td>
						<td>".$gs->getUserName($level["userID"])."</td>
						<td>".$level["starStars"]."</td>
						<td>".$level["coins"]."</td>
					</tr>";
	}
	$packtable .= "<tr>
					<th scope='row'>$x</th>
					<td>".htmlspecialchars($pack["name"],ENT_QUOTES)."</td>
					<td>".$pack["stars"]."</td>
					<td>".$pack["coins"].'</td>
					<td><a class="dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Show
						</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink"  style="padding:17px;">
							<table class="table">
								<thead>
									<tr>
										<th>'.$dl->getLocalizedString("ID").'</th>
										<th>'.$dl->getLocalizedString("name").'</th>
										<th>'.$dl->getLocalizedString("author").'</th>
										<th>'.$dl->getLocalizedString("stars").'</th>
										<th>'.$dl->getLocalizedString("userCoins").'</th>
									</tr>
								</thead>
								<tbody>
									'.$lvltable.'
								</tbody>
							</table>
						</div>
					</td>
					</tr>';
	$x++;
}
//Getting count
$query = $db->prepare("SELECT count(*) FROM mappacks");
$query->execute();
$packCount = $query->fetchColumn();
$pageCount = ceil($packCount / 10);
//Bottom row
$bottomRow = $dl->generateBottomRow($pageCount, $actualPage);
//Printing page
$dl->printPage('<table class="table table-inverse">
  <thead>
    <tr>
      <th>#</th>
      <th>'.$dl->getLocalizedString("name").'</th>
      <th>'.$dl->getLocalizedString("stars").'</th>
      <th>'.$dl->getLocalizedString("coins").'</th>
	  <th>'.$dl->getLocalizedString("levels").'</th>
    </tr>
  </thead>
  <tbody>
    '.$packtable.'
  </tbody>
</table>'
.$bottomRow, true, "browse");
?>