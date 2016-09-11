<?php 

/*	Get here from imhere.php..., Also now from all_sessions.php.
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
  if ( isset($_GET['prePost']) ) { $prePost = $_GET['prePost']; } else { $prePost = -999; }
  if ( isset($_GET['listAll']) ) { $listAll = 1; } else { $listAll = 0; }
  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['ORCIDiD']) ) { $ORCIDiD = $_GET['ORCIDiD']; } else { $ORCIDiD = ''; }
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
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event&ORCIDiD=$ORCIDiD\">Return to Check-In Menu</a></p>";

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
        $line = $name . ',' . $email . ',' . $currentSession . ',0,' . $date . ',0,' . $ORCIDiD . "\n";
        fwrite($log, $line);

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

    $line = "$name,$email,$session,1,$date,0,$ORCIDiD\n";
    if ( $prePost != -999 ) { # If we're coming from all_session.php, where prePost varialbe was set...
      if ($prePost == -1) { $value = 0; } # check in to past event, in/out set to zero
      if ($prePost == 1) { $value = 0; } # check in to future event, in/out set to zero
      if ($prePost == 0) { $value = 1; } # checking in to a current event
      $line = "$name,$email,$session,$value,$date,$prePost,$ORCIDiD\n";
    }
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

    $line = "$name,$email,$session,0,$date,0,$ORCIDiD\n";
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
    $attendees = getAttendees($session, $attendees_log); # In attendeeLog.php
	if ($listAll) { echo "<p>$session...<br>Attendees:</p>"; }
	else { echo "<p>$session...<br>Currently Checked-In Attendees:</p>"; }

    # For each attendee in the session...
    foreach ($attendees as $key => $value) { 
       # split value into email 
       $pp = explode(":", $value);
       $queryEmail = $pp[0];
       $value = $pp[1]; #In/Out status
       $queryORCIDiD = $pp[2];
       # find out if this person is discoverable
       $discover = isDiscoverable($key, $queryEmail); #in checkin.php
       $parts = explode(":", $discover);
       $discover = $parts[2];

       # if discover is 1 then the person is ok to list; if discover 0 then not discoverable by others
       if ($discover) {

		# Check for ResearchBit interface flag; If YES pull info from there; else get it from RegOnline interests export
		if ( $recommendation_interface ) {
           $line = "<p><a href=\"viewProfile_ResearchBit.php?name=$name&email=$email&event=$event&queryName=$key&queryEmail=$queryEmail\">$key</a></p>";
			}
			else { # Not interfacing with ResearchBit

/* Put this in if we're linking to ORCID through veiwPROFILE.PHP:
*/
				$line = "<p><a href=\"viewProfile.php?name=$name&email=$email&ORCIDiD=$ORCIDiD&queryName=$key&queryEmail=$queryEmail&queryORCIDiD=$queryORCIDiD&event=$event\">$key</a>";

/* Put this in if we want to go directly to the ORCHID profile display and bypass viewPrifile.php:
				if (!$queryORCIDiD) { # If no ORCID iD present, view registration.csv info
		        $line = "<p><a href=\"viewProfile.php?name=$name&email=$email&ORCIDiD=$ORCIDiD&queryName=$key&queryEmail=$queryEmail&event=$event\">$key</a></p>";
					}
				else { # Load ORCID profile viewer
				$aaa = 'http://orcid.org/' . $queryORCIDiD;
				$bbb = "$aaa . target=\"_blank\"";
				$line = "<p><a href=$bbb>$key</a></p>"; # Opens in new browser tab
					}
*/
				} # End Not interfacing with RB

         if ( ($value == 1) || ($listAll == 1)  ) { echo $line; }

       } # End if discover...

    } # End for each...

    echo "<br/>$returnLink";

  }

#---------------------------------------------------------------------------------------------------
  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
