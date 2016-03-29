<?php 

  # read the config file
  include 'config.php';

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
  $fh = fopen($beacon_log, 'a') or die("can't open file");
  fwrite($fh, "$log_line\n");
  fclose($fh);

?>
