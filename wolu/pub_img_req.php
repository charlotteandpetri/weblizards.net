<?php
$affiliate = $_REQUEST['affiliate'];
$timestamp = date("Y-m-d H:i:s");

$msisdn = $_REQUEST['msisdn'];
$name = $_REQUEST['name'];
$artist = $_REQUEST['artist'];
$operator = $_REQUEST['operator'];
$item_id = $_REQUEST['item_id'];
$type_id = $_REQUEST['type_id'];

$msisdn = str_replace(" ","",$msisdn); 
$msisdn = str_replace("-","",$msisdn); 
$msisdn = str_replace("(","",$msisdn); 
$msisdn = str_replace(")","",$msisdn); 

// provide correct URL's based on the affiliate
if (strtolower($operator) == "digicell") {
	$url = "tonos.digicel.com.sv/binary/moxusa02_digicell";
	$url_appr = "mcmnets.com/dc/site_include";
} else if (strtolower($operator) == "movil.com.co") {
	$url = "movil.mcmnets.com/binary/moxusa02_quattro";
	$url_appr = "movil.mcmnets.com/site_include";
}

// Open template file
$filename = "img_template.html";
$read = fopen($filename, "r");	// read template file
	if (!$read) {
		print("Could not open file");
		// exit;
	}
	$template = fread($read, filesize($filename));
fclose($read);

$template = ereg_replace ("#name#",$name,$template);
$template = ereg_replace ("#item_id#",$item_id,$template);
$template = ereg_replace ("#type_id#",$type_id,$template);
$template = ereg_replace ("#artist#",$artist,$template);
$template = ereg_replace ("#msisdn#",$msisdn,$template);
$template = ereg_replace ("#operator#",$operator,$template);
$template = ereg_replace ("#timestamp#",$timestamp,$template);
$template = ereg_replace ("#url#",$url,$template);
$template = ereg_replace ("#url_appr#",$url_appr,$template);

$subject = "Public Image Request - $operator";
$test = "pietu@weblizards.net"; // testing address
$to = "support@mycoolmobile.com"; // prod address


// Send HTML formatted E-mail
mail($to, $subject, $template,
	 "From: VisualPlayer2 <vp2@mcmnets.com>\r\n"
	."Reply-To: VisualPlayer2 <vp2@mcmnets.com>\r\n"
	."MIME-Version: 1.0\r\n"
	."Content-type: text/html; charset=iso-8859-1\r\n"
	."X-Mailer: PHP/" . phpversion());


echo "OK";
exit;
?>