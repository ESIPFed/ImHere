<?php 

/*	Get here from imhere.php...
		List attendees at this event
		Sorted by last name
		Dump all attendees for now
		May include a search routine eventually
*/

  # include the config file
  include 'config.php';

  # include the needed functions
  include 'checkin.php';
  include 'attendeeLog.php';
  include 'readCSV.php';
  include 'topics.php';

# set some display variables
  $space = "&nbsp;";
  $tab = $space . $space . $space . $space . $space;

  # setup html
  echo "<!DOCTYPE>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>ImHere List Event Attendees</title>";
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

#---------------------------------------------------------------------------------------------------

#  echo "<p>$event<br>Attendees checked in to this event:</p>";

echo "<p class=\"center\" style=\"font-weight:bold; color:#b30000\">$event<br>Attendees Checked in to This Event</p>";
echo "<p>";



  # Set return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";

#----------------------------------------------------------------

/*
Read every line in checkedIn.txt
Match event name to $event
Build an array, containing only the last (most current) line for each attendee (email), and prepended with attendee last name
*/

$result = array();
$handle = fopen($checkedIn_log, 'r') or die("In listAttendees.php, can't open file: $checkedIn.log");
while (($line = fgets($handle)) !== false) {
	$line = trim($line);
	$parts = explode(":", $line);
	$logName = $parts[0];
	$logEmail = $parts[1];
	$checkInFlag = $parts[2];
	$discoverableFlag = $parts[3];
	$logEvent = $parts[4];
	if ($logEvent==$event) {

		$qn = explode(" ", $logName);
		$lastName = $qn[1];
		$result[$logEmail] = $lastName . ":" . $line; 
		}
	}
fclose($handle);

#----------------------------------------------------------------

# Sort the array (by last name, since we prepended it)
asort($result);

#----------------------------------------------------------------

/* For each person in the array, 
	see if they're still checked in;
	see if they're checked in publicly;
	see if we're pulling profile info from ResearchBit, or from registration.csv (RegOnline)
*/
echo "<p>";
foreach ($result as $key => $value) {
#	echo "$value<br>"; # For debug purposes
	$parts = explode(":", $value);
	$queryName = $parts[1];
	$queryEmail = $parts[2];
	$InOutFlag = $parts[3];
	$discover = $parts[4];

	# If discover is 1 then the person is ok to list; if discover 0 then not discoverable by others
	if ($InOutFlag) { # If still checked in...
		if ($discover) { # If checked in "Publicly"...
			if ( $recommendation_interface ) {
				$line = "<a href=\"viewProfile_ResearchBit.php?name=$name&email=$email&event=$event&queryName=$queryName&queryEmail=$queryEmail\">$queryName</a>";
				} else { # Look for this person's interests in the RegOnline export data

/* This is from when we tried to pull "interests" from the RegOnline export...
				$interests = getInterests($queryName,$queryEmail,$event_logs); # in topics.php
				if ( sizeof($interests) > 0 ) { 
					$line = "<a href=\"profile.php?name=$queryName&email=$queryEmail&event_logs=$event_logs\">$queryName</a>";
					} else { $line = "$queryName"; }
*/
# Here's what we're doing now...
       $line = "<p><a href=\"viewProfile.php?name=$queryName&email=$queryEmail&event=$event\">$queryName</a>";

				} # End of else no reccomendation interface
			echo "$line<br>$tab($queryEmail)<br>";
	       } # End of if discover...
		} # End of if still checked in...
    } # End for each...
echo "</p>";
echo "$returnLink";


#---------------------------------------------------------------------------------------------------
  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
