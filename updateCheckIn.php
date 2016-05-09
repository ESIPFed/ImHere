<?php 
/* 	Logs who checks in/out of the event
		Updates checkedIn.txt
		Format: name:email:public(1)/private(0)flag:in(1)/out(0)status:event
	Also interfaces with ResearchBit Recommendation System	
*/
  ob_start();

  include 'config.php';

  $url = $server . "imhere.php"; # In case something goes wrong, Load imhere.php with no GET variables

  # check for GET variables
  if ( isset($_GET['name']) && isset($_GET['email']) && isset($_GET['locate']) && isset($_GET['checkin']) ) {

     $name = $_GET['name'];
     $email = $_GET['email'];
     $locate = $_GET['locate'];
     $checkin = $_GET['checkin'];
     $event = $_GET['event'];

#----------------------------------------------------------------------------
# Check Out of this event
     if ( $checkin == 0 ) { 

# Set the return URL to imhere.php with no GET variables; Unset the cookie
       $url = $server . "imhere.php";
       if ( isset($_COOKIE['esip']) ) {
         unset($_COOKIE['esip']);
         setcookie('esip', false, time()-3600, '/');
       }
# Leave them logged in to the app, but checked out of the event:
#	   $event='';
#      $url = $server . "imhere.php?name=$name&email=$email&event=$event"; # Load imhere.php with GET variables
       $url = $server . "imhere.php?name=$name&email=$email"; # Load imhere.php with GET variables

#----------------------------------------------------------------------------
# Check In to this event
     } else { 
     # See if the cookie has an event attached to it
     if (isset($_COOKIE["esip"])) {
       $cvalue = $_COOKIE["esip"];
       $parts = explode(":", $cvalue);
       $cevent = $parts[2];
	     if ( $cevent == "" ) {
	       $cookie_name = "esip";
	       $cookie_value = "$name:$email:$event";
	       setcookie($cookie_name, $cookie_value, time()+(86400*7), "/"); 
	     }       
     }
       $url = $server . "imhere.php?name=$name&email=$email&event=$event"; # Load imhere.php with GET variables
     }

#----------------------------------------------------------------------------
# Update the file checkedIn.txt
   $fh = fopen($checkedIn_log, 'a') or die("can't open file: $checkedIn_log");
     fwrite($fh, "$name:$email:$locate:$checkin:$event\n");
     fclose($fh);

#----------------------------------------------------------------------------
# Update the ResearchBit Recommendation System
# Read the event_list.csv file, find the correct event, pull the recommendation_interface flag
#       if ($recommendation_interface == "Yes") {
# This is where we post to the Recommendation System
# Can maybe do something like we just did updating the checkIn.txt file:
#     fwrite($fh, "$name:$email:$locate:$checkin:$event\n");
#       }


#----------------------------------------------------------------------------
     while (ob_get_status()) { ob_end_clean(); }
     header( "Location: $url" );
     
  } 

?>
