<?php 

/*  
Export an attendance list for all sessions to a spreadsheet-readable .csv file:
	Read the schedule.csv file, create a list of all sessions for this event;
	For each session, create a list of all attendees who checked-in;
	For each attendee, post a line to the export file, if they were checked in "publicly".
*/

# html setup
  echo "<!DOCTYPE html>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>ImHere Reset</title>\n";
  echo "  </head>\n";
  echo "  <body style=\"background-color:darkseagreen;\">\n";
  echo "  <body>\n";

  echo "<p>Exporting session attendance list...<br><br>";

include 'config.php';
include 'readCSV.php';
include 'attendeeLog.php';
include 'checkin.php';

$event = $_GET['event'];

# Find the line in event_list.csv that matches $event, pull log directory name
     $event_logs = '';
     $handle = fopen($event_list,"r");
       if ($handle) {
         while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $line_event = $parts[2];
         if ( ($line_event == $event) ) { 
         	$event_logs = $parts[3];
            }
         }
       fclose($handle);
     } else { die("Couldn't open file: $event_list"); }

# Set path to attendees.txt
	 $attendees_log = $log_dir . $event_logs . '/' . 'attendees.txt';
	 # echo "attendees_log: $attendees_log<br><br>"; # For debug purposes

# Delete & re-create the export file
	 $attendance_export = $log_dir . $event_logs . '/' . 'attendance_export.csv';
	 $export_handle = fopen($attendance_export, 'w') or die("In exportAttendanceList.php, can't open file: attendance_export.csv ($attendance_export)");
	 fwrite($export_handle, "Session Name,Session Date,Start Time,End Time,Attendee Name,Attendee Email\n");

# Read schedule.csv, create an array of session names
	 $schedule = $log_dir . $event_logs . '/' . 'schedule.csv';
	 # echo "schedule: $schedule<br><br>"; # For debug purposes
     $sessionList = readCSV($schedule);	# returns an array of all lines from $schedule
	 $s = sizeof($sessionList);
	 # echo "Size of sessionList: $s<br><br>"; # For debug purposes

# -----------------------------------------------------------------
# For each session in the array...
	for ($i=0; $i<$s; $i++) {
	$sessionDate = $sessionList[$i][0];
	$startTime = $sessionList[$i][1];
	$endTime = $sessionList[$i][2];
	$sessionName = $sessionList[$i][3];
	if ($sessionDate == "") { $sessionDate = "Every Day"; }
	echo "$sessionName $sessionDate $startTime $endTime<br>"; # For debug purposes

	# Read attendees.txt, create an array of checked-in attendees
	$attendees = getAttendees($sessionName, $attendees_log); # Build an array of session attendees as name,email,:,status

	# For each checked-in attendee...
	foreach ($attendees as $key => $value) {
		# split value into email 
		$pp = explode(":", $value);
		$queryEmail = $pp[0];
		$value = $pp[1]; # In/out status
		# echo "$key $queryEmail $value<br>"; # For debug purposes

		# find out if this person is discoverable
		$discover = isDiscoverable($key, $queryEmail); #in checkin.php
		$parts = explode(":", $discover);
		$discover = $parts[2];
		# echo "Discoverable: $discover<br>"; # For debug purposes
		if ($discover) { # Add them to the export only if they are discoverable
			# Add a line to the export file
			$line = "$sessionName,$sessionDate,$startTime,$endTime,$key,$queryEmail\n";
			echo "$line<br>";
			fwrite($export_handle, $line);
			} # End if discover...

		} # End for each checked-in attendee...

	} # End for each session in the array...

# -----------------------------------------------------------------

# We're done
	fclose($export_handle);
	include './config.php';
    $url = $server . "imhere.php";
	echo "<p>Session attendance export file created: $attendance_export.</p>";
	echo "<p>Click <a href=\"$url\">here</a> to exit.</p>";

  # close out the html
  echo "  </body>\n";
  echo "</html>\n";
  
?>
