<?php
$affiliate = $_REQUEST['affiliate'];
$timestamp = date("Y-m-d H:i:s");
$test = "Pietu <pietu@weblizards.net>"; // testing address

$e_mail = urldecode($_REQUEST['email']);
$message = urldecode($_REQUEST['comment']);
$name = urldecode($_REQUEST['name']);
$phone_model = urldecode($_REQUEST['phone_model']);
$phone_number = urldecode($_REQUEST['phone_number']);

if ($affiliate == "alert") {
	$subject = "ALERT";
	$to = "ALERT <info@waponelineusa.com>"; // testing address
} else {
	$subject = "MCM Wap Comment";
	$to = "MCM WAP Support <info@mycoolmobile.com>"; // testing address
}

$body = "$subject
Submitted on $timestamp
Name. . . . . . . : $name
E-mail Address. . : $e_mail
Phone Model . . . : $phone_model
Phone Number. . . : $phone_number
MESSAGE:
$message
";
// was inside of the message body

mail($to, $subject, $body,
	 "From: $name <$e_mail>\r\n"
	."Reply-To: $name <$e_mail>\r\n"
	//."Message-ID: " . msgid() . "@server1.weblizards.net\r\n"
	."X-Mailer: PHP/" . phpversion());
	
// redirecting to the thank you page
echo "OK";

// Functions
//Generate Message-ID
function msgid() {
	$guid = date("Ymd.");
	for ($i = 0; $i < 16; $i++) {
		$guid .= rand(0,9);
	}
	return $guid;
}
?>
