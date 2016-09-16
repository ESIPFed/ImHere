<?php

# ------------------------------------------------------------------------------------
# Get variables from the url
$event_logs = $_GET['event_logs'];
$event = $_GET['event'];
$email = $_GET['email'];
$regCount = $_GET['regCount'];
$tot = $_GET['tot'];
$pct = $_GET['pct'];
$in = $_GET['in'];
$aaa = $_GET['aaa'];
$currentSessionsLog = $_GET['currentSessionsLog'];

# ------------------------------------------------------------------------------------

include 'config.php';

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

$regFile = $log_dir . $event_logs . '/' . 'registration.csv';
$regCount = count(file($regFile));

$pct = ($tot/$regCount) * 100;
$pct = number_format($pct, 0);

$space = "&nbsp;";
echo "   <h3 style=\"text-align:center\">$event</h3>";
echo "   <h3 style=\"text-align:center\">Registrations: $regCount $space Event Check-Ins: $tot $space ($pct%)</h3>";
echo "   <h3 style=\"text-align:center\">Check-Ins by Session - Currently Running Sessions";

echo "   <script>var aLog = \"$currentSessionsLog\";</script>\n";
echo "   <script src=\"http://d3js.org/d3.v3.min.js\"></script>\n";
echo "   <script src=\"bar_chart.js\"></script>\n";

#----------

echo "   </body>\n";
echo "</html>";

?>
