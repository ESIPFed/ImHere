<?php 

  function inRange($month, $day, $year, $sessionDate, $startTime, $endTime, $time) {
 
    $parts = explode("/", $sessionDate);

    $match = 0;
    if ( ($month == $parts[0]) && ($day == $parts[1]) && ($year == $parts[2]) ) { 
      $startTimestamp = strtotime($startTime);
      $endTimestamp = strtotime($endTime);
      $currentTimestamp = strtotime($time);
      if (($currentTimestamp >= $startTimestamp) && ($currentTimestamp <= $endTimestamp)) { $match = 1; }
    }
    return $match;

  }

  function getCurrentSessions( $sessionArray ) {

    # timezone and time
    date_default_timezone_set('America/New_York');
    $date = date("m/d/y_H:i");

    # split date and time into parts
    $parts = explode("_", $date);
    $parts2 = explode("/", $parts[0]);
    $month = $parts2[0];
    $day = $parts2[1];
    $year = $parts2[2];
    $time = $parts[1];

    # get the size of the array
    $s = sizeof($sessionArray);

    # loop over all the sessions in the schedule
    $results = array();
    for ($i=0; $i<$s; $i++) {
      $sessionDate = $sessionArray[$i][0];
      $startTime = $sessionArray[$i][1];
      $endTime = $sessionArray[$i][2];
      $name = $sessionArray[$i][3];
      $match = inRange($month, $day, $year, $sessionDate, $startTime, $endTime, $time);
      if ($match) { $results[] = $name; }
    }

    return $results;

  }


?>
