<?php 

# 05/04 - Addded $schedule_timezone as getCurrentSesions function argument
# 05/17 - Added check for null session date (means it's running every day)

  function inRange($month, $day, $year, $sessionDate, $startTime, $endTime, $time) {

    $parts = explode("/", $sessionDate);

    $match = 0;

#    if ( ($month == $parts[0]) && ($day == $parts[1]) && ($year == $parts[2]) ) { 
 if (($sessionDate == '') || ( ($month == $parts[0]) && ($day == $parts[1]) && ($year == $parts[2]) )) { 
      $startTimestamp = strtotime($startTime);
      $endTimestamp = strtotime($endTime);
      $currentTimestamp = strtotime($time);
      if (($currentTimestamp >= $startTimestamp) && ($currentTimestamp <= $endTimestamp)) { $match = 1; }
    }
     return $match;

  }

  function getCurrentSessions( $sessionArray, $schedule_timezone ) {

    # include the config file
    include 'config.php';

    # timezone and time
    if ( $GLOBALS['spoofedDate'] != '' ) {
      $date = $GLOBALS['spoofedDate'];
    } else {
      date_default_timezone_set($schedule_timezone);
      $date = date($schedule_format);
    }
#	echo "$schedule_timezone<br>"; # For debug purposes
#	echo "$date<br>"; # For debug purposes
    # split date and time into parts
    $parts = explode("_", $date);
    $parts2 = explode("/", $parts[0]);
    $month = $parts2[0];
    $day = $parts2[1];
    $year = $parts2[2];
    $time = $parts[1];

    # get the size of the array
    $s = sizeof($sessionArray);
#	echo "$s<br>"; # For debug purposes

    # loop over all the sessions in the schedule
    $results = array();
    for ($i=0; $i<$s; $i++) {
      $sessionDate = $sessionArray[$i][0];
      $startTime = $sessionArray[$i][1];
      $endTime = $sessionArray[$i][2];
      $name = $sessionArray[$i][3];
      $id = $sessionArray[$i][4];
#	  echo "$id $tab $name $tab $sessionDate $tab $startTime $tab $endTime<br>"; # For debug purposes
      $match = inRange($month, $day, $year, $sessionDate, $startTime, $endTime, $time);
#	  echo "Match = $match<br>"; # For debug purposes

#      if ($match) { $results[] = $name; }
      $combo = "$name,$id";
      if ($match) { $results[] = $combo; }
    }

    return $results;

  }


?>
