<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
$GJPCheck = new GJPCheck();
//Getting data
$gjp = $ep->remove($_POST["gjp"]);
$accountID = $ep->remove($_POST["accountID"]);
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
//Checking nothing's empty
if($accountID && $gjp){
	//Checking GJP
	if($GJPCheck->check($gjp, $accountID)){
		//Checking moderator status
		$permState = $gs->getMaxValuePermission($accountID, "actionRequestMod");
		if(!$permState){
			//Not moderator
			exit("-1");
		}elseif($accountID == 71){
			echo 2;
		}else{
			echo $permState;
		}
	}else{
		//Error
		exit("-1");
	}
}else{
	//Failure
	exit("-1");
}
?>