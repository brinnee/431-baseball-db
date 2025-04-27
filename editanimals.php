<?php
  require_once('config.php');
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Add and Edit Animals</title>
  </head>
  <body>
    <h1 style="text-align:center">California Zoo Association Animal Information</h1>

    <?php
      // require_once('Address.php');
      require_once('AnimalData.php');

      // Connect to database
      require_once( 'Adaptation.php' );
      @$db = new mysqli(DATA_BASE_HOST, USER_NAME, USER_PASSWORD, DATA_BASE_NAME);

      if( $db->connect_errno != 0)
      {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
      }
      else // Connection succeeded
      {
        // Build query to retrieve player's name, address, and averaged statistics from the joined Team Roster and Statistics tables
//////// TO-DO:  Begin Student Region ///////////
        $query = "SELECT animalID, name, care_takerID,exhibit,age,weight,feeding_time,food_type,Fname, Lname FROM animals a LEFT JOIN employee e ON a.care_takerID=e.employeeID;";

//////// END-TO-DO:  End Student Region ///////////
        // Prepare, execute, store results, and bind results to local variables
//////// TO-DO:  Begin Student Region ///////////
        $stmt = $db->prepare($query);
        // no query parameters to bind
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($animalID,
                           $name,
                           $care_takerID,
                           $exhibit,
                           $age,
                           $weight,
                           $feeding_time,
                           $food_type,
                           $e_Fname,
                           $e_Lname);
      }
//////// END-TO-DO:  End Student Region ///////////
    ?>
    <table style="width: 100%; border:0px solid black; border-collapse:collapse;">
      <tr>
        <th style="width: 40%;">Add Animal</th>
        <th style="width: 60%;">Edit Animal</th>
      </tr>
      <tr>
        <td style="vertical-align:top; border:1px solid black;">
          <!-- FORM to enter Add Animal -->
          <form action="processAnimalData.php" method="POST">
            <table style="margin: 0px auto; border: 0px; border-collapse:separate;">
              <tr>
                <td style="text-align: right; background: lightblue;">Name</td>
                <td><input type="text" name="name" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Caretaker ID</td>
               <td><input type="text" name="caretaker" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Exhibit</td>
               <td><input type="text" name="exhibit" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Age</td>
                <td><input type="text" name="age" value="" size="10" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Weight</td>
                <td><input type="text" name="weight" value="" size="20" maxlength="100"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Feeding Time</td>
                <td><input type="text" name="feeding_time" value="" size="15" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Food Type</td>
                <td><input type="text" name="food" value="" size="10" maxlength="10"/></td>
              </tr>

              <tr>
               <td colspan="2" style="text-align: center;"><input type="submit" value="Add Animal" /></td>
              </tr>
            </table>
          </form>
        </td>

        <td style="vertical-align:top; border:1px solid black;">
          <!-- FORM to enter game statistics for a particular player -->
          <form action="processAnimaldata.php" method="post">
            <table style="margin: 0px auto; border: 0px; border-collapse:separate;">
              <tr>
                <td style="text-align: right; background: lightblue;">Name/ID</td>
<!--            <td><input type="text" name="name" value="" size="50" maxlength="500"/></td>  -->
                <td><select name="name_ID" required>
                  <option value="" selected disabled hidden>Choose animal here</option>
                  <?php
                    // for each row of data returned,
                    //   construct an Address object providing first and last name
                    //   emit an option for the pull down list such that
                    //     the displayed name is retrieved from the Address object
                    //     the value submitted is the unique ID for that player
                    // for example:
                    //     <option value="101">Duck, Daisy</option>
//////// TO-DO:  Begin Student Region ///////////
                    $stmt->data_seek(0);
                    while( $stmt->fetch() )
                    {
                      $animal = new Animal($name, $care_takerID);
                      echo "<option value=\"$Name_ID\">".$animal->name().', ID: '.$animalID."</option>\n";
                    }
//////// END-TO-DO:  End Student Region ///////////
                  ?>
                </select></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Age</td>
               <td><input type="text" name="points" value="" size="10" maxlength="3"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Weight</td>
               <td><input type="text" name="points" value="" size="20" maxlength="3"/></td>
              </tr>
              <tr>

                <td style="text-align: right; background: lightblue;">Feeding Time (Hour:Min)</td>
               <td><input type="text" name="time" value="" size="15" maxlength="5"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Food Type</td>
                <td><input type="text" name="assists" value="" size="10" maxlength="2"/></td>
              </tr>

              <tr>
               <td colspan="2" style="text-align: center;"><input type="submit" value="Edit Data" /></td>
              </tr>
            </table>
          </form>
        </td>
      </tr>
    </table>


    <h2 style="text-align:center">Animals</h2>

    <?php
      // emit the number of rows (records) in the table
//////// TO-DO:  Begin Student Region ///////////
      // echo "Number of Records:  ".$stmt->num_rows."<br/>";
      echo "Number of Records:  ".$stmt->num_rows."<br/>";

//////// END-TO-DO:  End Student Region ///////////
    ?>

    <table style="border:1px solid black; border-collapse:collapse;">
      <tr>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">ID</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Name</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Caretaker ID</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Exhibit</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Age</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Weight</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Feeding Time</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Food Type</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Caretaker Name</th>
      </tr>
      <?php
//////// TO-DO:  Begin Student Region ///////////
        $fmt_style = 'style="vertical-align:top; border:1px solid black;"';
        $stmt->data_seek(0);
        $row_number = 0;
//////// END-TO-DO:  End Student Region ///////////

//////// TO-DO:  Begin Student Region ///////////
        while( $stmt->fetch() )
        {
//////// END-TO-DO:  End Student Region ///////////
          // construct Address and PlayerStatistic objects supplying as constructor parameters the retrieved database columns
//////// TO-DO:  Begin Student Region ///////////
          $animal = new Animal($name, $care_takerID, $exhibit, $age, $weight, $feeding_time, $food_type);

//////// END-TO-DO:  End Student Region ///////////
          // Emit table row data using appropriate getters from the Address and PlayerStatistic objects
//////// TO-DO:  Begin Student Region ///////////
          echo "      <tr>";
          echo "      <td  $fmt_style>".$animalID;
          echo "      <td  $fmt_style>".$animal->name();
          echo "      <td  $fmt_style>".$animal->caretaker();
          echo "      <td  $fmt_style>".$animal->exhibit();
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
          if (!is_null($food_type))
            echo "      <td  $fmt_style>".$animal->food()."\n";
          else 
            echo "        <td  style=\"border:1px solid black; border-collapse:collapse; background:rgb(158, 158, 158);\">\n";
            echo "        <td  $fmt_style>".$e_Lname.', '.$e_Fname;
        }
//////// END-TO-DO:  End Student Region ///////////
      ?>
    </table>

  </body>
</html>
