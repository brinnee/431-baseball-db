<?php
session_start();
require_once('Adaptation.php');

$team_id = (int)$_GET['team_id'];


list($idsend, $teamsend) = explode('|', $_POST['pIDsend']);
list($idreceive, $teamreceive) = explode('|', $_POST['pIDreceive']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno == 0) {
        $sendquery = "UPDATE members SET team_id = ? WHERE playerID = ?";
        $sendstmt = $db->prepare($sendquery);
        $sendstmt->bind_param("ii", $teamreceive, $idsend);
        $sendstmt->execute();
        $sendstmt->close();

        $receivequery = "UPDATE members SET team_id = ? WHERE playerID = ?";
        $receivestmt = $db->prepare($receivequery);
        $receivestmt->bind_param("ii", $teamsend, $idreceive);
        $receivestmt->execute();
        $receivestmt->close();
    }
    $db->close();
    header("Location: viewteam.php?team_id=$team_id");
    exit;
}
?>