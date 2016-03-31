<?php

  ## Config file for variables used throughout ImHere
  ## configuration variables are set here and then
  ## imported elsewhere

  # server location
  # production server host by Tom at Marymount
  $server = 'http://38.118.61.102/sloan/';

  # log files
  $log_dir = '/var/www/html/sloan/logs/';
  $attendees_log = $log_dir . 'attendees.txt';
  $beacon_log = $log_dir . 'beacon_log.txt';
  $checkIn_log = $log_dir . 'checkedIn.txt';
  $imhere_log = $log_dir . 'imhere_log.txt';

  # schedule file
  $schedule = $log_dir . 'schedule.csv';

  # timezone and time for log files (H indicates 24 time format)
  date_default_timezone_set('UTC');
  $date = date("m-d-Y_H:i:s");

  # timezone and time format for uploaded schedule file
  # this may change depending upon where the meeting is held
  $schedule_timezone = 'America/New_York';
  $schedule_format = 'm/d/y_H:i';

  # password for online uploading
  $pwd = 'ESIP2016!';

?>
