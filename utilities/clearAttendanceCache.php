<?php

include '../config.php';

#######

$form = "    <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\"> " .
        "      <p style=\"color:red\">Clear Attendance Cache Data</p><br/> " .
        "      Password: <input type=\"password\" name=\"pwd\"/> " .
        "      <input type=\"submit\" /> " .
        "    </form><br/> "; 

echo $form;

if ( isset($_POST['pwd']) && $_POST['pwd']==$pwd ) { 

  $cache = $log_dir . 'attendance_cache/';
  $files = scandir($cache);

  # ignore
  $f1 = $cache . '.';
  $f2 = $cache . '..';

  # loop over the files and delete them
  foreach($files as $f) { 
    $file = $cache . $f;
    if ( ($file != $f1) && ($file != $f2) ) {
      unlink($file); 
    }
  }

  echo "Cache cleared";

}

if ( isset($_POST['pwd']) && $_POST['pwd']!=$pwd ) { echo "Error: Pasword Incorrect"; }
   
?>
