<?php
session_start();
//Requesting files
include "../../incl/lib/connection.php";
require_once "../../incl/lib/exploitPatch.php";
require_once "../incl/dashboardLib.php";
$dl = new dashboardLib();
$ep = new exploitPatch();
if(isset($_GET["h"]) && $_GET["h"] != "" && isset($_GET["e"]) && $_GET["e"] != ""){
	//Here im getting all the data
    $hash = $ep->remove(base64_decode($_GET["h"]));
    $email = $ep->remove(base64_decode($_GET["e"]));
    //Checking if hash and email exist
    $query2 = $db->prepare("SELECT count(*) FROM accounts WHERE hash = :hash AND email = :email");
	$query2->execute([':hash' => $hash, ':email' => $email]);
    $regusrs = $query2->fetchColumn();
    if($regusrs > 0){
        //Activating account
        $query = $db->prepare("UPDATE `accounts` SET `active` = 1 WHERE hash = :hash AND active = 0");
        $query->execute([':hash' => $hash]);
        //Printing box
        $dl->printBox("<h1>".$dl->getLocalizedString("activate")."</h1>
							<p>".$dl->getLocalizedString("activated")."</p>","login");
	}else{
        //Printing error
        $errorDesc = $dl->getLocalizedString("activateError-2");
        exit($dl->printBox('<h1>'.$dl->getLocalizedString("activate")."</h1>
                        <p>$errorDesc</p>
                        <a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","login"));
    }
}else{
    //Printing error
    $errorDesc = $dl->getLocalizedString("activateError-1");
    exit($dl->printBox('<h1>'.$dl->getLocalizedString("activate")."</h1>
                    <p>$errorDesc</p>
                    <a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","login"));
}
?>