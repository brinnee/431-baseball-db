<?php
session_start();
require_once('config.php');

$team_id = (int)$_GET['team_id'];
$pID = trim($_POST['playerID']);


require_once('Adaptation.php');
$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

if ($db->connect_errno == 0) {
    $query = "UPDATE members SET team_id = 0 WHERE playerID = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i',$pID);
    $stmt->execute();
    $stmt->close();
}
$db->close();

header("Location: viewteam.php?team_id=$team_id");
exit;
?>