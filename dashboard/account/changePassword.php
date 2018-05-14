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
include_once "../../config/security.php";
include_once "../../incl/lib/defuse-crypto.phar";
$generatePass = new generatePass();
$ep = new exploitPatch();
$dl = new dashboardLib();
$gs = new mainLib();
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
//Checking if is username change request and is not empty
if(isset($_GET["e"]) && isset($_GET["u"]) && isset($_GET["o"]) && isset($_GET["n"]) && $_GET["e"] != "" && $_GET["u"] != "" && $_GET["o"] != "" && $_GET["n"] != ""){
	//Decoding
	$userName = $ep->remove(base64_decode($_GET["u"]));
	$email = $ep->remove(base64_decode($_GET["e"]));
	$oldPassword = $ep->remove(base64_decode($_GET["o"]));
	$newPassword = $ep->remove(base64_decode($_GET["n"]));
	$salt = "";
	//Checking password
	$pass = $generatePass->isValidUsrname($userName, $oldPassword);
	if($pass == 1){
		//Checking save encryption
		if($cloudSaveEncryption == 1){
			//Updating key
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName AND email = :email LIMIT 1");	
			$query->execute([':userName' => $userName, ':email' => $email]);
			$accountID = $query->fetchColumn();
			$saveData = file_get_contents("../../data/accounts/$accountID");
			if(file_exists("../../data/accounts/keys/$accountID")){
				$protected_key_encoded = file_get_contents("../../data/accounts/keys/$accountID");
				$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key_encoded);
				$user_key = $protected_key->unlockKey($oldPassword);
				try {
					$saveData = Crypto::decrypt($saveData, $user_key);
				} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
					//Printing error
					$errorDesc = $dl->getLocalizedString("changePasswordError-2");
					exit($dl->printBox('<h1>'.$dl->getLocalizedString("changePassword")."</h1>
									<p>$errorDesc</p>
									<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
				}
				$protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($newPassword);
				$protected_key_encoded = $protected_key->saveToAsciiSafeString();
				$user_key = $protected_key->unlockKey($newPassword);
				$saveData = Crypto::encrypt($saveData, $user_key);
				file_put_contents("../../data/accounts/$accountID",$saveData);
				file_put_contents("../../data/accounts/keys/$accountID",$protected_key_encoded);
			}
		}
		//Creating pass hash
		$passhash = password_hash($newPassword, PASSWORD_DEFAULT);
		//Updating password
		$query = $db->prepare("UPDATE accounts SET password = :password, salt = :salt WHERE userName = :userName AND email = :email");	
		$query->execute([':password' => $passhash, ':userName' => $userName, ':salt' => $salt, ':email' => $email]);
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
$userName = $ep->remove($_POST["userName"]);
$oldpass = $_POST["oldpassword"];
$newpass = $_POST["newpassword"];
$baseUsername = base64_encode($userName);
$basePassword = base64_encode($oldpass);
$baseNewPassword = base64_encode($newpass);
$query = $db->prepare("SELECT email FROM accounts WHERE userName = :userName LIMIT 1");	
$query->execute([':userName' => $userName]);
$email = $query->fetchColumn();
$baseEmail = base64_encode($email);
//Checking nothing's empty
if($userName != "" AND $newpass != "" AND $oldpass != ""){
	//Checking pass
	$pass = $generatePass->isValidUsrname($userName, $oldpass);
	if ($pass == 1) {
		//Sending email
		if($emailEnabled == 1){
			$body = "";
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
		}elseif($emailEnabled == 0){
			//Checking save encryption
			if($cloudSaveEncryption == 1){
				//Updating key
				$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName AND email = :email LIMIT 1");	
				$query->execute([':userName' => $userName, ':email' => $email]);
				$accountID = $query->fetchColumn();
				$saveData = file_get_contents("../../data/accounts/$accountID");
				if(file_exists("../../data/accounts/keys/$accountID")){
					$protected_key_encoded = file_get_contents("../../data/accounts/keys/$accountID");
					$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key_encoded);
					$user_key = $protected_key->unlockKey($oldpass);
					try {
						$saveData = Crypto::decrypt($saveData, $user_key);
					} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
						//Printing error
						$errorDesc = $dl->getLocalizedString("changePasswordError-2");
						exit($dl->printBox('<h1>'.$dl->getLocalizedString("changePassword")."</h1>
										<p>$errorDesc</p>
										<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
					}
					$protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($newpass);
					$protected_key_encoded = $protected_key->saveToAsciiSafeString();
					$user_key = $protected_key->unlockKey($newpass);
					$saveData = Crypto::encrypt($saveData, $user_key);
					file_put_contents("../../data/accounts/$accountID",$saveData);
					file_put_contents("../../data/accounts/keys/$accountID",$protected_key_encoded);
				}
			}
			//Creating pass hash
			$passhash = password_hash($newpass, PASSWORD_DEFAULT);
			//Updating password
			$query = $db->prepare("UPDATE accounts SET password = :password, salt = :salt WHERE userName = :userName AND email = :email");	
			$query->execute([':password' => $passhash, ':userName' => $userName, ':salt' => $salt, ':email' => $email]);
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
						<input type="text" class="form-control" id="changePasswordUsername" name="userName" placeholder="'.$dl->getLocalizedString("changePasswordUserNameFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="changePasswordPassword" name="oldpassword" placeholder="'.$dl->getLocalizedString("changePasswordOldPasswordFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="changeUsernameNewPassword" name="newpassword" placeholder="'.$dl->getLocalizedString("changePasswordNewPasswordFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
				</form>',"account");
}
?>