<?php 

/*	Get here from imhere.php...
		One of three routines can be performed once we're here:
		If $check='in' - Check attendee in to a session; check them out of whatever they were in before
		If $check='out' - Check attendee out of a session
		If $check='' - List attendees in a session
	We're also (going to) post to the Recommendation System here
*/

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
  if ( isset($_GET['recommendation_interface']) ) { $recommendation_interface = $_GET['recommendation_interface']; } else { $recommendation_interface = 'Not_Supposed_to_Happen'; }

  # open the attendee log file
# Should watch for errors here. If permissions on the file have changed (like when I download to my Mac) we're don't get any warning.
  $log = fopen($attendees_log, 'a');

  # return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";

#---------------------------------------------------------------------------------------------------
  # check in
  if ( $check == 'in' ) { 

    # determine if this person is already checked into a session
    # if yes then automatically check them out

    # check if this person is checked in to a session
    $line = getAttendeesByEmail($email, $attendees_log); # Determine the session this attendee is currently checked in to 
    if ( $line != '' ) {
      $lineParts = explode(",", $line);
      $currentSession = $lineParts[2];
      $currentStatus = $lineParts[3];
      if ( $currentStatus ) { # If currently checked in to a session, check them out
        $line = $name . ',' . $email . ',' . $currentSession . ',0,' . $date . "\n";
        fwrite($log, $line);

# Post check out of recommendation system here:
	if ( $recommendation_interface ) {
	# Do it here	
		}
      }
    }

# Post check in to recommendation system here:
	if ( $recommendation_interface ) {
	# Do it here	
		}

    echo "<p>You have been Checked In to: $session</p>";
    echo "$returnLink";
    $line = "$name,$email,$session,1,$date\n";
    fwrite($log, $line); # Check them in to this session

  }

#---------------------------------------------------------------------------------------------------
  # check out
  if ( $check == 'out' ) {

# Post check out of recommendation system here:
	if ( $recommendation_interface ) {
	# Do it here	
		}

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

    $attendees = getAttendees($session, $attendees_log);
    echo "<p>Currently Checked-In Attendees</p>";

    # For each attendee in the session...
    foreach ($attendees as $key => $value) { 
       # split value into email 
       $pp = explode(":", $value);
       $email = $pp[0];
       $value = $pp[1];

       # find out if this person is discoverable
       $discover = isDiscoverable($key, $email); #in checkIn.php
       $parts = explode(":", $discover);
       $discover = $parts[2];
       # if discover is 1 then the person is ok to list
       # if discover is 0 then the person is not discoverable by others
  
       if ($discover) {

# Check for ResearchBit interface flag; If YES pull info from there; else get it from RegOnline interests export
	if ( $recommendation_interface ) {
           $line = "<p><a href=\"viewProfile.php?name=$key&email=$email&event=$event\">$key</a></p>";
		}
	
	else { # Look for this person's interests in the RegOnline export data

#		echo "Attendees.php event_logs = $event_logs<br>"; # For debug purposes
         $interests = getInterests($key,$email,$event_logs); # in topics.php
         if ( sizeof($interests) > 0 ) { 
#		echo "<br>Attendees.php: Ready to go...<br>"; # For debug purposes
           $line = "<p><a href=\"profile.php?name=$key&email=$email&event_logs=$event_logs\">$key</a></p>";
         } else {
           $line = "<p>$key</p>";
         }
		} # End no recommendation interface
         if ( $value == 1 ) { echo $line; }
       } # End if discover...
    } # End for each...

    echo "<br/>$returnLink";

  }

#---------------------------------------------------------------------------------------------------
  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
