<?php
    require_once('config.php');
      require_once('AnimalData.php');

    // Connect to database
    require_once( 'Adaptation.php' );
    @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

    if( $db->connect_errno != 0) {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
    }
    else {// Connection succeeded 
        $query = "SELECT animalID, name, care_takerID,exhibit,age,weight,feeding_time,food_type,Fname, Lname FROM animals a LEFT JOIN employee e ON a.care_takerID=e.employeeID;";

        $stmt = $db->prepare($query);
        // no query parameters to bind
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $animalID,
                            $name,
                            $care_takerID,
                            $exhibit,
                            $age,
                            $weight,
                            $feeding_time,
                            $food_type,
                            $e_Fname,
                            $e_Lname );
    }
    $teamname = "Jits";
    $positions = ['BENCHED','meat','leafy greens','P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH',];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Team Viewer</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f4f4f4;
    }
    h1 {
      text-align: center;
    }
    h2 {
        margin-left: 100px;
    }
    table {
      width: 90%;
      margin: 0 auto;
      border-collapse: collapse;
      background-color: #fff;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #2c3e50;
      color: #fff;
    }
    tr:nth-child(even) {
      background-color:rgb(209, 209, 209);
    }
  </style>
</head>
<body>

    <h1><?php echo $teamname?> Team Roster</h1>

    
    <h2>Upcoming Matches: </h2>
    <form method="POST" action="updatepos.php">
        <table>
         <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Date of Birth</th>
            <th>Position</th>
            <th>Games Played</th>
            <th>Plate Appeareances</th>
            <th>Runs Scored</th>
            <th>Hits</th>
            <th>Home Runs</th>
         </tr>
          <?php
            $fmt_style = 'style="vertical-align:top; border:1px solid black;"';
            $stmt->data_seek(0);
            $row_number = 0;
            while( $stmt->fetch() )
            {
                $animal = new Animal($name, $care_takerID, $exhibit, $age, $weight, $feeding_time, $food_type);

                // Emit table row data 
                echo "      <tr>";
                echo "      <td  $fmt_style>".$animalID;
                echo "      <td  $fmt_style>".$animal->name();
                echo "      <td  $fmt_style>".$animal->caretaker();
                echo "      <td  $fmt_style>".$animal->exhibit();
                // Dropdown for Position
                echo "      <td  $fmt_style>";
                echo "<select name='position'>";
                foreach ($positions as $option) {
                    $selected = ($option==$food_type) ? "selected" : '';
                    echo "<option value='$option' $selected>$option</option>";
                }
                echo "</select>";

                if ($age > 0)
                    echo "      <td  $fmt_style>".$animal->age();
                else 
                    echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background:rgb(158, 158, 158);\">";
                if ($weight > 0)
                echo "      <td  $fmt_style>".$animal->weight();
                else 
                    echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background:rgb(158, 158, 158);\">";
                if ($feeding_time !== '00:00')
                    echo "      <td  $fmt_style>".$animal->feeding_time();
                else 
                    echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background:rgb(158, 158, 158);\">";
                
                
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
