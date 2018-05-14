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
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$email = $ep->remove($_POST["email"]);
	$baseEmail = base64_encode($email);
	$secret = "";
	//Checking if name is taken
	$query2 = $db->prepare("SELECT count(*) FROM accounts WHERE userName LIKE :userName");
	$query2->execute([':userName' => $userName]);
	$regusrs = $query2->fetchColumn();
	if($regusrs > 0){
		//Taken
		exit("-2");
	}else{
		//Generating email
		$hashpass = password_hash($password, PASSWORD_DEFAULT);
		if($emailEnabled == 1){
			$hash = md5(rand(0,1000));
			$baseHash = base64_encode($hash);
			$body = "";
			$mail = $gs->sendMail($emailMail, $email, "Account activation", $body);
			if(PEAR::isError($mail)){
				//Error
				exit("-1");
			}
			//Registering
			$query = $db->prepare("INSERT INTO accounts (userName, password, email, secret, saveData, registerDate, saveKey, hash)
			VALUES (:userName, :password, :email, :secret, '', :time, '', :hash)");
			$query->execute([':userName' => $userName, ':password' => $hashpass, ':email' => $email, ':secret' => $secret, ':time' => time(), ':hash' => $hash]);
			echo "1";
		}elseif($emailEnabled == 0){
			//Registering
			$query = $db->prepare("INSERT INTO accounts (userName, password, email, secret, saveData, registerDate, saveKey, hash)
			VALUES (:userName, :password, :email, :secret, '', :time, '', '')");
			$query->execute([':userName' => $userName, ':password' => $hashpass, ':email' => $email, ':secret' => $secret, ':time' => time()]);
			echo "1";
		}
	}
}else{
	//Failure
	exit("-1");
}
?>