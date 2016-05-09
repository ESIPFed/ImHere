<?php

   function readCSV( $file ) {

     $result = array();
     $i = 0;
     $csvFile = file($file);
     foreach ($csvFile as $line) {
       $result[$i] = str_getcsv($line);
       $i++;
     }
     return $result;

   }

?>
