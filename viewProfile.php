<?php 

/*	Get here from imhere.php...
		Attendee can view their own profile info here


This needs a lot of work!!!
(It's a copy of attendees.php)

*/

  # include the config file
  include 'config.php';

  # include the needed functions
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
  echo "  <body style=\"background-color:darkseagreen;\">\n";
  echo "  <body>\n";

  # look for GET variables
  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['session']) ) { $session = $_GET['session']; } else { $session = ''; }
  if ( isset($_GET['check']) ) { $check = $_GET['check']; } else { $check = ''; }
  if ( isset($_GET['event']) ) { $event = $_GET['event']; } else { $event = ''; }
  if ( isset($_GET['event_logs']) ) { $event_logs = $_GET['event_logs']; } else { $event_logs = ''; }
  if ( isset($_GET['attendees_log']) ) { $attendees_log = $_GET['attendees_log']; } else { $attendees_log = 'Not_Supposed_to_Happen'; }

  # open the attendee log file
  $log = fopen($attendees_log, 'a');

  # return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";

#---------------------------------------------------------------------------------------------------

  
    # look for this person's interests
         $interests = getInterests($key,$email,$event_logs);
         if ( sizeof($interests) > 0 ) { 
           $line = "<p><a href=\"profile.php?name=$key&email=$email&event_logs=$event_logs\">$name</a></p>";
         } else {
           $line = "<p>$key</p>";
         }
         if ( $value == 1 ) { echo $line; } 
   

    }
    echo "<br/>$returnLink";

#---------------------------------------------------------------------------------------------------
  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
