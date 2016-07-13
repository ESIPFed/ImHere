<?php 

# Count the number of attendees checked in to the event right now

  include 'config.php';

    $event = $_GET['event'];

/*
List event attendees into an array (as in listAttendees.php)
Read every line in checkedIn.txt (Format name:email:check-in(1)/out(0)flag:public(1)/private(0)flag:eventName)
Match event name to $event
Build an array, containing only the last (most current) line for each attendee (email), and prepended with attendee last name
Array format:
	Indexed by email; array data is lastName:name:email:check-in(1)/out(0)flag:public(1)/private(0)flag:eventName
*/

$result = array();
$handle = fopen($checkedIn_log, 'r') or die("In countCheckIns.php, can't open file: $checkedIn_log");
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

# For each person in the array, if they're still checked in, post a line to ResearchBit
# Array format:
#	Indexed by email; array data is lastName:name:email:check-in(1)/out(0)flag:public(1)/private(0)flag:eventName

$n=0;
$n1=0;
foreach ($result as $key => $value) {
	$parts = explode(":", $value);
	$InOutFlag = $parts[3];
	$discover = $parts[4];
	if ($InOutFlag) { $n=($n+1); }
		else { $n1=($n1+1); }
    } # End for each...

die ("Checked in: $n<br>Checked out: $n1");

?>
