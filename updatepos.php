<?php
session_start();
require_once('Adaptation.php');

$team_id = (int)$_GET['team_id'];

if (isset($_POST['cancel'])) {
    header("Location: viewteam.php?team_id=$team_id");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno == 0) {
        // Player: Only allowed to update their own position
        if ($_SESSION['role'] === 'player') {
            $playerID = $_SESSION['playerID'];
            if (isset($_POST['position'][$playerID])) {
                $position = $_POST['position'][$playerID];
                $query = "UPDATE members SET position = ? WHERE playerID = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("si", $position, $playerID);
                $stmt->execute();
                $stmt->close();
            } else {
                die("Invalid player position input.");
            }
        } else {
            // Coach or manager: Allowed to update multiple players
            foreach ($_POST['position'] as $playerID => $position) {
                $query = "UPDATE members SET position = ? WHERE playerID = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("si", $position, $playerID);
                $stmt->execute();
                $stmt->close();
            }
        }
        $db->close();
    }

    header("Location: viewteam.php?team_id=$team_id");
    exit;
}
?>