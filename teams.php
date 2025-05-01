<?php
require_once('config.php');
require_once('adaptation.php');

// Connect to the database
@$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

if( $db->connect_errno != 0)
      {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
      }
else {
    // Fetch all teams
    $query = "SELECT * FROM TEAM WHERE ID > 0";
    $stmt = $db->prepare($query);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($ID, $team_name, $city);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teams List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Teams List</h1>

<a href="logout.php" style="margin-left:100px">Logout</a>
<table>
    <thead>
        <tr>
            <th>Team ID</th>
            <th>Team Name</th>
            <th>City</th>
        </tr>
    </thead>
    <tbody>
        <?php 
         $stmt -> data_seek(0);
         while($stmt-> fetch()){
            echo "<tr>";
            echo "<td>".$ID;
            echo "<td><a href='viewteam.php?team_id=" . urlencode($ID) . "'>" . htmlspecialchars($team_name) . "</a></td>";
            echo "<td>".$city;
         }

        ?>
    </tbody>
</table>

</body>
</html>