<?php 
/*
Sync routine - Purpose is to update ResearchBit files to be the same as ImHere.
That is, who is checked into this event right now?

Just doing event check-ins now. How do we do sessions? I don't know...

We first do a reset of the ResearchBit database (This should be made event-specific in the future)
Maybe put it in a cURL request eventually, but for now, do it by loading a URL link

We then clear rb_response.txt so we can verify sync success afterwards 

Next batch post the event check-in data to ResearchBit
	Maybe prompt for event later; but for now, make sure it's loaded as a GET variable
	e.g. "syncResearchBit.php?event=ESIP Summer Meeting 2016"


*/
#----------------------------------------------------------------------------
#----------------------------------------------------------------------------
#----------------------------------------------------------------------------

  # setup html
  echo "<!DOCTYPE>\n";
  echo "<html>\n";
  echo "  <head>\n";
  echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  echo "    <title>ImHere Sync ResearchBit</title>";
  echo "  </head>\n";
  echo "  <body style=\"background-color:darkseagreen;\">\n";
  echo "  <body>\n";

$event = $_GET['event'];
$event = urlencode ($event);
/*

Display three links:

	Reset ResearchBit Data Files

	Post ImHere Check-in Data to RB

	Back to ImHere System Menu

*/

$url="http://54.175.39.137:5000/reset";
echo "<p><br><br><a href=$url>Reset ResearchBit Data Files</p></a>";

echo "<p><br><a href=syncResearchBit_post?event=$event>Post event check-in's to ResearchBit</p></a>";

echo "<p><br><a href=\"imhere.php\">Back to ImHere System Menu</a></p>";

echo "  </body>\n";
echo "</html>\n";



?>