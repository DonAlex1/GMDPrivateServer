<?php
//Requesting files
include "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
require "../../incl/lib/generatePass.php";
require "../../incl/lib/mainLib.php";
$gp = new generatePass();
$dl = new dashboardLib();
$gs = new mainLib();
//Checking if already logged in
$dl = new dashboardLib();
session_start();
if(isset($_SESSION["accountID"]) AND $_SESSION["accountID"] != 0){
	$dl->printLoginBox("<p>You are already logged in. <a href='..'>Click here to continue</a></p>");
	exit();
}
//Checking nothing's emtpy
if(isset($_POST["userName"]) AND isset($_POST["password"])){
	//Getting form data
	$userName = $_POST["userName"];
	$password = $_POST["password"];
	//Checking username and pasword
	$valid = $gp->isValidUsrname($userName, $password);
	if($valid == 0){
		//Printing error
		$dl->printLoginBoxInvalid();
		exit();
	}
	//Getting account info
	$accountID = $gs->getAccountIDFromName($userName);
	if($accountID == 0){
		//Printing error
		$dl->printLoginBoxError("Invalid accountID");
		exit();
	}
	//Checking if banned
	$query = $db->prepare("SELECT isBanned FROM accounts WHERE accountID = :accID LIMIT 1");
	$query->execute([':accID' => $accountID]);
	$result = $query->fetchColumn();
	if($result == 1){
		//Printing error
		$dl->printLoginBoxError("Account banned");
		exit();
	}
	if($result2 == 0){
		//Printing error
		$dl->printLoginBoxError("Account has not been activated.");
	}
	//Setting data
	$_SESSION["accountID"] = $accountID;
	if(isset($_POST["ref"])){
		header('Location: ' . $_POST["ref"]);
	}elseif(isset($_SERVER["HTTP_REFERER"])){
		header('Location: ' . $_SERVER["HTTP_REFERER"]);
	}
	//Printing message
	$dl->printLoginBox("<p>You are now logged in. <a href='..'>Please click here to continue.</a></p>");
}else{
	//Printing page
	$loginbox = '<form action="" method="post">
							<div class="form-group">
								<label for="usernameField">Username</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter username">
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