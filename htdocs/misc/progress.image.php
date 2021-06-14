<?PHP

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
	header("Content-type: image/png");

	if (isset($_GET['p']))
		$p = intval($_GET['p']);
	else
		$p = 0;
	$p = max(0,$p);
	$p = min(100,$p);

	if (isset($_GET['w']))
		$width = intval($_GET['w']);
	else
		$width = 400;
	if (isset($_GET['h']))
		$height = intval($_GET['h']);
	else
		$height = 20;

	if (isset($_GET['r']))
		$reverse = true;
	else
		$reverse = false;

	$im = imagecreatetruecolor($width,20);
	$colBlack = imagecolorallocate($im,0,0,0);
	$colWhite = imagecolorallocate($im,255,255,255);

	$colRed = imagecolorallocate($im,255,0,0);
	$colGreen = imagecolorallocate($im,0,255,0);



	$r=$g=$b=0;
	if ($reverse)
		$cp = 100-$p;
	else
		$cp = $p;

		if ($cp<50)
			$r = 220+(floor(25/50*$cp));
		else
			$r = 255 - floor(255/50*($cp-50));
		if ($cp<50)
			$g = floor(255/50*$cp);
		else
			$g = 255 - floor(100/50*($cp-50));

	$col = imagecolorallocate($im, (int) $r, (int) $g,$b);
	imagefilledrectangle($im,0,0, (int) round($width/100*$p),$height,$col);



	$fcol = imagecolorallocate($im,0,0,0);
	$w = (int) (($width/2)-round(imagefontwidth(2)*strlen($p."%")/2));
	imagestring($im,2, $w,2,$p."%",$fcol);
	imagestring($im,2, $w,4,$p."%",$fcol);
	imagestring($im,2,$w-1,3,$p."%",$fcol);
	imagestring($im,2,$w+1,3,$p."%",$fcol);
	$fcol = imagecolorallocate($im,255,255,255);
	imagestring($im,2, $w,3,$p."%",$fcol);

	imagerectangle($im,0,0,$width-1,$height-1,imagecolorallocate($im,200,200,200));
	imagerectangle($im,1,1,$width-2,$height-2,imagecolorallocate($im,100,100,100));


	imagepng($im);
?>
