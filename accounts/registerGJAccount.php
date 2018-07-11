<?php
//Requesting files
include "../incl/lib/connection.php";
include "../config/email.php";
require_once "../incl/lib/exploitPatch.php";
require_once "../incl/lib/mainLib.php";
$gs = new mainLib();
$ep = new exploitPatch();
//Checking nothing's empty
if(isset($_POST["userName"]) && $_POST["userName"] != "" && isset($_POST["password"]) && $_POST["password"] != "" && isset($_POST["email"]) && $_POST["email"] != ""){
	//Getting data
	$username = $ep->remove($_POST["userName"]);
	$password = $_POST["password"];
	$email = $ep->remove($_POST["email"]);
	$baseEmail = base64_encode($email);
	//Checking if name is taken
	$query = $db->prepare("SELECT count(*) FROM accounts WHERE username LIKE :username");
	$query->execute([':username' => $username]);
	if($query->fetchColumn() > 0){
		//Taken
		exit("-2");
	}else{
		//Generating email
		$hashpass = password_hash($password, PASSWORD_DEFAULT);
		if($emailEnabled){
			$hash = md5(rand(0, 1000));
			$baseHash = base64_encode($hash);
			$URI = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$body = "Username: $username\nPassword: $password\n\nActivation link: ".dirname($URI)."/dashboard/account/activate.php?h=$baseHash&e=$baseEmail";
			if(PEAR::isError($gs->sendMail($emailMail, $email, "Account activation", $body))) exit("-1");
			//Registering
			$query = $db->prepare("INSERT INTO accounts (username, password, email, registerDate, hash)
			VALUES (:username, :password, :email, :time, :hash)");
			$query->execute([':username' => $username, ':password' => $hashpass, ':email' => $email, ':time' => time(), ':hash' => $hash]);
		}elseif(!$emailEnabled){
			//Registering
			$query = $db->prepare("INSERT INTO accounts (username, password, email, registerDate)
			VALUES (:username, :password, :email, :time)");
			$query->execute([':username' => $username, ':password' => $hashpass, ':email' => $email, ':time' => time()]);
		}
		echo "1";
	}
}else{
	//Failure
	exit("-1");
}
?>