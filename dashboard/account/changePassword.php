<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) || !$_SESSION["accountID"]) exit(header("Location: ../login/login.php"));
//Requesting files
include "../../config/email.php";
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/exploitPatch.php";
require_once "../../incl/lib/generatePass.php";
$gs = new mainLib();
$ep = new exploitPatch();
$dl = new dashboardLib();
$generatePass = new generatePass();
//Checking password change request
if(isset($_GET["e"]) && isset($_GET["u"]) && isset($_GET["o"]) && isset($_GET["n"]) && $_GET["e"] && $_GET["u"] && $_GET["o"] && $_GET["n"]){
	//Decoding
	$username = $ep->remove(base64_decode($_GET["u"]));
	$email = $ep->remove(base64_decode($_GET["e"]));
	$oldPassword = $ep->remove(base64_decode($_GET["o"]));
	$newPassword = $ep->remove(base64_decode($_GET["n"]));
	//Checking password
	if($generatePass->isValidUsrname($username, $oldPassword)){
		//Creating pass hash
		$passhash = password_hash($newPassword, PASSWORD_DEFAULT);
		//Updating password
		$query = $db->prepare("UPDATE accounts SET password = :password WHERE username = :username AND email = :email");	
		$query->execute([':password' => $passhash, ':username' => $username, ':email' => $email]);
		$dl->printBox("<h1>".$dl->getLocalizedString("changePassword")."</h1>
		<p>".$dl->getLocalizedString("passwordChanged")."</p>","account");
	}else{
		//Printing error
		$errorDesc = $dl->getLocalizedString("changePasswordError-1");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("changePassword")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
	}
}
//Getting form data
$username = $ep->remove($_POST["username"]);
$oldPassword = $_POST["oldPassword"];
$newPassword = $_POST["newPassword"];
$baseUsername = base64_encode($username);
$basePassword = base64_encode($oldPassword);
$baseNewPassword = base64_encode($newPassword);
$query = $db->prepare("SELECT email FROM accounts WHERE username = :username LIMIT 1");	
$query->execute([':username' => $username]);
$email = $query->fetchColumn();
$baseEmail = base64_encode($email);
//Checking nothing's empty
if(isset($_POST["username"]) && isset($_POST["newPassword"]) && isset($_POST["oldPassword"])){
	//Checking pass
	if ($generatePass->isValidUsrname($username, $oldPassword)) {
		//Sending email
		if($emailEnabled){
			$URI = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$body = dirname($URI)."/dashboard/account/changePassword.php?u=$baseUsername&e=$baseEmail&n=$baseNewPassword&o=$basePassword.";
			$mail = $gs->sendMail($emailMail, $email, "Change password", $body);
			if(PEAR::isError($mail)){
				//Printing error
				$errorDesc = $dl->getLocalizedString("changePasswordError-3");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("changePassword")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}else{
				$dl->printBox("<h1>".$dl->getLocalizedString("changePassword")."</h1>
					<p>".$dl->getLocalizedString("emailSended")."</p>","account");
			}
		}elseif(!$emailEnabled){
			//Creating pass hash
			$passhash = password_hash($newPassword, PASSWORD_DEFAULT);
			//Updating password
			$query = $db->prepare("UPDATE accounts SET password = :password WHERE username = :username AND email = :email");
			$query->execute([':password' => $passhash, ':username' => $username, ':email' => $email]);
			$dl->printBox("<h1>".$dl->getLocalizedString("changePassword")."</h1>
			<p>".$dl->getLocalizedString("passwordChanged")."</p>","account");
		}
	}else{
		//Printing error
		$errorDesc = $dl->getLocalizedString("changePasswordError-1");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("changePassword")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
	}
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("changePassword").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="changePasswordUsername" name="username" placeholder="'.$dl->getLocalizedString("changePasswordUserNameFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="changePasswordPassword" name="oldPassword" placeholder="'.$dl->getLocalizedString("changePasswordOldPasswordFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="changeUsernameNewPassword" name="newPassword" placeholder="'.$dl->getLocalizedString("changePasswordNewPasswordFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
				</form>',"account");
}
?>