<?php

include 'config.php';
include 'readCSV.php';

function compareDateTime($month, $day, $year, $sessionDate, $startTime, $endTime, $time) {

    ## -1 = historical (checked in to a past event)
    ##  1 = future (checked into an event that has not started yet)
    ##  0 = checked into currently running session

    $parts = explode("/", $sessionDate);

    $match = 0;

    if (($sessionDate == '') || ( ($month == $parts[0]) && ($day == $parts[1]) && ($year == $parts[2]) )) { 
      $startTimestamp = strtotime($startTime);
      $endTimestamp = strtotime($endTime);
      $currentTimestamp = strtotime($time);

      if ($currentTimestamp < $startTimestamp) { $match = 1; }
      if (($currentTimestamp >= $startTimestamp) && ($currentTimestamp <= $endTimestamp)) { $match = 0; }
      if ($currentTimestamp > $endTimestamp) { $match = -1; } 
    }
    return $match;

}

function getCurrentDateTime( $schedule_timezone ) {

    include "config.php";

    date_default_timezone_set($schedule_timezone);
    $date = date($schedule_format);
#	echo "$schedule_timezone<br>"; # For debug purposes
#	echo "$date<br>"; # For debug purposes

    # split date and time into parts
    $parts = explode("_", $date);
    $parts2 = explode("/", $parts[0]);
    $month = $parts2[0];
    $day = $parts2[1];
    $year = $parts2[2];
    $time = $parts[1];

    $results = array();
    $results[0] = $month;
    $results[1] = $day;
    $results[2] = $year;
    $results[3] = $time;
    return $results;

}

# get the event from the url
$event = $_GET['event'];
$name = $_GET['name'];
$email = $_GET['email'];

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

# get current date/time
$dateTime = getCurrentDateTime( $schedule_timezone );

# find all the sessions
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
  echo " <p>&nbsp;&nbsp;$dates[$i], $startTimes[$i] to  $endTimes[$i]<br/>";
  echo " &nbsp;&nbsp;$sessions[$i]<br/>";
  $r = compareDateTime($dateTime[0], $dateTime[1], $dateTime[2], $dates[$i], $startTimes[$i], $endTimes[$i], $dateTime[3]);
  $logFile = $log_dir . $event_logs . '/attendees.txt';
  $url1 = "<a href=\"attendees.php?check=in&name=$name&email=$email&session=$sessions[$i]&event=$event&attendees_log=$logFile&prePost=$r\">Check In</a>";
  $url2 = "<a href=\"attendees.php?name=$name&email=$email&session=$sessions[$i]&event=$event&attendees_log=$logFile\">Who's There Now</a>";
  $url3 = "<a href=\"attendees.php?listAll=1&name=$name&email=$email&session=$sessions[$i]&event=$event&attendees_log=$logFile\">List All Check-ins</a>";
  echo "&nbsp;&nbsp;$url1, $url2, $url3</p>";
}
echo "   </body>\n";
echo "</html>";

?>
