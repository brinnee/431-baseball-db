<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['role']) || !isset($_SESSION['playerID'])) {
    die("Unauthorized access. session role and  id:");
}

$team_id = (int)$_GET['team_id'];
$pID = trim($_POST['playerID']);

// Prevents players from updating stats of others
if ($_SESSION['role'] === 'player' && $_SESSION['playerID'] != $pID) {
    die("Access denied: you may only update your own statistics.");
}

$games_played = (int) ($_POST['games_played'] ?? NULL);
$plate_appearances = (int) ($_POST['plate_appearances'] ?? NULL);
$runs_scored = (int) ($_POST['runs_scored'] ?? NULL);
$hits = (int) ($_POST['hits'] ?? NULL);
$home_runs = (int) ($_POST['home_runs'] ?? NULL);

$cols = [];
$vals = [];
$types = '';

if (!empty($games_played)) {
    $cols[] = 'Games_played = ?';
    $vals[] = $games_played;
    $types .= 'i';
}
if (!empty($plate_appearances)) {
    $cols[] = 'Plate_appearances = ?';
    $vals[] = $plate_appearances;
    $types .= 'i';
}
if (!empty($runs_scored)) {
    $cols[] = 'Runs_scored = ?';
    $vals[] = $runs_scored;
    $types .= 'i';
}
if (!empty($hits)) {
    $cols[] = 'Hits = ?';
    $vals[] = $hits;
    $types .= 'i';
}
if (!empty($home_runs)) {
    $cols[] = 'Home_runs = ?';
    $vals[] = $home_runs;
    $types .= 'i';
}

if ($pID > 0 && count($cols) > 0) {
    require_once('Adaptation.php');
    $db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno == 0) {
        $vals[] = $pID;
        $types .= 'i';
        $query = "UPDATE statistics SET " . implode(', ', $cols) . " WHERE ID = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$vals);
        $stmt->execute();
        $stmt->close();
    }
    $db->close();
}

header("Location: viewteam.php?team_id=$team_id");
exit;
?>