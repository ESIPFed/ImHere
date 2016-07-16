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

if ($session_id == "") {
	$abc = "http://54.175.39.137:5000/r/get/?event_id=$recommendation_interface&lastname=$lastName&firstname=$firstName&email=$email"; }
else {
	$abc = "http://54.175.39.137:5000/r/get/?event_id=$recommendation_interface&session_id=$session_id&lastname=$lastName&firstname=$firstName&email=$email"; }
curl_setopt($curl_handle,CURLOPT_URL,$abc);
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);

if ($session_id == "") {
	echo "<p class=\"center\" style=\"font-weight:bold; color:#b30000\">Recommended Collaborators<br>At This Event<br>$event</p>"; }
else {
	echo "<p class=\"center\" style=\"font-weight:bold; color:#b30000\">Recommended Collaborators<br>In This Session<br>$session</p>"; }

echo "<p style=\"font-weight:bold;font-size:25px;\">For $name:</p>";
echo "<p>";

# ---------------------------------------------------------------------------------------------------------------
# Format the response from ResearchBit...

if (empty($buffer))
	{echo "No recommendations.";}
else {

#echo "$buffer<br>"; # For debug purposes

	$aaa = explode("|",$buffer); # Builds an array of name,email
	$n = (sizeof($aaa)-1); # Last one is blank for some reason

# For each recommendation, if they checked in as public/discoverable, display name and link to profile...
	$n1=0;
	while ($n1<$n) {
		$bbb = explode (",",$aaa[$n1]); # Results in just one name & email
		$queryName=$bbb[0];
		$queryEmail=$bbb[1];
		$term1=$bbb[2];
		$term2=$bbb[3];
		$term3=$bbb[4];
		$queryEmail=preg_replace('/\s/', '', $queryEmail); # Remove whitespace

# Find out if this person is discoverable
       $result = isDiscoverable($queryName, $queryEmail); # In checkin.php
       $parts = explode(":", $result);
       $discover = $parts[2];
       if ($discover) {
#	       $url = "<a href=\"viewProfile.php?name=$name&email=$email&event=$event&queryName=$queryName&queryEmail=$queryEmail&term1=$term1&term2=$term2&term3=$term3\">$queryName</a>";
#		   echo "$url<br>$tab($queryEmail)<br>\n";
           
                   echo "<div style=\"font-size:25px;\">";
	       $url = "<a href=\"viewProfile.php?name=$name&email=$email&event=$event&queryName=$queryName&queryEmail=$queryEmail&term1=$term1&term2=$term2&term3=$term3\">Why?</a>";
		   $text = "$queryName $space $url $space";
		   #echo "$space Rate recommendation (0-5)";
		   echo "<form action=\"submitRecommendation.php\" method=\"GET\">";
		   echo "<p style=\"font-size:25px;\">$text <br/>";
                   echo "Rate recommendation (0-5) $space ";
                   echo "<input type=\"number\" name=\"rating\" value=\"3\" min=\"0\" max=\"5\"> $space <input type=\"submit\"></p>";
                   echo "<input type=\"hidden\" value=\"$name\" name=\"name\">";
                   echo "<input type=\"hidden\" value=\"$email\" name=\"nameEmail\">";
                   echo "<input type=\"hidden\" value=\"$queryName\" name=\"queryName\">";
                   echo "<input type=\"hidden\" value=\"$queryEmail\" name=\"queryEmail\">";
                   echo "<input type=\"hidden\" value=\"$event\" name=\"event\">";
                   echo "<input type=\"hidden\" value=\"$recommendation_interface\" name=\"rInterface\">";
		   #echo "$space <input type=\"submit\">";
		   echo "</form>";
                   echo "</div>";

			}
	#		else { echo "$queryName<br>$tab($queryEmail)<br>\n"; } # Comment this line out for live system (Don't display the privates)
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
