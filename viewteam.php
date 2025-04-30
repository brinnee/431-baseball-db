<?php
    require_once('config.php');
    require_once('playerinfo.php');
    require_once('playerstats.php');

    // Connect to database
    require_once( 'Adaptation.php' );
    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if( $db->connect_errno != 0) {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
    }
    else {// Connection succeeded 
        $team_id = isset($_GET['team_id']) ? (int)$_GET['team_id'] : 0;

        $query = "SELECT p.playerID, p.name, p.age, p.position, p.dob, p.Street, p.City, p.State, p.Country, p.ZipCode, s.Games_played,. s.Plate_appearance,. s.Runs_Scored,. s.Hits,. s.Home_runs, t.team_name, t.city FROM players p LEFT JOIN statistics s ON p.playerID = s.ID LEFT JOIN team t ON p.team_id = t.ID WHERE p.team_id=?;";

        $stmt = $db->prepare($query);
        $stmt -> bind_param('i',$team_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($playerID, $name, $age, $position, $dob, $street, $city, $state, $country, $zip, $games_played, $plate_appeareances, $runs_scored, $hits, $home_runs, $team, $team_city);
    }
    $positions = ['BENCHED','P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH',];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Team Viewer</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php 
    $stmt -> fetch();
    echo "<h1>". $team_city.' '. $team.' '."Team Roster</h1>"
    ?>

    
    <h2>Team Roster: </h2>
    <form method="POST" action="updatepos.php?team_id=<?php echo $team_id;?>">
        <table>
         <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Date of Birth</th>
            <th>Position</th>
            <th>Address</th>
            <th>Games Played</th>
            <th>Plate Appeareances</th>
            <th>Runs Scored</th>
            <th>Hits</th>
            <th>Home Runs</th>
            <th>Team</th>
         </tr>
          <?php
            function renderCell($value) {
              // $style = 'style="border:1px solid black; border-collapse:collapse;"';
          
              if (is_null($value)) {
                  echo '<td style="background:rgb(135, 135, 135);"></td>';
              } else {
                  echo '<td>' . htmlspecialchars($value) . '</td>';
              }
            }
            // $fmt_style = 'style="vertical-align:top; border:1px solid black;"';
            $stmt->data_seek(0);
            while( $stmt->fetch() )
            {
                // Emit table row data, directly output not null values
                echo "<tr>";
                echo "<td>$playerID";
                echo "<td>$name";
                renderCell($age);
                renderCell($dob);

                echo "<td>";
                echo "<select name='position[$playerID]'>";
                foreach ($positions as $position_option) {
                    $selected = ($position == $position_option) ? "selected" : "";
                    echo "<option value='$position_option' $selected>$position_option</option>";
                }
                echo "</select>";
                echo "</td>";

                echo "<td>address";
                echo "<td>$games_played";
                renderCell($plate_appeareances);
                renderCell($runs_scored);
                renderCell($hits);
                renderCell($home_runs);
                echo "<td>$team";
                
                
            }
           ?>
        </table>
        
        <div style="text-align:right; margin-right:100px; margin-top:25px;">
            <button type="submit" style="padding: 12px 24px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 6px; cursor: pointer;"> Update </button>

            <button type="submit" name="cancel" value="1" style="padding: 12px 24px; font-size: 16px; background-color: #f44336; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;"> Cancel </button>
        </div>
    </form>
    <h2>Upcoming Matches: </h2>
    <div>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Opponent</th>
          <th>Location</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>2025-05-03</td>
          <td>New York Yankees</td>
          <td>Fenway Park</td>
          <td>7:05 PM</td>
        </tr>
        <tr>
          <td>2025-05-05</td>
          <td>Toronto Blue Jays</td>
          <td>Rogers Centre</td>
          <td>1:10 PM</td>
        </tr>
      </tbody>
    </table>
    </div>

</body>
</html>
