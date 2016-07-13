<?php 

# 06/06/16 - Added queryName & queryEmail to differentiate between user name/email, and that of the person we're displaying profile info for

/*	This routine called from...
		imhere.php - For users to view their own profile info
		attendees.php - For users to view the profile of others
		viewRecommendations.php - For users to view the profile of recommendations
*/

  include 'config.php';
  $space = "&nbsp;";

  # setup html
  #echo "<!DOCTYPE>\n";
  #echo "<html>\n";
  #echo "  <head>\n";
  #echo "    <link rel=\"stylesheet\" href=\"stylesheet.css\">\n";
  #echo "    <title>Display Attendee Profile</title>";
  #echo "  </head>\n";
  #echo "  <body style=\"background-color:darkseagreen;\">\n";
  #echo "  <body>\n";

  # look for GET variables
  if ( isset($_GET['name']) ) { $name = $_GET['name']; } else { $name = ''; }
  if ( isset($_GET['email']) ) { $email = $_GET['email']; } else { $email = ''; }
  if ( isset($_GET['queryName']) ) { $queryName = $_GET['queryName']; } else { $queryName = $name; }
  if ( isset($_GET['queryEmail']) ) { $queryEmail = $_GET['queryEmail']; } else { $queryEmail = $email; }
  if ( isset($_GET['session']) ) { $session = $_GET['session']; } else { $session = ''; }
  if ( isset($_GET['check']) ) { $check = $_GET['check']; } else { $check = ''; }
  if ( isset($_GET['event']) ) { $event = $_GET['event']; } else { $event = ''; }
  if ( isset($_GET['event_logs']) ) { $event_logs = $_GET['event_logs']; } else { $event_logs = ''; }
  if ( isset($_GET['attendees_log']) ) { $attendees_log = $_GET['attendees_log']; } else { $attendees_log = 'Not_Supposed_to_Happen'; }
  if ( isset($_GET['recommendation_interface']) ) { $recommendation_interface = $_GET['recommendation_interface']; } else { $recommendation_interface = 'Not_Supposed_to_Happen'; }
  if ( isset($_GET['term1']) ) { $term1 = $_GET['term1']; } else { $term1 = ''; }
  if ( isset($_GET['term2']) ) { $term2 = $_GET['term2']; } else { $term2 = ''; }
  if ( isset($_GET['term3']) ) { $term3 = $_GET['term3']; } else { $term3 = ''; }

  # return link
  $returnLink = "<p>$space<a href=\"imhere.php?name=$name&email=$email&event=$event\">Return to Check-In Menu</a></p>";


$nameParts = explode(" ",$queryName);
$firstName = $nameParts[0];
$lastName = $nameParts[1];

$curl_handle=curl_init();
curl_setopt($curl_handle,CURLOPT_URL,"http://54.175.39.137:5000/p/get/?lastname=$lastName&firstname=$firstName&email=$queryEmail");
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);

#if (empty($buffer))
#{print "ResearchBit returned blank profile.<p>";}
#else
#{print $buffer;}

######
$xml = new DOMDocument();
$xml->loadXML($buffer);

$xpath = new DOMXpath($xml);
$in = $xpath->query("/root/Information/Personal/Institution");
$name = $xpath->query("/root/Information/Personal/Name/First");
$email = $xpath->query("/root/Information/Personal/Email");
$esip = $xpath->query("/root/Information/Conference/MailingList");
$key = $xpath->query("/root/Information/Publications/item/PublicationInformation/Keywords");

echo "<!DOCTYPE html>\n";
echo "  <html>\n";
echo "    <head>\n";
echo "      <title>Display Attendee Profile</title>\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "      <style>\n";
echo "         .bold { font-weight: bold; }\n";
echo "      </style>\n";
echo "    </head>\n";
echo "    <body style=\"background-color:darkseagreen;\">\n";



if ($term1) {
echo "<h4><br/>$space This recommendation was made based on the<br/>$space three most common terms found in both your profiles:<br/>";
echo "$space \" $term1, $term2, $term3 \"<br/>";

#echo "<br/>$space Rate this recommendation (0-5):</h4>";
#echo "<form>";
#echo "$space $space <input type=\"number\" name=\"rating\" value=\"3\" min=\"0\" max=\"5\">";
#echo "$space <input type=\"submit\">";
#echo "</form>";
}
echo "		<h3><br/>$space Profile Information for $queryName<br/><br/><h4>";



#echo "      <div class=\"container\">\n";
echo "      <table class=\"table\">";
echo "        <tbody>\n";
echo "          <tr>\n";
echo "            <td class=\"bold\">Name</td>\n";
echo "            <td>" . $name->item(0)->nodeValue . "</td>\n";
echo "          </tr>\n";
echo "          <tr>\n";
echo "            <td class=\"bold\">Institution</td>\n";
echo "            <td>" . $in->item(0)->nodeValue . "</td>\n";
echo "          </tr>\n";
echo "          <tr>\n";
echo "            <td class=\"bold\">Email</td>\n";
echo "            <td>" . $email->item(0)->nodeValue . "</td>\n";
echo "          </tr>\n";

foreach($esip as $e) {
   echo "<tr>\n";
   echo "  <td class=\"bold\">ESIP Mailing Lists</td>\n";
   echo "  <td></td>\n";
   echo "</tr>\n";
   $childNodes = $e->childNodes;
   $nodes = returnSubElements($childNodes);
   foreach($nodes as $node) {
     echo "<tr>\n";
     echo "  <td></td>\n";
     echo "  <td>" . $node . "</td>\n";
     echo "</tr>\n";
   }
}

echo "<tr>\n";
echo "  <td class=\"bold\">Keywords of Interest</td>\n";
echo "  <td></td>\n";
echo "</tr>\n";
$keywords = array();
foreach($key as $k) {
   $childNodes = $k->childNodes;
   foreach ($childNodes as $child) {
     $value = trim($child->nodeValue);
     if ($value != '' ) { array_push($keywords, $value); }
   }
}
$keys = array_unique($keywords);
foreach ($keys as $k) { 
  echo "<tr>\n";
  echo "  <td></td>\n";
  echo "  <td>$k</td>\n"; 
  echo "</tr>\n";
}

#echo "</div>\n";
echo "</table>\n";

function printSubElements ($childNodes) {
   foreach ($childNodes as $child) {
     $value = trim($child->nodeValue);
     if ($value != "") { echo "&nbsp;&nbsp; " . $value . "<br/>"; }
   }
}

function returnSubElements ($childNodes) {
   $results = array();
   foreach ($childNodes as $child) {
      $value = trim($child->nodeValue);
      if ($value != "") { array_push($results, $value); }
   }
   return $results;
}

######


echo "<h4><br/>$returnLink<br><br></h4>";

  # close html
  echo "  </body>\n";
  echo "</html>\n";

?>
