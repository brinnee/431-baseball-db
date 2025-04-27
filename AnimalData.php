<?php
class Animal
{
   // Instance attributes
   private $name         = array('FIRST'=>"", 'LAST'=>null);
   private $caretaker = 0;
   private $exhibit     = '';
   private $age  = 0;
   private $weight = 0;
   private $feeding_time = array('HOURS' =>0,  'MIN'=>0);
   private $food_type    = '';

   // Operations

   function name()
   {
    if( func_num_args() == 0 )
    {
      return $this->name;
    }

    else if( func_num_args() == 1 )
    {
      $this->name = htmlspecialchars(trim(func_get_arg(0)));
    }

    return $this;
  }


   // caretaker() prototypes:
   function caretaker()
   {
     // int caretaker()
     if( func_num_args() == 0 )
     {
       return $this->caretaker;
     }

     // void caretaker($value)
     else if( func_num_args() == 1 )
     {
       $this->caretaker = (int)func_get_arg(0);
     }

     return $this;
   }
   
   // exhibit() prototypes:
   //   exhibit()               returns the number of exhibit taken.
   //
   //   void exhibit(int $value)    set object's $exhibit attribute

   function exhibit()
   {
     if( func_num_args() == 0 )
     {
       return $this->exhibit;
     }

     else if( func_num_args() == 1 )
     {
       $this->exhibit = htmlspecialchars(trim(func_get_arg(0)));
     }

     return $this;
   }


   // age() prototypes:
   //   int age()               returns the number of points scored.
   //
   //   void age(int $value)    set object's $age attribute
   function age()
   {
     // int age()
     if( func_num_args() == 0 )
     {
       return $this->age;
     }

     // void age($value)
     else if( func_num_args() == 1 )
     {
       $this->age = (int)func_get_arg(0);
     }

     return $this;
   }

   // weight() prototypes:
   //   int weight()               returns the number of scoring weight.
   //
   //   void weight(int $value)    set object's $weight attribute
   function weight()
   {
     // int weight()
     if( func_num_args() == 0 )
     {
       return $this->weight;
     }

     // void weight($value)
     else if( func_num_args() == 1 )
     {
       $this->weight = (int)func_get_arg(0);
     }

     return $this;
   }

   // feeding_time() prototypes:
   //   string feeding_time()                          returns playing time in "minutes:seconds" format.
   //
   //   void feeding_time(string $value)               set object's $feeding_time attribute
   //                                                 in "minutes:seconds" format.
   //
   //   void feeding_time(array $value)                set object's $feeding_time attribute
   //                                                 in [minutes, seconds] format
   //
   //   void feeding_time(int $minutes, int $seconds)  set object's $feeding_time attribute
   function feeding_time()
   {
     // string feeding_time()
     if( func_num_args() == 0 )
     {
       return $this->feeding_time['HOURS'].':'.$this->feeding_time['MIN'];
     }

     // void feeding_time($value)
     else if( func_num_args() == 1 )
     {
       $value = func_get_arg(0);

       if( is_string($value) ) $value = explode(':', $value); // convert string to array
       if( is_array ($value) )
       {
         if ( count($value) >= 2 ) $this->feeding_time['MIN'] = (int)$value[1];
         else                      $this->feeding_time['MIN'] = 0;
         $this->feeding_time['HOURS'] = (int)$value[0];
       }
     }

     // void feeding_time($hours, $min)
     else if( func_num_args() == 2 )
     {
       $this->feeding_time['HOURS'] = (int)func_get_arg(0);
       $this->feeding_time['MIN'] = (int)func_get_arg(1);
     }

     return $this;
   }


   // food() prototypes:
   //   food()               returns the number of food taken.
   //
   //   void food(int $value)    set object's $food attribute

   function food()
   {
     if( func_num_args() == 0 )
     {
       return $this->food;
     }

     else if( func_num_args() == 1 )
     {
       $this->food = htmlspecialchars(trim(func_get_arg(0)));
     }

     return $this;
   }






   function __construct($name="",$caretaker=0, $exhibit='', $age=0, $weight=0, $feeding_time="0:0",  $food='')
   {
     // if $name contains at least one tab character, assume all attributes are provided in
     // a tab separated list.  Otherwise assume $name is just the player's name.
     if( is_string($name) && strpos($name, "\t") !== false) // Note, can't check for "true" because strpos() only returns the boolean value "false", never "true"
     {
       // assign each argument a value from the tab delineated string respecting relative positions
       list($name, $caretaker, $exhibit, $age, $weight,$feeding_time,$food) = explode("\t", $name);
     }

     // delegate setting attributes so validation logic is applied
     $this->name($name);
     $this->caretaker($caretaker);
     $this->exhibit($exhibit);
     $this->age($age);
     $this->weight($weight);
     $this->feeding_time($feeding_time);
     $this->food($food);
   }



   function __toString()
   {
     return (var_export($this, true));
   }


//    // Returns a tab separated value (TSV) string containing the contents of all instance attributes
//    function toTSV()
//    {
//        return implode("\t", [$this->name(), $this->feeding_time(), $this->age(), $this->weight(), $this->exhibit()]);
//    }








//    // Sets instance attributes to the contents of a string containing ordered, tab separated values
//    function fromTSV(string $tsvString)
//    {
//      // assign each argument a value from the tab delineated string respecting relative positions
//      list($name, $time, $points, $weight, $exhibit) = explode("\t", $tsvString);
//      $this->name($name);
//      $this->feeding_time($time);
//      $this->age($points);
//      $this->weight($weight);
//      $this->exhibit($exhibit);
//    }
} // end class PlayerStatistic

// ?>
