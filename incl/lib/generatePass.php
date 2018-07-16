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
		$query = $db->prepare("SELECT accountID, password, isAdmin FROM accounts WHERE username LIKE :username");
		$query->execute([':username' => $username]);
		if(!$query->rowCount()) return false;
		$result = $query->fetch();
		if(password_verify($pass, $result["password"])){
			$modipCategory = $gs->getMaxValuePermission($result["accountID"], "modipCategory");
			if($modipCategory > 0){
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
			return false;
		}
	}

	public function isValid($accountID, $password){
		include dirname(__FILE__)."/connection.php";
		$query = $db->prepare("SELECT username FROM accounts WHERE accountID = :accountID");
		$query->execute([':accountID' => $accountID]);
		if(!$query->rowCount()) return false;
		$result = $query->fetch();
		$username = $result["username"];
		return $this->isValidUsrname($username, $password);
		//return $username;
	}
}
?>
