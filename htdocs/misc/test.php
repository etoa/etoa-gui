<?php
	include("image.inc.php");
	$im = imagecreate(400,400);

	$black = imagecolorallocate($im,0,0,0);
	$red = imagecolorallocate($im,255,0,0);

	imagearrow($im,10,10,300,30,$red);

	imagepng($im);

?>
