<?php

  $pwd = 'PASSWORD';

  if ( isset($_POST['pwd']) ) { 
    if ( ($_POST['pwd']==$pwd) ) {

      $dir = '/var/www/html/sloan/logs/';

      $log1 = $dir . 'attendees.txt';
      $log2 = $dir . 'beacon_log.txt';
      $log3 = $dir . 'checkedIn.txt';
      $log4 = $dir . 'imhere_log.txt';

      $handle = fopen($log1, "w");
      fclose($handle);
      $handle = fopen($log2, "w");
      fclose($handle);
      $handle = fopen($log3, "w");
      fclose($handle);
      $handle = fopen($log4, "w");
      fclose($handle);

      chmod($log1, 0664);
      chmod($log2, 0664);
      chmod($log3, 0664);
      chmod($log4, 0644);

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
