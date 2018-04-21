<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
require_once "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
require_once "../incl/dashboardLib.php";
$dl = new dashboardLib();
$generatePass = new generatePass();
$ep = new exploitPatch();
//Getting data
if(!empty($_POST["userhere"]) AND !empty($_POST["passhere"]) AND !empty($_POST["usertarg"]) AND !empty($_POST["passtarg"])){
	$userhere = $ep->remove($_POST["userhere"]);
	$passhere = $ep->remove($_POST["passhere"]);
	$usertarg = $ep->remove($_POST["usertarg"]);
	$passtarg = $ep->remove($_POST["passtarg"]);
	$pass = $generatePass->isValidUsrname($userhere, $passhere);
	if ($pass == 1) {
		//Requesting target server
		$url = $_POST["server"];
		$udid = "S" . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(1,9);
		$sid = mt_rand(111111111,999999999) . mt_rand(11111111,99999999);
		$post = ['userName' => $usertarg, 'udid' => $udid, 'password' => $passtarg, 'sID' => $sid, 'secret' => 'Wmfv3899gc9'];
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($ch);
		curl_close($ch);
		//Checking result
		if($result == "" OR $result == "-1" OR $result == "No no no"){
			if($result == ""){
				//Printing error
				$errorDesc = $dl->getLocalizedString("linkAccountError-2");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}elseif($result == "-1"){
				//Printing error
				$errorDesc = $dl->getLocalizedString("linkAccountError-1");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}else{
				//Printing error
				$errorDesc = $dl->getLocalizedString("linkAccountError-3");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}
		}else{
			$parsedurl = parse_url($url);
			//Checking if is local server
			if($parsedurl["host"] == $_SERVER['SERVER_NAME']){
				//Printing error
				$errorDesc = $dl->getLocalizedString("linkAccountError-4");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}
			//Getting account stuff
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName LIMIT 1");
			$query->execute([':userName' => $userhere]);
			$accountID = $query->fetchColumn();
			$query = $db->prepare("SELECT userID FROM users WHERE extID = :extID LIMIT 1");
			$query->execute([':extID' => $accountID]);
			$userID = $query->fetchColumn();
			$targetAccountID = explode(",",$result)[0];
			$targetUserID = explode(",",$result)[1];
			//Checking if already linked
			$query = $db->prepare("SELECT count(*) FROM links WHERE targetAccountID = :targetAccountID LIMIT 1");
			$query->execute([':targetAccountID' => $targetAccountID]);
			if($query->fetchColumn() != 0){
				//Printing error
				$errorDesc = $dl->getLocalizedString("linkAccountError-5");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}
			//Checking if are numeric
			if(!is_numeric($targetAccountID) OR !is_numeric($accountID)){
				//Printing error
				$errorDesc = $dl->getLocalizedString("linkAccountError-6");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
			}
			$server = $parsedurl["host"];
			//Linking account
			$query = $db->prepare("INSERT INTO links (accountID, targetAccountID, server, timestamp, userID, targetUserID)
											 VALUES (:accountID,:targetAccountID,:server,:timestamp,:userID,:targetUserID)");
			$query->execute([':accountID' => $accountID, ':targetAccountID' => $targetAccountID, ':server' => $server, ':timestamp' => time(), 'userID' => $userID, 'targetUserID' => $targetUserID]);
			//Printing box
			$dl->printBox("<h1>".$dl->getLocalizedString("linkAccount")."</h1>
			<p>".$dl->getLocalizedString("accountLinked")."</p>","account");
		}
	}else{
		//Printing error
		$errorDesc = $dl->getLocalizedString("linkAccountError-7");
				exit($dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount")."</h1>
								<p>$errorDesc</p>
								<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","account"));
	}
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("linkAccount").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="changeUsernameUserName" name="userhere" placeholder="'.$dl->getLocalizedString("linkAccountLocalUserNameFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="changeUsernameNewUserName" name="passhere" placeholder="'.$dl->getLocalizedString("linkAccountLocalPasswordFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="changeUsernamePassword" name="usertarg" placeholder="'.$dl->getLocalizedString("linkAccountTargetUserNamePlaceholder").'"><br>
						<input type="password" class="form-control" id="changeUsernameNewUserName" name="passtarg" placeholder="'.$dl->getLocalizedString("linkAccountTargetPasswordFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="changeUsernamePassword" name="server" value="http://www.boomlings.com/database/accounts/loginGJAccount.php placeholder="'.$dl->getLocalizedString("linkAccountTargetURLFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("linkBTN").'</button>
				</form>',"account");
}
?>