<?php

function httpPost($postData, $rb_response) {
	
#	$postURL="http://54.174.17.206:5000/post/";
	$postURL="http://54.175.39.137:5000/post/";

	$curl_handle=curl_init();
	curl_setopt($curl_handle,CURLOPT_URL,$postURL);
	curl_setopt($curl_handle,CURLOPT_POST, true);
	curl_setopt($curl_handle,CURLOPT_POSTFIELDS,$postData);
	curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);
	
	if (empty($buffer)) {$buffer="No response from HTTP post request to ResearchBit";}
	
	$fh = fopen($rb_response, 'a') or die("In httpPostRequests.php, can't open file: rb_response.txt ($rb_response)");
	fwrite($fh, "Post URL: $postURL\n");
	fwrite($fh, "POST data: $postData\n");
	fwrite($fh, "ResearchBit response: $buffer\n");
	fclose($fh);
	
#	echo "$rb_response<br>"; # For debug purposes
#	echo "$buffer"; # For debug purposes
#	die("<p>Here we are in httpPostRequests...<br>"); # For debug purposes
	
		
	}

?>
