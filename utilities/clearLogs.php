<?php

  # read the config file
  include '../config.php';

  if ( isset($_POST['pwd']) ) { 
    if ( ($_POST['pwd']==$pwd) ) {

# This won't work unless it's made event-specific...
#      $handle = fopen($attendees_log, "w");
#      fclose($handle);

	  $aaa =  '../logs/checkedIn.txt';
      $handle = fopen($aaa, "w");
      fclose($handle);

	  $bbb =  '../logs/imhere_log.txt';
      $handle = fopen($bbb, "w");
      fclose($handle);

      chmod($log1, 0664);
      chmod($log2, 0664);

      echo "<h2 stye=\text-align:center\">Success: Logs Cleared</h2><br/>";

    } else {

        echo "<h2 style=\"text-align:center;color:red\">ERROR: Password is incorrect</h2><br/>"; 

    }
  }

?>

<html>
  <body>
    <form action="" method="POST" enctype="multipart/form-data">
      Password: <input type="password" name="pwd"/>
      <input type="submit" />
    </form>
  </body>
</html>
