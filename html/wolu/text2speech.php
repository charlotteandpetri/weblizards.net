<?php
$affiliate = $_REQUEST['affiliate'];
$timestamp = date("Y-m-d H:i:s");

$to = "text2speech@digital-vanity.net";
$from = "t2s@mycoolmobile.com";
$subject = "TTSV";
$magic_number = "89532769";

// mandatory values
$recipient = urldecode($_REQUEST["recipient"]);
$message = urldecode($_REQUEST["message"]);
// optional values
$character = $_REQUEST["character"];
$lang = $_REQUEST["lang"];
$format = $_REQUEST["format"];

// form the message
$body = "$magic_number
$from
$recipient
$character
$lang
$format
$message
";

// send the message
mail($to, $subject, $body,
	 "From: $from\r\n"
	."Reply-To: $from\r\n"
	."X-Mailer: PHP/" . phpversion());
	
// redirecting to the thank you page
//echo $body."<br/>";
echo "OK";
?>