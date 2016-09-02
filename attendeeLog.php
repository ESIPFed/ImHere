<?php

   # attendee log format
   # name:email:session:status (1 checked in, 0 checked out):time:prePost flag:ORCIDId

#----------------------------------------------------------------
   function getAttendeesByEmail($email, $attendees_log) {	# Find last line in attendees.txt that matches email address; return line in $session

  include 'config.php';

     $session = '';
     $handle = fopen($attendees_log, "r");
     if ($handle) {
       while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $logemail = $parts[1];
         if ( ($logemail == $email) ) { $session = $line; }
       }
       fclose($handle);
     } else { die("In AttendedeLog.php, couldn't open file: $attendees_log"); }
     return $session;

   }

#----------------------------------------------------------------
#----------------------------------------------------------------
#----------------------------------------------------------------
   function getAttendees($session, $attendees_log) { # Build an array of session attendees as name,email,:,status

     $result = array();
     $line = readAttendeeLog($session, $attendees_log); # See function below
     foreach($line as $value) {
       $parts = explode(",", $value);
       $name = $parts[0];
       $email = $parts[1];
       $status = $parts[3];
       $ORCIDiD = $parts[6];
       $result[$name] = $email . ':' . $status . ':' . $ORCIDiD; # Add ORCIDiD to array line
     }
     return $result;
 
   }

#----------------------------------------------------------------
#----------------------------------------------------------------
#----------------------------------------------------------------
# Create an array of everyone checked into this session

  function readAttendeeLog($s, $attendees_log) { # Find all lines in attendees.txt that match $s (the name of a session); return lines in an array

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
