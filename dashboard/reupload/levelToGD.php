<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
require "../../incl/lib/XORCipher.php";
require_once "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
require_once "../../incl/lib/generateHash.php";
require_once "../incl/dashboardLib.php";
$generatePass = new generatePass();
$dl = new dashboardLib();
$ep = new exploitPatch();
$gh = new generateHash();
$xc = new XORCipher();
function chkarray($source){
	if($source == ""){
		$target = "0";
	}else{
		$target = $source;
	}
	return $target;
}
//Checking nothing's empty
if(!empty($_POST["userhere"]) AND !empty($_POST["passhere"]) AND !empty($_POST["usertarg"]) AND !empty($_POST["passtarg"]) AND !empty($_POST["levelID"])){
	//Getting form data
	$userhere = $ep->remove($_POST["userhere"]);
	$passhere = $ep->remove($_POST["passhere"]);
	$usertarg = $ep->remove($_POST["usertarg"]);
	$passtarg = $ep->remove($_POST["passtarg"]);
	$levelID = $ep->remove($_POST["levelID"]);
	$server = trim($_POST["server"]);
	//Checking username
	$pass = $generatePass->isValidUsrname($userhere, $passhere);
	if ($pass != 1) {
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelToGDError-4")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
	}
	//Checking if levels is owned
	$query = $db->prepare("SELECT * FROM levels WHERE levelID = :level");
	$query->execute([':level' => $levelID]);
	$levelInfo = $query->fetch();
	$userID = $levelInfo["userID"];
	$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :user");
	$query->execute([':user' => $userhere]);
	$accountID = $query->fetchColumn();
	$query = $db->prepare("SELECT userID FROM users WHERE extID = :ext");
	$query->execute([':ext' => $accountID]);
	if($query->fetchColumn() != $userID){
		//Printing error
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelToGDError-3")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
	}
	//Setting data
	$udid = "S" . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(1,9); //getting accountid
	$sid = mt_rand(111111111,999999999) . mt_rand(11111111,99999999);
	$post = ['userName' => $usertarg, 'udid' => $udid, 'password' => $passtarg, 'sID' => $sid, 'secret' => 'Wmfv3899gc9'];
	$ch = curl_init($server . "/accounts/loginGJAccount.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch);
	curl_close($ch);
	//Checking result
	if($result == "" OR $result == "-1" OR $result == "No no no"){
		if($result == ""){
			//Printing error
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelToGDError")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}else if($result == "-1"){
			//Printing error
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelToGDError-2")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}
	}
	//Checking if level ID is valid
	if(!is_numeric($levelID)){
		exit("Invalid levelID");
	}
	//Generating seed2
	$levelString = file_get_contents("../data/levels/$levelID");
	$seed2 = base64_encode($xc->cipher($gh->genSeed2noXor($levelString),41274));
	//Reuploading level to GD
	$accountID = explode(",",$result)[0];
	$gjp = base64_encode($xc->cipher($passtarg,37526));
	$post = ['gameVersion' => $levelInfo["gameVersion"], 
	'binaryVersion' => $levelInfo["binaryVersion"], 
	'gdw' => "0", 
	'accountID' => $accountID, 
	'gjp' => $gjp,
	'userName' => $usertarg,
	'levelID' => "0",
	'levelName' => $levelInfo["levelName"],
	'levelDesc' => $levelInfo["levelDesc"],
	'levelVersion' => $levelInfo["levelVersion"],
	'levelLength' => $levelInfo["levelLength"],
	'audioTrack' => $levelInfo["audioTrack"],
	'auto' => $levelInfo["auto"],
	'password' => $levelInfo["password"],
	'original' => "0",
	'twoPlayer' => $levelInfo["twoPlayer"],
	'songID' => $levelInfo["songID"],
	'objects' => $levelInfo["objects"],
	'coins' => $levelInfo["coins"],
	'requestedStars' => $levelInfo["requestedStars"],
	'unlisted' => "0",
	'wt' => "0",
	'wt2' => "3",
	'extraString' => $levelInfo["extraString"],
	'seed' => "v2R5VPi53f",
	'seed2' => $seed2,
	'levelString' => $levelString,
	'levelInfo' => $levelInfo["levelInfo"],
	'secret' => "Wmfd2893gb7"];
	$ch = curl_init($server . "/uploadGJLevel21.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch);
	curl_close($ch);
	//Checking result
	if($result == "" OR $result == "-1" OR $result == "No no no"){
		if($result == ""){
			//Printing error
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelToGDError")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}else{
			//Printing error
			exit($dl->printBox('<h1>'.$dl->getLocalizedString("levelReupload")."</h1>
				<p>".$dl->getLocalizedString("levelToGDError-1")."</p>
				<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
		}
	}
	//Printing box
	$dl->printBox("<h1>".$dl->getLocalizedString("levelReupload")."</h1>
					<p>".sprintf($dl->getLocalizedString("levelReuploaded"), $result)."</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("levelReuploadAnotherBTN")."</a>","reupload");
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("levelToGD").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="userhere" name="userhere" placeholder="'.$dl->getLocalizedString("levelToGDUserFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="passhere" name="passhere" placeholder="'.$dl->getLocalizedString("levelToGDPassFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="levelID" name="levelID" placeholder="'.$dl->getLocalizedString("levelToGDlevelIDFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="usertarg" name="usertarg" placeholder="'.$dl->getLocalizedString("levelToGDUserTargFieldPlaceholder").'"><br>
						<input type="password" class="form-control" id="passtarg" name="passtarg" placeholder="'.$dl->getLocalizedString("levelToGDPassTargFieldPlaceholder").'"><br>
						<input type="text" class="form-control" id="server" name="server" value="http://www.boomlings.com/database/" placeholder="'.$dl->getLocalizedString("levelToGDServerFieldPlaceholder").'"><br>
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("reuploadBTN").'</button>
				</form>',"reupload");
}
?>