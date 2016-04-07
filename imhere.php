<?php 

/*  Dan's changes to-date:
	  	Remove Sloan sponsor logo
	  	Display header lines even on mobile devices (see stylesheet.css)
		Change event name $heading to "ESIP telecons"
		Add bold format to display of application name and event name 
		Change verbiage to "Not Checked In" ($sessionIn) and "You Last Checked In To:" ($checkInHead)
		Removed extra blank line above "Currently Running Sessions:"
		Changed verbiage related to public/private event check in
*/

/*  Changes we want to make:
		Remove 7-day cookie expiration?
		Display start & end time of currently running sessions
		Add a hidden, user-entered date variable that overrides current date, for testing purposes
*/

/*	Big change coming up: Incorporate logic for multiple events (ESIP Telecons, Winter Meeting, etc.):
		New file in root dir listing all events with event name, password, start & end dates, timezone
		Check into an event same as we do sessions
			If currently checked in to an event…
				Display name of event checked in to
				Proceed with session check in logic
			If not checked in to an event…
				List currently running events, with start & end dates
				Have options to check in, and List Attendees
				Other Actions: Log out of everything (remove name & email, cookie)
		Set variable $event to indicate the event we're processing
		Keep separate schedule file, attendee file, and log files for each event
		$log_dir = '$event/logs/';	Something like that
*/

// ----------------------------------------------------------- 
/* List of log files...
  
	attendees.txt
 		Logs who checks in/out of sessions
  		Format: name,email,session,status (1 chked in, 0 chked out),time
	checkedIn.txt
		Logs who checks in/out of the event
		Format: name:email:in/out status (1=in, 0=out):public/private flag (1=public, 0=private)
	imhere_log.txt
		Purpose - ???
		Format: date/time,name,email
		A line is written to this file every time someone:
			Submits a name & email;
			Checks in to an event;
			Checks in to a session;
			Checks out of a session;
			Clicks List Attendees;
			Reloads/refreshes the page.
  	Schedule.csv
  		Format: date,start time,end time,session name
	Registration.csv
  		Format: 
  			Header line; then
  			Line number,email,name (first last),Job title,Company/organization
*/			
  
// ------------------------------------------------------------ 
/* List of program files (extension .php)...
	- imhere.php - This file
	- config.php - Sets up lots of configuration variabls used throughout the system
	- attendeeLog.php
		Functions:
			get AttendeesByEmail - Determine the session this attendee is currently checked in to 
			getAttendees - Build an array of session attendees as name,email,:,status
			readAttendeeLog -  Find all lines in attendees.txt that match this session
	- readCSV.php - Returns an array of lines from $schedule
	- attendees.php
	- beacon.php
	- checkin.php
	- clearLogs.php
	- currentSessions.php
	- profile.php
	- newAttendees.php
	- newSchedule.php
	- topics.php
	- updateCheckIn.php  
	- stylesheet.css - CSS file
*/
// ------------------------------------------------------------
/*  List of variables...

   From config.php:
  	 $log_dir 			= '/var/www/html/sloan/logs/';
  	 $attendees_log 	= $log_dir . 'attendees.txt';
  	 $beacon_log 		= $log_dir . 'beacon_log.txt';
  	 $checkIn_log 		= $log_dir . 'checkedIn.txt';
  	 $imhere_log 		= $log_dir . 'imhere_log.txt';
  	 $schedule 			= $log_dir . 'schedule.csv';
  	 $date 				= date("m-d-Y_H:i:s");
  	 $schedule_timezone	= 'America/New_York';
  	 $schedule_format 	= 'm/d/y_H:i';
  	 $pwd 				= 'ESIP2016!';

  As they are encountered herein:
  	$space			"&nbsp;"
  	$tab			Five x $space
  	$heading		ESIP Winter Meeting 2016 or ESIP Telecons
  	$sessionIn		Name of the session the attendee is checked in to
  	$checkInHead	"Currently checked in to: or "Last checked in to:
  	$name			Attendee name
  	$email			Attendee email
  	$log_line		Date, name, email written to log file $imhere_log'
  	$cvalue			Cookie Value - what we get from performing $_COOKIE["esip"]
  	$parts			Cookie Parts, from explode(":", $cvalue)
  	$cookie_name	When writting new cookie, = "esip"
  	$cookie_value	When writting new cookie, = "$name:$email"
  	$checkedIn		Checked in to the event? From isCheckedIn($name, $email)
  	$sessions		Array of all lines from $schedule - Format is date, start time, end time, session name
  	$cSessions		Array of session names currently running
  	$line			Last line in $attendees that matches this $email
  	$lineParts		$line formatted into an array
  	$currentSession	Last session attendee checked-in to
  	$currentStatus	Status assoc w/ $currentSession (1=checked-in; 0=checked-out)
  	$checkout		Link to attendees.php checkout-of-this-session routine
  	$counter		Number to diplay in front of a currently running session
  	$s				Name of a currently running session
  	$checkin		Link to attendees.php checkin-to-this-session routine
  	$participants	Link to attendees.php list attendees routine
  	$url			Link to updateCheckIn.php checkout-of-this-event routine
  	$fh				File handle when writing log_line (date,name,email) to $imhere_log
  	$action
*/

# ------------------------------------------------------------
# include the config file
  include 'config.php';
# ------------------------------------------------------------
# include needed functions
  include 'checkin.php'; 
  include 'readCSV.php';			# returns an array of lines from 'schedule.csv'
  include 'attendeeLog.php';
  include 'currentSessions.php';
# ------------------------------------------------------------
# html setup
  echo "<!DOCTYPE html>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>Session Check In</title>\n";
  echo "  </head>\n";
  echo "  <body>\n";

  # return heading
  $space = "&nbsp;";
  $tab = $space . $space . $space . $space . $space;
  $heading = "<p class=\"center\" style=\"font-weight:bold\">ESIP Telecons</p>\n"; # was ESIP Winter Meeting 2016
  $sessionIn = "<p style=\"font-style:italic\">Not Checked-In</p>";				# was Not Checked-In To A Session
  $checkInHead = "<p style=\"font-weight:bold\">You Last Checked In To:</p>";	# was You Are Currently Checked-In To:

# ------------------------------------------------------------
# Try to find a name and email...
# Check for GET variables
  if ( isset($_GET['name']) && isset($_GET['email']) ) {
     $name = $_GET['name'];
     $email = $_GET['email'];
     $log_line = "$date,$name,$email";
  } else { 
# no GET variables try looking for the esip cookie
     if (isset($_COOKIE["esip"])) {
       $cvalue = $_COOKIE["esip"];
       $parts = explode(":", $cvalue);
       $name = $parts[0];
       $email = $parts[1];
       $event = $parts[2];	# DK
     }
  }
  # ------------------------------------------------------------
  # Display sponsor logo
  #  echo "<img class=\"img\" src=\"images/sloan_logo.png\"><br/>";
  #  echo "<img class=\"img\" src=\"images/sloan_small.png\"><br/>";
  # ------------------------------------------------------------
  # Display application name
  echo "<p class=\"center\" style=\"font-weight:bold\">ImHere Check In System</p>";
  # ------------------------------------------------------------
  # Display event name
  echo $heading;
  # ------------------------------------------------------------
  # If we have a name and email,
  # and if this person has checked in to the event,
  # then display the session they're currently checked in to
  
  if ( isset($name) && isset($email) ) {	# If we have a name and email...
  
     # try to set a cookie for this user to expire in 7 days
     if ( !isset($_COOKIE["esip"]) ) {
       $cookie_name = "esip";
       $cookie_value = "$name:$email";
       setcookie($cookie_name, $cookie_value, time()+(86400*7), "/"); 
     }

     $checkedIn = isCheckedIn($name, $email);
     if ( $checkedIn ) {	# has this person checked into the event?

       $sessions = readCSV($schedule);	# returns an array of lines from $schedule
       $cSessions = getCurrentSessions($sessions);	# returns an array of sessions currently running
       echo "$checkInHead"; # "Currently checked in to:"
       
       # Display session this attendee is currently checked in to and offer to "Check Out"
       $line = getAttendeesByEmail($email);	# read $attendees; return last line in the file that matches $email
       
       if ( $line != '' ) {		    
         $lineParts = explode(",", $line);
         $currentSession = $lineParts[2];	# Last session attendee checked-in to
         $currentStatus = $lineParts[3]; # 1=checked-in; 0=checked-out
         if ( $currentStatus ) {	# if this person still checked in to this session
            $checkout = "<a href=\"attendees.php?name=$name&email=$email&session=$currentSession&check=out\">Check Out</a>";
            echo "<p>$currentSession $tab $checkout</p>";
         } else {
            echo "$sessionIn";
         }
       } else { echo "$sessionIn"; }

  # ------------------------------------------------------------
  # Display Currently Running Sessions
#       echo "<br /><p style=\"font-weight:bold\">Currently Running Sessions:</p>\n";
       echo "<p style=\"font-weight:bold\">Currently Running Sessions:</p>\n";
       $counter = 1;
       
       foreach($cSessions as $s) {		# cSessions is an array of session names currently running
      
		# Set up link to Check In to this session
         $checkin = "<a href=\"attendees.php?name=$name&email=$email&session=$s&check=in\">Check In</a>";
		# Set up link to Check Out of this session
         #$checkout = "<a href=\"attendees.php?name=$name&email=$email&session=$s&check=out\">Check Out</a>";
		# Set up link to List Attendees in this session
         $participants = "<a href=\"attendees.php?name=$name&email=$email&session=$s\">List Attendees</a>";

       # Display the counter and the session name
         echo "<p>$counter. $s 
         <br/> 
         $space $space $checkin $tab $participants</p>\n"; # Display links to Check In and List Attendees
         $counter++;
       }
  # ------------------------------------------------------------
	   # Display Other Actions
       echo "<br/>\n";
       echo "<p style=\"font-weight:bold\">Other Actions<p>\n";
       
       # Set up & display link to "check-out-of-this-event" routine in updateCheckIn.php 
       $url = "updateCheckIn.php?name=$name&email=$email&checkin=0&locate=0";
       echo "<p><a href=\"$url\">Check Out Of This Event</a></p>\n";
     } else {

  # ------------------------------------------------------------
  # Not checked in to the event
       $url = "updateCheckIn.php?name=$name&email=$email&checkin=1";
       echo "<p>$name you are not yet checked into the event.</p>\n";
       echo "<p><a href=\"$url&locate=1\">Check In</a> $tab or $tab <a href=\"$url&locate=0\">Private Check In</a></p>\n";

       echo "<p style=\"font-weight:bold\">Check In allows others to locate you at this event</p>\n";
       echo "Private Check In will prevent others from locating you</p>\n";
     }

   # ------------------------------------------------------------
   # Write log_line (date,name,email) to $imhere_log
     $fh = fopen($imhere_log, 'a') or die("can't open this file: $imhere_log");
     fwrite($fh, "$log_line\n");
     fclose($fh);
  } else {
    # ------------------------------------------------------------
    # No Name or email - make 'em enter it here:
     $action = htmlspecialchars($_SERVER["PHP_SELF"]);
     echo "<form method=\"GET\" action=\"$action\">\n";
     echo "  Name: <input type=\"text\" name=\"name\" >\n";
     echo "$tab Email: <input type=\"text\" name=\"email\" >\n";  
     echo "$tab <input type=\"submit\">\n";
     echo "</form>\n";
  }
  # ------------------------------------------------------------
  # close out the html
  echo "  </body>\n";
  echo "</html>\n";
  
?>
