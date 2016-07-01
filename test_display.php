<?php

$xml = new DOMDocument();
$xml->load('test.xml');

$xpath = new DOMXpath($xml);
$in = $xpath->query("/root/Information/Personal/Institution");
$name = $xpath->query("/root/Information/Personal/Name/First");
$email = $xpath->query("/root/Information/Personal/Email");
$esip = $xpath->query("/root/Information/Conference/MailingList");
$key = $xpath->query("/root/Information/Publications/item/PublicationInformation/Keywords");

echo "Name: " . $name->item(0)->nodeValue . "<br/>";
echo "Institution: " . $in->item(0)->nodeValue . "<br/>";
echo "Email: " . $email->item(0)->nodeValue . "<br/>";

foreach($esip as $e) {
   echo "ESIP Mailing Lists:<br/>";
   $childNodes = $e->childNodes;
   printSubElements($childNodes);
}

echo "<br/>Keywords of Interest:<br/>";
$keywords = array();
foreach($key as $k) {
   $childNodes = $k->childNodes;
   foreach ($childNodes as $child) {
     $value = trim($child->nodeValue);
     if ($value != '' ) { array_push($keywords, $value); }
   }
}
$keys = array_unique($keywords);
foreach ($keys as $k) { echo "&nbsp;&nbsp; $k<br/>"; }

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
