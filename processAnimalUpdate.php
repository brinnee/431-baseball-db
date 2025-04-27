<?php
require_once('config.php');
// create short variable names
$animalID       = (int) $_POST['animalID'];
$exhibit        = trim( preg_replace("/\t|\R/",' ',$_POST['exhibit'])    );
$age            = trim( preg_replace("/\t|\R/",' ',$_POST['age'])      );
$weight         = trim( preg_replace("/\t|\R/",' ',$_POST['weight'])     );
$feeding_time   = trim( preg_replace("/\t|\R/",' ',$_POST['feeding_time'])   );
$food           = trim( preg_replace("/\t|\R/",' ',$_POST['food'])   );

$cols = [];
$vals = [];
$types = '';
if( !empty($exhibit)) {
    $cols[] = 'exhibit = ?';
    $vals[] = $exhibit;
    $types .= 's';
}
if( !empty($age)) {
    $cols[] = 'age = ?';
    $vals[] = $age;
    $types .= 'i';
}
if( !empty($weight)) {
    $cols[] = 'weight = ?';
    $vals[] = $weight;
    $types .= 'i';
}
if( !empty($feeding_time)) {
    $cols[] = 'feeding_time = ?';
    $vals[] = $feeding_time;
    $types .= 's';
}
if( !empty($food)) {
    $cols[] = 'food_type = ?';
    $vals[] = $food;
    $types .= 's';
}



if( $animalID != 0 ) // Verify required fields are present
{
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
    $vals[] = $animalID;
    $types .= 'i';
    $query = "UPDATE animals SET ". implode(', ', $cols). " WHERE animalID = ?";
    $stmt = $db -> prepare($query);
    $stmt -> bind_param($types,...$vals);
    @$stmt -> execute();
  }
}

require('editanimals.php');
?>
