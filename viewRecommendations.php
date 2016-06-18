<?php 

/*	This routine called from...
		imhere.php - For users to see recommendations from ResearchBit
*/

  include 'config.php';
# include needed functions
  include 'checkin.php'; 

# set some display variables
  $space = "&nbsp;";
  $tab = $space . $space . $space . $space . $space;

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
  if ( isset($_GET['session_id']) ) { $session_id = $_GET['session_id']; } else { $session_id = ''; }

  # return link
  $returnLink = "<p><a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";

# ---------------------------------------------------------------------------------------------------------------
# Send cURL request for recommendations to ResearchBit system...

$nameParts = explode(" ",$name);
$firstName = $nameParts[0];
$lastName = $nameParts[1];

$curl_handle=curl_init();

#$abc = "http://54.165.138.137:5000/r/get/?event_id=$recommendation_interface&lastname=$lastName&firstname=$firstName&email=$email";
if ($session_id == "") {
	$abc = "http://54.175.39.137:5000/r/get/?event_id=$recommendation_interface&lastname=$lastName&firstname=$firstName&email=$email"; }
else {
	$abc = "http://54.175.39.137:5000/r/get/?event_id=$recommendation_interface&session_id=$session_id&lastname=$lastName&firstname=$firstName&email=$email"; }
curl_setopt($curl_handle,CURLOPT_URL,$abc);
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);

#echo "<p class=\"center\" style=\"font-weight:bold; color:#b30000\">$event<br>Recommended Collaborators<br>At This Event</p>";
if ($session_id == "") {
	echo "<p class=\"center\" style=\"font-weight:bold; color:#b30000\">Recommended Collaborators<br>At This Event<br>$event</p>"; }
else {
	echo "<p class=\"center\" style=\"font-weight:bold; color:#b30000\">Recommended Collaborators<br>In This Session<br>$session</p>"; }

echo "<p style=\"font-weight:bold\">For $name:</p>";
echo "<p>";

if (empty($buffer))
	{echo "No recommendations.";}

# ---------------------------------------------------------------------------------------------------------------
# Format the response from ResearchBit...

else {
#echo "$buffer<br>";
	$aaa = explode("|",$buffer); # Builds an array of name,email
	$n = (sizeof($aaa)-1); # Last one is blank for some reason

# For each recommendation, if they checked in as public/discoverable, display name and link to profile...
	$n1=0;
	while ($n1<$n) {
		$bbb = explode (",",$aaa[$n1]); # Results in just one name & email
		$queryName=$bbb[0];
		$queryEmail=$bbb[1];
		$queryEmail=preg_replace('/\s/', '', $queryEmail); # Remove whitespace

# Find out if this person is discoverable
       $result = isDiscoverable($queryName, $queryEmail); # In checkin.php
       $parts = explode(":", $result);
       $discover = $parts[2];
       if ($discover) {
	       $url = "<a href=\"viewProfile.php?name=$name&email=$email&event=$event&queryName=$queryName&queryEmail=$queryEmail\">$queryName</a>";
		   echo "$url<br>$tab($queryEmail)<br>\n";
			}
		else { echo "$queryName<br>$tab($queryEmail)<br>\n"; } # Comment this line out for live system (Don't display the privates)
	$n1++;

	} # End of While loop

 } # End of Else not an empty buffer...

echo "</p>";

# ---------------------------------------------------------------------------------------------------------------

echo "$returnLink";

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
