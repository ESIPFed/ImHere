<?php 

  # log files
  $log = './logs/beacon_log.txt';

  # timezone and time
  date_default_timezone_set('UTC');
  $date = date("m-d-Y_h:i:s");

  # get the POST data
  $pdata = file_get_contents('php://input');
  $log_line = $date . ',' . $pdata;

  # check for POST variables
  #if ( isset($_POST['email']) && isset($_POST['name']) ) {
  #   $name = $_POST['name'];
  #   $email = $_POST['email'];
  #   $beacon = $_POST['beacon'];
  #   $log_line = "$date,$name,$email,$beacon";
  #}

  # write results to file
  $fh = fopen($log, 'a') or die("can't open file");
  fwrite($fh, "$log_line\n");
  fclose($fh);

?>
