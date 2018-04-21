<?php
session_start();
//Requesting files
include "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
//Checking if language exists
if(isset($_GET["lang"]) AND ctype_alpha($_GET["lang"])){
	//Changing language
	setcookie("lang", strtoupper($_GET["lang"]), time() - 3600, "/a/dashboard");
	setcookie("lang", strtoupper($_GET["lang"]), time() - 3600, "/a/dashboard/lang");
	setcookie("lang", strtoupper($_GET["lang"]), 2147483647, "/");
	if(isset($_SERVER["HTTP_REFERER"])){
		header('Location: ' . $_SERVER["HTTP_REFERER"]);
	}
	//Printing box
	$dl->printBox("<p>Language changed. <a href='index.php'>Click here to continue</a></p>");
}else{
	//Printing error
	$dl->printBox("Invalid language. <a href='..'>Click here to continue</a></p>");
}
?>