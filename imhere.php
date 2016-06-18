<?php 
# Control program for the ImHere Check In system
# See ./utilities/notes.txt for (some) documentation

# include the config file
  include 'config.php';
#
# include needed functions
  include 'checkin.php'; 
  include 'readCSV.php';
  include 'attendeeLog.php';
  include 'currentSessions.php';
  include 'currentEvents.php';
#
# html setup
  echo "<!DOCTYPE html>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>Session Check In</title>\n";

  echo "<style>
	a:link {color: mediumblue;}
	a:visited {color: mediumblue;}
	a:hover {color: blue;}
	a:active {color: mediumblue;}
	</style>";

  echo "  </head>\n";

# set some display variables
  $space = "&nbsp;";
  $tab = $space . $space . $space . $space . $space;
  $tab2 = $space . $space;

#  $color1 = "skyblue";
#  $color1 = "lightblue";
#  $color1 = "cadetblue";
  $color1 = "darkseagreen";

#  $color2 = "#80b380"; # Slightly darker green
  $color2 = "darkseagreen";

#  $color3 = "black";
#  $color3 = "crimson";
  $color3 = "#b30000";

#  $color4 = "black";
#  $color4 = "crimson";
  $color4 = "#b30000";

  echo "  <body style=\"background-color:$color1;\">\n";

# --------------------------------------------------------------------------------------------------------------------

 # Start painting the screen

  # Display sponsor logo
  #  echo "<img class=\"img\" src=\"images/sloan_logo.png\"><br/>";
  #  echo "<img class=\"img\" src=\"images/sloan_small.png\"><br/>";

# ------------------------------------------------------------
# Look for name & email in GET variables first, then in a cookie...
        # Check for spoofed date
        $GLOBALS['spoofedDate'] = '';
        if ( isset($_GET['spoofedDate']) ) { $GLOBALS['spoofedDate'] = $_GET['spoofedDate']; }

	# Check for GET variables
	 $event=""; # Set event default
	 if ( isset($_GET['name']) && isset($_GET['email']) ) {
     $name = $_GET['name'];
     $email = $_GET['email'];
     if (isset($_GET['event'])) {
          $event = $_GET['event'];
     }
     $log_line = "$date,$name,$email,$event";
     #echo "GET variables found: $name, $email, $event <br>"; # For debug purposes

  } else { # no GET variables try looking for the esip cookie
     if (isset($_COOKIE["esip"])) {
       $cvalue = $_COOKIE["esip"];
       $parts = explode(":", $cvalue);
       $name = $parts[0];
       $email = $parts[1];
       $event = $parts[2];
       #echo "Cookie 1 found: $name, $email, $event <br>"; # For debug purposes
     }
  }
# ------------------------------------------------------------
# If we have a name and email...

  if ( isset($name) && isset($email) ) {	# If we have a name and email...
  
	# Look for secret RESET option in GET variable...
	#echo "Name 1: $name<br>"; # For debug purposes
  	if ($name == "reset" || $name == "Reset") {   	# Unset the cookie; Set the return URL to imhere.php with no GET variables

     if (isset($_COOKIE["esip"])) {
       $cvalue = $_COOKIE["esip"];
       $parts = explode(":", $cvalue);
       $cname = $parts[0];
       $cemail = $parts[1];
       $cevent = $parts[2];
       #echo "Cookie 2 found: $cname, $cemail, $cevent<br>"; # For debug purposes
	   unset($_COOKIE['esip']);
       setcookie('esip', false, time()-3600, '/');
       }
	# Update the file checkedIn.txt
     $fh = fopen($checkedIn_log, 'a') or die("can't open file: $checkIn_log");

# Should we be using cname and cemail here???
#echo "Name 2: $name<br>";
#echo "cName: $cname<br>";
     fwrite($fh, "$name:$email:0:0:$event\n");
     fclose($fh);

	# Set the return URL to imhere.php with no GET variables
	$url = $server . "imhere.php"; # Load imhere.php with no GET variables
	echo "<p>System reset, click <a href=\"$url\">here</a></p>\n";

    # ------------------------------------------------------------
     }	else	{	# Not processing a reset...

     # try to set a cookie for this user to expire in 7 days
     if ( !isset($_COOKIE["esip"]) ) {
       $cookie_name = "esip";
       $cookie_value = "$name:$email:$event";
       # the cookie spec requires an expiration date
       # also, php's implementation of cookies will auto expire a cookie if 
       # the date is too far into the future
       # to have a "non-expiring" cookie and still conform to the spec
       # we'll set an expiration date of 10 years
       setcookie($cookie_name, $cookie_value, time()+(10*365*24*60*60), "/"); 
     }

# ======================================================================================================
# ======================================================================================================
# ======================================================================================================

# See if this person has checked in to an event...
     $checkedIn = isCheckedIn($name, $email); # (In checkin.php)

# If we ARE checked in, then see if the event checked in to can be found in the event_list file
	 if ( $checkedIn != '' ) {
 	
# Find the line in event_list.csv that matches $event, pull log directory name, timezone, & interface flag
	 $event = $checkedIn;
     $event_logs = '';
     $handle = fopen($event_list,"r");
       if ($handle) {
         while (($line = fgets($handle)) !== false) {
         $line = trim($line);
         $parts = explode(",", $line);
         $line_event = $parts[2];
         if ( ($line_event == $event) ) { 
         	$event_logs = $parts[3];
         	$schedule_timezone = $parts[4];
         	$recommendation_interface = $parts[6]; }
       }
       fclose($handle);
     } else { die("Couldn't open file: $event_list"); }

# Point log files to an event-specific directory; check that the log files exist
	 $attendees_log = $log_dir . $event_logs . '/' . 'attendees.txt';
	 #echo "$attendees_log<br>";
     $handle = fopen($attendees_log, "r");
     if ($handle) { fclose($handle); }
     else { $checkedIn = ''; } # Missing file - reset the checkedIn flag

	 $schedule = $log_dir . $event_logs . '/' . 'schedule.csv';
	 #echo "$schedule<br>";
     $handle = fopen($schedule, "r");
     if ($handle) { fclose($handle); }
     else { $checkedIn = ''; } # Missing file - reset the checkedIn flag
}     

# If not checked in to an event (or event no longer exists in event_list.csv)...
     if ( $checkedIn == '' ) {	# If not checked in to an event...

  # Display application name
  echo "<p class=\"center\" style=\"font-weight:bold; color:$color3\">ImHere<br>";
  echo "<class=\"center\" style=\"font-weight:bold; color:$color3\">Event Check In System</p>";

       $d = $GLOBALS['spoofedDate'];
       $url = "updateCheckIn.php?spoofedDate=$d&name=$name&email=$email&checkin=1";
       
  # Display a list of currently running events
       $allEvents = readCSV($event_list);	# returns an array of all lines from $event_list
#	   $s = sizeof($allEvents);  # For debug purposes
#	   echo "Size of allEvents: $s<br>"; # For debug purposes
       $cEvents = getCurrentEvents($allEvents);	# returns an array of events currently running
#	   $s = sizeof($cEvents);  # For debug purposes
#	   echo "Size of cEvents: $s<br>"; # For debug purposes

       echo "<p style=\"font-weight:bold; color:$color4\">Currently Running Event(s):</p>\n";

   # Select from the list and check 'em in
       $counter = 1;
	   $displayFlag = 0;
       foreach($cEvents as $s) {		# cEvents is an array of event names currently running
      
	if ($s == "ESIP Telecons") {
	   $public = "<a href=\"$url&event=$s&locate=1\">Check In</a>";
       $private = ""; }
	else {
	   $displayFlag = 1;
       $public = "<a href=\"$url&event=$s&locate=1\">Public Check In</a>";
       $private = "<a href=\"$url&event=$s&locate=0\">Private Check In</a>"; }

       # Display the counter and the event name, then public & private check in links
         echo "<p>$counter. $s <br/> $space $space $space $public $space $space $private</p>\n"; # loads updateCheckIn.php
         $counter++;
       }
       	if ($counter == 1) { echo "None"; }
  	
	if ($displayFlag) {
	   echo "<p><br>\"Public Check In\" allows others to locate you and view your profile;";
       echo "<br>\"Private Check In\" will prevent others from locating you and viewing your profile.</p>\n"; }

  # ------------------------------------------------------------
	   # Display Other Actions
       echo "<p style=\"font-weight:bold; color:$color4\"><br>Other Actions:<p>\n";

	   # View profile
       $url = "<a href=\"viewProfile.php?name=$name&email=$email&event=$event\">View Profile:</a>";
       echo "<p> $space $space $url<br>";
	   echo "$space $space $space $space $name ($email)</p>\n";

       # Count of Attendees by Session
	   $url = "<a href=\"attendance_events.php\">Count Attendees by Session</a>";
       echo "<p> $space $space $url<br>";

       # Reset name & email
       echo "<p>$space $space <a href=\"reset.php\">Reset Name & Email</a></p>\n";

  # ------------------------------------------------------------

  	} else	{
# ======================================================================================================
# ======================================================================================================
# ======================================================================================================

# We ARE checked in to an event... 
 	
	 echo "<p class=\"center\" style=\"font-weight:bold; color:$color3\">$event<br>Check In System</p>\n"; # Display event name

       $sessions = readCSV($schedule);	# returns an array of all lines from $schedule
       $cSessions = getCurrentSessions($sessions, $schedule_timezone);	# in currentSessions.php - returns an array of session names & ID's
       
  # ------------------------------------------------------------
  # Display Currently Running Sessions

		 $sessionType = "Sessions:";
		 if ($event == "ESIP Telecons") { $sessionType = "Telecons:"; }

		echo "<p> <span style=\"background-color:$color2; color:$color4; font-weight:bold\">Currently Running $sessionType</p>\n";


       $counter = 1;
       foreach($cSessions as $s) {		# cSessions is an array of currently running session names, ID's

		$parts = explode (",",$s);
      
		# Set up link to Check In to this session
         $checkin = "<a href=\"attendees.php?name=$name&email=$email&session=$parts[0]&check=in&event=$event&event_logs=$event_logs&attendees_log=$attendees_log&recommendation_interface=$recommendation_interface&session_id=$parts[1]\">Check In</a>";
		# Set up link to List Attendees in this session
         $participants = "<a href=\"attendees.php?name=$name&email=$email&session=$parts[0]&event=$event&event_logs=$event_logs&attendees_log=$attendees_log&recommendation_interface=$recommendation_interface&session_id=$parts[1]\">Who's There</a>";
		# Set up link to List Recommendations in this session
         $recommendations = "<a href=\"viewRecommendations.php?name=$name&email=$email&event=$event&recommendation_interface=$recommendation_interface&session=$parts[0]&session_id=$parts[1]\">Recommendations</a>";

        # Display the counter and the session name
         echo "<p>$counter. $parts[0]<br/>";
         if (!$recommendation_interface) {echo "$space $space"; }
		 echo "$checkin";
         if (!$recommendation_interface) {echo "$tab $participants"; }
         else {echo "$tab2 $participants $tab2 $recommendations"; }
		 echo "</p>\n";
         $counter++;
       }
       	if ($counter == 1) { echo "<p>$space $space None</p>"; }

  # ------------------------------------------------------------
  # Display Last Session/Telecon checked-in to

       $line = getAttendeesByEmail($email, $attendees_log);	# in attendeeLog.php, read $attendees, return last line in the file that matches $email

	   # If we have ever checked in to a session...
       if ( $line != '' ) { # Display session this attendee is currently checked in to and offer to "Check Out"
         $lineParts = explode(",", $line);
         $currentSession = $lineParts[2];	# Last session attendee checked-in to
         $currentStatus = $lineParts[3]; # 1=checked-in; 0=checked-out

         if ( $currentStatus ) {	# if still checked in to this session
            $checkout = "<a href=\"attendees.php?name=$name&email=$email&session=$currentSession&check=out&event=$event&event_logs=$event_logs&attendees_log=$attendees_log&recommendation_interface=$recommendation_interface\">Check Out</a>"; }
         else { $checkout = "(Checked out)"; }

#		 $sessionType = "Session";
#		 if ($event == "ESIP Telecons") { $sessionType = "Telecon"; }
# 		 echo "<p> <span style=\"background-color:$color2; font-weight:bold\">Your Last $sessionType Check In:</p>"; 

 		 echo "<p> <span style=\"background-color:$color2; color:$color4; font-weight:bold\">Your Last Check In:</p>"; 
         echo "<p style=\"font-weight:normal\">$currentSession <br> $space $space";
         echo "$checkout</p>";

        } # End of If we have ever checked in to a session

  # ------------------------------------------------------------
	   # Display Other Actions
       echo "<p> <span style=\"background-color:$color2; color:$color4; font-weight:bold\">Other Actions:<p>\n";

	 if ($event != "ESIP Telecons") {

       # Count of Attendees by Session
	   $url = "<a href=\"attendance_summary.php?event=$event\">Count Attendees by Session</a>";
       echo "<p> $space $space $url<br>";

       # List Event Attendees
	   $url = "<a href=\"listAttendees.php?name=$name&email=$email&event=$event&event_logs=$event_logs&recommendation_interface=$recommendation_interface\">List Event Attendees</a>";
       echo "<p> $space $space $url<br>";

       # List Recommended Collaborators (only if flag in event_list file is not null (it's an event ID)):
       if ($recommendation_interface) {
	       $url = "<a href=\"viewRecommendations.php?name=$name&email=$email&event=$event&recommendation_interface=$recommendation_interface\">List Event Recommendations</a>";
	       echo "<p> $space $space $url</p>\n";
       	   }
		}

       # Check-out-of-this-event (in updateCheckIn.php) 
       $d = $GLOBALS['spoofedDate'];
       $url = "updateCheckIn.php?spoofedDate=$d&name=$name&email=$email&checkin=0&locate=0&event=$event";
	   #echo "URL prior to checkout: $url";       # For degug purposes
       echo "<p>$space $space <a href=\"$url\">Check Out Of This Event</a></p>\n";

	 if ($event != "ESIP Telecons") {

	   # View profile
       $url = "<a href=\"viewProfile.php?name=$name&email=$email&event=$event\">View Profile:</a>";
       echo "<p> $space $space $url<br>";
	   echo "$space $space $space $space $name ($email)</p>\n";
		}
  # ------------------------------------------------------------
   # Write log_line (date,name,email) to $imhere_log
     $fh = fopen($imhere_log, 'a') or die("can't open this file: $imhere_log");
     fwrite($fh, "$log_line\n");
     fclose($fh);

     } # END of ELSE: We ARE checked in

   }   # END of ELSE: Not processing a RESET

   # ------------------------------------------------------------
  
    } else {	# No $name or $email - make 'em enter it here:
  echo "<p class=\"center\" style=\"font-weight:bold; color:$color3\">ImHere<br>";
  echo "<class=\"center\" style=\"font-weight:bold; color:$color3\">Event Check In System</p>";
     $action = htmlspecialchars($_SERVER["PHP_SELF"]);
     echo "<form method=\"GET\" action=\"$action\">\n";
     echo "  <label>Name:</label> <input type=\"text\" name=\"name\" >\n";
     echo "$tab <br/><label>Email:</label> <input type=\"text\" name=\"email\" >\n";  
     echo "$tab <br/><br/><input type=\"submit\">\n";

echo "<p>Enter your name and email address. ";
echo "Some systems attempt to auto-capitalize when you enter your address. ";
echo "Please verify that the capitalization is correct in your address.</p>";


     echo "</form>\n";
	  }
    # ------------------------------------------------------------
  
   # close out the html
  echo "  </body>\n";
  echo "</html>\n";
  
?>
