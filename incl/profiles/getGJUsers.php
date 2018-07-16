<?php
//Requesting files
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
//Getting data
$str = $ep->remove($_POST["str"]);
$page = $ep->remove($_POST["page"]);
if($ep->remove($_POST["secret"]) != "Wmfd2893gb7") exit("-1");
$userstring = "";
$usrpagea = $page * 10;
//Getting users data
$query = $db->prepare("SELECT * FROM users WHERE userID = :str OR userName LIKE CONCAT('%', :str, '%') ORDER BY stars DESC LIMIT 10 OFFSET $usrpagea");
$query->execute([':str' => $str]);
$result = $query->fetchAll();
//Count
$countquery = $db->prepare("SELECT count(*) FROM users WHERE userName LIKE CONCAT('%', :str, '%') OR userID = :str");
$countquery->execute([':str' => $str]);
$usercount = $countquery->fetchColumn();
if(!$usercount) exit("-1");
foreach($result as &$user) $userstring .= "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":6:".$user["diamonds"].":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$user["extID"].":3:".$user["stars"].":8:".round($user["creatorPoints"],0,PHP_ROUND_HALF_DOWN).":4:".$user["demons"]."|";
//Printning users
$userstring = substr($userstring, 0, -1);
echo $userstring;
echo "#".$usercount.":".$usrpagea.":10";
?>