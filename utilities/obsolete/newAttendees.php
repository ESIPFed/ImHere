<?php

  # read the config file
  include 'config.php';

  if ( isset($_POST['pwd']) ) { 
    if ( isset($_FILES['schedule']) && ($_POST['pwd']==$pwd) ) {

      $newFile = $log_dir . 'registration.csv';
      $tmpFile = $_FILES['schedule']['tmp_name'];
      move_uploaded_file($tmpFile, $newFile);
      echo "<h2 stye=\text-align:center\">Success: File Uploaded</h2><br/>";

      # convert newlines
      $fixedSchedule = array();
      $handle = fopen($newFile, "r");
      if ($handle) {
        while (($line = fgets($handle)) !== false) {
           $line = preg_replace('~\R~u', "\r\n", $line);
           $fixedSchedule[] = $line;
        }
        fclose($handle);
      } 

      # write out the fixed schedule
      $fh = fopen($newFile, "w");
      foreach($fixedSchedule as $line) {
        fwrite($fh, $line);
      }
      fclose($file);

    } else {

      if ($_POST['pwd'] != $pwd) { 
        echo "<h2 style=\"text-align:center;color:red\">ERROR: Password is incorrect</h2><br/>"; 
      }
      if (!isset($_FILES['schedule'])) { echo "No file selected for upload"; }

    }
  }

?>

<html>
  <body>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="file" name="schedule" />
      Password: <input type="password" name="pwd"/>
      <input type="submit" />
    </form>
  </body>
</html>
