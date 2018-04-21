<?php
//Requeting files
chdir(dirname(__FILE__));
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
include_once "../config/security.php";
include_once "../incl/lib/defuse-crypto.phar";
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
$ep = new exploitPatch();
$generatePass = new generatePass();
//Getting data
$userName = $ep->remove($_POST["userName"]);
$password = $_POST["password"];
$secret = "";
//Checking username
$pass = $generatePass->isValidUsrname($userName, $password);
if($pass == 1){
	//Getting account data
	$query = $db->prepare("SELECT accountID, saveData FROM accounts WHERE userName = :userName");
	$query->execute([':userName' => $userName]);
	$account = $query->fetch();
	$accountID = $account["accountID"];
	//Checking if is numeric
	if(!is_numeric($accountID)){
		//Error
		exit("-1");
	}
	//Checking if save data exists
	if(!file_exists("../data/accounts/$accountID")){
		$saveData = $account["saveData"];
		if(substr($saveData,0,4) == "SDRz"){
			$saveData = base64_decode($saveData);
		}
	}else{
		//Getting saved data
		$saveData = file_get_contents("../data/accounts/$accountID");
		if(file_exists("../data/accounts/keys/$accountID")){
			if(substr($saveData,0,3) != "H4s"){
				$protected_key_encoded = file_get_contents("../data/accounts/keys/$accountID");
				$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key_encoded);
				$user_key = $protected_key->unlockKey($password);
				try {
					$saveData = Crypto::decrypt($saveData, $user_key);
				} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
					//Error
					exit("-2");	
				}
			}
		}
	}
	//Loading saved data
	echo $saveData.";21;30;a;a";
}else{
	//Failure
	exit("-1");
}
?>