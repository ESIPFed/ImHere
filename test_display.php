<?php

$xml = new DOMDocument();
$xml->load('test.xml');

$xpath = new DOMXpath($xml);
$in = $xpath->query("/root/Information/Personal/Institution");
$name = $xpath->query("/root/Information/Personal/Name/First");
$email = $xpath->query("/root/Information/Personal/Email");
$esip = $xpath->query("/root/Information/Conference/MailingList");
$key = $xpath->query("/root/Information/Publications/item/PublicationInformation/Keywords");

echo "<!DOCTYPE html>\n";
echo "  <html>\n";
echo "    <head>\n";
echo "      <title>Profile Viewer</title>\n";
echo "      <link rel=\"stylesheet\" href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\">\n";
echo "      <style>\n";
echo "         .bold { font-weight: bold; }\n";
echo "      </style>\n";
echo "    </head>\n";
echo "    <body style=\"background-color:darkseagreen;\">\n";
echo "      <div class=\"container\">\n";
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

echo "</div>\n";
echo "</table>\n";
echo "</body>\n";
echo "</html>\n";

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

?>
