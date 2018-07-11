<?php
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
$gs = new mainLib();
$gp = new generatePass();
$dl = new dashboardLib();
$ep = new exploitPatch();
$dl = new dashboardLib();
//Checking if already logged in
session_start();
if(isset($_SESSION["accountID"]) && !$_SESSION["accountID"]) exit($dl->printLoginBox("<p>You are already logged in. <a href='..'>Click here to continue</a></p>"));
//Checking nothing's emtpy
if(isset($_POST["username"]) && isset($_POST["password"])){
	//Getting form data
	$username = $ep->remove($_POST["username"]);
	$password = $_POST["password"];
	//Checking username and pasword
	if(!$gp->isValidUsrname($username, $password)) exit($dl->printLoginBoxInvalid());
	//Getting account info
	$accountID = $gs->getAccountIDFromName($username);
	if(!$accountID) exit($dl->printLoginBoxError("Invalid accountID"));
	//Checking if banned
	if($gs->isBanned($accountID, "account")) exit($dl->printLoginBoxError("Account banned"));
	//Checking if actiavted
	$query = $db->prepare("SELECT active FROM accounts WHERE accountID = :accountID LIMIT 1");
	$query->execute([':accountID' => $accountID]);
	if(!$query->fetchColumn()) exit($dl->printLoginBoxError("Account has not been activated."));
	//Setting data
	$_SESSION["accountID"] = $accountID;
	header('Location: ../');
	//Printing message
	$dl->printLoginBox("<p>You are now logged in. <a href='..'>Please click here to continue.</a></p>");
}else{
	//Printing page
	$loginbox = '<form action="" method="post">
							<div class="form-group">
								<label for="usernameField">Username</label>
								<input type="text" class="form-control" id="usernameField" name="username" placeholder="Enter username">
							</div>
							<div class="form-group">
								<label for="passwordField">Password</label>
								<input type="password" class="form-control" id="passwordField" name="password" placeholder="Password">
							</div>';
	if(isset($_SERVER["HTTP_REFERER"])){
		$loginbox .= '<input type="hidden" name="ref" value="'.$_SERVER["HTTP_REFERER"].'">';
	}
	$loginbox .= '<button type="submit" class="btn btn-primary">Log In</button>
						</form>';
	$dl->printLoginBox($loginbox);
}
?>