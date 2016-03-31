<?php 

  ob_start();

  include 'config.php';

  # check for GET variables
  if ( isset($_GET['name']) && isset($_GET['email']) && isset($_GET['locate']) && isset($_GET['checkin']) ) {

     $name = $_GET['name'];
     $email = $_GET['email'];
     $locate = $_GET['locate'];
     $checkin = $_GET['checkin'];
     if ( $checkin == 0 ) {

       $url = $server . "imhere.php";

       # unset the cookie if present
       if ( isset($_COOKIE['esip']) ) {
         unset($_COOKIE['esip']);
         setcookie('esip', false, time()-3600, '/');
       }

     } else {
       $url = $server . "imhere.php?name=$name&email=$email";
     }

     # write results to file
     $fh = fopen($checkIn_log, 'a') or die("can't open file: $checkIn_log");
     fwrite($fh, "$name:$email:$locate:$checkin\n");
     fclose($fh);

     while (ob_get_status()) { ob_end_clean(); }
     header( "Location: $url" );
     
  } 

?>
