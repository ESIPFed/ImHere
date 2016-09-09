<?php

# ------------------------------------------------------------------------------------
# Functions

function sessionTotal ($array) {
  $count = 0;
  foreach ($array as $person => $status) {
    if ( $status == 1 ) { $count++; }
  }
  return $count;
}

#----------

function sessionTotal2 ($array) {
  $count = 0;
  foreach ($array as $person => $status) {
    $count++;
  }
  return $count;
}

#----------

function peopleStatus ($array, $session) {
  $results = array();
  foreach ($array as $e => $value) { 
    $parts = explode(";", $e);
    $s = $parts[0];
    $person = $parts[1];
    if ( $s == $session) {
      $results[$person] = $value;
    }
  }
  return $results;
}

# ------------------------------------------------------------------------------------
# Setup

include 'config.php';
include 'readCSV.php';
include 'currentSessions.php';

# get the event & email from the url
$event = $_GET['event'];
$email = $_GET['email'];

# json file to write to
$formatted = str_replace(' ', '_', $event);
#$aLog = './logs/attendance_cache/attendance_data_' . $formatted . '_' . uniqid() . '.json';
#$aLog2 = './logs/attendance_cache/attendance_data2_' . $formatted . '_' . uniqid() . '.json';
$currentSessionsLog = './logs/attendance_cache/' . $formatted . '_current_sessions_' . $email . '.json';
$allSessionsLog = './logs/attendance_cache/' . $formatted . '_all_sessions_' . $email . '.json';

# ------------------------------------------------------------------------------------
# Count the number of attendees checked in to the event

# First build an array of all people who checked in to this event

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
     $result[$logEmail] = $lastName . ":" . $checkInFlag; 
  }
}
fclose($handle);

# Read through the array, count the total as well as just those still checked in; save counts for later display

$in=0;
$tot=0;
foreach ($result as $key => $value) {
   $parts = explode(":", $value);
   $InOutFlag = $parts[1]; # lastname : in/out flag
   $tot=$tot+1;
   if ($InOutFlag) { $in=$in+1; }
} # End for each...

# ------------------------------------------------------------------------------------
# Get log directory path and timezone from event_list.csv

$handle = fopen($event_list,"r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    $parts = explode(",", $line);
    $line_event = $parts[2];
    if ( ($line_event == $event) ) { $event_logs = $parts[3]; $schedule_timezone = $parts[4];}  
  }
  fclose($handle);
} else { die("Couldn't open file: $event_list"); }

# ------------------------------------------------------------------------------------
# Build an array of currently running sessions, in $currentSessions

$sFile = $log_dir . $event_logs . '/' . 'schedule.csv';
$allSessions = readCSV($sFile);
$cSessions = getCurrentSessions($allSessions, $schedule_timezone);
$currentSessions = array();
foreach($cSessions as $c) { 
  $parts = explode(",", $c);
  array_push($currentSessions, $parts[0]); # parts[1] is session ID
}

#--------------------------------------------------
# Read all lines from attendees.txt. For each...
# If $session from line is found in the array of current sessions, then
# 	If $session has NOT already been added to $sessions array, push it there
#	Do something with an associative array
# Else if $session is NOT found in the array of current sessions, then
# 	Do the same stuff, just in different arrays

$sessions = array(); # empty array, which will later be filled with results
$sessions2 = array(); # empty array, which will later be filled with results

$people = array(); # empty array, which will later be filled with results
$people2 = array(); # empty array, which will later be filled with results

$file = $log_dir . $event_logs . '/' . 'attendees.txt';
$handle = fopen($file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) { # Read a line from attendees.txt
       $line = trim($line);
       $pieces = explode(",",$line);
       $name = $pieces[0];
       $session = $pieces[2];
       $status = $pieces[3];

       if ( in_array($session, $currentSessions) ) # if it's a currently running session...
		 { 
         if (!in_array($session, $sessions)) { array_push($sessions, $session); } # and it's not already in the $sessions array, put it there
         # Associative Array
         # Let's count everybody, not just those still checked-in
         # $people[$session . ";" .$name] = $status;
         $people[$session . ";" .$name] = 1;
         }
# Remove the ELSE here. Let's make it an ALL SESSIONS array instead of just NON_CURRENT sessions
#	     else # else it's NOT a currently running session... 
		 {
         if (!in_array($session, $sessions2)) { array_push($sessions2, $session); } # if it's not already in the $sessions2 array, put it there
         # Associative Array
         $people2[$session . ";" .$name] = 1;
	     }

    }
    fclose($handle);
} else { die("Couldn't open file... $file"); }

# ------------------------------------------------------------------------------------
# ------------------------------------------------------------------------------------
# ------------------------------------------------------------------------------------
# write to a JSON file
$size = sizeof($sessions);
$counter = 0;
$myfile = fopen($currentSessionsLog, "w") or die ("Unable to open JSON log file.");
fwrite($myfile, "{\n");
fwrite($myfile, " \"name\": \"sessions\",\n");
fwrite($myfile, " \"children\": [\n");
foreach ($sessions as $session) {
  $results = peopleStatus($people, $session);
  $total = sessionTotal($results);
  $line =  "    {\"name\": \"$session\", \"size\": $total}";
  if ($counter < $size-1) { $line = $line . ",\n"; } else { $line = $line . "\n"; }
  $counter++;
  fwrite($myfile, $line);
}
fwrite($myfile, "  ]\n");
fwrite($myfile, "}\n");
fclose($myfile);
$aaa=sizeof($sessions);

# ------------------------------------------------------------------------------------
# write to another JSON file
$size = sizeof($sessions2);
$counter = 0;
$myfile = fopen($allSessionsLog, "w") or die ("Unable to open 2nd JSON log file.");
fwrite($myfile, "{\n");
fwrite($myfile, " \"name\": \"sessions\",\n");
fwrite($myfile, " \"children\": [\n");
foreach ($sessions2 as $session) {
  $results = peopleStatus($people2, $session);
  $total = sessionTotal2($results);
  $line =  "    {\"name\": \"$session\", \"size\": $total}";
  if ($counter < $size-1) { $line = $line . ",\n"; } else { $line = $line . "\n"; }
  $counter++;
  fwrite($myfile, $line);
}
fwrite($myfile, "  ]\n");
fwrite($myfile, "}\n");
fclose($myfile);
$bbb=sizeof($sessions2);

# ------------------------------------------------------------------------------------

# Set URL of page name to reload. (Default is this one.)
#$page = $_SERVER['PHP_SELF'] . '?event=' . $event;
$page = $server . "attendance_summary.php?event=$event&email=$email";

# If there are sessions currently running, we load a different page instead:
if ($aaa!=0) { $page = $server . "attendance_summary2.php?event=$event&email=$email&tot=$tot&in=$in&aaa=$aaa&currentSessionsLog=$currentSessionsLog"; }

# How many seconds before auto-reload
$sec = "5";

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"refresh\" content=\"$sec;URL='$page'\">\n";
echo "      <meta charset=\"utf-8\">\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "	    <style>\n";
echo "         body { background-color:darkseagreen; }\n";
echo "         text {	font: 15px sans-serif; } \n";
echo "         rect.background { fill: white; }\n";
echo "         .axis { shape-rendering: crispEdges; }\n";
echo "         .axis path, .axis line {\n";
echo "            fill: none;\n";
echo "            stroke: #000;\n";
echo "         }\n";
echo "      </style>\n";
echo "  </head>\n";
echo "  <body>\n";

#----------

$space = "&nbsp;";
echo "   <h3 style=\"text-align:center\">$event</h3>";
#echo "   <h3 style=\"text-align:center\">Total Event Attendees: $tot $space Still Checked-In: $in</h3>";
echo "   <h3 style=\"text-align:center\">Total Event Attendees: $tot</h3>";
echo "   <h3 style=\"text-align:center\">Attendee Count by Session";

#----------

if ($bbb!=0) # If any non-current sessions to display...
	{
	echo " - All Sessions</h3>";
	echo "   <script>var aLog2 = \"$allSessionsLog\";</script>\n";
	echo "   <script src=\"http://d3js.org/d3.v3.min.js\"></script>\n";
	echo "   <script src=\"bar_chart_2.js\"></script>\n";
	}
else 
	{
	if ($aaa!=0) # If any current sessions to display...
		{
		echo " - Current Sessions</h3>";
		echo "   <script>var aLog = \"$currentSessionsLog\";</script>\n";
		echo "   <script src=\"http://d3js.org/d3.v3.min.js\"></script>\n";
		echo "   <script src=\"bar_chart.js\"></script>\n";
		}
		else
			{ echo "</h3><h3 style=\"text-align:center\">No session data to display</h3>"; }
	}


#----------

echo "   </body>\n";
echo "</html>";

?>
