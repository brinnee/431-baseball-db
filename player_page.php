<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'playerstats.php';
require_once 'playerinfo.php';
require_once 'updatestats.php';
require_once('Adaptation.php');

$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'player') {
    die("Access denied. Please <a href='login.php'>log in</a>.");
}

if ($db->connect_errno != 0) {
    echo "Error: Failed to make a MySQL connection, here is why: <br/>";
    echo "Errno: " . $db->connect_errno . "<br/>";
    echo "Error: " . $db->connect_error . "<br/>";
    exit();
}

$playerID = $_SESSION['playerID']; // Must be set at login

$getPlayerinfo = "
    SELECT 
        p.playerID,
        p.name,
        p.age,
        p.position,
        p.dob,
        p.Street,
        p.City,
        p.State,
        p.Country,
        p.ZipCode,
        t.team_name,
        s.Games_played,
        s.Plate_appearance,
        s.Runs_Scored,
        s.Hits,
        s.Home_runs
    FROM Players p
    JOIN TEAM t ON p.team_id = t.ID
    LEFT JOIN Statistics s ON p.playerID = s.Player
    WHERE p.playerID = ?
";

$stmt = $db->prepare($getPlayerinfo);
$stmt->bind_param("i", $playerID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<h1>Player Profile: " . htmlspecialchars($row['name']) . "</h1>";
    echo "<p><strong>Age:</strong> " . htmlspecialchars($row['age']) . "</p>";
    echo "<p><strong>Position:</strong> " . htmlspecialchars($row['position']) . "</p>";
    echo "<p><strong>DOB:</strong> " . htmlspecialchars($row['dob']) . "</p>";
    echo "<p><strong>Team:</strong> " . htmlspecialchars($row['team_name']) . "</p>";
    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['City'] . ', ' . $row['State']) . "</p>";

    echo "<h2>Statistics</h2>";
    echo "<ul>";
    echo "<li><strong>Games Played:</strong> " . htmlspecialchars($row['Games_played'] ?? 'N/A') . "</li>";
    echo "<li><strong>Plate Appearances:</strong> " . htmlspecialchars($row['Plate_appearance'] ?? 'N/A') . "</li>";
    echo "<li><strong>Runs Scored:</strong> " . htmlspecialchars($row['Runs_Scored'] ?? 'N/A') . "</li>";
    echo "<li><strong>Hits:</strong> " . htmlspecialchars($row['Hits'] ?? 'N/A') . "</li>";
    echo "<li><strong>Home Runs:</strong> " . htmlspecialchars($row['Home_runs'] ?? 'N/A') . "</li>";
    echo "</ul>";
} else {
    echo "<p>No player profile or stats found.</p>";
}

$stmt->close();
$db->close();
?>
