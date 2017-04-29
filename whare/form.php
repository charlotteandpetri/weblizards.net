<?php
session_start();
$_SESSION['rcv'] = $_REQUEST['rcv']; //reveive target

if (isset($_REQUEST['submit'])) {
	//start processing the form
	$_SESSION['req_type_info'] = $_REQUEST['req_type_info']; //yes
	$_SESSION['req_type_service'] = $_REQUEST['req_type_service']; //yes
	$_SESSION['req_type_custinfo'] = $_REQUEST['req_type_custinfo']; //yes
	$_SESSION['req_type_other'] = $_REQUEST['req_type_other']; //yes
	$_SESSION['name'] = $_REQUEST['name']; //name
	$_SESSION['title'] = $_REQUEST['title']; //title
	$_SESSION['company'] = $_REQUEST['company']; //company
	$_SESSION['address1'] = $_REQUEST['address1']; //address1
	$_SESSION['address2'] = $_REQUEST['address2']; //address2
	$_SESSION['city'] = $_REQUEST['city']; //city
	$_SESSION['state'] = $_REQUEST['state']; //state
	$_SESSION['zip'] = $_REQUEST['zip']; //zip
	$_SESSION['phone'] = $_REQUEST['phone']; //5552302345
	$_SESSION['fax'] = $_REQUEST['fax']; //5552305432
	$_SESSION['email'] = $_REQUEST['email']; //email
	$_SESSION['time_to_contact'] = $_REQUEST['time_to_contact']; //besttime
	$_SESSION['business_descr'] = $_REQUEST['business_descr']; //collision
	$_SESSION['products_interested'] = $_REQUEST['products_interested']; //product_interested
	$_SESSION['timeframe'] = $_REQUEST['timeframe']; //3-6_mos
	$_SESSION['layout_assist'] = $_REQUEST['layout_assist']; //yes
	$_SESSION['existing_prod_type'] = $_REQUEST['existing_prod_type']; //prod_type
	$_SESSION['existing_prod_model'] = $_REQUEST['existing_prod_model']; //prod_model
	$_SESSION['existing_prod_partn'] = $_REQUEST['existing_prod_partn']; //prod_part
	$_SESSION['other_questions'] = $_REQUEST['other_questions']; //other questions

	// check required fields
	include 'functions.php';
	$validate = MailVal($_SESSION['email'], 2);
	$error = "";
	
	if (!$_SESSION['req_type_info']
		&& !$_SESSION['req_type_service']
		&& !$_SESSION['req_type_custinfo']
		&& !$_SESSION['req_type_other']) {
		$error = "y";
		$err_message = "Required Field \"Request\" Missing!";
	} else if (!$_SESSION['name']) {
		$error = "y";
		$err_message = "Required Field \"Name\" Missing!";
	} else if (!$_SESSION['phone']) {
		$error = "y";
		$err_message = "Required Field \"Phone Number\" Missing!";
	} else if (!$_SESSION['email']) {
		$error = "y";
		$err_message = "Required Field \"E-Mail\" Missing!";
	} else if ($validate) {
		$error = "y";
		$err_message = "Required Field \"E-Mail\" Value not valid!";
	} else if (!$_SESSION['business_descr']) {
		$error = "y";
		$err_message = "Required Field \"Business Description\" Missing!";
	}

	if ($error == "") {
		// generate random tracking
		getRand($rand,6);
		
		// compose e-mail
		$date = date("Y-m-d H:i:s");
		$message = "\tINFORMATION REQUEST FORM SUBMITTED ON $date\n\n\n";  
		$message .= "Reference code: ($rand)\n\n";
		$message .= "Request Type:\n";
		$message .= "\tInfo (".$_SESSION['req_type_info'].")\n";
		$message .= "\tService (".$_SESSION['req_type_service'].")\n";
		$message .= "\tUpdate Customer Information (".$_SESSION['req_type_custinfo'].")\n";
		$message .= "\tOther (".$_SESSION['req_type_other'].")\n\n";
	
		$message .= "Title . . . . : ".$_SESSION['title']."\n";
		$message .= "Name. . . . . : ".$_SESSION['name']."\n";
		$message .= "Company . . . : ".$_SESSION['company']."\n";
		$message .= "Address1. . . : ".$_SESSION['address1']."\n";
		$message .= "Address2. . . : ".$_SESSION['address2']."\n";
		$message .= "City. . . . . : ".$_SESSION['city']."\n";
		$message .= "State . . . . : ".$_SESSION['state']."\n";
		$message .= "Zip . . . . . : ".$_SESSION['zip']."\n";
		$message .= "Phone . . . . : ".$_SESSION['phone']."\n";
		$message .= "Fax . . . . . : ".$_SESSION['fax']."\n";
		$message .= "E-mail. . . . : ".$_SESSION['email']."\n\n";
		
		$message .= "Best time to contact . . . : ".$_SESSION['time_to_contact']."\n";
		$message .= "Business description . . . : ".$_SESSION['business_descr']."\n";
		$message .= "Products interested. . . . : ".$_SESSION['products_interested']."\n\n";
		$message .= "Desicion timeframe . . . . : ".$_SESSION['timeframe']."\n";
		$message .= "Need shop layout assistance: ".$_SESSION['layout_assist']."\n";
		$message .= "Existing product type. . . : ".$_SESSION['existing_prod_type']."\n";
		$message .= "Existing product model . . : ".$_SESSION['existing_prod_model']."\n";
		$message .= "Existing product part# . . : ".$_SESSION['existing_prod_partn']."\n";
		$message .= "Other questions / needs. . :\n ".$_SESSION['other_questions']."\n";
	
		switch ($_SESSION['rcv']) {
			case "aes":
				$to = "aesrequest@t6industries.com";
				break;
			case "blh":
				$to = "blhrequest@t6industries.com";
				break;
			case "esr":
				$to = "esrequest@t6industries.com";
				break;
			case "ffr":
				$to = "ffrequest@t6industries.com";
				break;
			case "mye":
				$to = "myersrequest@t6industries.com";
				break;
			case "war":
				$to = "warequest@t6industries.com";
				break;
			case "whr":
				$to = "info@wildhareproductions.com";
				break;
			default:
				$to = "info@wildhareproductions.com";
		}
		
		$subject = "Request Form ($rand)";
		$email = $_SESSION['email'];
		
		mail($to, $subject, $message,
			 "From: \"".$_SESSION['name']."\" <".$email.">\r\n"
			."Reply-To: \"".$_SESSION['name']."\" <".$email.">\r\n"
			."X-Mailer: PHP/" . phpversion());

		// send confirmation. Note! $email and $to fields reversed
		$subject = "Request Receipt";
		$message = "Thank you for your interest in T6 Industries.\n";
		$message.= "Your request is being processed.\n\n";
		$message.= "Tracking Code: $rand";
		mail($email, $subject, $message,
			 "From: \"t6industries\" <".$to.">\r\n"
			."Reply-To: \"t6industries\" <".$to.">\r\n"
			."X-Mailer: PHP/" . phpversion());

		header("location: thankyou.php");
	}
}
?>
<html>
<head>
<title>Request Form</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="styles.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-color: #CCCCCC;
}
-->
</style></head>

<body>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
  <table width="652" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
    <tr>
      <td align="center">
  <table width="650" border="0" align="left" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF">
    <tr align="center">
      <td height="60" colspan="3" class="title"><h1>request form </h1></td>
    </tr>
    <tr>
      <td colspan="3" class="text"><span class="question"><img src="bullet.gif" width="10" height="10"></span> Denotes
        required field </td>
    </tr>
    <?php if ($error == "y") { ?>
	<tr align="center" bgcolor="#FF6666">
      <td colspan="3" class="errortext"><?=$err_message?></td>
    </tr>
	<?php } ?>
    <tr>
      <td colspan="3" class="text">&nbsp;</td>
    </tr>
	  <tr>
      <td class="question"><img src="bullet.gif" width="10" height="10"></td>
      <td class="question">Request:</td>
      <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="5%"><input name="req_type_info" type="checkbox" id="req_type_info" value="yes" <?php if ($_SESSION['req_type_info']) { echo "checked=\"checked\""; } ?>></td>
            <td width="17%" class="text">Information</td>
            <td width="5%"><input name="req_type_service" type="checkbox" id="req_type_service" value="yes" <?php if ($_SESSION['req_type_service']) { echo "checked=\"checked\""; } ?>></td>
            <td width="11%" class="text">Service</td>
            <td width="5%"><input name="req_type_custinfo" type="checkbox" id="req_type_custinfo" value="yes" <?php if ($_SESSION['req_type_custinfo']) { echo "checked=\"checked\""; } ?>></td>
            <td width="34%" class="text">Update Customer Information</td>
            <td width="5%"><input name="req_type_other" type="checkbox" id="req_type_other" value="yes" <?php if ($_SESSION['req_type_other']) { echo "checked=\"checked\""; } ?>></td>
            <td width="18%" class="text">Other</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td class="question"><img src="bullet.gif" width="10" height="10"></td>
      <td class="question">Name:</td>
      <td><input name="name" type="text" class="answerbox150" id="name" value="<?=$_SESSION['name']?>"></td>
    </tr>
    <tr>
      <td class="question">&nbsp;</td>
      <td class="question">Title:</td>
      <td><input name="title" type="text" class="answerbox150" id="title" value="<?=$_SESSION['title']?>"></td>
    </tr>
    <tr>
      <td class="question">&nbsp;</td>
      <td class="question">Company:</td>
      <td><input name="company" type="text" class="answerbox150" id="company" value="<?=$_SESSION['company']?>"></td>
    </tr>
    <tr>
      <td class="question">&nbsp;</td>
      <td class="question">Address 1: </td>
      <td><input name="address1" type="text" class="answerbox150" id="address1" value="<?=$_SESSION['address1']?>"></td>
    </tr>
    <tr>
      <td class="question">&nbsp;</td>
      <td class="question">Address 2:</td>
      <td><input name="address2" type="text" class="answerbox150" id="address2" value="<?=$_SESSION['address2']?>"></td>
    </tr>
    <tr>
      <td class="question">&nbsp;</td>
      <td class="question">City:</td>
      <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="21%"><input name="city" type="text" class="answerbox100" id="city" value="<?=$_SESSION['city']?>"></td>
            <td width="13%" class="question">State:</td>
            <td width="22%"><input name="state" type="text" class="answerbox100" id="state" value="<?=$_SESSION['state']?>"></td>
            <td width="8%" class="question">Zip:</td>
            <td width="36%"><input name="zip" type="text" class="answerbox100" id="zip" value="<?=$_SESSION['zip']?>"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td class="question"><img src="bullet.gif" width="10" height="10"></td>
      <td class="question">Phone Number:</td>
      <td><input name="phone" type="text" class="answerbox100" id="phone" value="<?=$_SESSION['phone']?>"></td>
    </tr>
    <tr>
      <td class="question">&nbsp;</td>
      <td class="question">Fax Number: </td>
      <td><input name="fax" type="text" class="answerbox100" id="fax" value="<?=$_SESSION['fax']?>"></td>
    </tr>
    <tr>
      <td class="question"><img src="bullet.gif" width="10" height="10"></td>
      <td class="question">E-Mail:</td>
      <td><input name="email" type="text" class="answerbox200" id="email" value="<?=$_SESSION['email']?>"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="350" class="question">What would be the best time to contact
              you?</td>
            <td><input name="time_to_contact" type="text" class="answerbox150" id="time_to_contact" value="<?=$_SESSION['time_to_contact']?>"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td><span class="question"><img src="bullet.gif" width="10" height="10"></span></td>
      <td colspan="2"><span class="question">Which best describes your business?</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td><input name="business_descr" type="checkbox" id="business_descr" value="industrial_manufacturing" <?php if ($_SESSION['business_descr'] == "industrial_manufacturing") { echo "checked=\"checked\""; } ?>></td>
            <td class="text">Industrial/Manufacturing</td>
            <td><input name="business_descr" type="checkbox" id="business_descr" value="dealer" <?php if ($_SESSION['business_descr'] == "dealer") { echo "checked=\"checked\""; } ?>></td>
            <td class="text">Automotive Dealer </td>
            <td><input name="business_descr" type="checkbox" id="business_descr" value="collision_center" <?php if ($_SESSION['business_descr'] == "collision_center") { echo "checked=\"checked\""; } ?>></td>
            <td class="text">Collision Center/Body Shop</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="350" class="question">Which product or products are you
              interested in?</td>
            <td><input name="products_interested" type="text" class="answerbox150" id="products_interested" value="<?=$_SESSION['products_interested']?>"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="350" class="question">What is your time frame for making
              a decision?</td>
            <td><select name="timeframe" class="answerbox100" id="timeframe">
                <option value="immediately" <?php if ($_SESSION['timeframe'] == "immediately") { echo "selected=\"selected\""; } ?>>immediately</option>
                <option value="under_3_mos" <?php if ($_SESSION['timeframe'] == "under_3_mos") { echo "selected=\"selected\""; } ?>>&lt; 3 months</option>
                <option value="3-6_mos" <?php if ($_SESSION['timeframe'] == "3-6_mos") { echo "selected=\"selected\""; } ?>>3 - 6 months</option>
                <option value="6-12_mos" <?php if ($_SESSION['timeframe'] == "6-12_mos") { echo "selected=\"selected\""; } ?>>6 - 12 months</option>
            </select></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="350" class="question">Will you need shop layout assistance?</td>
            <td class="text"><input name="layout_assist" type="radio" value="yes" <?php if ($_SESSION['layout_assist'] == "yes") { echo "checked=\"checked\""; } ?>>
            YES /
              <input name="layout_assist" type="radio" value="no" <?php if ($_SESSION['layout_assist'] == "no") { echo "checked=\"checked\""; } ?>>
            NO</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><span class="question">Do you have an existing product
          that needs servicing or replacement parts?</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><table width="528"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="70" class="text">Type:</td>
            <td><input name="existing_prod_type" type="text" class="answerbox100" id="existing_prod_type" value="<?=$_SESSION['existing_prod_type']?>"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><table width="533"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="70" class="text">Model:</td>
            <td><input name="existing_prod_model" type="text" class="answerbox100" id="existing_prod_model" value="<?=$_SESSION['existing_prod_model']?>"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><table width="534"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="70" class="text">Part #:</td>
            <td><input name="existing_prod_partn" type="text" class="answerbox100" id="existing_prod_partn" value="<?=$_SESSION['existing_prod_partn']?>"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><span class="question">Other questions or specific needs
          you might have?</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><textarea name="other_questions" class="textfield400" id="textarea2"><?=$_SESSION['other_questions']?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>
	    <input type="hidden" name="rcv" value="<?=$_SESSION['rcv'];?>">
	    <input type="hidden" name="<?=session_name()?>" value="<?=session_id()?>">
	  </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input name="submit" type="submit" class="submitbutton" id="submit" value="Submit"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>  </td>
      <td width="3" valign="top" bgcolor="#666666" color="#000000"><img src="graydot.gif" width="3" height="3"></td>
    </tr>
    <tr>
      <td height="3" valign="top" bgcolor="#666666" color="#000000"><img src="graydot.gif" width="3" height="3"></td>
      <td width="3" height="3" bgcolor="#666666" color="#000000"></td>
    </tr>
  </table>
</form>
</body>
</html>
