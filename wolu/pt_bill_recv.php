<?php
while(list($var,$val) = each($_REQUEST)) {
	$display .= "$var = $val <br/>\n";
}
$display = urldecode(str_replace("\n","<br/>",$display));
echo "Incoming Values : $display";
?>