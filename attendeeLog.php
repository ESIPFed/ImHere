<?php

   # attendee log format
   # name:email:session:status (1 checked in, 0 checked out):time

   function getAttendeesByEmail($email) {

     $session = '';
     $handle = fopen("./logs/attendees.txt", "r");
     if ($handle) {
       while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $logemail = $parts[1];
         if ( ($email == $logemail) ) { $session = $line; }
       }
       fclose($handle);
     } else { die("Couldn't open attendees.txt"); }
     return $session;

   }

   function getAttendees($session) {

     $result = array();
     $line = readAttendeeLog($session);
     foreach($line as $value) {
       $parts = explode(",", $value);
       $name = $parts[0];
       $email = $parts[1];
       $status = $parts[3];
       $result[$name] = $email . ':' . $status;
     }
     return $result;
 
   }

   function readAttendeeLog($s) {

      $result = array();
      $handle = fopen("./logs/attendees.txt", "r");
      if ($handle) {
        while (($line = fgets($handle)) !== false) {
          $line = trim($line);
          $parts = explode(",", $line);
          $logSession = $parts[2];

          if ( ($s == $logSession) ) { $result[] = $line; } 
        }
        fclose($handle);
      } else { die("Couldn't open attendees.txt"); }
      return $result;
   } 
