<?php
session_start();
require_once('config.php');

// if (!isset($_SESSION['role']) || !isset($_SESSION['playerID'])) {
//     die("Unauthorized access.");
// }

$team_id = (int)$_GET['team_id'];

$pID = trim($_POST['playerID']);

// Prevents players from updating other players
if ($_SESSION['role'] === 'player' && $_SESSION['playerID'] != $pID) {
    die("Access denied: you may only update your own data.");
}

$age = (int) trim($_POST['age'] ?? NULL);
$street = trim($_POST['street'] ?? NULL);
$city = trim($_POST['city'] ?? NULL);
$state = trim($_POST['state'] ?? NULL);
$country = trim($_POST['country'] ?? NULL);
$zip = trim($_POST['zip'] ?? NULL);

$cols = [];
$vals = [];
$types = '';

if (!empty($age)) {
    $cols[] = 'age = ?';
    $vals[] = $age;
    $types .= 'i';
}
if (!empty($street)) {
    $cols[] = 'street = ?';
    $vals[] = $street;
    $types .= 's';
}
if (!empty($city)) {
    $cols[] = 'city = ?';
    $vals[] = $city;
    $types .= 's';
}
if (!empty($state)) {
    $cols[] = 'state = ?';
    $vals[] = $state;
    $types .= 's';
}
if (!empty($country)) {
    $cols[] = 'country = ?';
    $vals[] = $country;
    $types .= 's';
}
if (!empty($zip)) {
    $cols[] = 'ZipCode = ?';
    $vals[] = $zip;
    $types .= 's';
}

if ($pID > 0 && count($cols) > 0) {
    require_once('Adaptation.php');
    $db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if ($db->connect_errno == 0) {
        $vals[] = $pID;
        $types .= 'i';
        $query = "UPDATE members SET " . implode(', ', $cols) . " WHERE playerID = ?";
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