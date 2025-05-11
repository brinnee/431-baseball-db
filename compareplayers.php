<?php
session_start();
require_once('Adaptation.php');

$team_id = (int)$_GET['team_id'];


$pID1 = trim($_POST['pID1']) ?? NULL;
$pID2 = trim($_POST['pID2']) ?? NULL;

require_once( 'Adaptation.php' );
@$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

if ($db->connect_errno != 0) {
    echo "Error: Failed to make a MySQL connection, here is why: <br/>";
    echo "Errno: " . $db->connect_errno . "<br/>";
    echo "Error: " . $db->connect_error . "<br/>";
    $p1query = "SELECT m.name, m.age, m.position, s.Games_played, s.Plate_appearances, s.Runs_Scored, s.Hits, s.Home_runs , t.team_name
        FROM members m LEFT JOIN statistics s ON m.playerID = s.ID LEFT JOIN team t ON m.team_id = t.ID 
        WHERE m.playerID=? OR m.playerID=?;";
    $p1stmt = $db->prepare($p1query);
    $p1stmt->bind_param("ii", $pID1, $pID2);
    $p1stmt->execute();
    $p1stmt->bind_result($name1, $age1, $position1, $games_played1, $plate_appearances1, $runs_scored1, $hits1, $home_runs1, $team1);
}
else {
    $query = "SELECT m.name, m.age, m.position, s.Games_played, s.Plate_appearances, s.Runs_Scored, s.Hits, s.Home_runs , t.team_name
        FROM members m LEFT JOIN statistics s ON m.playerID = s.ID LEFT JOIN team t ON m.team_id = t.ID 
        WHERE m.playerID=? OR m.playerID=?;";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $pID1, $pID2);
    $stmt->execute();
    $stmt->bind_result($name, $age, $position, $games_played, $plate_appearances, $runs_scored, $hits, $home_runs, $team);

    $players = [];

    while ($stmt->fetch()) {
        $players[] = [
            'name' => $name,
            'Age' => $age,
            'Position' => $position,
            'Team' => $team,
            'Games Played' => $games_played,
            'Plate Appearances' => $plate_appearances,
            'Runs Scored' => $runs_scored,
            'Hits' => $hits,
            'Home Runs' => $home_runs
        ];
    }
    echo "<div style=\"display: flex; align-items: center; gap: 12px; justify-content:center\">";
    echo "<a href=\"viewteam.php?team_id=$team_id\">
           <button type=\"button\">Back to Team Page</button>
        </a>";
    $stmt->fetch();
    echo "<h1>Player Comparison Tool</h1></div>";

    echo "<table>";
        echo "<link rel=\"stylesheet\" href='styles.css'>";
        echo "<tr><th> {$players[0]['name']} </th><th> vs </th><th> {$players[1]['name']}</th></tr>";
        $numericFields = ['Games Played', 'Plate Appearances', 'Runs Scored', 'Hits', 'Home Runs'];
        foreach (array_slice(array_keys($players[0]), 1) as $key) {            
            $val1 = $players[0][$key];
            $val2 = $players[1][$key];
        
            // Add markers only for numeric comparisons
            if (in_array($key, $numericFields)) {
                if (is_numeric($val1) && is_numeric($val2)) {
                    if ($val1 > $val2) {
                        $val1 .= " ↑";
                        $val2 .= " ↓";
                    } elseif ($val2 > $val1) {
                        $val2 .= " ↑";
                        $val1 .= " ↓";
                    }
                }
            }
        
            echo "<tr>
                    <td>$val1</td>
                    <td>$key</td>
                    <td>$val2</td>
                  </tr>";
        }
    echo "</table>";
}



?>