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
$id = $ep->remove($_POST["accountID"]);
//Checking nothing's empty
if($id != "" AND $gjp != ""){
	//Checking GJP
	$gjpresult = $GJPCheck->check($gjp,$id);
	if($gjpresult == 1){
		//Checking moderator status
		$permState = $gs->getMaxValuePermission($id, "actionRequestMod");
		if($permState == 0){
			//Not moderator
			exit("-1");
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