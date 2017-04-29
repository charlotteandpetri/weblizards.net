<?php
// user info for mailbox
$user = "bfailure";
$pass = "nakkenakuttaja";
$host = "localhost";

echo "Connecting to e-mail server... $server<br>";

$conn = imap_open ("{localhost:995/pop3/ssl/novalidate-cert}INBOX", $user, $pass) or die("Connection to server failed...." . imap_last_error());

echo "Get e-mail headers...<br>";

$headers = imap_headers($conn)                                 			// get e-mail headers
  or die("Couldn't get emails or no new mails" . imap_last_error());    // if failed getting headers

$numEmails = sizeof($headers);                                  		// number of emails in a mailbox
print ("You have $numEmails mail(s) in your mailbox<br><br>");

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
			echo "DELETING!!!<br>\n";
			imap_delete ($conn, $i);
		} elseif ($bcc != "") {
			echo "DELETING!!!<br>\n";
			imap_delete ($conn, $i);
		} elseif (ereg("^RE:", $subject) == true) {
			echo "DELETING!!!<br>\n";
			imap_delete ($conn, $i);
		} elseif (ereg("^FW:", strtoupper($subject)) == true || ereg("^FWD:", strtoupper($subject)) == true) {
			echo "DELETING!!!<br>\n";
			imap_delete ($conn, $i);
		} else {

			/*  Check for 'Billing Failure Threshold...' mail  */
			if(stristr($subject,"Billing Failure Threshold")) {
				$ordnum_strt = strpos($body,"Periodic Order ") + 15;
				$ordnum_end = strpos($body," has failed");
				$ordnum = substr($body, $ordnum_strt, $ordnum_end - $ordnum_strt);
				$ret = send_to_mox($ordnum,"cancel");
				print("RET $ret<br/>\nFAIL! Date: $date - Order Number: '$ordnum'\n<br>");
				//post($ordnum,"cancel",$ret);
				//imap_delete ($conn, $i);          // Mark read messages for deletion
			}
		  
			/*  Check for Approved monthly billing  */
			if(stristr($subject,"Periodic Bill for Order") && substr($subject,-8) == "Approved") {
				$ordnum_strt = strpos($body,"Periodic Billing Order ") + 23;
				$ordnum_end = strpos($body," Approved");
				$ordnum = trim(substr($body, $ordnum_strt, $ordnum_end - $ordnum_strt));
				print("RENEW! Date: $date - Order Number: '$ordnum'<br>\n");	  
			
				$startdte_strt = strpos($body,"Startdate: ") + 11;
				$start_date = trim(substr($body, $startdte_strt, 10));
				$start_date = str_replace("/","-",$start_date);
				$today = date("Y-m-d");

				if ($start_date != $today) {
					$ret = send_to_mox($ordnum,"payment");
					print("RET: $ret <br/>\nStart Date: '$start_date'<br><br>\n\n");	  
					//post($ordnum,"payment",$ret);
				} else {
					print("<b>Start Date: '$start_date' (TODAY!)</b><br><br>\n\n");	  
				}
				//imap_delete ($conn, $i);          // Mark read messages for deletion
			}
		}
	}                                       // END of for loop
	//imap_expunge ($conn);               // delete messages marked for deletion
	imap_close($conn);                    // Close mailbox connection
} else {
	exit;
}	// Close first Loop

// functions
function send_to_mox($oid,$action) {

	// actions: payment | cancel
/*	if ($action == "cancel") {
		$host = "https://secure.mcmnetwork.com/members/auto_cancel.php";
	} else {
		$host = "https://secure.mcmnetwork.com/members/auto_credits.php";
	}
*/	
	$host = "https://secure.mcmnetwork.com/members/auto_process.php";
	
	$post = "oid=".$oid."&action=".$action;
	echo "Posting: $post<br/>\n";
	
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

function post($oid,$action,$res) {
	$to = "pietu@weblizards.net";
	$from = "mcm_autoprocess@mcmnetwork.com";
	$subject = "AUTOMATED ".$action." ".$res;
	list($status,$msisdn) = explode(":",$res);
	$message = "Automated $action processed for member msisdn ($msisdn).\n";
	$message.= "Process result: ".strtoupper($status)."\n";
	$headers = "From: $from\r\nX-Mailer: PHP/" . phpversion()."\r\n-f $from";
	mail($to,$subject,$message,$headers);
}
/*
$db_conn = mssql_connect("mssql", "wwwuser", "wwwuser") or die("DB connection failed");
mssql_select_db("members");

// Get cc number
$db_conn = mssql_connect("localhost", "miscu8er", "miscluser");
mssql_select_db("miscstuff");
$sql = "select * from canceling where orderid ='$code'";
$rst = mssql_query($sql);
$row = mssql_fetch_assoc($rst);
$CCNumber = $row[cardnumber];
$CCExpMonth = $row[expmonth];
$CCExpYear = $row[expyear];
$ordertotal = $row[ordertotal];
$member_sid = $row[MemberSID];

print_r($row) . "<br><br>";

$order["pbordertype"]="PbOrder_Cancel";
$order['oid'] = $code;        
$order['cardnumber'] = $CCNumber;
$order['expyear'] = $CCExpYear;
$order['expmonth'] =$CCExpMonth;
$order['chargetotal'] = $ordertotal;

// Make mail to send
$out_to = "periodicfailure@mycoolmobile.com";
$out_subject = "Automatic cancellation script: ";

$result = $cl_lpphp->SetPeriodic($order);
if($result[statusCode] == 0) {
	$out_subject .= "failure";
	$out_message = "Cancellation failed, reason: " . $result['statusMessage'] . "\n\n";
	$out_message .= "Code: $code\n";
	mail($out_to,$out_subject,$out_message);
} else {
	$out_subject .= "success";
	$out_message = "Cancellation successful!\n\n";
	$out_message .= "Code: $code\n";
	mail($out_to,$out_subject,$out_message);
	$db_conn = mssql_connect("localhost", "miscu8er", "miscluser");
	mssql_select_db("miscstuff");
	$sql = "delete from canceling where orderid='$code'";
	$rst = mssql_query($sql);
	
	$sql = "exec sp_Members_CancelMembership $member_sid";
	$rst = mssql_query($sql);
}
*/ 
?>