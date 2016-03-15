<?php 

  ob_start();

  # log files
  $log = './logs/checkedIn.txt';

  # check for GET variables
  if ( isset($_GET['name']) && isset($_GET['email']) && isset($_GET['locate']) && isset($_GET['checkin']) ) {

     $name = $_GET['name'];
     $email = $_GET['email'];
     $locate = $_GET['locate'];
     $checkin = $_GET['checkin'];
     if ( $checkin == 0 ) {

       $url = "http://38.118.61.102/sloan/imhere.php";

       # unset the cookie if present
       if ( isset($_COOKIE['esip']) ) {
         unset($_COOKIE['esip']);
         setcookie('esip', false, time()-3600, '/');
       }

     } else {
       $url = "http://38.118.61.102/sloan/imhere.php?name=$name&email=$email";
     }

     # write results to file
     $fh = fopen($log, 'a') or die("can't open file: $log");
     fwrite($fh, "$name:$email:$locate:$checkin\n");
     fclose($fh);

     while (ob_get_status()) { ob_end_clean(); }
     header( "Location: $url" );
     
  } 

?>
