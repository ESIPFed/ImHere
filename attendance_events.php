<?php

include 'config.php';
include 'currentEvents.php';

$email = $_GET['email'];

$events = array();

# read event_list.csv 
# skip the first line, it's a header
$counter = 0;
$handle = fopen($event_list,"r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    if ( $counter > 0 ) {
      $line = trim($line);
      $parts = explode(",", $line);
      $line_event = $parts[2];
      #$event_logs = $parts[3];   
      array_push($events, $line_event);
    } 
    $counter++;
  }
  fclose($handle);
} else { die("Couldn't open file: $event_list"); }

echo "<html>\n";
echo "  <head>\n";
#echo "      <meta http-equiv=\"refresh\" content=\"$sec;URL='$page'\">\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "  </head>\n";
echo " <body>\n";
echo "    <table class=\"table table-striped\">\n";
echo "       <thead>\n";
echo "         <tr>\n";
echo "           <th>Event</th>\n";
echo "           <th>Link</th>\n";
echo "         </tr>\n";
echo "       </thead>\n";
echo "       <tbody>\n";

$lnk = $server . 'attendance_summary.php?event=';

foreach ($events as $event) {
  $link = $lnk . $event . '&email=' . $email;
  echo "     <tr>\n";
  echo "       <td>$event</td>\n";
  echo "       <td><a href=\"$link\">Event Attendance</a></td>\n";
  echo "     </tr>\n";
}

echo "           </tbody>\n";
echo "        </table>\n";
echo "   </body>\n";
echo "</html>";

?>
