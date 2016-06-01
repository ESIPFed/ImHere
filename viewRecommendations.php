<?php 

/*	This routine called from...
		imhere.php - For users to see recommendations from ResearchBit
*/

  include 'config.php';

  # setup html
  echo "<!DOCTYPE>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>View Recommendations</title>";
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

  # return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";


$nameParts = explode(" ",$name);
$firstName = $nameParts[0];
$lastName = $nameParts[1];

$curl_handle=curl_init();

$abc = "http://54.165.138.137:5000/r/get/?event_id=$recommendation_interface&lastname=$lastName&firstname=$firstName&email=$email";
#echo "$abc<br>"; # For debug purposes

curl_setopt($curl_handle,CURLOPT_URL,$abc);
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);

echo "<p>$event...<br>Recommended Collaborators:</p>";

if (empty($buffer))
{echo "No recommendations.";}
else {
	echo "$buffer<br>";

/*
# We need to...
# Build an array named $attendees, as name,email,:,status

	# How it's done elsewhere:
    $attendees = getAttendees($session, $attendees_log); # Returns an array of session attendees as name,email,:,status

	# How we'll do it:
	# Load buffer contents into an array named $attendees, as name,email,:,status





# Then...
    foreach ($attendees as $key => $value) { 
       # split value into email 
       $pp = explode(":", $value);
       $email = $pp[0];
       $value = $pp[1];

       # find out if this person is discoverable
       $discover = isDiscoverable($key, $email); #in checkin.php
       $parts = explode(":", $discover);
       $discover = $parts[2];

       # if discover is 1 then the person is ok to list; if discover 0 then not discoverable by others
       if ($discover) {
	       $line = "<p>$key <a href=\"viewProfile.php?name=$key&email=$email&event=$event\">View Profile</a></p>";
	       if ( $value == 1 ) { echo $line; }
       } # End if discover...
    } # End for each...

*/
 } # End of Else not an empty buffer...




echo "<br/>$returnLink";

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
