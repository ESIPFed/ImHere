<?php 

function convertNewLines($fileName) {

      # convert newlines
      $fixedSchedule = array();
      $handle = fopen($fileName, "r");
      if ($handle) {
        while (($line = fgets($handle)) !== false) {
           #$line = preg_replace('~\R~u', "\r\n", $line);
           $line = preg_replace('/(\r\n|\r|\n)/s', "\n", $line);
           $fixedSchedule[] = $line;
        }
        fclose($handle);
      } 

      # write out the fixed schedule
      $fh = fopen($fileName, "w");

	  if ($fh) {
      foreach($fixedSchedule as $line) {
        fwrite($fh, $line);
      }
      fclose($file);
	  } else { echo "Not really a success...<br>Couldn't open $fileName in write mode<p>"; }
}

function createForm($eventName, $logDir) {

#echo "logDir: $logDir<br>"; # Dan testing

  $form = "    <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\"> " .
          "      <p style=\"color:red\">$eventName</p><br/> " .
          "      Schedule: <input type=\"file\" name=\"schedule\" /> " .
          "      Registration: <input type=\"file\" name=\"registration\" /> " .
          "      Password: <input type=\"password\" name=\"pwd\"/> " .
          "      <input type=\"hidden\" name=\"logDir\" value=\"$logDir\" /> " .
          "      <input type=\"submit\" /> " .
          "    </form><br/> "; 

  return $form;

}

# include the config file
include '../config.php';
include '../readCSV.php';

### process form ###

  if ( isset($_POST['pwd']) && $_POST['pwd']==$pwd ) { 

    $scheduleFile = $_FILES['schedule']['tmp_name'];
    $regFile = $_FILES['registration']['tmp_name'];

    if ( $scheduleFile != '' ) {

      $newFile = $_POST['logDir'] . '/schedule.csv';
      $tmpFile = $_FILES['schedule']['tmp_name'];
      $r = move_uploaded_file($tmpFile, $newFile);
      convertNewLines($newFile);
      echo "<h2 style=\"text-align:center\">Success: Schedule File Uploaded to $newFile</h2><br/>";

    } 

    if ( $regFile != '' ) { 

      $newFile = $_POST['logDir'] . '/registration.csv';
      $tmpFile = $_FILES['registration']['tmp_name'];
      $r = move_uploaded_file($tmpFile, $newFile);
      convertNewLines($newFile);
      echo "<h2 style=\"text-align:center\">Success: Registration File Uploaded to $newFile</h2><br/>";
    
    }
   
  }

  if ( isset($_POST['pwd']) && $_POST['pwd']!=$pwd ) {

     echo "<h2 style=\"text-align:center;color:red\">ERROR: Password is incorrect</h2><br/>"; 
      
  }
      
  if ( isset($_POST['pwd']) && !isset($_FILES['schedule']) && !isset($_FILES['registration'])) { 
    echo "No files selected for upload"; 
  }

####################

# opening HTML
echo "<html>";
echo " <body>";
echo "  <h2 style=\"text-align:center\">Upload new schedule or registration for any of the following events</h2>";

	  $schedule = '../logs/event_list.csv'; # Had to add this to work on Dan's local system

$events = readCSV($schedule);	# returns an array of all lines from $schedule

# each line = Start Date (0), End Date (1), Event Name (2), Log Directory (3), Time Zone (4), Password (5), Recommendation System (6)
$lines = 1;
$eventName = '';
foreach ($events as $event) {

  # ignore line 1 (header)
  if ($lines != 1) {
    $counter = 0;
    foreach ($event as $part) {

      # event name
      if ($counter == 2) { $eventName = $part; }

      # log directory
      if ($counter == 3) { 

        $dir = $log_dir . $part; # Doesn't work on Dan's local dev system (= ./logs)
#echo "dir: $dir<br>"; # Dan testing
        $dir = '../logs/' . $part; # Had to insert to work on Dan's local dev system
#echo "dir: $dir<br>"; # Dan testing

        if ( !file_exists($dir) ) { mkdir($dir,0777); }
        $form = createForm($eventName, $dir);
        echo "$form";
      }

      $counter++;
      if ($counter == 7) { $counter = 0; }
    }
  }
  $lines++;

}

# closing HTML
echo " </body>";
echo "</html>";
       
?>
