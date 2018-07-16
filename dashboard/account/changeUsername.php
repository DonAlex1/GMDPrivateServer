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
//Checking username change request
if(isset($_GET["e"]) && isset($_GET["u"]) && isset($_GET["p"]) && isset($_GET["n"]) && $_GET["e"] && $_GET["u"] && $_GET["p"] && $_GET["n"]){
	//Decoding
	$username = $ep->remove(base64_decode($_GET["u"]));
	$email = $ep->remove(base64_decode($_GET["e"]));
	$password = $ep->remove(base64_decode($_GET["p"]));
	$newUsername = $ep->remove(base64_decode($_GET["n"]));
	//Checking password
	if($generatePass->isValidUsrname($username, $password)){
		//Checking if username exists
		$query = $db->prepare("SELECT username FROM accounts WHERE username = :username LIMIT 1");
		$query->execute([':username' => $newUsername]);
		if($query->rowCount() > 0){
			//Printing error
			$errorDesc = sprintf($dl->getLocalizedString("changeUsernameError-2"), $newusr);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}
		//Updating username
		$query = $db->prepare("UPDATE acccomments SET username = :newUsername WHERE username = :username");
		$query->execute([':newUsername' => $newUsername, ':username' => $username]);
		$query = $db->prepare("UPDATE comments SET username = :newUsername WHERE username = :username");
		$query->execute([':newUsername' => $newUsername, ':username' => $username]);
		$query = $db->prepare("UPDATE levels SET username = :newUsername WHERE username = :username");
		$query->execute([':newUsername' => $newUsername, ':username' => $username]);
		$query = $db->prepare("UPDATE messages SET username = :newUsername WHERE username = :username");
		$query->execute([':newUsername' => $newUsername, ':username' => $username]);
		$query = $db->prepare("UPDATE users SET username = :newUsername WHERE username = :username");
		$query->execute([':newUsername' => $newUsername, ':username' => $username]);
		$query = $db->prepare("UPDATE accounts SET username= :newUsername WHERE username = :username AND email = :email");	
		$query->execute([':newUsername' => $newUsername, ':username' => $username, ':email' => $email]);
		if($query->rowCount() == 0){
			//Printing error
			$errorDesc = $dl->getLocalizedString("changeUsernameError-1");
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}else{
			$query = $db->prepare("UPDATE accounts SET usernameCount = usernameCount + 1 WHERE username= :username AND email = :email");
			$query->execute([':username' => $newUsername, ':email' => $email]);
			$query = $db->prepare("UPDATE accounts SET usernameChangeDate = :timestamp WHERE username = :username AND email = :email");
			$query->execute([':timestamp' => time(), ':username' => $newUsername, ':email' => $email]);
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
$username = $ep->remove($_POST["username"]);
$baseUsername = base64_encode($username);
$newUsername = $ep->remove($_POST["newUsername"]);
$baseNewUsername = base64_encode($newUsername);
$password = $ep->remove($_POST["password"]);
$basePassword = base64_encode($password);
$query = $db->prepare("SELECT email FROM accounts WHERE username = :username LIMIT 1");
$query->execute([':username' => $username]);
$email = $query->fetchColumn();
$baseEmail = base64_encode($email);
//Checking nothing's empty
if(isset($_POST["username"]) && isset($_POST["newUsername"]) && isset($_POST["password"])){
	//Checking pass
	if ($generatePass->isValidUsrname($username, $password)) {
		//Checking how many times username has been changed
		$query = $db->prepare("SELECT usernameCount FROM accounts WHERE username = :username AND email = :email LIMIT 1");
		$query->execute([':username' => $username, ':email' => $email]);
		if($query->fetchColumn() > 2){
			$errorDesc = $dl->getLocalizedString("changeUsernameError-4");
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}
		//Checking when was the last username change
		$query = $db->prepare("SELECT usernameChangeDate FROM accounts WHERE username = :username AND email = :email LIMIT 1");
		$query->execute([':username' => $username, ':email' => $email]);
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
		$query = $db->prepare("SELECT username FROM accounts WHERE username = :username LIMIT 1");
		$query->execute([':username' => $newUsername]);
		if($query->rowCount() > 0){
			//Printing error
			$errorDesc = sprintf($dl->getLocalizedString("changeUsernameError-2"), $newUsername);
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
							<p>$errorDesc</p>
							<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
		}
		if($emailEnabled){
			//Sending email
			$URI = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$body = dirname($URI)."/dashboard/account/changeUsername.php?u=$baseUsername&e=$baseEmail&n=$baseNewUsername&p=$basePassword.";
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
		}elseif(!$emailEnabled){
			//Updating username
			$query = $db->prepare("UPDATE accComments SET username = :newUsername WHERE username = :username");
			$query->execute([':newUsername' => $newUsername, ':username' => $username]);
			$query = $db->prepare("UPDATE comments SET username = :newUsername WHERE username = :username");
			$query->execute([':newUsername' => $newUsername, ':username' => $username]);
			$query = $db->prepare("UPDATE levels SET username = :newUsername WHERE username = :username");
			$query->execute([':newUsername' => $newUsername, ':username' => $username]);
			$query = $db->prepare("UPDATE messages SET username = :newUsername WHERE username = :username");
			$query->execute([':newUsername' => $newUsername, ':username' => $username]);
			$query = $db->prepare("UPDATE users SET username = :newUsername WHERE username = :username");
			$query->execute([':newUsername' => $newUsername, ':username' => $username]);
			$query = $db->prepare("UPDATE accounts SET username = :newUsername WHERE username = :username AND email = :email");	
			$query->execute([':newUsername' => $newUsername, ':username' => $username, ':email' => $email]);
			if(!$query->rowCount()){
				//Printing error
				$errorDesc = $dl->getLocalizedString("changeUsernameError-1");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}else{
				$query = $db->prepare("UPDATE accounts SET usernameCount = usernameCount + 1 WHERE username = :username AND email = :email");
				$query->execute([':username' => $newUsername, ':email' => $email]);
				$query = $db->prepare("UPDATE accounts SET usernameChangeDate = :timestamp WHERE username = :username AND email = :email");
				$query->execute([':timestamp' => time(), ':username' => $newUsername, ':email' => $email]);
				$dl->printBox("<h1>".$dl->getLocalizedString("changeUsername")."</h1>
				<p>".sprintf($dl->getLocalizedString("usernameChanged"), $newUsername)."</p>","account");
			}
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
						<input type="text" class="form-control" id="changeUsernameUserName" name="username" placeholder="'.$dl->getLocalizedString("changeUsernameUserNameFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="changeUsernameNewUserName" name="newUsername" placeholder="'.$dl->getLocalizedString("changeUsernameNewUserFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="changeUsernamePassword" name="password" placeholder="'.$dl->getLocalizedString("changeUsernamePasswordFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeBTN").'</button>
				</form>',"account");
}
?>