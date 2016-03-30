<?php

   # check in log format
   # name:email:discoverable by others:checked out

   function isDiscoverable($name, $email) {

     include 'config.php';

     $result = '';
     $handle = fopen($checkIn_log, "r");
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

   function isCheckedIn($name, $email) {

     $result = 0;
     $line = readCheckInLog($name, $email);
     if ( $line != '' ) { 
       $parts = explode(":", $line);
       $checkedIn = $parts[3];
       if ( $checkedIn == 1 ) { $result = 1; }
     }
     return $result;
 
   }

   function readCheckInLog($name, $email) {

      include 'config.php';

      $result = '';
      $handle = fopen($checkIn_log, "r");
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
