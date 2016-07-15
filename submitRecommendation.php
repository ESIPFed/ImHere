<?php

include 'config.php';

$name = $_GET['name'];
$nameEmail = $_GET['nameEmail'];
$queryName = $_GET['queryName'];
$queryEmail = $_GET['queryEmail'];
$rating = $_GET['rating'];
$event = $_GET['event'];
$rInterface = $_GET['rInterface'];

$parts1 = explode(" ", $name);
$parts2 = explode(" ", $queryName);

# log for our our use
$id = uniqid();
$fileName = $parts1[0] . '_' . $parts1[1] . '_' . $id;
$logFile = "logs/ratings_cache/$fileName";
$file = fopen($logFile, 'w');
fwrite($file, "$name,$nameEmail,$queryName,$queryEmail,$rating,$event");
fclose($file);

# send cURL rating to ResearchBit system...
$postData = "lastname=$parts1[1]&firstname=$parts1[0]&email=$nameEmail&";
$postData = $postData . "recommendation_lastname=$parts2[1]&recommendation_firstname=$parts2[0]&recommendation_email=$queryEmail&";
$postData = $postData . "rating=$rating";
$response = httpPost($postData);

# response
#echo $response;

# return link
$link1 = $server . "viewRecommendations.php?name=$name&email=$nameEmail&event=$event&recommendation_interface=$rInterface";
$link2 = $server . "imhere.php?name=$name&email=$nameEmail&event=$event";

# page to display
echo "<!DOCTYPE html>\n";
echo "  <html>\n";
echo "    <head>\n";
echo "      <title>Recommendation Ranking</title>\n";
echo "    </head>\n";
echo "    <body style=\"background-color:darkseagreen;\">\n";
echo "      <h2>Rating Submitted</h2>";
echo "      <h2><a href=\"$link1\">Return to Recommendations</a></h2>";
echo "      <h2><a href=\"$link2\">Return to Check-In Menu</a></h2>";
echo "    </body>\n";
echo "  </html>\n";

function httpPost($postData) {
	
   $postURL="http://54.175.39.137:5000/feedback/";

   $curl_handle=curl_init();
   curl_setopt($curl_handle,CURLOPT_URL,$postURL);
   curl_setopt($curl_handle,CURLOPT_POST, true);
   curl_setopt($curl_handle,CURLOPT_POSTFIELDS,$postData);
   curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
   curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
   $buffer = curl_exec($curl_handle);
   curl_close($curl_handle);
	
   if (empty($buffer)) { $buffer="No response from HTTP post request to ResearchBit"; }
   return $buffer;
	
}

?>
