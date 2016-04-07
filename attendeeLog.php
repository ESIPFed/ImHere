<?php

# DK changes 3/31: Include config.php in a couple of places; set relative rather than static addresses in fopen commands

   # attendee log format
   # name:email:session:status (1 checked in, 0 checked out):time

#----------------------------------------------------------------
# Find last line in attendees.txt that matches email address; return line in $session
   function getAttendeesByEmail($email) {

# include the config file
  include 'config.php';
	
     $session = '';
     $handle = fopen($attendees_log, "r");
     if ($handle) {
       while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $logemail = $parts[1];
         if ( ($email == $logemail) ) { $session = $line; }
       }
       fclose($handle);
     } else { die("Couldn't open file: $attendees_log"); }
     return $session;

   }

#----------------------------------------------------------------
# Build an array of session attendees as name,email,:,status
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

 #----------------------------------------------------------------
 # Find all lines in attendees.txt that match $s (the name of a session); return lines in an array
  function readAttendeeLog($s) {

# include the config file
  include 'config.php';
	
      $result = array();

     $handle = fopen($attendees_log, "r");
      if ($handle) {
        while (($line = fgets($handle)) !== false) {
          $line = trim($line);
          $parts = explode(",", $line);
          $logSession = $parts[2];

          if ( ($s == $logSession) ) { $result[] = $line; } 
        }
        fclose($handle);
      } else { die("Couldn't open file... $attendees_log"); }
      return $result;
   } 
