<?php

$team_id = (int)$_GET['team_id'];
if (isset($_POST['cancel'])) {
    header("Location: viewteam.php?team_id=$team_id"  );
    exit;
}

// Continue processing form if it's not a cancel action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['cancel'])) {

    
    header("Location: viewteam.php?team_id=$team_id"  );
    exit;

}
?>