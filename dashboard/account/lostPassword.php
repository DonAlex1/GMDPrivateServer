<?php
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
if(isset($_GET["e"]) && isset($_GET["u"]) && isset($_GET["h"]) && isset($_GET["n"]) && $_GET["e"] != "" && $_GET["u"] != "" && $_GET["h"] != "" && $_GET["n"] != ""){
	//Decoding
	$userName = $ep->remove(base64_decode($_GET["u"]));
	$email = $ep->remove(base64_decode($_GET["e"]));
	$hash = $ep->remove(base64_decode($_GET["h"]));
	$newPassword = $ep->remove(base64_decode($_GET["n"]));
	$salt = "";
	//Checking password
    $query = $db->prepare("SELECT count(*) FROM accounts WHERE userName = :userName AND email = :email AND hash = :hash LIMIT 1");
    $query->execute([':userName' => $userName, ':email' => $email, ':hash' => $hash]);
    $result = $query->fetchColumn();
	if($result == 1){
		//Checking save encryption
		if($cloudSaveEncryption == 1){
			//Updating key
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName AND email = :email AND hash = :hash LIMIT 1");	
			$query->execute([':userName' => $userName, ':email' => $email, ':hash' => $hash]);
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
					$errorDesc = $dl->getLocalizedString("lostPasswordError-2");
					exit($dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword")."</h1>
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
		$query = $db->prepare("UPDATE accounts SET password = :password, salt = :salt WHERE userName = :userName AND email = :email AND hash = :hash");	
		$query->execute([':password' => $passhash, ':userName' => $userName, ':salt' => $salt, ':email' => $email, ':hash' => $hash]);
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
$userName = $ep->remove($_POST["userName"]);
$newpass = $_POST["newpassword"];
$baseUsername = base64_encode($userName);
$baseNewPassword = base64_encode($newpass);
$query = $db->prepare("SELECT email FROM accounts WHERE userName = :userName LIMIT 1");	
$query->execute([':userName' => $userName]);
$email = $query->fetchColumn();
$baseEmail = base64_encode($email);
//Checking nothing's empty
if($userName != "" AND $newpass != ""){
    //Creating hash
    $query = $db->prepare("SELECT hash FROM accounts WHERE userName = :userName AND email = :email LIMIT 1");
    $query->execute([':userName' => $userName, ':email' => $email]);
    $hash = $query->fetchColumn();
	$baseHash = base64_encode($hash);
	//Sending email
	$body = "";
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
}elseif($userName != "" && $newpass != "" && isset($_POST["email"])){
	$email = $ep->remove($_POST["email"]);
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
				$errorDesc = $dl->getLocalizedString("lostPasswordError-2");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword")."</h1>
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
	$passhash = password_hash($newpass, PASSWORD_DEFAULT);
	//Updating password
	$query = $db->prepare("UPDATE accounts SET password = :password, salt = :salt WHERE userName = :userName AND email = :email");
	$query->execute([':password' => $passhash, ':userName' => $userName, ':salt' => $salt, ':email' => $email]);
	$dl->printBox("<h1>".$dl->getLocalizedString("lostPassword")."</h1>
	<p>".$dl->getLocalizedString("passwordChanged")."</p>","account");
}else{
	if($emailEnabled == 1){
		//Printing page
		$dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword").'</h1>
					<form action="" method="post">
						<div class="form-group">
							<input type="text" class="form-control" id="changePasswordEmail" name="email" placeholder="'.$dl->getLocalizedString("lostPasswordEmailFieldPlaceholder").'"><br>
							<input type="password" class="form-control" id="changeUsernameNewPassword" name="newpassword" placeholder="'.$dl->getLocalizedString("changePasswordNewPasswordFieldPlaceholder").'">
						</div>
						<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
					</form>',"account");
	}elseif($emailEnabled == 0){
		//Printing page
		$dl->printBox('<h1>'.$dl->getLocalizedString("lostPassword").'</h1>
					<form action="" method="post">
						<div class="form-group">
							<input type="text" class="form-control" id="changePasswordUsername" name="userName" placeholder="'.$dl->getLocalizedString("changePasswordUserNameFieldPlaceholder").'"><br>
							<input type="password" class="form-control" id="changeUsernameNewPassword" name="newpassword" placeholder="'.$dl->getLocalizedString("changePasswordNewPasswordFieldPlaceholder").'">
						</div>
						<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
					</form>',"account");
	}
}
?>