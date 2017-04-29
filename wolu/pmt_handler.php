<?php
// user info for mailbox
$user = "bfailure";
$pass = "nakkenakuttaja";
$host = "localhost";

echo "Connecting to e-mail server... $server\n";

$conn = imap_open ("{localhost:995/pop3/ssl/novalidate-cert}INBOX", $user, $pass) or die("Connection to server failed...." . imap_last_error());

echo "Get e-mail headers...\n";

$headers = imap_headers($conn)                                 			// get e-mail headers
  or die($get_status = "Couldn't get emails or no new mail." . imap_last_error());    // if failed getting headers

if ($headers > 0) {
	$get_status = sizeof($headers);                                  		// number of emails in a mailbox
}

$numEmails = sizeof($headers);                                  		// number of emails in a mailbox
print ("You have $numEmails mail(s) in your mailbox\n\n");

if (sizeof($headers) > 0 ) {
	// proceed with the script
	for($i = 1; $i < $numEmails+1; $i++) {          	// listing through each of the email in a box
		$mailHeader = @imap_headerinfo($conn, $i);    	// info about e-mail headers
		$from = $mailHeader->fromaddress;             	// from field
		ereg ("(.*)<(.*)>(.*)", $from, $from_clean);  	// clean "from" variable into variable "from_clean"
		$subject = strip_tags($mailHeader->subject);  	// subject
		$date = $mailHeader->date;                    	// date for logs
		$body = strip_tags(imap_body($conn, $i));     	// body = other info
		$cc = $mailHeader->ccaddress;
		$bcc = $mailHeader->bccaddress;
	
	
		// If something in cc or bcc fields, consider as spam, and delete message.
		// Also, if reply or forward, delete as spam....
		
		if ($cc != "") {
			echo "DELETING!!!\n";
			imap_delete ($conn, $i);
		} elseif ($bcc != "") {
			echo "DELETING!!!\n";
			imap_delete ($conn, $i);
		} elseif (ereg("^RE:", $subject) == true) {
			echo "DELETING!!!\n";
			imap_delete ($conn, $i);
		} elseif (ereg("^FW:", strtoupper($subject)) == true || ereg("^FWD:", strtoupper($subject)) == true) {
			echo "DELETING!!!\n";
			imap_delete ($conn, $i);
		} else {

			/*  Check for 'Billing Failure Threshold...' mail  */
			if(stristr($subject,"Billing Failure Threshold")) {
				$ordnum_strt = strpos($body,"Periodic Order ") + 15;
				$ordnum_end = strpos($body," has failed");
				$ordnum = substr($body, $ordnum_strt, $ordnum_end - $ordnum_strt);
				print("FAIL! Date: $date - Order Number: '$ordnum'\n");
				$ret = send_to_mox($ordnum,"cancel");
				post($ordnum,"cancel",$ret,$get_status,$ordnum,$body);
				imap_delete ($conn, $i);          // Mark read messages for deletion
			}
		  
			/*  Check for Approved monthly billing  */
			if(stristr($subject,"Periodic Bill for Order") && substr($subject,-8) == "Approved") {
				$ordnum_strt = strpos($body,"Periodic Billing Order ") + 23;
				$ordnum_end = strpos($body," Approved");
				$ordnum = trim(substr($body, $ordnum_strt, $ordnum_end - $ordnum_strt));
				print("RENEW! Date: $date - Order Number: '$ordnum'\n");	  
			
				$startdte_strt = strpos($body,"Startdate: ") + 11;
				$start_date = trim(substr($body, $startdte_strt, 10));
				$start_date = str_replace("/","-",$start_date);
				$today = date("Y-m-d");

				if ($start_date != $today) {
					print("Start Date: '$start_date'\n");	  
					$ret = send_to_mox($ordnum,"payment");
					post($ordnum,"payment",$ret,$get_status,$ordnum,$body);
				} else {
					print("<b>Start Date: '$start_date' (TODAY!)</b>\n");	  
				}
				imap_delete ($conn, $i);          // Mark read messages for deletion
			}
		}
	}                                       // END of for loop
	imap_expunge ($conn);               // delete messages marked for deletion
	imap_close($conn);                    // Close mailbox connection
} else {
	post("","check payments","",$get_status,$ordnum,$body);
	exit;
}	// Close first Loop

// functions
function send_to_mox($oid,$action) {

	$host = "https://secure.mcmnetwork.com/members/auto_process.php";
	$post = "oid=".$oid."&action=".$action;
	echo "Posting: $post\n\n";
	
	$ch = curl_init ();
	curl_setopt ($ch, CURLOPT_URL,$host);
	curl_setopt ($ch, CURLOPT_POST, 1);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
//	curl_setopt ($ch, CURLOPT_CAPATH, "/usr/share/ssl/certs/ca-bundle.crt");
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
//	curl_setopt ($ch, CURLOPT_HEADER, 1);  // for debugging
//	curl_setopt ($ch, CURLOPT_VERBOSE, 1);  // for debugging
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	
//	Send the post string
	$result = curl_exec ($ch);
	curl_close($ch);
	return $result;
}

function post($oid,$action,$res,$get_status,$ordnum,$body) {

	list($ret_status,$msisdn,$ret_message) = explode(":",$res,3);
	if (strtolower($ret_status) == "fail") {
		$to = "info@mycoolmobile.com";
	} else {
		$to = "pietu@weblizards.net";
	}	
	$from = "mcm_autoprocess@mcmnetwork.com";
	$subject = "AUTOMATED ".$action." (".$ret_status.")";
	$message = "Automated $action processed for member msisdn ($msisdn).\n";
	$message.= "Process result: ".strtoupper($ret_status)."\n";
	$message.= "Process message: ".strtoupper($ret_message)."\n";
	$message.= "OID used: ".$ordnum."\n\n";
	$message.= "Body of the original message:\n".$body."\n";
	$headers = "From: $from\r\nX-Mailer: PHP/" . phpversion().", -f $from";
	mail($to,$subject,$message,$headers);
}
?>
