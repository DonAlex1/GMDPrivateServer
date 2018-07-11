<?php
class generatePass
{
	public function isValidUsrname($username, $pass) {
		//Requesting files
		include dirname(__FILE__)."/connection.php";
		require_once dirname(__FILE__)."/mainLib.php";
		$gs = new mainLib();
		//Getting IP
		$hostname = $gs->getIP();
		$query = $db->prepare("SELECT accountID, salt, password, isAdmin FROM accounts WHERE username LIKE :username");
		$query->execute([':username' => $username]);
		if($query->rowCount() == 0){
			return false;
		}
		$result = $query->fetch();
		if(password_verify($pass, $result["password"])){
			$modipCategory = $gs->getMaxValuePermission($result["accountID"], "modipCategory");
			if($modipCategory > 0){ //modIPs
				$query4 = $db->prepare("SELECT count(*) FROM modips WHERE accountID = :id");
				$query4->execute([':id' => $result["accountID"]]);
				if ($query4->fetchColumn() > 0) {
					$query6 = $db->prepare("UPDATE modips SET IP = :hostname, modipCategory = :modipCategory WHERE accountID = :id");
				}else{
					$query6 = $db->prepare("INSERT INTO modips (IP, accountID, isMod, modipCategory) VALUES (:hostname, :id, '1', :modipCategory)");
				}
				$query6->execute([':hostname' => $hostname, ':id' => $result["accountID"], ':modipCategory' => $modipCategory]);
			}
			return true;
		}else{
			$md5pass = md5($pass . "epithewoihewh577667675765768rhtre67hre687cvolton5gw6547h6we7h6wh");
			CRYPT_BLOWFISH or die ("-2");
			$Blowfish_Pre = '$2a$05$';
			$Blowfish_End = '$';
			$hashed_pass = crypt($md5pass, $Blowfish_Pre . $result['salt'] . $Blowfish_End);
			if ($hashed_pass == $result['password']) {
				$pass = password_hash($pass, PASSWORD_DEFAULT);
				//Updating hash
				$query = $db->prepare("UPDATE accounts SET password = :password WHERE username = :username LIMIT 1");
				$query->execute([':username' => $username, ':password' => $pass]);
				return true;
			} else {
				if($md5pass == $result['password']){
					$pass = password_hash($pass, PASSWORD_DEFAULT);
					//Updating hash
					$query = $db->prepare("UPDATE accounts SET password = :password WHERE username = :username LIMIT 1");
					$query->execute([':username' => $username, ':password' => $pass]);
					return true;
				} else {
					return false;
				}
			}
		}
	}

	public function isValid($accountID, $password){
		include dirname(__FILE__)."/connection.php";
		$query = $db->prepare("SELECT username FROM accounts WHERE accountID = :accountID");
		$query->execute([':accountID' => $accountID]);
		if($query->rowCount() == 0){
			return false;
		}
		$result = $query->fetch();
		$username = $result["username"];
		$generatePass = new generatePass();
		return $generatePass->isValidUsrname($username, $password);
		//return $username;
	}
}
?>
