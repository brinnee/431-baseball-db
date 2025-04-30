<?php
require_once('config.php');

$team_id = (int)$_GET['team_id'];

$pID = $_POST['playerID'];
$games_played = $_POST['games_played'] ?? NUll;
$plate_appeareances = $_POST['plate_appeareances'] ?? NUll;
$runs_scored = $_POST['runs_scored'] ?? NUll;
$hits = $_POST['hits'] ?? NUll;
$home_runs = $_POST['home_runs'] ?? NUll;

$cols = [];
$vals = [];
$types = '';
if( !empty($games_played)) {
    $cols[] = 'Games_played = ?';
    $vals[] = $games_played;
    $types .= 'i';
}
if( !empty($plate_appeareances)) {
    $cols[] = 'Plate_appereances = ?';
    $vals[] = $age;
    $types .= 'i';
}
if( !empty($runs_scored)) {
    $cols[] = 'Runs_scored = ?';
    $vals[] = $runs_scored;
    $types .= 'i';
}
if( !empty($hits)) {
    $cols[] = 'Hits = ?';
    $vals[] = $hits;
    $types .= 'i';
}
if( !empty($home_runs)) {
    $cols[] = 'Home_runs = ?';
    $vals[] = $home_runs;
    $types .= 'i';
}

if ($pID > 0) {
    // Connect to database
    require_once( 'Adaptation.php' );
    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    // if connection was successful
    if( $db->connect_errno != 0)
    {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
    }
    else // Connection succeeded
    {
        $vals[] = $pID;
        $types .= 'i';
        $query = "UPDATE statistics SET ". implode(', ', $cols). " WHERE ID = ?";
        $stmt = $db -> prepare($query);
        $stmt -> bind_param($types,...$vals);
        @$stmt -> execute();
        $stmt -> close();
    }
    $db->close();
    
    header("Location: viewteam.php?team_id=$team_id"  );
    exit;
}

?>