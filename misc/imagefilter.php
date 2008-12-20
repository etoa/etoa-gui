<?PHP
	$file=$_GET['file'];
	
	if (substr($file,0,17)!="images/imagepacks")	
	{
		header("Location: ../$file");
		exit;
	}
	
	$ext = substr($file,strrpos($file,".")+1);
	$filter=$_GET['filter'];
	if (file_exists("../".$file))
	{
		$error = false;
		switch($ext)
		{
			case "jpg":
				header ("Content-type: image/jpeg");
				$im = imagecreatefromjpeg("../".$file);
				break;
			case "png":
				header ("Content-type: image/png");
				$im = imagecreatefrompng("../".$file);
				break;
			case "gif":
				header ("Content-type: image/gif");
				$im = imagecreatefromgif("../".$file);
				break;
			default:
				header ("Content-type: image/jpeg");
				$im = imagecreate(100,100);
				$error=true;
		}
			
		if (!$error)	
		{
			switch ($filter)
			{
				case "building":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 0, 100, 0);
					break;
				case "destructing":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 100, 65, 0);
					break;
				case "lowres":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 100, 0, 0);
					break;
				case "red":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 127, 0, 0);
					break;
				case "orange":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 127, 65, 0);
					break;
				case "yellow":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 127, 127, 0);
					break;
				case "green":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 0, 127, 0);
					break;
				case "blue":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_COLORIZE, 0, 0, 127);
					break;
				case "na":
					imagefilter($im, IMG_FILTER_GRAYSCALE);
					imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
					imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
					imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
					break;
				case "negate":
					imagefilter($im, IMG_FILTER_NEGATE);
					break;	
				case "brightness":
					imagefilter($im, IMG_FILTER_BRIGHTNESS, 50);
					break;
				case "emboss":
					imagefilter($im, IMG_FILTER_EMBOSS);
					break;		
				case "blur":
					imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
					break;	
				case "removal":
					imagefilter($im, IMG_FILTER_MEAN_REMOVAL);
					break;	
				case "smooth":
					imagefilter($im, IMG_FILTER_SMOOTH, 10);
					break;		
				case "edgedetect":
					imagefilter($im, IMG_FILTER_EDGEDETECT);
					break;																				
					
			}
		}
		else
		{
			$black = imagecolorallocate($im,0,0,0);
			$white = imagecolorallocate($im,255,255,255);
			imagestring($im,2,5,5,"Fehler: Ungültiger Dateityp!",$white);
		}
		
		switch($ext)
		{
			case "jpg":
				imagejpeg($im);		
			case "png":
				imagepng($im);		
			case "gif":
				imagegif($im);		
			default:
				imagejpeg($im);		
		}		
	}
	else
	{
		header ("Content-type: image/jpeg");
		$im = imagecreate(200,200);		
		$black = imagecolorallocate($im,0,0,0);
		$white = imagecolorallocate($im,255,255,255);
		imagestring($im,2,5,5,"Fehler: Datei nicht gefunden!",$white);
		imagejpeg($im);		
	}

?>