<?php

include 'config.php';
include 'readCSV.php';
include 'currentSessions.php';

# get the event from the url
$event = $_GET['event'];

# find the line in event_list.csv that matches $event
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

# find the currently running sessions
$sessions = array();
$dates = array();
$startTimes = array();
$endTimes = array();
$sFile = $log_dir . $event_logs . '/' . 'schedule.csv';
$allSessions = readCSV($sFile);
#$cSessions = getCurrentSessions($allSessions, $schedule_timezone);
#$currentSessions = array();
foreach($allSessions as $s) { 
  array_push($dates, $s[0]);
  array_push($startTimes, $s[1]);
  array_push($endTimes, $s[2]);
  array_push($sessions, $s[3]); 
}

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"refresh\" content=\"$sec;URL='$page'\">\n";
echo "      <meta charset=\"utf-8\">\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "	    <style>\n";
echo "         body { background-color:darkseagreen; }\n";
echo "         text {	font: 15px sans-serif; } \n";
echo "      </style>\n";
echo "  </head>\n";
echo "  <body>\n";
echo "   <h3 style=\"text-align:center\">$event</h3>";
$size = sizeOf($sessions);
for ($i=0; $i<$size; $i++) {
  echo " <p>&nbsp;$dates[$i], $startTimes[$i] to  $endTimes[$i]</p>";
  echo " <p>&nbsp;&nbsp;$sessions[$i]</p>";
  $url1 = "<a href=\"\">Check In</a>";
  $url2 = "<a href=\"\">Who's There Now</a>";
  $url3 = "<a href=\"\">List All Check-ins</a>";
  echo "$url1, $url2, $url3";
}
echo "   <h3 style=\"text-align:center\">$event_logs</h3>";
echo "   <h3 style=\"text-align:center\">$schedule_timezone</h3>";
echo "   </body>\n";
echo "</html>";

?>
