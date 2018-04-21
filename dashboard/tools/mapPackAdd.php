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
require_once "../../incl/lib/exploitPatch.php";
require_once "../../incl/lib/mainLib.php";
$ep = new exploitPatch();
$dl = new dashboardLib();
$gs = new mainLib();
//Checking permissions
$perms = $gs->checkPermission($_SESSION["accountID"], "dashboardModTools");
if(!$perms){
	//Printing error
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
//Checking nothing's empty
if(!empty($_POST["levels"]) && !empty($_POST["stars"]) && !empty($_POST["name"])){
	//Getting data
	$packName = $ep->remove($_POST["name"]);
	$levels = $ep->remove($_POST["levels"]);
	$stars = $ep->remove($_POST["stars"]);
	$coins = $ep->remove($_POST["coins"]);
	$color = $ep->remove($_POST["color"]);
	//Checking values are valid
	if(!is_numeric($stars) OR !is_numeric($coins) OR $stars > 10 OR $coins > 2){
		//Printing error
		$errorDesc = sprintf($dl->getLocalizedString("packAddError-4"), $color);
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("packAdd")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
	if(strlen($color) != 6){
		//Printing error
		$errorDesc = $dl->getLocalizedString("packAddError-3");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("packAdd")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
	//Printing data
	$rgb = hexdec(substr($color,0,2)).
		",".hexdec(substr($color,2,2)).
		",".hexdec(substr($color,4,2));
	$lvlsarray = explode(",", $levels);
	foreach($lvlsarray AS &$level){
		//Checking if is numeric
		if(!is_numeric($level)){
			//Printing error
			$errorDesc = sprintf($dl->getLocalizedString("packAddError-2"), $level);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("packAdd")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
		}
		//Getting level data
		$query = $db->prepare("SELECT levelName FROM levels WHERE levelID=:levelID");	
		$query->execute([':levelID' => $level]);
		if($query->rowCount() == 0){
			//Printing error
			$errorDesc = sprintf($dl->getLocalizedString("packAddError-1"), $level);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("packAdd")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
		}
		$levelName = $query->fetchColumn();
		$levelstring .= $levelName . ", ";
	}
	$levelstring = substr($levelstring,0,-2);
	$diff = 0;
	$diffname = "Auto";
	//Checking stars
	switch($stars){
		case 1:
			$diffname = "Auto";
			$diff = 0;
			break;
		case 2:
			$diffname = "Easy";
			$diff = 1;
			break;
		case 3:
			$diffname = "Normal";
			$diff = 2;
			break;
		case 4:
		case 5:
			$diffname = "Hard";
			$diff = 3;
			break;
		case 6:
		case 7:
			$diffname = "Harder";
			$diff = 4;
			break;
		case 8:
		case 9:
			$diffname = "Insane";
			$diff = 5;
			break;
		case 10:
			$diffname = "Demon";
			$diff = 6;
			break;
	}
	//Adding map pack
	$query = $db->prepare("INSERT INTO mappacks     (name, levels, stars, coins, difficulty, rgbcolors)
											VALUES (:name,:levels,:stars,:coins,:difficulty,:rgbcolors)");
	$query->execute([':name' => $packName, ':levels' => $levels, ':stars' => $stars, ':coins' => $coins, ':difficulty' => $diff, ':rgbcolors' => $rgb]);
	$query = $db->prepare("INSERT INTO modactions  (type, value, timestamp, account, value2, value3, value4, value7) 
											VALUES ('11',:value,:timestamp,:account,:levels, :stars, :coins, :rgb)");
	$query->execute([':value' => $packName, ':timestamp' => time(), ':account' => $accountID, ':levels' => $levels, ':stars' => $stars, ':coins' => $coins, ':rgb' => $rgb]);
	$dl->printBox("<h1>".$dl->getLocalizedString("packAdd")."</h1>
					<p>".sprintf($dl->getLocalizedString("packAdded"), $packName)."</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("songAddAnotherBTN")."</a>","mod");
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("packAdd").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="mapPackName" name="name" placeholder="'.$dl->getLocalizedString("mapPackNameFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="mapPackLevels" name="levels" placeholder="'.$dl->getLocalizedString("mapPackLevelsFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="mapPackStars" name="stars" placeholder="'.$dl->getLocalizedString("mapPackStarsFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="mapPackCoins" name="coins" placeholder="'.$dl->getLocalizedString("mapPackCoinsFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="mapPackColor" name="color" placeholder="'.$dl->getLocalizedString("mapPackColorFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("createBTN").'</button>
				</form>',"mod");
}
?>