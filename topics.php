<?php 

function getInterests ($person, $email) {

  # entry, email, full name, job title
  $topics = array(
   '5'  => "Big Data",
   '6'  => "Cloud Computing", 
   '7'  => "Data Discovery",
   '8'  => "Data Management",
   '9'  => "Data Preservation",
   '10'  => "Data Stewardship",
   '11'  => "Disasters",
   '12'  => "Drones",
   '13'  => "Documentation",
   '14'  => "Drupal",
   '15'  => "ESDA",
   '16'  => "Education",
   '17'  => "Energy and Climate",
   '18'  => "EnviroSensing",
   '19'  => "Geospatial",
   '20'  => "Information Quality",
   '21'  => 'IT&I',
   '22'  => "Libraries",
   '23'  => "Partnership",
   '24'  => 'Products & Services',
   '25'  => "Semantic Web",
   '26'  => "Science Software",
   '27'  => "Testbed",
   '28'  => 'Visualization',
   '29'  => 'Web Services');

  # file to use
  $file = './logs/registration.csv';

  # read the attendee data
  $results = readCSV( $file );

  $interests = array();
  foreach( $results as $s ) { 
    $i = 0;
    $name = strtolower($s[2]);
    $e = trim($s[1]);
    $person = strtolower($person);
    if ( ($name == $person) && ($e == $email) ) {
      foreach ( $s as $part ) {
        if ( $i == 4 ) { $interests[] = $s[4]; } # affiliation
        if ( ($i>=5) && ($part=='Checked') ) { $interests[] =  "$topics[$i] "; }
        if ( ($i==30) && ($part!='') ) { $interests[] = $part; }
        $i++;
      }
    }  
  }

  return $interests;

}

?>
