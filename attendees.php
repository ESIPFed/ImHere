<?php 

/*	Get here from imhere.php...
		One of three routines can be performed once we're here:
		If $check='in' - Check attendee in to a session; check them out of whatever they were in before
		If $check='out' - Check attendee out of a session
		If $check='' - List attendees in a session
	We're also posting to the Recommendation System here
*/

  # include the config file
  include 'config.php';

  # include the needed functions
  include 'checkin.php';
  include 'attendeeLog.php';
  include 'readCSV.php';
  include 'topics.php';
  include 'httpPostRequests.php';

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
  if ( isset($_GET['recommendation_interface']) ) { $recommendation_interface = $_GET['recommendation_interface']; } else { $recommendation_interface = ''; }
  if ( isset($_GET['session_id']) ) { $session_id = $_GET['session_id']; } else { $session_id = ''; }

  # open the attendee log file
  $log = fopen($attendees_log, 'a') or die("In attendees.php, can't open file: $attendees_log");

  # return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";

#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
  # Session check in
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

		#------------------------------------------
		# Post check out to recommendation system:
/* 
		Supposedly, we don't need to do this since we're checking right back in to another session
			if ( $recommendation_interface ) {
			$discover = isDiscoverable($name, $Email); #in checkin.php, get the public/private status
			$parts = explode(":", $discover);
			$discover = $parts[2];
			$newName = preg_replace('/\s/', '', $name);
			$newName = strtolower($newName);
			$postData="name=$newName&email=$email&check_in=1&public_tag=$discover&event_id=$recommendation_interface";
			$rb_response = $log_dir . $event_logs . '/rb_response.txt'; # Name of event-specific log file
			httpPost($postData, $rb_response); # Call the function (in httpPostRequests.php)
		} # End of Recommendation System check out post
		#------------------------------------------
*/
      }
    }

	#------------------------------------------
	# Post session check in to recommendation system:
	
	if ( $recommendation_interface ) {
		$discover = isDiscoverable($name, $email); #in checkin.php, get the public/private status
		$parts = explode(":", $discover);
		$discover = $parts[2];
		$newName = preg_replace('/\s/', '', $name);
		$newName = strtolower($newName);
		$postData="name=$newName&email=$email&check_in=1&public_tag=$discover&event_id=$recommendation_interface&session_id=$session_id&session_name=$session";
		$rb_response = $log_dir . $event_logs . '/rb_response.txt'; # Name of event-specific log file
		httpPost($postData, $rb_response); # Call the function (in httpPostRequests.php)
	} # End of Recommendation System check in post
	#------------------------------------------

    $line = "$name,$email,$session,1,$date\n";
    fwrite($log, $line); # Check them in to this session
    echo "<p>You have been Checked In to: $session</p>";
    echo "$returnLink";

  }

#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
  # Session check out
  if ( $check == 'out' ) {

	#------------------------------------------
	# Post session check out to recommendation system:
	# SESSION check-outs are simply an EVENT check-in, with no session ID arguement
	if ( $recommendation_interface ) {
		$discover = isDiscoverable($name, $email); #in checkin.php, get the public/private status
		$parts = explode(":", $discover);
		$discover = $parts[2];
		$newName = preg_replace('/\s/', '', $name);
		$newName = strtolower($newName);
		$postData="name=$newName&email=$email&check_in=1&public_tag=$discover&event_id=$recommendation_interface&event_name=$event";
		$rb_response = $log_dir . $event_logs . '/rb_response.txt'; # Name of event-specific log file
		httpPost($postData, $rb_response); # Call the function (in httpPostRequests.php)
	} # End of Recommendation System check out post
	#------------------------------------------

    $line = "$name,$email,$session,0,$date\n";
    fwrite($log, $line);
    echo "<p>You have been Checked Out of: $session</p>";
    echo "$returnLink";

  }

#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
  # close the log file

  fclose($log);

#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
#---------------------------------------------------------------------------------------------------
  # List attendees in a session
  if ( $check == '' ) {

    $attendees = getAttendees($session, $attendees_log);
    echo "<p>$session...<br>Currently Checked-In Attendees:</p>";

    # For each attendee in the session...
    foreach ($attendees as $key => $value) { 
       # split value into email 
       $pp = explode(":", $value);
       $queryEmail = $pp[0];
       $value = $pp[1];

       # find out if this person is discoverable
       $discover = isDiscoverable($key, $queryEmail); #in checkin.php
       $parts = explode(":", $discover);
       $discover = $parts[2];

       # if discover is 1 then the person is ok to list; if discover 0 then not discoverable by others
       if ($discover) {

# Check for ResearchBit interface flag; If YES pull info from there; else get it from RegOnline interests export
	if ( $recommendation_interface ) {
           $line = "<p><a href=\"viewProfile.php?name=$name&email=$email&event=$event&queryName=$key&queryEmail=$queryEmail\">$key</a></p>";
		}
	
	else { # Look for this person's interests in the RegOnline export data

#		echo "Attendees.php event_logs = $event_logs<br>"; # For debug purposes
         $interests = getInterests($key,$queryEmail,$event_logs); # in topics.php
         if ( sizeof($interests) > 0 ) { 
#		echo "<br>Attendees.php: Ready to go...<br>"; # For debug purposes
           $line = "<p><a href=\"profile.php?name=$key&email=$queryEmail&event_logs=$event_logs\">$key</a></p>";
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
