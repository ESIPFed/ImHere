<?php 

  # include the config file
  include 'config.php';

  # include the needed functions
  include 'checkin.php';
  include 'attendeeLog.php';
  include 'readCSV.php';
  include 'topics.php';

  # setup html
  echo "<!DOCTYPE>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>ImHere Attendees</title>";
  echo "  </head>\n";
  echo "  <body>\n";

  # look for GET variables
  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['session']) ) { $session = $_GET['session']; } else { $session = ''; }
  if ( isset($_GET['check']) ) { $check = $_GET['check']; } else { $check = ''; }

  # open the attendee log file
  $log = fopen($attendees_log, 'a');

  # return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email\">Return to Check-In Menu</a></p>";

#---------------------------------------------------------------------------------------------------
  # check in
  if ( $check == 'in' ) { 

    # determine if this person is already checked into a session
    # if yes then automatically check them out

    # check if this person is checked in to a session
    $line = getAttendeesByEmail($email);
    if ( $line != '' ) {
      $lineParts = explode(",", $line);
      $currentSession = $lineParts[2];
      $currentStatus = $lineParts[3];
      if ( $currentStatus ) {
        $line = $name . ',' . $email . ',' . $currentSession . ',0,' . $date . "\n";
        fwrite($log, $line);
      }
    }

    echo "<p>You have been Checked In to: $session</p>";
    echo "$returnLink";
    $line = "$name,$email,$session,1,$date\n";
    fwrite($log, $line);

  }

#---------------------------------------------------------------------------------------------------
  # check out
  if ( $check == 'out' ) {

    echo "<p>You have been Checked Out of: $session</p>";
    echo "$returnLink";
    $line = "$name,$email,$session,0,$date\n";
    fwrite($log, $line);

  }

#---------------------------------------------------------------------------------------------------
  # close the log file

  fclose($log);

#---------------------------------------------------------------------------------------------------
  # list attendees in a session
  if ( $check == '' ) {

    $attendees = getAttendees($session);
    echo "<p>Currently Checked-In Attendees</p>";
    foreach ($attendees as $key => $value) { 

       # split value into email 
       $pp = explode(":", $value);
       $email = $pp[0];
       $value = $pp[1];

       # find out if this person is discoverable
       $discover = isDiscoverable($key, $email);
       $parts = explode(":", $discover);
       $discover = $parts[2];

       # if discover is 1 then the person is ok to list
       # if discover is 0 then the person is not discoverable by others
  
       if ($discover) {
       
         # look for this person's interests
         $interests = getInterests($key,$email);
         if ( sizeof($interests) > 0 ) { 
           $line = "<p><a href=\"profile.php?name=$key&email=$email\">$key</a></p>";
         } else {
           $line = "<p>$key</p>";
         }
         if ( $value == 1 ) { echo $line; } 
   
       }

    }
    echo "<br/>$returnLink";

  }

#---------------------------------------------------------------------------------------------------
  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
