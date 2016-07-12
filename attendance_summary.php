<?php

function sessionTotal ($array) {

  $count = 0;
  foreach ($array as $person => $status) {
    if ( $status == 1 ) { $count++; }
  }
  return $count;

}

function peopleStatus ($array, $session) {

  $results = array();

  foreach ($array as $e => $value) { 
    $parts = explode(":", $e);
    $s = $parts[0];
    $person = $parts[1];
    if ( $s == $session) {
      $results[$person] = $value;
    }
  }

  return $results;

}

include 'config.php';
include 'readCSV.php';
include 'currentSessions.php';

# how many seconds before auto-reload
$sec = "60";

# get the event from the url
$event = $_GET['event'];

# json file to write to
$formatted = str_replace(' ', '_', $event);
$aLog = './logs/attendance_cache/attendance_data_' . $formatted . '_' . uniqid() . '.json';

# what page to load (this one, i.e. re-load)
$page = $_SERVER['PHP_SELF'] . '?event=' . $event;

# find the line in event_list.csv that matches $event
$handle = fopen($event_list,"r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    $parts = explode(",", $line);
    $line_event = $parts[2];
    if ( ($line_event == $event) ) { $event_logs = $parts[3]; }  
  }
  fclose($handle);
} else { die("Couldn't open file: $event_list"); }

# find the currently running sessions
$sFile = $log_dir . $event_logs . '/' . 'schedule.csv';
$allSessions = readCSV($sFile);
$cSessions = getCurrentSessions($allSessions, $schedule_timezone);
$currentSessions = array();
foreach($cSessions as $c) { 
  $parts = explode(",", $c);
  array_push($currentSessions, $parts[0]); # parts[1] is session ID
}

# we're looking for attendees.txt in this directory 
$file = $log_dir . $event_logs . '/' . 'attendees.txt';

# empty array, which will later be filled with results
$sessions = array();

# read the correct attendees.txt file
$handle = fopen($file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
       $line = trim($line);
       $pieces = explode(",",$line);

       $name = $pieces[0];
       $session = $pieces[2];
       $status = $pieces[3];

       # if it's a currently running session then add the attendance
       if ( in_array($session, $currentSessions) ) {
       
         if (!in_array($session, $sessions)) { array_push($sessions, $session); }

         # Associative Array
         $people[$session . ":" .$name] = $status;
 
       }
    }
    fclose($handle);
} else { die("Couldn't open file... $file"); }

# write to a JSON file
$size = sizeof($sessions);
$counter = 0;
$myfile = fopen($aLog, "w") or die ("Unable to open JSON log file.");
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
echo "   <h3 style=\"text-align:center\">$event</h3>";
echo "   <h3 style=\"text-align:center\">Real-time Session Attendance Count</h3>";
echo "   <script>var aLog = \"$aLog\";</script>\n";
echo "   <script src=\"http://d3js.org/d3.v3.min.js\"></script>\n";
echo "   <script src=\"bar_chart.js\"></script>\n";
#echo "    <table class=\"table table-striped\">\n";
#echo "       <thead>\n";
#echo "         <tr>\n";
#echo "           <th>Session</th>\n";
#echo "           <th>Attendance</th>\n";
#echo "         </tr>\n";
#echo "       </thead>\n";
#echo "       <tbody>\n";

#foreach ($sessions as $session) {
#  $results = peopleStatus($people, $session);
#  $total = sessionTotal($results); 
#  echo "     <tr>\n";
#  echo "       <td>$session</td>\n";
#  echo "       <td>$total</td>\n";
#  echo "     </tr>\n";
#}

#echo "           </tbody>\n";
#echo "        </table>\n";
echo "   </body>\n";
echo "</html>";

?>
