<?php

## Config file for variables used throughout ImHere

# Define server and log file locations

  $server = 'http://38.118.61.102/imhere/';	  # production server host by Tom at Marymount
  $log_dir = '/var/www/html/imhere/logs/';
 
# Insert the following to run this on Dan's local server
#  $server = 'http://10.0.0.9/imhere/';	# Dan's local server (funky cookies here?)
#  $server = 'http://localhost/imhere/';	# Dan's local server (but this doesn't work for remote devices)
#  $server = 'http://dansmacbook.local/imhere/';	# The only thing working as of 4/22...

# Works on Dan's local server, but won't this work on th production server as well?:
#  $log_dir = './logs/';

  $checkedIn_log = $log_dir . 'checkedIn.txt';
  $imhere_log = $log_dir . 'imhere_log.txt';
  $event_list = $log_dir . 'event_list.csv';

$schedule = $log_dir . 'event_list.csv';

# Timezone and time for log files (H indicates 24 time format)
  date_default_timezone_set('UTC');
  $date = date("m-d-Y_H:i:s");
  $schedule_format = 'm/d/y_H:i';

# Password for online uploading of new/updated event lists, sessions schedules, and attendee lists
  $pwd = 'ESIP2016!';

?>
