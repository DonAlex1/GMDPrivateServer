<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
$dl = new dashboardLib();
$gs = new mainLib();
//Checking nothing's empty
if(!empty($_POST["url"])){
	//Getting form data
	$songID = $gs->songReupload($_POST["url"]);
	if($songID < 0){
		//Printing error
		$errorDesc = $dl->getLocalizedString("songAddError$songID");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("songAdd")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","reupload"));
	}else{
		//Printing box
		$dl->printBox("<h1>".$dl->getLocalizedString("songAdd")."</h1>
						<p>".sprintf($dl->getLocalizedString("songReuploaded"), $songID)."</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("songAddAnotherBTN")."</a>","reupload");
	}
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("songAdd").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<label for="urlField">'.$dl->getLocalizedString("songAddUrlFieldLabel").'</label>
						<input type="text" class="form-control" id="urlField" name="url" placeholder="'.$dl->getLocalizedString("songAddUrlFieldPlaceholder").'">
					</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("addBTN").'</button>
				</form>',"reupload");
}
?>