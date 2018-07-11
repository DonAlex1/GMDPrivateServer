<?php
session_start();
unset($_SESSION["accountID"]);
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
if(isset($_SERVER["HTTP_REFERER"])) exit(header("Location: ../"));
$dl->printLoginBox("<p>You are now logged out. <a href='..'>Click here to continue</a></p>");
?>