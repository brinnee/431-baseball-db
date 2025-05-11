<?php
session_start();
require_once('Adaptation.php');

$team_id = (int)$_GET['team_id'];



if (isset($_POST['edit'])) {
    $mID    = (int) trim($_POST['matchID']);
    $hscore = (int) trim($_POST['home_score'] ?? NULL);
    $ascore = (int) trim($_POST['away_score'] ?? NULL);
    $status =       trim($_POST['match_status'] ?? NULL);

    require_once('Adaptation.php');
    $db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno == 0) {
        $query = "UPDATE matches SET home_score = ?, away_score = ?, match_status = ? WHERE ID = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("iisi", $hscore, $ascore, $status, $mID);
        $stmt->execute();
        $stmt->close();
        $db->close();
    }
    header("Location: viewteam.php?team_id=$team_id");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hID    = (int) trim($_POST['home']);
    $aID    = (int) trim($_POST['away']);
    $hscore = (int) trim($_POST['home_score'] ?? NULL);
    $ascore = (int) trim($_POST['away_score'] ?? NULL);
    $dob    =       trim( preg_replace("/\t|\R/",' ',$_POST['match_date']) ) ?? NUll;
    $status =       trim($_POST['match_status'] ?? NULL);
    $db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno == 0) {
        $query = "INSERT INTO matches SET 
            home_team = ?,
            away_team = ?,
            home_score = ?,
            away_score = ?,
            match_date = ?,
            match_status = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("iiiiss", $hID, $aID, $hscore, $ascore, $dob, $status);
        $stmt->execute();
        $stmt->close();
        $db->close();
    }

    header("Location: viewteam.php?team_id=$team_id");
    exit;
}
?>