<?php 

/*  
Export an attendance list for all sessions to a spreadsheet-readable .csv file:
	Read the schedule.csv file, create a list of all sessions for this event;
	For each session, create a list of all attendees who checked-in;
	For each attendee, post a line to the export file:
		attendance_export_public.csv if they were checked in "publicly",
		attendance_export_private.csv if they were checked in "privately".

Run this routine at the following URL: ../exportAttendanceList.php?event=<eventname>

*/


# html setup
  echo "<!DOCTYPE html>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>Export Attendance List</title>\n";
  echo "  </head>\n";
  echo "  <body style=\"background-color:darkseagreen;\">\n";
  echo "  <body>\n";

  echo "<p>Exporting session attendance list...<br>$attendance_export<br>";

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

# Delete & re-create the export files
	 $attendance_export_public = $log_dir . $event_logs . '/' . 'attendance_export_public.csv';
	 $export_handle_public = fopen($attendance_export_public, 'w') or die("In exportAttendanceList.php, can't open file: attendance_export_public.csv ($attendance_export_public)");
	 fwrite($export_handle_public, "Session Name,Session Date,Start Time,End Time,Attendee Name,Attendee Email\n");
	 $attendance_export_private = $log_dir . $event_logs . '/' . 'attendance_export_private.csv';
	 $export_handle_private = fopen($attendance_export_private, 'w') or die("In exportAttendanceList.php, can't open file: attendance_export_private.csv ($attendance_export_private)");
	 fwrite($export_handle_private, "Session Name,Session Date,Start Time,End Time,Attendee Name,Attendee Email\n");
	 echo "Export files: $attendance_export_public, $attendance_export_private<br><br>";

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
	echo "$sessionName,$sessionDate,$startTime,$endTime<br>"; # For debug purposes

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
		$line = "$sessionName,$sessionDate,$startTime,$endTime,$key,$queryEmail\n";
		echo "$line<br>";
		if ($discover) { fwrite($export_handle_public, $line); }
		else { fwrite($export_handle_private, $line); }

		} # End for each checked-in attendee...

	} # End for each session in the array...

# -----------------------------------------------------------------

# We're done
	fclose($export_handle);
	include './config.php';
    $url = $server . "imhere.php";
	echo "<p>Session attendance export files created:<br>$attendance_export_public<br>$attendance_export_private</p>";
	echo "<p>Click <a href=\"$url\">here</a> to exit.</p>";

  # close out the html
  echo "  </body>\n";
  echo "</html>\n";
  
?>
