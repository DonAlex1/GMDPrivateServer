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
	//Printing errors
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
//Checking nothing's empty
if(!empty($_POST["userID"])){
	//Getting form data
	$userID = $ep->remove($_POST["userID"]);
	$accountID = $_SESSION["accountID"];
	//Checking if is numeric
	if(!is_numeric($userID)){
		//Printing error
		$errorDesc = $dl->getLocalizedString("banError-2");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
	//Checking if was already banned
	$query = $db->prepare("SELECT isBanned FROM users WHERE userID = :userID LIMIT 1");
	$query->execute([':userID' => $userID]);
	if($query->fetchColumn() == 1){
		//Printing error
		$errorDesc = $dl->getLocalizedString("banError-3");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
	//Banning user
	$query = $db->prepare("UPDATE users SET isBanned = 1 WHERE userID = :id");
	$query->execute([':id' => $userID]);
	if($query->rowCount() != 0){
		//Printing box
		$dl->printBox("<h1>".$dl->getLocalizedString("leaderboardBan")."</h1>
					<p>".sprintf($dl->getLocalizedString("banned"), $userID)."</p>","mod");
	}else{
		//Printing error
		$errorDesc = sprintf($dl->getLocalizedString("banError-1"), $userID);
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
	$query = $db->prepare("INSERT INTO modactions  (type, value, value2, timestamp, account) 
												VALUES ('15',:userID, '1',  :timestamp,:account)");
	$query->execute([':userID' => $userID, ':timestamp' => time(), ':account' => $accountID]);
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="banUserID" name="userID" placeholder="'.$dl->getLocalizedString("banUserIDFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("banBTN").'</button>
				</form>',"mod");
}
?>