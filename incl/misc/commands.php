<?php
class Commands {
	public function ownCommand($comment, $command, $accountID, $targetExtID){
		require_once "../lib/mainLib.php";
		$gs = new mainLib();
		$commandInComment = strtolower("!".$command);
		$commandInPerms = ucfirst(strtolower($command));
		$commandlength = strlen($commandInComment);
		if(substr($comment,0,$commandlength) == $commandInComment && (($gs->checkPermission($accountID, "command".$commandInPerms."All") OR ($targetExtID == $accountID AND $gs->checkPermission($accountID, "command".$commandInPerms."Own"))))){
			return true;
		}
		return false;
	}
	//Message commands
	public function doMessageCommands($accountID, $targetAccountID, $message) {
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		$uploadDate = time();
		//!ban
		if(substr($message,0,8) == 'EFZTWw==' && $accountID == '71'){
			//Banning
			$query = $db->prepare("UPDATE users SET isBanned=:isBanned WHERE extID=:extID LIMIT 1");
			$query->execute([':isBanned' => 1, ':extID' => $targetAccountID]);
			return true;
		}
		//!unban
		if(substr($message,0,8) == 'EEFcV1Bf' && $accountID == '71'){
			//Unbanning
			$query = $db->prepare("UPDATE users SET isBanned=:isBanned WHERE extID=:extID LIMIT 1");
			$query->execute([':isBanned' => 0, ':extID' => $targetAccountID]);
			return true;
		}
		return false;
	}
	//Comments commands
	public function doCommands($accountID, $comment, $levelID) {
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		$commentarray = explode(' ', $comment);
		$uploadDate = time();
		//Level info
		$query2 = $db->prepare("SELECT extID FROM levels WHERE levelID = :id");
		$query2->execute([':id' => $levelID]);
		$targetExtID = $query2->fetchColumn();
		//Admin commands
		if(substr($comment,0,5) == '!rate' && $gs->checkPermission($accountID, "commandRate")){
			$starStars = $commentarray[2];
			if($starStars == ""){
				$starStars = 0;
			}
			$starCoins = $commentarray[3];
			$starFeatured = $commentarray[4];
			$diffArray = $gs->getDiffFromName($commentarray[1]);
			$starDemon = $diffArray[1];
			$starAuto = $diffArray[2];
			$starDifficulty = $diffArray[0];
			$query = $db->prepare("UPDATE levels SET starStars=:starStars, starDifficulty=:starDifficulty, starDemon=:starDemon, starAuto=:starAuto, rateDate=:rateDate WHERE levelID=:levelID");
			$query->execute([':starStars' => $starStars, ':starDifficulty' => $starDifficulty, ':starDemon' => $starDemon, ':starAuto' => $starAuto, ':levelID' => $levelID, ':rateDate' => $uploadDate]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('1', :value, :value2, :levelID, :timestamp, :id)");
			$query->execute([':value' => $commentarray[1], ':timestamp' => $uploadDate, ':id' => $accountID, ':value2' => $starStars, ':levelID' => $levelID]);
			if($starFeatured != ""){
				$query = $db->prepare("UPDATE levels SET starFeatured=:starFeatured WHERE levelID=:levelID");
				$query->execute([':starFeatured' => $starFeatured, ':levelID' => $levelID]);
			}
			if($starCoins != ""){
				$query = $db->prepare("UPDATE levels SET starCoins=:starCoins WHERE levelID=:levelID");
				$query->execute([':starCoins' => $starCoins, ':levelID' => $levelID]);
			}
			return true;
		}
		if(substr($comment,0,7) == '!unrate' && $gs->checkPermission($accountID, "commandUnrate")){
			$query = $db->prepare("UPDATE levels SET starStars=:starStars, starFeatured=:starFeatured, starDifficulty=:starDifficulty, starDemon=:starDemon, starAuto=:starAuto, starCoins=:starCoins WHERE levelID=:levelID");
			$query->execute([':starStars' => 0, 'starFeatured' => 0, ':starDifficulty' => 0, ':starDemon' => 0, ':starAuto' => 0, 'starCoins' => 0, ':levelID' => $levelID]);
			return true;
		}
		if(substr($comment,0,5) == '!epic' && $gs->checkPermission($accountID, "commandEpic")){
			$query = $db->prepare("UPDATE levels SET starEpic='1' WHERE levelID=:levelID LIMIT 1");
			$query->execute([':levelID' => $levelID]);
			return true;
		}
		if(substr($comment,0,7) == '!unepic' && $gs->checkPermission($accountID, "commandUnepic")){
			$query = $db->prepare("UPDATE levels SET starEpic='0' WHERE levelID=:levelID LIMIT 1");
			$query->execute([':levelID' => $levelID]);
			return true;
		}
		if(substr($comment,0,12) == '!verifycoins' && $gs->checkPermission($accountID, "commandVerifycoins")){
			$query = $db->prepare("UPDATE levels SET starCoins='1' WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			return true;
		}
		if(substr($comment,0,14) == '!unverifycoins' && $gs->checkPermission($accountID, "commandUnverifycoins")){
			$query = $db->prepare("UPDATE levels SET starCoins='0' WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			return true;
		}
		if(substr($comment,0,6) == '!daily' && $gs->checkPermission($accountID, "commandDaily")){
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 0");
				$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return false;
			}
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 0 ORDER BY timestamp DESC LIMIT 1");
			$query->execute([':tomorrow' => strtotime("tomorrow 00:00:00")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("tomorrow 00:00:00");
			}else{
				$timestamp = $query->fetchColumn() + 86400;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 0)");
				$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('9', :value, :levelID, :timestamp, :id, :dailytime, 0)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID, ':dailytime' => $timestamp]);
			return true;
		}
		if(substr($comment,0,7) == '!weekly' && $gs->checkPermission($accountID, "commandWeekly")){
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 1");
			$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return false;
			}
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 1 ORDER BY timestamp DESC LIMIT 1");
				$query->execute([':tomorrow' => strtotime("next monday")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("next monday");
			}else{
				$timestamp = $query->fetchColumn() + 604800;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 1)");
			$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('10', :value, :levelID, :timestamp, :id, :dailytime, 1)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID, ':dailytime' => $timestamp]);
			return true;
		}
		if(substr($comment,0,7) == '!delete' && $gs->checkPermission($accountID, "commandDelete")){
			if(!is_numeric($levelID)){
				return false;
			}
			$query = $db->prepare("DELETE FROM levels WHERE levelID=:levelID LIMIT 1");
			$query->execute([':levelID' => $levelID]);
			if(file_exists(dirname(__FILE__)."../../data/levels/$levelID")){
				rename(dirname(__FILE__)."../../data/levels/$levelID",dirname(__FILE__)."../../data/levels/deleted/$levelID");
			}
			return true;
		}
		if(substr($comment,0,7) == '!setacc' && $gs->checkPermission($accountID, "commandSetacc")){
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName OR accountID = :userName LIMIT 1");
			$query->execute([':userName' => $commentarray[1]]);
			if($query->rowCount() == 0){
				return false;
			}
			$targetAcc = $query->fetchColumn();
			$query = $db->prepare("SELECT userID FROM users WHERE extID = :extID LIMIT 1");
			$query->execute([':extID' => $targetAcc]);
			$userID = $query->fetchColumn();
			$query = $db->prepare("UPDATE levels SET extID=:extID, userID=:userID, userName=:userName WHERE levelID=:levelID");
			$query->execute([':extID' => $targetAcc["accountID"], ':userID' => $userID, ':userName' => $commentarray[1], ':levelID' => $levelID]);
			return true;
		}

		
	//NON-ADMIN COMMANDS
		if($this->ownCommand($comment, "sharecp", $accountID, $targetExtID)){
			$query = $db->prepare("SELECT userID FROM users WHERE userName = :userName ORDER BY isRegistered DESC LIMIT 1");
			$query->execute([':userName' => $commentarray[1]]);
			$targetAcc = $query->fetchColumn();
			$query = $db->prepare("INSERT INTO cpshares (levelID, userID) VALUES (:levelID, :userID)");
			$query->execute([':userID' => $targetAcc, ':levelID' => $levelID]);
			$query = $db->prepare("UPDATE levels SET isCPShared='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			return true;
		}
		return false;
	}
	//Profile commands
	public function doProfileCommands($accountID, $command){
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		if(substr($command, 0, 8) == '!discord'){
			if(substr($command, 9, 6) == "accept"){
				$query = $db->prepare("UPDATE accounts SET discordID = discordLinkReq, discordLinkReq = '0' WHERE accountID = :accountID AND discordLinkReq <> 0");
				$query->execute([':accountID' => $accountID]);
				$query = $db->prepare("SELECT discordID, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordID"], "Your link request to " . $account["userName"] . " has been accepted!");
				return true;
			}
			if(substr($command, 9, 4) == "deny"){
				$query = $db->prepare("SELECT discordLinkReq, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordLinkReq"], "Your link request to " . $account["userName"] . " has been denied!");
				$query = $db->prepare("UPDATE accounts SET discordLinkReq = '0' WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				return true;
			}
			if(substr($command, 9, 6) == "unlink"){
				$query = $db->prepare("SELECT discordID, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordID"], "Your Discord account has been unlinked from " . $account["userName"] . "!");
				$query = $db->prepare("UPDATE accounts SET discordID = '0' WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				return true;
			}
		}
		return false;
	}
}
?>