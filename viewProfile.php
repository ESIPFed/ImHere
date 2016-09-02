<?php 

/*	New routine created 08/17/16 to display profile info when not interfacing with ResearchBit

		This routine called from...
			imhere.php - For users to view their own profile info
			attendees.php - For users to view the profile of others

*/

  include 'config.php';
  include 'readCSV.php';

  $space = "&nbsp;";

  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['event']) ) { $event = $_GET['event']; } else { $event = ''; }
  if ( isset($_GET['ORCIDiD']) ) { $ORCIDiD = $_GET['ORCIDiD']; } else { $ORCIDiD = ''; }

  $returnLink = "<p>$space<a href=\"imhere.php?name=$name&email=$email&event=$event&ORCIDiD=$ORCIDiD\">Return to Check-In Menu</a></p>";


echo "<!DOCTYPE html>\n";
echo "  <html>\n";
echo "    <head>\n";
echo "      <title>Display Attendee Profile</title>\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "      <style>\n";
echo "         .bold { font-weight: bold; }\n";
###### For mobile devices (small screens) 
echo "         @media screen and (max-width: 980px) {\n";
echo "            td { font-size: 30px; }\n";
echo "         }\n";
######
echo "      </style>\n";
echo "    </head>\n";
echo "    <body style=\"background-color:darkseagreen;\">\n";
echo "		<h3><br/>$space Registration Information for $name<br/><br/><h4>";

#-------------------------------------------------------------------------------------------------
# Pull $event_logs and the ORCID Interface flag from event_list.csv
# Read the file, find the line that matches $event
     $handle = fopen($event_list,"r");
       if ($handle) {
         while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $line_event = $parts[2];
         if ( ($line_event == $event) ) { 
         	$event_logs = $parts[3];
         	$ORCID = $parts[7]; } # ORCID Interface flag
       }
       fclose($handle);
     } else { die("Couldn't open file: $event_list"); }

#-------------------------------------------------------------------------------------------------
# Display registration information from the registration.csv file

  $file = $log_dir . $event_logs . '/' . 'registration.csv';
  $results = readCSV( $file ); # Build an array of all attendees from registration.csv
  foreach( $results as $line ) { # For each person in the registration.csv file
	$firstName = ($line[0]);
	$lastName = ($line[2]);
	$lineName = ($firstName . ' ' . $lastName);
    $lineName = strtolower($lineName);
    $lineEmail = trim($line[13]);
    $person = strtolower($name);
    if ( ($lineName == $person) && ($lineEmail == $email) ) { # If this is the right person...
		echo "$space Name: $name<br>";
		echo "$space Email: $email<br>";
		$data = ($line[4]);
		echo "$space Title: $data<br>";
		$data = ($line[5]);
		echo "$space Organization: $data<br>";
		$data = ($line[6]);
		echo "$space Org Type: $data<br>";
#		$data = ($line[7]);
#		echo "$space Address 1: $data<br>";
#		$data = ($line[8]);
#		echo "$space Address 2: $data<br>";
#		$data = ($line[9]);
#		echo "$space City: $data<br>";
#		$data = ($line[10]);
#		echo "$space State: $data<br>";
#		$data = ($line[11]);
#		echo "$space Zip: $data<br>";
		$data = ($line[12]);
		echo "$space Country: $data<br>";
		$data = ($line[14]);
		echo "$space Email 2: $data<br>";
		$data = ($line[15]);
		echo "$space Twitter: $data<br>";

    }  
  }

#-------------------------------------------------------------------------------------------------
# If event is interfacing with ORCID, and this person has an ORCID account...

#if ($ORCID == "Y" and $ORCIDiD != "") {
#echo "$space ORCID iD = $ORCIDiD<br><br>";
#$url = 'http://orcid.org/' . $ORCIDiD;
#echo "$space <a href=\"$url\" target=\"_blank\">View ORCID Profile</a></p>\n"; # Opens in new browser tab
#}

echo "<h4><br/>$returnLink<br><br></h4>";

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
