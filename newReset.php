<?php 
/*
We were going to build a menu of reset functions here, for ImHere and ResearchBit, but have abandoned/tabled the effort.
*/

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