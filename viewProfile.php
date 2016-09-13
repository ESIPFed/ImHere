<?php 

/*	New routine created 08/17/16 to display profile info when not interfacing with ResearchBit

		This routine called from...
			imhere.php - For users to view their own profile info
			listAttendees.php - For users to view profile of others at the event
			attendees.php - For users to view the profile of others in this session
*/

  include 'config.php';
  include 'readCSV.php';

  $space = "&nbsp;";

  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['event']) ) { $event = $_GET['event']; } else { $event = ''; }
  if ( isset($_GET['ORCIDiD']) ) { $ORCIDiD = $_GET['ORCIDiD']; } else { $ORCIDiD = ''; }
  if ( isset($_GET['queryName']) ) { $queryName = $_GET['queryName']; } else { $queryName = ''; }
  if ( isset($_GET['queryEmail']) ) { $queryEmail = $_GET['queryEmail']; } else { $queryEmail = ''; }
  if ( isset($_GET['queryORCIDiD']) ) { $queryORCIDiD = $_GET['queryORCIDiD']; } else { $queryORCIDiD = ''; }

  $returnLink = "<p>$space<a href=\"imhere.php?name=$name&email=$email&event=$event&ORCIDiD=$ORCIDiD\">Return to Check-In Menu</a></p>";

  # setup html
  echo "<!DOCTYPE>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>Display Attendee Profile</title>";
  echo "  </head>\n";
  echo "  <body style=\"background-color:darkseagreen;\">\n";
  echo "  <body>\n";

  # Display header:
  echo "<p class=\"center\" style=\"font-weight:bold; color:#b30000\">$event<br>Attendee Registration Information</p>";
  echo "<p>";

#-------------------------------------------------------------------------------------------------
# Pull $event_logs from event_list.csv
# Read the file, find the line that matches $event
     $handle = fopen($event_list,"r");
       if ($handle) {
         while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $line_event = $parts[2];
         if ( ($line_event == $event) ) { 
         	$event_logs = $parts[3]; }
       }
       fclose($handle);
     } else { die("Couldn't open file: $event_list"); }


#-------------------------------------------------------------------------------------------------
# Display registration information from the registration.csv file

  $file = $log_dir . $event_logs . '/' . 'registration.csv';
  $results = readCSV( $file ); # Build an array of all attendees from registration.csv
  $found = 0;
  foreach( $results as $line ) { # For each person in the registration.csv file
	$firstName = ($line[0]);
	$lastName = ($line[2]);
	$lineName = ($firstName . ' ' . $lastName);
    $lineName = strtolower($lineName);
    $lineEmail = trim($line[14]);
    $person = strtolower($queryName);
	$lowerQueryEmail = strtolower($queryEmail);

    if ($lineName == $person) {
		if (($lineEmail == $queryEmail)||($lineEmail == $lowerQueryEmail)) {
		echo "$space Name: $queryName<br>";
		if ($queryEmail==$lineEmail) {
			echo "$space Email: $queryEmail<br>"; }
		else {
			echo "$space Check-In Email: $queryEmail<br>$space Registration Email: $lineEmail<br>"; }
		$data = ($line[4]);
		echo "$space Title: $data<br>";
		$data = ($line[5]);
		echo "$space Organization: $data<br>";
		$data = ($line[6]);
		echo "$space Org Type: $data<br>";
		$data = ($line[12]);
		echo "$space Country: $data<br>";
		$data = ($line[15]);
		echo "$space Email 2: $data<br>";
		$data = ($line[16]);
		echo "$space Twitter: $data<br>";
		$found = 1;
     } # End if emails match
    } # End if names match 
  } # End for each...
	if ($found == 0) {echo "$space Cannot find registration info for $queryName<br> $space ($queryEmail)"; }

#-------------------------------------------------------------------------------------------------

if ($queryORCIDiD) {
	$aaa = 'http://orcid.org/' . $queryORCIDiD;
	$bbb = "$aaa . target=\"_blank\"";
	$url = "<a href=$bbb>View ORCID profile</a>"; # Opens in new browser tab
	echo "<p> $space $url<br>";
}

echo "<br/>$returnLink<br><br>";

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
