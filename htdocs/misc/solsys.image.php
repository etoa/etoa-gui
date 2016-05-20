<?PHP

	include("image.inc.php");

	$im_size=400;
	define('PI',3.141592654);
	define('G',0.0000000000667428);	// Newtonian constant of gravitation [m^3 kg^-1 s^-1]
	define('SOLSYS_SIZE',60); // Diameter of solar system [AU]
	define('M',1988400000000000000000000000000); // Mass of sol [Kg]
	define('METER_PER_AU',149597870691);	// Meters per AU
	define('GAME_SECOND',3600*24);	// Real second is xx seconds ingame
	define('SOL_RADIUS',15);
	define('PLANET_RADIUS',7);
	define('MOON_RADIUS',3);

	$im = imagecreate($im_size,$im_size);
	$imb = imagecreatefromjpeg("images/background.jpg");
	imagecopy($im,$imb,0,0,0,0,$im_size,$im_size);
	$col_black=imagecolorallocate($im,0,0,0);
	$col_white=imagecolorallocate($im,255,255,255);
	$col_orange=imagecolorallocate($im,255,180,0);
	$col_green=imagecolorallocate($im,0,255,0);
	$col_yellow=imagecolorallocate($im,255,255,0);
	$col_grey=imagecolorallocate($im,100,100,100);
	$col_red=imagecolorallocate($im,255,0,0);

	if (isset($_SESSION['planets']))
	{
		$planet = $_SESSION['planets'];
		
		// Sol
		$x0 = $im_size/1.8;
		$y0 = $im_size/2;
		if (isset($_SESSION['sol']))
		{
			$sol = $_SESSION['sol'];
			$sims = getimagesize("../".$sol['image']);
			$simr = imagecreatefromgif("../".$sol['image']);
			$simw = $sims[0];
			$simh = $sims[1];			
			imagecopy($im,$simr,$x0-($simw/2),$y0-($simh/2),0,0,$simw,$simh);
		}
		else
		{
			imagefilledellipse($im,$x0,$y0,SOL_RADIUS,SOL_RADIUS,$col_yellow);
		}

		$i=0;
		foreach ($planet as $p)
		{
			$i++;
			// Transform semi major axis
			$a=$p['semi_major_axis']*$im_size/SOLSYS_SIZE;
			// Transform ecccentricity
			$e=$p['semi_major_axis']*$p['ecccentricity']*$im_size/SOLSYS_SIZE;
			// Calculate semi minor axis
			$b=sqrt(($a*$a)-($e*$e));
  	
			$a3 = $p['semi_major_axis']*$p['semi_major_axis']*$p['semi_major_axis']*METER_PER_AU*METER_PER_AU*METER_PER_AU;
		  $period = sqrt(($a3*4*PI*PI)/(G*(M+$p['mass'])));
  	
			// Draw orbit
			for ($t=0;$t<2*PI;$t+="0.01")
			{
				$x = $x0-$e+($a*cos($t));
				$y = $y0+($b*sin($t));
				imagesetpixel($im,$x,$y,$col_grey);
				
				
				if (isset($p['moon']))
				{
					foreach ($p['moon'] as $m)
					{
						$xm = $x+($m['dist']*cos($t*$m['period']));
						$ym = $y+($m['dist']*sin($t*$m['period']));			
						imagesetpixel($im,$xm,$ym,$col_grey);
					}
				}
			}	
			
			$tc = time()*GAME_SECOND * 2 * PI / $period;
			
			// Draw planet
			$x = $x0-$e+($a*cos($tc));
			$y = $y0-($b*sin($tc));

			if (isset($p['image']))
			{
				$pims = getimagesize("../".$p['image']);
				$pimr = imagecreatefromgif("../".$p['image']);
				$pimw = $pims[0];
				$pimh = $pims[1];			
				imagecopyresized($im,$pimr,$x-8,$y-8,0,0,16,16,$pimw,$pimh);
			}
			else
			{
				imagefilledellipse($im,$x,$y,PLANET_RADIUS,PLANET_RADIUS,$col_green);
			}
			
			if (isset($p['moon']))
			{
				foreach ($p['moon'] as $m)
				{			
					$xm = $x+($m['dist']*cos($tc*$m['period']));
					$ym = $y-($m['dist']*sin($tc*$m['period']));
					imagefilledellipse($im,$xm,$ym,MOON_RADIUS,MOON_RADIUS,$col_white);
				}
			}
			
			// Name
			imagestring($im,3,$x+3,$y+5,$i,$col_orange);
			//imagestring($im,3,$x+3,$y+5,$p['name'],$col_orange);
			
		}
	}
	else
	{
		imagestring($im,8,5,5,"Keine Planetendaten vorhanden!",$col_white);
	}
		
	imagepng($im);
	imagedestroy($im);
?>