<?php 

/*  
Reset the Check In system
Unset the cookie, if there is one
Check this person out of the event
Post this person's checkout to ResearchBit (not yet implemented)
Set the return URL to imhere.php with no GET variables
*/
# ------------------------------------------------------------

# Unset the cookie
     if (isset($_COOKIE["esip"])) {
       $cvalue = $_COOKIE["esip"];
       $parts = explode(":", $cvalue);
       $name = $parts[0];
       $email = $parts[1];
       $event = $parts[2];
       $ORCIDiD = $parts[3];
#	   echo "<p>Cookie found: $name, $email, $event, $ORCIDiD";
       unset($_COOKIE['esip']);
       setcookie('esip', false, time()-3600, '/');
       }

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
# Check this person out of the event (Update the file checkedIn.txt)

   $fh = fopen('./logs/checkedIn.txt', 'a') or die("<br>Can't open checkedIn.txt file");
     fwrite($fh, "$name:$email:0:0:$event\n");
     fclose($fh);

# Should probably be updating ResearchBit with check-out info here too...

# ------------------------------------------------------------
# Set the return URL to imhere.php with no GET variables
	include './config.php';
    $url = $server . "imhere.php";
#	echo "<p>Name & email reset.</p>";
	echo "<p><br><br>Click <a href=\"$url\">here</a> to re-enter name, email, ORCID iD.</p>";

  # close out the html
  echo "  </body>\n";
  echo "</html>\n";
  
?>
