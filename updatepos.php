<?php

$team_id = (int)$_GET['team_id'];
if (isset($_POST['cancel'])) {
    header("Location: viewteam.php?team_id=$team_id"  );
    exit;
}

// Continue processing form if it's not a cancel action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['cancel'])) {
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
        foreach ($_POST['position'] as $playerID => $position) {
            // Prepare and execute the query to update the position
            $query = "UPDATE members SET position = ? WHERE playerID = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("si", $position, $playerID);
            $stmt->execute();
        }
    }
    header("Location: viewteam.php?team_id=$team_id"  );
    exit;

}
?>