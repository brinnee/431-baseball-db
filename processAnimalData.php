<?php
require_once('config.php');

// create short variable names
$name     = trim( preg_replace("/\t|\R/",' ',$_POST['name']) );
$caretaker      = trim( preg_replace("/\t|\R/",' ',$_POST['caretaker'])  );
$exhibit        = trim( preg_replace("/\t|\R/",' ',$_POST['exhibit'])    );
$age          = trim( preg_replace("/\t|\R/",' ',$_POST['age'])      );
$weight         = trim( preg_replace("/\t|\R/",' ',$_POST['weight'])     );
$feeding_time       = trim( preg_replace("/\t|\R/",' ',$_POST['feeding_time'])   ).':00';
$food       = trim( preg_replace("/\t|\R/",' ',$_POST['food'])   );

if( empty($name) ) $name = null;
if( empty($caretaker)  ) $caretaker  = null;
if( empty($exhibit)    ) $exhibit    = null;
if( empty($age)      ) $age      = null;
if( empty($weight)     ) $weight     = null;
if( empty($feeding_time)   ) $feeding_time   = null;
if( empty($food)   ) $food   = null;


if( ! empty($name) ) // Verify required fields are present
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
    $query = "INSERT INTO animals SET
                name = ?,
                care_takerID  = ?,
                exhibit     = ?,
                age       = ?,
                weight      = ?,
                feeding_time    = ?,
                food_type    = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sssssss', $name, $caretaker, $exhibit, $age, $weight, $feeding_time, $food);

    @$stmt->execute();  // ignore errors, for now.
  }
}

require('editanimals.php');
?>
