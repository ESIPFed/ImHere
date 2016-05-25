<?php

  # read the config file
  include '../config.php';

  if ( isset($_POST['pwd']) ) { 
    if ( isset($_FILES['events']) && ($_POST['pwd']==$pwd) ) {

	  $schedule = '../logs/event_list.csv'; # Had to add this to work on Dan's local system

      $newFile = $schedule;
      $tmpFile = $_FILES['events']['tmp_name'];
      $r = move_uploaded_file($tmpFile, $newFile);
      echo "<h2 stye=\text-align:center\">Success: File Uploaded to $newFile</h2><br/>";

      # convert newlines
      $fixedSchedule = array();
      $handle = fopen($newFile, "r");
      if ($handle) {
        while (($line = fgets($handle)) !== false) {
           #$line = preg_replace('~\R~u', "\r\n", $line);
           $line = preg_replace('/(\r\n|\r|\n)/s', "\n", $line);
           $fixedSchedule[] = $line;
        }
        fclose($handle);
      } 

      # write out the fixed event list 
      $fh = fopen($newFile, "w");
	  if ($fh) {
	      foreach($fixedSchedule as $line) {
        fwrite($fh, $line);
      }
      fclose($file);
	  } else { echo "Not really a success...<br>Couldn't open $newFile in write mode<p>"; }

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
      <input type="file" name="events" />
      Password: <input type="password" name="pwd"/>
      <input type="submit" />
    </form>
  </body>
</html>
