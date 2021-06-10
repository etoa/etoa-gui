<?php

	chdir(realpath(dirname(__FILE__)."/../"));

	define('SKIP_XAJAX_INIT', true);

  if (isset($_GET['req_admin'])) {
    define('ADMIN_MODE',true);
  }
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
		imagesetstyle($image, $st);
		imageline($image, $x0, $y0, $x1, $y1, IMG_COLOR_STYLED);
	}

	function imagearrow($im,$x1,$y1,$x2,$y2, $color)
	{
		imageline($im,$x1,$y1,$x2,$y2,$color);

		$dx = abs($x2-$x1);
		$dy = abs($y2-$y1);
		$r = hypot($dx,$dy);
		$sin = $dy / $r;
		$cos = $dx / $r;

		if ($x1==$x2 && $y2>$y1)
			$poly = array($x2,$y2, $x2-5,$y2-10, $x2+5,$y2-10);
		elseif ($x1==$x2)
			$poly = array($x2,$y2, $x2-5,$y2+10, $x2+5,$y2+10);
		elseif ($y1==$y2 && $x1 > $x2)
			$poly = array($x2,$y2, $x2+10,$y2-5, $x2+10,$y2+5);
		else
			$poly = array($x2,$y2, $x2-10,$y2-5, $x2-10,$y2+5);

		imagefilledpolygon($im, $poly, 3, $color);
	}


?>
