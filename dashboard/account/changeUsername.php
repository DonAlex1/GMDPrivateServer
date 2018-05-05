<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
include "../../config/email.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/exploitPatch.php";
require_once "../../incl/lib/generatePass.php";
require_once "../../incl/lib/mainLib.php";
$gs = new mainLib();
$ep = new exploitPatch();
$generatePass = new generatePass();
$dl = new dashboardLib();
//Checking if is username change request and is not empty
if(isset($_GET["e"]) && isset($_GET["u"]) && isset($_GET["p"]) && isset($_GET["n"]) && $_GET["e"] != "" && $_GET["u"] != "" && $_GET["p"] != "" && $_GET["n"] != ""){
	//Decoding
	$userName = $ep->remove(base64_decode($_GET["u"]));
	$email = $ep->remove(base64_decode($_GET["e"]));
	$password = $ep->remove(base64_decode($_GET["p"]));
	$newUsername = $ep->remove(base64_decode($_GET["n"]));
	//Checking password
	$pass = $generatePass->isValidUsrname($userName, $password);
	if($pass == 1){
		//Checking if username exists
		$query = $db->prepare("SELECT userName FROM accounts WHERE userName = :userName LIMIT 1");
		$query->execute([':userName' => $newUsername]);
		$result = $query->rowCount();
		if($result == 1){
			//Printing error
			$errorDesc = sprintf($dl->getLocalizedString("changeUsernameError-2"), $newusr);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}
		//Updating username
		$query = $db->prepare("UPDATE acccomments SET userName = :newUsername WHERE userName = :userName");
		$query->execute([':newUsername' => $newUsername, ':userName' => $userName]);
		$query = $db->prepare("UPDATE comments SET userName = :newUsername WHERE userName = :userName");
		$query->execute([':newUsername' => $newUsername, ':userName' => $userName]);
		$query = $db->prepare("UPDATE levels SET userName = :newUsername WHERE userName = :userName");
		$query->execute([':newUsername' => $newUsername, ':userName' => $userName]);
		$query = $db->prepare("UPDATE messages SET userName = :newUsername WHERE userName = :userName");
		$query->execute([':newUsername' => $newUsername, ':userName' => $userName]);
		$query = $db->prepare("UPDATE users SET userName = :newUsername WHERE userName = :userName");
		$query->execute([':newUsername' => $newUsername, ':userName' => $userName]);
		$query = $db->prepare("UPDATE accounts SET userName= :newUsername WHERE userName= :userName AND email = :email");	
		$query->execute([':newUsername' => $newUsername, ':userName' => $userName, ':email' => $email]);
		if($query->rowCount() == 0){
			//Printing error
			$errorDesc = $dl->getLocalizedString("changeUsernameError-1");
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}else{
			$query = $db->prepare("SELECT userNameCount FROM accounts WHERE userName= :userName AND email = :email LIMIT 1");
			$query->execute([':userName' => $newUsername, ':email' => $email]);
			$count = $query->fetchColumn() + 1;
			$query = $db->prepare("UPDATE accounts SET userNameCount = :count WHERE userName= :userName AND email = :email");
			$query->execute([':count' => $count, ':userName' => $newUsername, ':email' => $email]);
			$query = $db->prepare("UPDATE accounts SET userNameDate = :timestamp WHERE userName= :userName AND email = :email");
			$query->execute([':timestamp' => time(), ':userName' => $newUsername, ':email' => $email]);
			$dl->printBox("<h1>".$dl->getLocalizedString("changeUsername")."</h1>
			<p>".sprintf($dl->getLocalizedString("usernameChanged"), $newUsername)."</p>","account");
		}
	}else{
		//Printing error
		$errorDesc = $dl->getLocalizedString("changeUsernameError-1");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
	}
}
//Getting form data
$userName = $ep->remove($_POST["userName"]);
$baseUsername = base64_encode($userName);
$newusr = $ep->remove($_POST["newusr"]);
$baseNewusr = base64_encode($newusr);
$password = $ep->remove($_POST["password"]);
$basePassword = base64_encode($password);
$query = $db->prepare("SELECT email FROM accounts WHERE userName = :userName LIMIT 1");
$query->execute([':userName' => $userName]);
$email = $query->fetchColumn();
$baseEmail = base64_encode($email);
//Checking nothing's empty
if($userName != "" AND $newusr != "" AND $password != ""){
	//Checking pass
	$pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
		//Checking how many times username has been changed
		$query = $db->prepare("SELECT userNameCount FROM accounts WHERE userName = :userName AND email = :email LIMIT 1");
		$query->execute([':userName' => $userName, ':email' => $email]);
		if($query->fetchColumn() == 3){
			$errorDesc = $dl->getLocalizedString("changeUsernameError-4");
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}
		//Checking when was the last username change
		$query = $db->prepare("SELECT userNameDate FROM accounts WHERE userName = :userName AND email = :email LIMIT 1");
		$query->execute([':userName' => $userName, ':email' => $email]);
		$lastChange = $query->fetchColumn();
		$timestamp = time() - 2592000;
		if($lastChange > $timestamp){
			$timestamp = $lastChange + 2592000;
			$timeLeft = date("m/d", $timestamp);
			$errorDesc = sprintf($dl->getLocalizedString("changeUsernameError-5"), $timeLeft);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}
		//Checking if username exists
		$query = $db->prepare("SELECT userName FROM accounts WHERE userName = :userName LIMIT 1");
		$query->execute([':userName' => $newusr]);
		$result = $query->rowCount();
		if($result == 1){
			//Printing error
			$errorDesc = sprintf($dl->getLocalizedString("changeUsernameError-2"), $newusr);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}
		//Sending email
		$body = "";
		$mail = $gs->sendMail($emailMail, $email, "Change username", $body);
		if(PEAR::isError($mail)){
			//Printing error
			$errorDesc = $dl->getLocalizedString("changeUsernameError-3");
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}else{
			$dl->printBox("<h1>".$dl->getLocalizedString("changeUsername")."</h1>
				<p>".$dl->getLocalizedString("emailSended")."</p>","account");
		}
	}else{
		//Printing error
		$errorDesc = $dl->getLocalizedString("changeUsernameError-1");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
						<p>".$dl->getLocalizedString("errorGeneric")." $errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
	}
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="changeUsernameUserName" name="userName" placeholder="'.$dl->getLocalizedString("changeUsernameUserNameFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="changeUsernameNewUserName" name="newusr" placeholder="'.$dl->getLocalizedString("changeUsernameNewUserFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="changeUsernamePassword" name="password" placeholder="'.$dl->getLocalizedString("changeUsernamePasswordFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
				</form>',"account");
}
?>