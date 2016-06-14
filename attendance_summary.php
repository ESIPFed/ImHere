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
include 'currentEvents.php';

# how many seconds before auto-reload
$sec = "60";

# get the event from the url
$event = $_GET['event'];

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

# we're looking for attendees.txt in this directory 
$file = $log_dir . $event_logs . '/' . 'attendees.txt';

echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"refresh\" content=\"$sec;URL='$page'\">\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "  </head>\n";
echo " <body>\n";
echo "    <table class=\"table table-striped\">\n";
echo "       <thead>\n";
echo "         <tr>\n";
echo "           <th>Session</th>\n";
echo "           <th>Attendance</th>\n";
echo "         </tr>\n";
echo "       </thead>\n";
echo "       <tbody>\n";

$sessions = array();

$handle = fopen($file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
       $line = trim($line);
       $pieces = explode(",",$line);

       $name = $pieces[0];
       $session = $pieces[2];
       $status = $pieces[3];

       if (!in_array($session, $sessions)) { array_push($sessions, $session); }

       # Associative Array
       $people[$session . ":" .$name] = $status;

    }
    fclose($handle);
} else { die("Couldn't open file... $file"); }

foreach ($sessions as $session) {
  $results = peopleStatus($people, $session);
  $total = sessionTotal($results); 
  echo "     <tr>\n";
  echo "       <td>$session</td>\n";
  echo "       <td>$total</td>\n";
  echo "     </tr>\n";
}

echo "           </tbody>\n";
echo "        </table>\n";
echo "   </body>\n";
echo "</html>";

?>
