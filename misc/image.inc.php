<?php

	chdir(realpath(dirname(__FILE__)."/../"));
	define('USE_HTML',false);
	require_once("inc/bootstrap.inc.php");

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
	if (!isset($noimg))
	{
		header("Content-type: image/png");
	}
	
	function MDashedLine($image, $x0, $y0, $x1, $y1, $fg, $bg)
	{
		$st = array($fg, $fg, $fg, $fg, $bg, $bg, $bg, $bg);
		ImageSetStyle($image, $st);
		ImageLine($image, $x0, $y0, $x1, $y1, IMG_COLOR_STYLED);
	}

	function imagearrow($im,$x1,$y1,$x2,$y2, $color)
	{
		imageline($im,$x1,$y1,$x2,$y2,$color);

		$dx = abs($x2-$x1);
		$dy = abs($y2-$y1);
		$r = hypot($dx,$dy);
		$sin = $dy / $r;
		$cos = $dx / $r;

		imagestring($im,3,10,10,$cos,$color);


		$poly = array($x2,$y2, $x2-10*$cos,$y2-10*$sin - 5*$cos, $x2-10*$cos,$y2-10*$sin + 5*$cos);

		ImageFilledPolygon($im, $poly, 3, $color);
	}


?>
