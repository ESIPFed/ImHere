<?php 

# 06/06/16 - Added queryName & queryEmail to differentiate between user name/email, and that of the person we're displaying profile info for

/*	This routine called from...
		imhere.php - For users to view their own profile info
		attendees.php - For users to view the profile of others
		viewRecommendations.php - For users to view the profile of recommendations
*/

  include 'config.php';

  # setup html
  echo "<!DOCTYPE>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>Display Attendee Profile</title>";
  echo "  </head>\n";
  echo "  <body style=\"background-color:darkseagreen;\">\n";
  echo "  <body>\n";

  # look for GET variables
  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['queryName']) ) { $queryName = $_GET['queryName']; } else { $queryName = $name; }
  if ( isset($_GET['queryEmail']) ) { $queryEmail = $_GET['queryEmail']; } else { $queryEmail = $email; }
  if ( isset($_GET['session']) ) { $session = $_GET['session']; } else { $session = ''; }
  if ( isset($_GET['check']) ) { $check = $_GET['check']; } else { $check = ''; }
  if ( isset($_GET['event']) ) { $event = $_GET['event']; } else { $event = ''; }
  if ( isset($_GET['event_logs']) ) { $event_logs = $_GET['event_logs']; } else { $event_logs = ''; }
  if ( isset($_GET['attendees_log']) ) { $attendees_log = $_GET['attendees_log']; } else { $attendees_log = 'Not_Supposed_to_Happen'; }
  if ( isset($_GET['recommendation_interface']) ) { $recommendation_interface = $_GET['recommendation_interface']; } else { $recommendation_interface = 'Not_Supposed_to_Happen'; }

  # return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";


$nameParts = explode(" ",$queryName);
$firstName = $nameParts[0];
$lastName = $nameParts[1];

$curl_handle=curl_init();
curl_setopt($curl_handle,CURLOPT_URL,"http://54.165.138.137:5000/p/get/?lastname=$lastName&firstname=$firstName&email=$queryEmail");
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);

if (empty($buffer))
{print "ResearchBit returned blank profile.<p>";}
else
{print $buffer;}




echo "<br/>$returnLink";

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
