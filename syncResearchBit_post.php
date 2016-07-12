<?php 

# Post check-in data to ResearchBit

echo "<p><br>Posting event check-in's to ResearchBit...</p>";

  include 'config.php';
  include 'httpPostRequests.php';

    $event = $_GET['event'];

# Get the path/name of the rb_response.txt file for logging responses

	 $recommendation_interface = "";
     $handle = fopen($event_list,"r");
       if ($handle) {
         while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $line_event = $parts[2];
         if ( ($line_event == $event) ) { 
			$rb_response = $log_dir . $parts[3] . '/rb_response.txt';
         	$recommendation_interface = $parts[6]; }
       }
       fclose($handle);
     }

# Clear the rb_response.txt file so we start with an empty file

	$fh = fopen($rb_response, 'w+') or die("In syncResearchBit_post.php, can't open file: rb_response.txt ($rb_response)");
	fclose($fh);

/*
List event attendees into an array (as in listAttendees.php)
Read every line in checkedIn.txt (Format name:email:check-in(1)/out(0)flag:public(1)/private(0)flag:eventName)
Match event name to $event
Build an array, containing only the last (most current) line for each attendee (email), and prepended with attendee last name
Array format:
	Indexed by email; array data is lastName:name:email:check-in(1)/out(0)flag:public(1)/private(0)flag:eventName
*/

$result = array();
$handle = fopen($checkedIn_log, 'r') or die("In syncResearchBit.php, can't open file: $checkedIn_log");
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

# Sort the array by last name - not necessary, but might look nice in rb_response.txt

asort($result);


# For each person in the array, if they're still checked in, post a line to ResearchBit
# Array format:
#	Indexed by email; array data is lastName:name:email:check-in(1)/out(0)flag:public(1)/private(0)flag:eventName

foreach ($result as $key => $value) {
	$parts = explode(":", $value);
	$name = $parts[1];
	$email = $parts[2];
	$InOutFlag = $parts[3];
	$discover = $parts[4];
	$newName = preg_replace('/\s/', '', $name);
	$newName = strtolower($newName);
	$postData="name=$newName&email=$email&check_in=$InOutFlag&public_tag=$discover&event_id=$recommendation_interface&event_name=$event";
	if ($InOutFlag) { # If still checked in...
		echo "$postData<br>";
		httpPost($postData, $rb_response); # Call the function httpPost, in httpPostRequests.php
		}
    } # End for each...

echo "<p><br>Done. (Check rb_response.txt for verification.)</p>";
echo "<p><a href=\"imhere.php\">Return to ImHere System Menu</a></p>";

?>
