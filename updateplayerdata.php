<?php
require_once('config.php');

$team_id = (int)$_GET['team_id'];

$pID                = trim( preg_replace("/\t|\R/",' ',$_POST['playerID']));
$age        = (int) trim( preg_replace("/\t|\R/",' ',$_POST['age']) ) ?? NUll;
$street     = trim( preg_replace("/\t|\R/",' ',$_POST['street']) ) ?? NUll;
$city       = trim( preg_replace("/\t|\R/",' ',$_POST['city']) ) ?? NUll;
$state      = trim( preg_replace("/\t|\R/",' ',$_POST['state']) ) ?? NUll;
$country    = trim( preg_replace("/\t|\R/",' ',$_POST['country']) ) ?? NUll;
$zip        = trim( preg_replace("/\t|\R/",' ',$_POST['zip']) ) ?? NUll;

$cols = [];
$vals = [];
$types = '';
if( !empty($age)) {
    $cols[] = 'age = ?';
    $vals[] = $age;
    $types .= 'i';
}
if( !empty($street)) {
    $cols[] = 'street = ?';
    $vals[] = $street;
    $types .= 's';
}
if( !empty($city)) {
    $cols[] = 'city = ?';
    $vals[] = $city;
    $types .= 's';
}
if( !empty($state)) {
    $cols[] = 'state = ?';
    $vals[] = $state;
    $types .= 's';
}
if( !empty($country)) {
    $cols[] = 'country = ?';
    $vals[] = $country;
    $types .= 's';
}
if( !empty($zip)) {
    $cols[] = 'ZipCode = ?';
    $vals[] = $zip;
    $types .= 's';
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
        $query = "UPDATE members SET ". implode(', ', $cols). " WHERE playerID = ?";
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