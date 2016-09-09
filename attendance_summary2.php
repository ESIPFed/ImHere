<?php

# ------------------------------------------------------------------------------------
# Get variables from the url
$event = $_GET['event'];
$email = $_GET['email'];
$tot = $_GET['tot'];
$in = $_GET['in'];
$aaa = $_GET['aaa'];
$currentSessionsLog = $_GET['currentSessionsLog'];

# ------------------------------------------------------------------------------------
# Set URL of page name to reload
$page = $server . "attendance_summary.php?event=$event&email=$email";

# How many seconds before auto-reload
$sec = "10";

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"refresh\" content=\"$sec;URL='$page'\">\n";
echo "      <meta charset=\"utf-8\">\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "	    <style>\n";
echo "         body { background-color:darkseagreen; }\n";
echo "         text {	font: 15px sans-serif; } \n";
echo "         rect.background { fill: white; }\n";
echo "         .axis { shape-rendering: crispEdges; }\n";
echo "         .axis path, .axis line {\n";
echo "            fill: none;\n";
echo "            stroke: #000;\n";
echo "         }\n";
echo "      </style>\n";
echo "  </head>\n";
echo "  <body>\n";

#----------

$space = "&nbsp;";
echo "   <h3 style=\"text-align:center\">$event</h3>";
#echo "   <h3 style=\"text-align:center\">Total Event Attendees: $tot $space Still Checked-In: $in</h3>";
echo "   <h3 style=\"text-align:center\">Total Event Attendees: $tot</h3>";
echo "   <h3 style=\"text-align:center\">Attendee Count by Session - Current Sessions</h3>";
echo "   <script>var aLog = \"$currentSessionsLog\";</script>\n";
echo "   <script src=\"http://d3js.org/d3.v3.min.js\"></script>\n";
echo "   <script src=\"bar_chart.js\"></script>\n";

#----------

echo "   </body>\n";
echo "</html>";

?>
