<?php
require_once('config.php');

$team_id = (int)$_GET['team_id'];

$name       = trim( preg_replace("/\t|\R/",' ',$_POST['name']) );
$age        = (int) trim( preg_replace("/\t|\R/",' ',$_POST['age']) ) ?? NUll;
$position   = trim( preg_replace("/\t|\R/",' ',$_POST['position']) ) ?? NUll;
$dob        = trim( preg_replace("/\t|\R/",' ',$_POST['dob']) ) ?? NUll;
$street     = trim( preg_replace("/\t|\R/",' ',$_POST['street']) ) ?? NUll;
$city       = trim( preg_replace("/\t|\R/",' ',$_POST['city']) ) ?? NUll;
$state      = trim( preg_replace("/\t|\R/",' ',$_POST['state']) ) ?? NUll;
$country    = trim( preg_replace("/\t|\R/",' ',$_POST['country']) ) ?? NUll;
$zip        = trim( preg_replace("/\t|\R/",' ',$_POST['zip']) ) ?? NUll;



require_once( 'Adaptation.php' );
if( ! empty($name)  ) // Verify required fields are present
{
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
    $query = "INSERT INTO members SET
                name = ?,
                age  = ?,
                position = ?,
                dob = ?,
                team_id = ?,
                Street     = ?,
                City       = ?,
                State      = ?,
                Country    = ?,
                ZipCode    = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sississsss', $name, $age, $position, $dob, $team_id, $street, $city, $state, $country, $zipCode);
    @$stmt->execute();  // ignore errors, for now.
  }
header("Location: viewteam.php?team_id=$team_id"  );
exit;
}
?>
