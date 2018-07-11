<?php
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
//Checking lost password request
if(isset($_GET["e"]) && isset($_GET["u"]) && isset($_GET["h"]) && isset($_GET["n"]) && $_GET["e"] && $_GET["u"] && $_GET["h"] && $_GET["n"]){
	//Decoding
	$username = $ep->remove(base64_decode($_GET["u"]));
	$email = $ep->remove(base64_decode($_GET["e"]));
	$hash = $ep->remove(base64_decode($_GET["h"]));
	$newPassword = $ep->remove(base64_decode($_GET["n"]));
	//Checking password
    $query = $db->prepare("SELECT count(*) FROM accounts WHERE username = :username AND email = :email AND hash = :hash LIMIT 1");
    $query->execute([':username' => $username, ':email' => $email, ':hash' => $hash]);
	if($query->fetchColumn() > 0){
		//Creating pass hash
		$passhash = password_hash($newPassword, PASSWORD_DEFAULT);
		//Updating password
		$query = $db->prepare("UPDATE accounts SET password = :password WHERE username = :username AND email = :email AND hash = :hash");	
		$query->execute([':password' => $passhash, ':username' => $username, ':email' => $email, ':hash' => $hash]);
		$dl->printBox("<h1>".$dl->getLocalizedString("lostPassword")."</h1>
		<p>".$dl->getLocalizedString("passwordChanged")."</p>","account");
	}else{
		//Printing error
		$errorDesc = $dl->getLocalizedString("lostPasswordError-1");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
	}
}
//Getting form data
$username = $ep->remove($_POST["username"]);
$newPassword = $_POST["newPassword"];
$baseUsername = base64_encode($username);
$baseNewPassword = base64_encode($newPassword);
$query = $db->prepare("SELECT email FROM accounts WHERE username = :username LIMIT 1");	
$query->execute([':username' => $username]);
$email = $query->fetchColumn();
$baseEmail = base64_encode($email);
//Checking nothing's empty
if($username && $newPassword){
    //Creating hash
    $query = $db->prepare("SELECT hash FROM accounts WHERE username = :username AND email = :email LIMIT 1");
    $query->execute([':username' => $username, ':email' => $email]);
    $hash = $query->fetchColumn();
	$baseHash = base64_encode($hash);
	//Sending email
	$URI = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$body = dirname($URI)."/dashboard/account/lostPassword.php?h=$baseHash&e=$baseEmail&n=$baseNewPassword&u=$baseUsername.";
	$mail = $gs->sendMail($emailMail, $email, "Lost password", $body);
	if(PEAR::isError($mail)){
		//Printing error
		$errorDesc = $dl->getLocalizedString("lostPasswordError-3");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
	}else{
		$dl->printBox("<h1>".$dl->getLocalizedString("lostPassword")."</h1>
			<p>".$dl->getLocalizedString("emailSended")."</p>","account");
	}
}elseif($newPassword && isset($_POST["email"])){
	$email = $ep->remove($_POST["email"]);
	//Creating pass hash
	$passhash = password_hash($newPassword, PASSWORD_DEFAULT);
	//Updating password
	$query = $db->prepare("UPDATE accounts SET password = :password WHERE username = :username AND email = :email");
	$query->execute([':password' => $passhash, ':username' => $username, ':email' => $email]);
	$dl->printBox("<h1>".$dl->getLocalizedString("lostPassword")."</h1>
	<p>".$dl->getLocalizedString("passwordChanged")."</p>","account");
}else{
	if(!$emailEnabled){
		//Printing page
		$dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword").'</h1>
					<form action="" method="post">
						<div class="form-group">
							<input type="text" class="form-control" id="changePasswordEmail" name="email" placeholder="'.$dl->getLocalizedString("lostPasswordEmailFieldPlaceholder").'"><br>
							<input type="password" class="form-control" id="changeUsernameNewPassword" name="newPassword" placeholder="'.$dl->getLocalizedString("changePasswordNewPasswordFieldPlaceholder").'">
						</div>
						<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
					</form>',"account");
	}elseif($emailEnabled){
		//Printing page
		$dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword").'</h1>
					<form action="" method="post">
						<div class="form-group">
							<input type="text" class="form-control" id="changePasswordUsername" name="username" placeholder="'.$dl->getLocalizedString("changePasswordUserNameFieldPlaceholder").'"><br>
							<input type="password" class="form-control" id="changeUsernameNewPassword" name="newPassword" placeholder="'.$dl->getLocalizedString("changePasswordNewPasswordFieldPlaceholder").'">
						</div>
						<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
					</form>',"account");
	}
}
?>