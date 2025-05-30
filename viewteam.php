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

        $query = "SELECT p.playerID, p.name, p.age, p.position, p.dob, p.Street, p.City, p.State, p.Country, p.ZipCode, s.Games_played, s.Plate_appearances, s.Runs_Scored,. s.Hits, s.Home_runs, t.team_name, t.city, t.ID
          FROM members p LEFT JOIN statistics s ON p.playerID = s.ID LEFT JOIN team t ON p.team_id = t.ID 
          WHERE p.team_id=?;";

        $stmt = $db->prepare($query);
        $stmt -> bind_param('i',$team_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($playerID, $name, $age, $position, $dob, $street, $city, $state, $country, $zip, $games_played, $plate_appearances, $runs_scored, $hits, $home_runs, $team, $team_city, $team_ids);
        
        $players_query = "SELECT m.playerID, m.name, t.ID, t.team_name FROM members m LEFT JOIN team t ON m.team_id = t.ID WHERE team_id != ?;";

        $players_stmt = $db -> prepare($players_query);
        $players_stmt -> bind_param('i',$team_id);
        $players_stmt -> execute();
        $players_stmt -> store_result();
        $players_stmt -> bind_result($p_id, $p_names, $t_ids, $t_names);

        $record_query = " SELECT 
                  SUM(CASE 
                        WHEN (home_team = ? AND home_score > away_score) OR 
                            (away_team = ? AND away_score > home_score) THEN 1 ELSE 0 
                      END) AS wins,
                  SUM(CASE 
                        WHEN (home_team = ? AND home_score < away_score) OR 
                            (away_team = ? AND away_score < home_score) THEN 1 ELSE 0 
                      END) AS losses
                FROM matches
                WHERE home_team = ? OR away_team = ?;";

        $record_stmt = $db->prepare($record_query);
        $record_stmt->bind_param("iiiiii", $team_id, $team_id, $team_id, $team_id, $team_id, $team_id);
        $record_stmt->execute();
        $record_stmt->store_result();
        $record_stmt->bind_result($wins, $losses);

        $match_query = "SELECT m.ID,t1.ID AS home_id, t2.ID AS away_id, t1.team_name AS home, t2.team_name AS away, m.home_score,m.away_score,t1.city AS hcity, t2.city AS acity, m. match_date, m.match_status 
          FROM matches m LEFT JOIN team t1 ON m.home_team=t1.ID LEFT JOIN team t2 ON m.away_team = t2.ID
          WHERE home_team = ? or away_team=?";
        $match_stmt = $db->prepare($match_query);
        $match_stmt -> bind_param('ii',$team_id,$team_id);
        $match_stmt -> execute();
        $match_stmt -> store_result();
        $match_stmt -> bind_result($matchID,$homeid,$awayid,$home,$away,$hscore,$ascore,$hcity,$acity,$matchdate,$matchstatus); 

        $team_query = "SELECT ID, team_name FROM team";
        $team_stmt = $db->prepare($team_query);
        $team_stmt -> execute();
        $team_stmt -> store_result();
        $team_stmt -> bind_result($t_id, $t_name);
      }
    $positions = ['BENCHED','P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];
    function renderCell($value) {
      // $style = 'style="border:1px solid black; border-collapse:collapse;"';
  
      if (is_null($value)) {
          echo '<td style="background:rgb(135, 135, 135);">-</td>';
      } else {
          echo '<td>' . htmlspecialchars($value) . '</td>';

      }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Team Viewer</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div style="display: flex; align-items: center; gap: 12px; justify-content:center">
    <form action="teams.php" method="get">
        <button type="submit" style="padding: 8px 16px; font-size: 14px;">Back</button>
    </form>
    <?php 
        $stmt->fetch();
        echo "<h1>" . $team_city . ' ' . $team . " Team Roster</h1>";
    ?>
</div>
    
<a href="logout.php" style="margin-left:100px">Logout</a>
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
            <th>Plate appearances</th>
            <th>Runs Scored</th>
            <th>Hits</th>
            <th>Home Runs</th>
            <th>Team</th>
         </tr>
          <?php
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
                // dropdown menu for position select
                if ($position =="COACH"){
                  renderCell($position);
                }
                else {
                  echo "<td>";
                  echo "<select name='position[$playerID]'>";
                  foreach ($positions as $position_option) {
                  
                    $selected = ($position == $position_option) ? "selected" : "";
                    echo "<option value='$position_option' $selected>$position_option</option>";
                  }
                echo "</select>";
                echo "</td>";
              }
                // combine multiple vars to create address
                if (empty($street) && empty($city) && empty($state) && empty($zip) && empty($country)){
                  echo '<td style="background:rgb(135, 135, 135);">-</td>';
                }
                else {
                  echo "<td>".$street."<br/>"
                  .$city.', '.$state.' '.$zip.'<br/>'
                  .$country."</td>\n";
                }
                renderCell($games_played);
                renderCell($plate_appearances);
                renderCell($runs_scored);
                renderCell($hits);
                renderCell($home_runs);
                echo "<td>$team";
                
                
            }
           ?>
        </table>
        <!-- buttons for updating player positions -->
        <div style="text-align:left; margin-left:100px; margin-top:25px;">
            <button type="submit" style="padding: 12px 24px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 6px; cursor: pointer;"> Update Positions </button>

            <button type="submit" name = 'cancel' style="padding: 12px 24px; font-size: 16px; background-color: #f44336; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;"> Cancel </button>
        </div>
    </form>
    <div style="display:flex">
    <!-- Statistics Update Form -->
    <div>  
    <details style="text-align:left;margin: 10px 0px 10px 100px;; background-color: rgb(157, 158, 162); border-radius: 6px">
      <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Update Player Stats</summary>
      <form action="updatestats.php?team_id=<?php echo $team_id;?>" method="post" style="margin-top: 10px; margin-left:10px; margin-right:10px">
        <label for ="playerID">Player:</label>
          <select name="playerID" required style= "height: 22px; font-size: 14px;">
            <option value="" selected disabled hidden>Choose Player Here</option>
            <?php
              $stmt->data_seek(0);
              while( $stmt->fetch() )
              {
                echo "<option value=\"$playerID\">".$name.', ID: '.$playerID."</option>\n";
              }
            ?>
          </select><br><br>

        <label for="games_played">Games Played:</label>
        <input type="text" name="games_played" id="games_played" maxlength="4" size = "5"><br><br>

        <label for="runs_scored">Plate appearances:</label>
        <input type="text" name="plate_appearances" id="plate_appearances" maxlength="4" size = "5"><br><br>

        <label for="runs_scored">Runs Scored:</label>
        <input type="text" name="runs_scored" id="runs_scored" maxlength="3" size = "4"><br><br>

        <label for="hits">Hits:</label>
        <input type="text" name="hits" id="hits" maxlength="3" size = "4"><br><br>

        <label for="home_runs">Home Runs:</label>
        <input type="text" name="home_runs" id="home_runs" maxlength="3" size = "4"><br><br>

        <button style="margin-bottom: 10px;" type="submit">Submit Stats</button>
      </form>
    </details>
    </div>
    <!-- Add Player Form-->
    <div>  
    <details style="text-align:left; margin: 10px 0px 10px 15px; background-color: rgb(157, 158, 162); border-radius: 6px">
      <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Add Player To Team</summary>
      <form action="addplayer.php?team_id=<?php echo $team_id;?>" method="post" style="margin-top:    10px; margin-left:10px; margin-right:10px">
            
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" maxlength="50" size= "15"><br><br>

        <label for="age">Age:</label>
        <input type="text" name="age" id="age" size= "3" maxlength="2" ><br><br>

        <label for="position">Position:</label>
        <input type="text" name="position" id="position" maxlength="10" size= "7"><br><br>

        <label for="dob">Date of Birth:</label>
        <input type="text" name="dob" id="dob" maxlength="10" size= "10"><br>
        <label for="dob"> (YYYY-MM-DD)</label><br><br>

        <label for="street">Street:</label>
        <input type="text" name="street" id="street" maxlength="100" size= "15"><br><br>

        <label for="city">City:</label>
        <input type="text" name="city" id="city" maxlength="100" size= "15"><br><br>

        <label for="state">State:</label>
        <input type="text" name="state" id="state" maxlength="100" size= "15"><br><br>
        
        <label for="country">Country:</label>
        <input type="text" name="country" id="country" maxlength="100" size= "15"><br><br>
        
        <label for="zip">Zip Code:</label>
        <input type="text" name="zip" id="zip" maxlength="100" size= "10"><br><br>

        <button style="margin-bottom: 10px;" type="submit">Add Player</button>
      </form>
    </details>
    </div>
    <!-- Remove Player Form-->
    <div>  
    <details style="text-align:left;margin: 10px 0px 10px 15px; background-color: rgb(157, 158, 162); border-radius: 6px">
      <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Remove Player From Team</summary>
      <form action="removeplayer.php?team_id=<?php echo $team_id;?>" method="post"       onsubmit="return confirm('Are you sure you want to remove this player from the team?');"
        style="margin-top:    10px; margin-left:10px; margin-right:10px">
            
        <label for ="playerID">Player:</label>
        <select name="playerID" required style= "height: 22px; font-size: 14px;">
          <option value="" selected disabled hidden>Choose Player Here</option>
          <?php
            $stmt->data_seek(0);
            while( $stmt->fetch() )
            {
              echo "<option value=\"$playerID\">".$name.', ID: '.$playerID."</option>\n";
            }
          ?>
        </select><br><br>
        <button style="margin-bottom: 10px;" type="submit">Remove Player</button>
      </form>
    </details>
            </div>
    <!-- Edit Player Form-->
    <div>  
    <details style="text-align:left;margin: 10px 0px 10px 15px; background-color: rgb(157, 158, 162); border-radius: 6px">
      <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Edit Player Info</summary>
      <form action="updateplayerdata.php?team_id=<?php echo $team_id;?>" method="post" style="margin-top:    10px; margin-left:10px; margin-right:10px">
            
        <label for ="playerID">Player:</label>
        <select name="playerID" required style= "height: 22px; font-size: 14px;">
          <option value="" selected disabled hidden>Choose Player Here</option>
          <?php
            $stmt->data_seek(0);
            while( $stmt->fetch() )
            {
              echo "<option value=\"$playerID\">".$name.', ID: '.$playerID."</option>\n";
            }
          ?>
        </select><br><br>

        <label for="age">Age:</label>
        <input type="text" name="age" id="age" size= "3" maxlength="2" ><br><br>

        <label for="street">Street:</label>
        <input type="text" name="street" id="street" maxlength="100" size= "15"><br><br>

        <label for="city">City:</label>
        <input type="text" name="city" id="city" maxlength="100" size= "15"><br><br>

        <label for="state">State:</label>
        <input type="text" name="state" id="state" maxlength="100" size= "15"><br><br>
        
        <label for="country">Country:</label>
        <input type="text" name="country" id="country" maxlength="100" size= "15"><br><br>
        
        <label for="zip">Zip Code:</label>
        <input type="text" name="zip" id="zip" maxlength="100" size= "10"><br><br>

        <button style="margin-bottom: 10px;" type="submit">Change Player Data</button>
      </form>
    </details>
            </div>
    <!-- Transfer Player Form-->
    <div>  
    <details style="text-align:left;margin: 10px 0px 10px 15px; background-color: rgb(157, 158, 162); border-radius: 6px">
      <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Trade Player</summary>
      <form action="transferplayer.php?team_id=<?php echo $team_id;?>" method="post" style="margin-top:    10px; margin-left:10px; margin-right:10px">
            
        <label for ="pIDsend">Send:</label>
        <select name="pIDsend" required style= "height: 22px; font-size: 14px;">
          <option value="" selected disabled hidden>Select Player Here</option>
          <?php
            $stmt->data_seek(0);
            while( $stmt->fetch() )
            {
              echo "<option value=\"{$playerID}|{$team_ids}\">".$name.', ID: '.$playerID.', Team: '.$team."</option>\n";
            }
          ?>
        </select><br><br>

        <label for ="pIDreceive">Receive:</label>
        <select name="pIDreceive" required style= "height: 22px; font-size: 14px;">
          <option value="" selected disabled hidden>Select Player Here</option>
          <?php
            $players_stmt->data_seek(0);
            while( $players_stmt->fetch() )
            {
              echo "<option value=\"{$p_id}|{$t_ids}\">".$p_names.', ID: '.$p_id.', Team: '.$t_names."</option>\n";
            }
          ?>
        </select><br><br>

        <button style="margin-bottom: 10px;" type="submit">Transfer Player</button>
      </form>
    </details>
            </div>
    <!-- Compare Player Form-->
    <div>  
    <details style="text-align:left;margin: 10px 0px 10px 15px; background-color: rgb(157, 158, 162); border-radius: 6px">
      <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Compare Players</summary>
      <form action="compareplayers.php?team_id=<?php echo $team_id;?>" method="post" style="margin-top:    10px; margin-left:10px; margin-right:10px">
            
        <label for ="pID1">Player 1:</label>
        <select name="pID1" required style= "height: 22px; font-size: 14px;">
          <option value="" selected disabled hidden>Select Player Here</option>
          <?php
            $stmt->data_seek(0);
            while( $stmt->fetch() )
            {
              echo "<option value=\"{$playerID}\">".$name.', ID: '.$playerID.', Team: '.$team."</option>\n";
            }
          ?>
        </select><br><br>

        <label for ="pID2">Player 2:</label>
        <select name="pID2" required style= "height: 22px; font-size: 14px;">
          <option value="" selected disabled hidden>Select Player Here</option>
          <?php
            $players_stmt->data_seek(0);
            while( $players_stmt->fetch() )
            {
              echo "<option value=\"{$p_id}\">".$p_names.', ID: '.$p_id.', Team: '.$t_names."</option>\n";
            }
          ?>
        </select><br><br>

        <button style="margin-bottom: 10px;" type="submit">Compare Players</button>
      </form>
    </details>
            </div>
    </div>
    <div style="display:flex; justify-content: space-between; align-items: center; margin-right:100px">
    <?php
      $record_stmt->data_seek(0);
      while( $record_stmt->fetch() )
      {
        // $record_stmt->fetch();
        echo "<h2>Matches: </h2>";
        echo "<h2>Record: W $wins - $losses L";
      }
      ?>
    </div>
    <div>
    <table>
      <thead>
        <tr>
          <th>Match ID</th>
          <th>Opponent</th>
          <th>Home/Away</th>
          <th>Location</th>
          <th>Match Date</th>
          <th>Score (H-A)</th>
          <th>Status</th>
          <th>Result</th>
        </tr>
      </thead>
      <tbody><?php
            // $fmt_style = 'style="vertical-align:top; border:1px solid black;"';
            $match_stmt->data_seek(0);
            while( $match_stmt->fetch() )
            {
                // Emit table row data, directly output not null values
                echo "<tr>";
                echo "<td>$matchID";
                // display opponent and location based on home/away
                if ($homeid == $team_id) {
                  echo "<td>$away";
                  echo "<td>HOME";
                  echo "<td>$hcity";
                }
                else {
                  echo "<td>$home";
                  echo "<td>AWAY";
                  echo "<td>$hcity";
                }

                echo "<td>$matchdate";
                echo "<td>$hscore - $ascore";
                echo "<td>$matchstatus";
                // display victory or defeat depending on score
                if ((($hscore > $ascore && $homeid == $team_id) || ($hscore < $ascore && $homeid != $team_id))){
                  echo "<td style=\"background:rgb(75, 241, 91);\">W";
                }
                elseif ($hscore==$ascore){
                  echo "<td style=\"background:rgb(121, 121, 121);\">-";
                }
                else {
                  echo "<td style=\"background:rgb(222, 51, 51);\">L";
                }

            }
           ?>
      </tbody>
    </table>
    </div>
    <div style="display:flex">
    <!-- Match Statistics Update Form -->
    <div>  
      <details style="text-align:left; margin: 15px 0px 10px 100px;; background-color: rgb(157, 158, 162); border-radius: 6px">
        <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Update Match Statistics</summary>
        <form action="editmatch.php?team_id=<?php echo $team_id;?>" method="post" style="margin-top: 10px; margin-left:10px; margin-right:10px">
        <label for ="matchID">Match:</label>
          <select name="matchID" required style= "height: 22px; font-size: 14px;">
            <option value="" selected disabled hidden>Choose Match Here</option>
            <?php
              $match_stmt->data_seek(1);
              while( $match_stmt->fetch() )
              {
                echo "<option value=\"$matchID\">".$home.' vs '.$away.', ID: '.$matchID."</option>\n";
              }
            ?>
          </select><br><br>

        <label for="home_score">Home Score:</label>
        <input type="text" name="home_score" id="hscore" maxlength="2" size = "2"><br><br>

        <label for="away_score">Away Score:</label>
        <input type="text" name="away_score" id="ascore" maxlength="2" size = "2"><br><br>

        <label for="match_status">Match Status:</label>
        <input type="text" name="match_status" id="match_status" maxlength="50" size = "14"><br><br>

        <button name="edit" style="margin-bottom: 10px;" type="submit">Submit Stats</button>
      </form>
      </details>
    </div>
    <div>  
      <details style="text-align:left; margin: 15px 0px 10px 15px; background-color: rgb(157, 158, 162); border-radius: 6px">
        <summary style=" padding: 12px 32px; font-size: 16px; background-color:rgb(54, 82, 244); color: white; border: none; border-radius: 6px; cursor: pointer; width: auto;"> Add Match</summary>
        <form action="editmatch.php?team_id=<?php echo $team_id;?>" method="post" style="margin-top: 10px; margin-left:10px; margin-right:10px">
        <label for ="home">Home Team:</label>
          <select name="home" required style= "height: 22px; font-size: 14px;">
            <option value="home" selected disabled hidden>Choose Home Team Here</option>
            <?php
              $team_stmt->data_seek(1);
              while( $team_stmt->fetch() )
              {
                echo "<option value=\"$t_id\">".$t_name.', ID: '.$t_id."</option>\n";
              }
            ?>
          </select><br><br>

        <label for ="away">Away Team:</label>
          <select name="away" required style= "height: 22px; font-size: 14px;">
            <option value="away" selected disabled hidden>Choose Away Team Here</option>
            <?php
              $team_stmt->data_seek(0);
              while( $team_stmt->fetch() )
              {
                echo "<option value=\"$t_id\">".$t_name.', ID: '.$t_id."</option>\n";
              }
            ?>
          </select><br><br>

        <label for="home_score">Home Score:</label>
        <input type="text" name="home_score" id="hscore" maxlength="2" size = "2"><br><br>

        <label for="away_score">Away Score:</label>
        <input type="text" name="away_score" id="ascore" maxlength="2" size = "2"><br><br>
        
        <label for="match_date">Match Date:</label>
        <input type="text" name="match_date" id="match_date" maxlength="10" size = "12"><br><br>

        <label for="match_status">Match Status:</label>
        <input type="text" name="match_status" id="match_status" maxlength="50" size = "14"><br><br>

        <button style="margin-bottom: 10px;" type="submit">Submit Stats</button>
      </form>    
      </details>
    </div>
</body>
</html>
