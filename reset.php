<?php 

/*  Reset the Check In system
*/
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
   $fh = fopen('./logs/checkedIn.txt', 'a') or die("<br>Can't open checkIn.txt file");
     fwrite($fh, "$name:$email:0:0:$event\n");
     fclose($fh);

# Set the return URL to imhere.php with no GET variables
	include './config.php';
    $url = $server . "imhere.php";
	echo "<p>Name & email reset.</p>";
	echo "<p>Click <a href=\"$url\">here</a> to continue.</p>";

 # ------------------------------------------------------------
  # close out the html
  echo "  </body>\n";
  echo "</html>\n";
  
?>
