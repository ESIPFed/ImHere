<?php 

  # include the needed functions
  include 'topics.php';
  include 'readCSV.php';

  # setup html
  echo "<!DOCTYPE>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>ImHere Attendees</title>";
  echo "  </head>\n";
  echo "  <body>\n";

  # look for GET variables
  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }

  # look for this person's interests
  $interests = getInterests($name,$email);
  $size = sizeof($interests);
  echo "<p>Name: $name</p>";
  echo "<p>Affiliation: $interests[0]</p>";
  if ($size > 1) {
    echo "<p>Interests:</p>";
    echo "<ul>";
    $count = 0;
    foreach ($interests as $in) {
      if ($count>=1) { echo "<li><p>$in</p></li>"; }
      $count++;
    }
    echo "</ul>";
  }

  # return link
  $link = "<br/><p><a href=\"imhere.php\">Return to Check-In Menu</a></p>";
  echo $link;

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
