<?php

   # check in log format: name:email:public(1)/private(0)flag:in(1)/out(0)status:eventID

# --------------------------------------------------------------------------------------------------- 
   function isDiscoverable($name, $email) {

     include 'config.php';

     $result = '';
     $handle = fopen($checkedIn_log, "r");
     if ($handle) {
       while (($line = fgets($handle)) !== false) {
          $line = trim($line);
          $parts = explode(":", $line);
          $logName = $parts[0];
          $logEmail = $parts[1];
          if ( ($name == $logName) && ($email == $logEmail) ) { $result = $line; }
       }
       fclose($handle);
     } else { die("Couldn't open checkedIn.txt"); }
     return $result;
 
   }

# --------------------------------------------------------------------------------------------------- 
# Find in(1)out(0) status from checkedIn.txt for this attendee
   function isCheckedIn($name, $email) {

#     $result = 0; # Default to not-checked-in
     $result = ''; # Default to not-checked-in
     $line = readCheckInLog($name, $email); # Find last line in checkedIn.txt that matches $name & $email
     if ( $line != '' ) { 
       $parts = explode(":", $line);
       $checkedIn = $parts[3];
       $event = $parts[4];
#       if ( $checkedIn == 1 and $event != '' ) { $result = 1; }
       if ( $checkedIn == 1 and $event != '' ) { $result = $event; }
     }
     return $result;
 
   }
# --------------------------------------------------------------------------------------------------- 
# Find last line in checkedIn.txt that matches $name & $email

   function readCheckInLog($name, $email) {

      include 'config.php';

      $result = '';
      $handle = fopen($checkedIn_log, "r");
#echo "checkedIn_log = $checkedIn_log<br>"; #For debug purposes
      if ($handle) {
        while (($line = fgets($handle)) !== false) {
          $line = trim($line);
          $parts = explode(":", $line);
          $logName = $parts[0];
          $logEmail = $parts[1];

          if ( ($name == $logName) && ($email == $logEmail) ) { $result = $line; } 
        }
        fclose($handle);
      } else { die("In checkin.php, couldn't open checkedIn.txt"); }
      return $result;
   } 
