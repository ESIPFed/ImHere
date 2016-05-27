<?php 
/* 	Logs who checks in/out of the event
		Updates checkedIn.txt
		Format: name:email:public(1)/private(0)flag:in(1)/out(0)status:event
	Also interfaces with ResearchBit Recommendation System	
*/
  ob_start();

  include 'config.php';

  $url = $server . "imhere.php"; # In case something goes wrong, Load imhere.php with no GET variables

  # check for spoofed date
  $sdate = $_GET['spoofedDate'];

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
       $url = $server . "imhere.php?spoofedDate=$sdate&name=$name&email=$email"; # Load imhere.php with GET variables

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
               # the cookie specification doesn't allow cookies that never expire
               # also, php will automatically expire a cookie if the date is too far in the future
               # we'll use 10 years as a way to not loose cookies and still comply with the spec
	       setcookie($cookie_name, $cookie_value, time()+(10*365*24*60*60), "/"); 
	     }       
     }
       $url = $server . "imhere.php?spoofedDate=$sdate&name=$name&email=$email&event=$event"; # Load imhere.php with GET variables
     }

#----------------------------------------------------------------------------
# Update the file checkedIn.txt
   $fh = fopen($checkedIn_log, 'a') or die("In updateCheckIn.php, can't open file: $checkedIn_log");
     fwrite($fh, "$name:$email:$locate:$checkin:$event\n");
     fclose($fh);

#----------------------------------------------------------------------------
# Update the ResearchBit Recommendation System
# Read the event_list.csv file, find the correct event, pull the recommendation_interface flag
	 $recommendation_interface = "";
     $handle = fopen($event_list,"r");
       if ($handle) {
         while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $line_event = $parts[2];
         if ( ($line_event == $event) ) { 
         	$recommendation_interface = $parts[6];
			$event_number = $parts[7]; }
       }
       fclose($handle);
     }
     if ($recommendation_interface == "Yes") {

# Post to the Recommendation System
$nameParts = explode(" ",$name);
$firstName = $nameParts[0];
$lastName = $nameParts[1];

# API example in python: curl --data "name=denisehills&email=dhills@gmail.com&check_in=1&public_tag=1&event_id=1" http://54.165.138.137:5000/post/

$curl_handle=curl_init();
# Once it's working this is what we want:
# name=$name&email=$email&check_in=$checkin&public_tag=$locate&event_id=$event_number

#$aaa="http://54.165.138.137:5000/post/?name=denisehills&email=dhills@gmail.com&check_in=1&public_tag=1&event_id=1";
$aaa="http://54.165.138.137:5000/";
#$aaa="http://www.google.com";
curl_setopt($curl_handle,CURLOPT_URL,$aaa);

curl_setopt($curl_handle,CURLOPT_POST, true);

$bbb="name=denisehills&email=dhills@gmail.com&check_in=1&public_tag=1&event_id=1";
curl_setopt($curl_handle,CURLOPT_POSTFIELDS,$bbb);

curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);

if (empty($buffer)) {$buffer="No response from HTTP post call to ResearchBit<br>";}
echo "$buffer";

   $fh = fopen("./researchBit_response.txt", 'a') or die("In updateCheckIn.php, can't open file: researchBit_response.txt");
     fwrite($fh, "$buffer\n");
     fclose($fh);


die("Here we are after the cURL...<br>");


       } # End of Recommendation System post


#----------------------------------------------------------------------------
     while (ob_get_status()) { ob_end_clean(); }
     header( "Location: $url" );
     
  } 
?>
