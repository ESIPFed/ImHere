<?php 
/*
This is the new sync routine

We would first do a reset of the ResearchBit database (event-specific?)

Next we call this routine
Either ask for event name, or bring it in as GET variable

We want to list event attendees (as in listAttendees.php)

For each, 

updateCheckIn.php sets parameters $postData and $rb_response, calls function httpPost (in httpPostRequests.php) which does the cURL


*/

#----------------------------------------------------------------------------
# Here we want to list event attendees (as in listAttendees.php)








     $name = 
     $email = 
     $locate = 
     $checkin = 

#----------------------------------------------------------------------------
# For each attendee checked in to the event, do this:
# This logic is from updateCheckIn.php...
# Coming in we need $event, $event_list

# Read the event_list.csv file, find the correct event, pull the recommendation_interface flag

# Might need to back up a directory to get these:
  include 'config.php';
  include 'httpPostRequests.php';

  # Pull GET variable
    $event = $_GET['event'];




	 $recommendation_interface = "";
     $handle = fopen($event_list,"r");
       if ($handle) {
         while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $line_event = $parts[2];
         if ( ($line_event == $event) ) { 
			$rb_response = $log_dir . $parts[3] . '/rb_response.txt';
         	$recommendation_interface = $parts[6]; }
       }
       fclose($handle);
     }
	#------------------------------------------
	# Post to the Recommendation System
     if ($recommendation_interface) {
		$newName = preg_replace('/\s/', '', $name);
		$newName = strtolower($newName);
		$postData="name=$newName&email=$email&check_in=$checkin&public_tag=$locate&event_id=$recommendation_interface&event_name=$event";
		httpPost($postData, $rb_response); # Call the function (in httpPostRequests.php)
     }
	#------------------------------------------

#----------------------------------------------------------------------------





























///// functions ///// 

# ------------------------------------------------------------
function myFirst(){ 
# Unset the cookie
     if (isset($_COOKIE["esip"])) {
       $cvalue = $_COOKIE["esip"];
       $parts = explode(":", $cvalue);
       $name = $parts[0];
       $email = $parts[1];
       $event = $parts[2];
#	   echo "<p>Cookie found: $name, $email, $event";
       unset($_COOKIE['esip']);
       setcookie('esip', false, time()-3600, '/');
       }

# Update the file checkedIn.txt
   $fh = fopen('./logs/checkedIn.txt', 'a') or die("<br>Can't open checkedIn.txt file");
     fwrite($fh, "$name:$email:0:0:$event\n");
     fclose($fh);

# Should probably be updating ResearchBit with check-out info here too...




    echo 'Name & email reset.'; 
} 

# ------------------------------------------------------------
function mySecond(){ 
    echo 'ResearchBit system reset.'; 
} 

///// START ///// 

# ------------------------------------------------------------
# html setup
  echo "<!DOCTYPE html>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>ImHere Reset</title>\n";
  echo "  </head>\n";
  echo "  <body style=\"background-color:darkseagreen;\">\n";
  echo "  <body>\n";
# ------------------------------------------------------------


if (isset($_GET['run'])) $linkchoice=$_GET['run']; 
else $linkchoice=''; 

switch($linkchoice){ 

case 'first' : 
    myFirst(); 
    break; 

case 'second' : 
    mySecond(); 
    break; 

default : 
    echo '<b>Pick one:</b>'; 

} 

?> 
<p>
<a href="?run=first">Reset name & email in ImHere</a> 
<br> <br>
<a href="?run=second">Reset ResearchBit check-in logs</a> 
<br> <br> 
<a href="?run=0">Refresh No run</a> 

</body></html>