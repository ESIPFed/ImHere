<?php 

  function getCurrentEvents( $eventArray ) {

    include 'config.php';

    # timezone and time
    date_default_timezone_set($schedule_timezone);
    $date = date($schedule_format);
    # Put current date in YYMMDD format
    $parts = explode("_", $date);
    $parts2 = explode("/", $parts[0]);
    $month = $parts2[0];
    $day = $parts2[1];
    $year = $parts2[2];
    $currentDate = ($year . $month . $day);
#      echo "Current month: $month $tab"; # For debug purposes
#      echo "Current day: $day $tab"; # For debug purposes
#      echo "Current year: $year $tab"; # For debug purposes
#      echo "YYMMDD: $currentDate $tab<br>"; # For debug purposes

    # get the size of the array
    $s = sizeof($eventArray);
    
#	echo "Size of event array = $s<br>"; # For debug purposes

    # loop over all the events in the schedule (skipping the header line)
    $results = array();
    for ($i=1; $i<$s; $i++) {
      $eventStartDate = $eventArray[$i][0];
      $eventEndDate = $eventArray[$i][1];
      $eventName = $eventArray[$i][2];

	# Convert dates to YYMMDD format
    $parts = explode("/", $eventStartDate);
    $month = prependLeadingZero($parts[0]);
    $day = prependLeadingZero($parts[1]);
    $year = prependLeadingZero($parts[2]);
    $eventStartDate = ($year . $month . $day);

    $parts = explode("/", $eventEndDate);
    $month = prependLeadingZero($parts[0]);
    $day = prependLeadingZero($parts[1]);
    $year = prependLeadingZero($parts[2]);
    $eventEndDate = ($year . $month . $day);

#      echo "$eventName $tab"; # For debug purposes
#      echo "Start Date: $eventStartDate $tab"; # For debug purposes
#      echo "End Date: $eventEndDate $tab<br>"; # For debug purposes

	if ($eventStartDate <= $currentDate) {	
		if ($eventEndDate >= $currentDate) {
	       $results[] = $eventName; # load event name into array
		}
	}
}
    return $results;
  }


function prependLeadingZero($value) {
	if ($value == "0") { $value = "00"; }
	if ($value == "1") { $value = "01"; }
	if ($value == "2") { $value = "02"; }
	if ($value == "3") { $value = "03"; }
	if ($value == "4") { $value = "04"; }
	if ($value == "5") { $value = "05"; }
	if ($value == "6") { $value = "06"; }
	if ($value == "7") { $value = "07"; }
	if ($value == "8") { $value = "08"; }
	if ($value == "9") { $value = "09"; }
    return $value;
}

?>
