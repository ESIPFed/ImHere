<?php 

/*	New routine created 08/17/16 to display profile info when not interfacing with ResearchBit

		This routine called from...
			imhere.php - For users to view their own profile info
			attendees.php - For users to view the profile of others

	First we check to see if we're interfacing with ORCID
	If NOT, then display profile data from the registration.csv file
	If YES, then ???

*/

  include 'config.php';
  include 'readCSV.php';

  # look for GET variables
  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['event']) ) { $event = $_GET['event']; } else { $event = ''; }
#  if ( isset($_GET['event_logs']) ) { $event_logs = $_GET['event_logs']; } else { $event_logs = ''; }

  # return link
  $returnLink = "<p>$space<a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";


#-------------------------------------------------------------------------------------------------
# Pull the ORCID Interface flag from event_list.csv

# Find the line that matches $event, pull the ORCID interface flag
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

# If interfacing with ORCID...

# And if this person has an ORCID account...

if ($ORCID == "Y") {

}

#-------------------------------------------------------------------------------------------------
# Else not interfacing with ORCID...

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
echo "		<h3><br/>$space Profile Information for $name<br/><br/><h4>";

$space = "&nbsp;";

echo "ORCID = $ORCID<br>";


# Find this person in the registration.csv file

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
		echo "Name: $name<br>";
		echo "Email: $email<br>";
		$data = ($line[4]);
		echo "Title: $data<br>";
		$data = ($line[5]);
		echo "Organization: $data<br>";
		$data = ($line[6]);
		echo "Org Type: $data<br>";
		$data = ($line[7]);
		echo "Address 1: $data<br>";
		$data = ($line[8]);
		echo "Address 2: $data<br>";
		$data = ($line[9]);
		echo "City: $data<br>";
		$data = ($line[10]);
		echo "State: $data<br>";
		$data = ($line[11]);
		echo "Zip: $data<br>";
		$data = ($line[12]);
		echo "Country: $data<br>";
		$data = ($line[14]);
		echo "Email 2: $data<br>";
		$data = ($line[15]);
		echo "Twitter: $data<br>";
    }  
  }



















echo "<h4><br/>$returnLink<br><br></h4>";

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
