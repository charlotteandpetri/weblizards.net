<?php
$thumbs_folder = "ca";
$fullsize_url = "http://guruscooter.mine.nu:55080/canada_trip";

$source = dir($thumbs_folder);
$filenames = array();

// collect filenames into array
while ($files = $source->read()) {
	if ($files != "." && $files != "..") {
		array_push($filenames, $files);
	}
}
sort($filenames);
$source->close();
$img_count = 0;

// build table for thumbnails
$table_set = "<table border='1'>\n";
$table_set .= "<tr>";
foreach ($filenames as $image) {
	if ($img_count == 3) {
		$table_set .= "</tr>\n<tr>";
		$img_count = 1;
	} else {
		$img_count++;
	}
	$table_set .= "<td class='cell'><a href='".$fullsize_url."/".strtoupper($image)."' target='_blank'><img src='".$thumbs_folder."/".$image."' border='0'></a></td>";
}
$table_set .= "</tr>\n";
$table_set .= "</table>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Canada Trip Thumbnails</title>
<style type="text/css">
<!--
.cell {
	text-align: center;
	padding: 6px;
	border: 1px solid #999999;
}
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9px;
}
-->
</style>
</head>
<body>
<h3>All photos from our trip to Canada.</h3>
<span class="style1">(click on the thumbnail opens another window with unedited full size photo)</span><br />
<?php echo $table_set;?>
</body>
</html>
