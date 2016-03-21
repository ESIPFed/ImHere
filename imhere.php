<?php 
  # ------------------------------------------------------------
  # In an attempt to follow what's going on in this code, I need to add a bunch of formatting and comments. DK.
  # ------------------------------------------------------------
  
  # include needed functions
  include 'checkin.php'; 
  include 'readCSV.php';
  include 'attendeeLog.php';
  include 'currentSessions.php';

  # ------------------------------------------------------------
  # Define variables
  
  # log file and schedule
  $log = './logs/imhere_log.txt';
  $schedule = './logs/schedule.csv';

  # timezone and time
  date_default_timezone_set('UTC');
  $date = date("m-d-Y_h:i:s");

    # ------------------------------------------------------------
# html setup
  echo "<!DOCTYPE html>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>Session Check In</title>\n";
  echo "  </head>\n";
  echo "  <body>\n";

  # return heading
  $space = "&nbsp;";
  $tab = $space . $space . $space . $space . $space;
  $heading = "<p class=\"center\">ESIP Winter Meeting 2016</p>\n";
  $sessionIn = "<p style=\"font-style:italic\">Not Checked-In To A Session</p>";
  $checkInHead = "<p style=\"font-weight:bold\">You Are Currently Checked-In To:</p>";

  # ------------------------------------------------------------

  # check for GET variables
  if ( isset($_GET['name']) && isset($_GET['email']) ) {
     $name = $_GET['name'];
     $email = $_GET['email'];
     $log_line = "$date,$name,$email";
  } else { 

     # no GET variables try looking for the esip cookie
     if (isset($_COOKIE["esip"])) {
       $cvalue = $_COOKIE["esip"];
       $parts = explode(":", $cvalue);
       $name = $parts[0];
       $email = $parts[1];
     }

  }

  # ------------------------------------------------------------
  # Display sponsor logo
  echo "<img class=\"img\" src=\"images/sloan_logo.png\"><br/>";

  # ------------------------------------------------------------
  # Display application name
  echo "<p class=\"center\">ImHere Check In System</p>";

  # ------------------------------------------------------------
  # Display event name
  echo $heading;

  # ------------------------------------------------------------
  # if we have name and email then print them along with the sessions
  if ( isset($name) && isset($email) ) {

     # try to set a cookie for this user to expire in 7 days
     if ( !isset($_COOKIE["esip"]) ) {
       $cookie_name = "esip";
       $cookie_value = "$name:$email";
       setcookie($cookie_name, $cookie_value, time()+(86400*7), "/"); 
     }

     # has this person checked into the conference?
     $checkedIn = isCheckedIn($name, $email);

     if ( $checkedIn ) {

       $sessions = readCSV($schedule);
       $cSessions = getCurrentSessions($sessions);

       #echo "<p>Checking In: $name</p>\n";
       echo "$checkInHead";

       # check if this person is checked in to a session
       $line = getAttendeesByEmail($email);
       if ( $line != '' ) {
         $lineParts = explode(",", $line);
         $currentSession = $lineParts[2];
         $currentStatus = $lineParts[3];
         if ( $currentStatus ) {
            $checkout = "<a href=\"attendees.php?name=$name&email=$email&session=$currentSession&check=out\">Check Out</a>";
            echo "<p>$currentSession $tab $checkout</p>";
         } else {
            echo "$sessionIn";
         }
       } else { echo "$sessionIn"; }

       echo "<br /><p style=\"font-weight:bold\">Currently Running Sessions:</p>\n";
       $counter = 1;
       foreach($cSessions as $s) {
      
         $checkin = "<a href=\"attendees.php?name=$name&email=$email&session=$s&check=in\">Check In</a>";
         #$checkout = "<a href=\"attendees.php?name=$name&email=$email&session=$s&check=out\">Check Out</a>";
         $participants = "<a href=\"attendees.php?name=$name&email=$email&session=$s\">List Attendees</a>";

         echo "<p>$counter. $s <br/> $space $space $checkin $tab $participants</p>\n";
         $counter++;

       }
       echo "<br/>\n";
       echo "<p style=\"font-weight:bold\">Other Actions<p>\n";
       $url = "updateCheckIn.php?name=$name&email=$email&checkin=0&locate=0";
       echo "<p><a href=\"$url\">Check Out Of This Event</a></p>\n";
     } else {
       $url = "updateCheckIn.php?name=$name&email=$email&checkin=1";
       echo "<p>$name you are not yet checked into the event.</p><br/>\n";
       echo "<br/><p><a href=\"$url&locate=1\">Check me in and allow others to locate me at this event.</a></p><br/>\n";
       echo "<br/><p><a href=\"$url&locate=0\">Check me in, but do not allow others to locate me at this event.</a></p>\n";
     }

     # write results to file
     $fh = fopen($log, 'a') or die("can't open file");
     fwrite($fh, "$log_line\n");
     fclose($fh);

  } else {
     $action = htmlspecialchars($_SERVER["PHP_SELF"]);
     echo "<form method=\"GET\" action=\"$action\">\n";
     echo "  Name: <input type=\"text\" name=\"name\" >\n";
     echo "$tab Email: <input type=\"text\" name=\"email\" >\n";  
     echo "$tab <input type=\"submit\">\n";
     echo "</form>\n";
  }

  # close out the html
  echo "  </body>\n";
  echo "</html>\n";
  
?>
