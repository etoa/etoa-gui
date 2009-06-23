<?PHP
	class UserStats
	{
		
		static function generateImage($file)
		{
		$w = 700;
		$h = 400;
		$borderLeftRight=50;
		$borderTop=70;
		$borderBottom=80;
		$yLegend = 20;
		$bottomLegend = 65;
			
		$totalSteps = 288;
		
		$im = imagecreate($w,$h);
		$imh = imagecreatefromjpeg(RELATIVE_ROOT."images/logo_trans.jpg");
		ImageCopyresized($im,$imh,($w-imagesx($imh))/2,($h-imagesy($imh))/2,0,0,imagesx($imh),imagesy($imh),imagesx($imh),imagesy($imh));
		
		$colWhite = imagecolorallocate($im,255,255,255);
		$colBlack = imagecolorallocate($im,0,0,0);
		$colGrey = imagecolorallocate($im,180,180,180);
		$colBlue = imagecolorallocate($im,0,0,255);
		$colGreen = imagecolorallocate($im,0,200,0);
		$colRed = imagecolorallocate($im,255,0,0);
		
		imagerectangle($im,0,0,$w-1,$h-1,$colBlack);
		imagerectangle($im,$borderLeftRight,$borderTop,$w-$borderLeftRight,$h-$borderBottom,$colBlack);
		
		$data=array();
		$max=0;
		$time = time()-date("s");
	
		// Renderzeit-Start festlegen
		$render_time = explode(" ",microtime());
		$render_starttime=$render_time[1]+$render_time[0];
	
		$data=array();
		$max=0;
		$maxo=0;
		$acto=false;
		$actr=false;
		$index0 = 0;
		$res=dbquery("SELECT 
			stats_count,
			stats_regcount,
			stats_timestamp 
		FROM 
			user_onlinestats 
		ORDER BY 
			stats_timestamp DESC 
		LIMIT 
			".($totalSteps+1).";");
		$mnr = mysql_num_rows($res);
		$sumo = $sumr = 0;
		if ($mnr>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$t = date("dmyHi",$arr['stats_timestamp']);
				$data[$t]['o']=$arr['stats_count'];
				$data[$t]['r']=$arr['stats_regcount'];
				$max = max($max,$arr['stats_regcount']);
				$maxo = max($maxo,$arr['stats_count']);
				if (!isset($acto))
					$acto = $arr['stats_count'];
				if (!isset($actr))
					$actr = $arr['stats_regcount'];
				$sumo+=$arr['stats_count'];
				$sumr+=$arr['stats_regcount'];
				$index0=$arr['stats_timestamp'];
			}
			$avgo = round($sumo / $mnr,2);
			$avgr = round($sumr / $mnr,2);
		

		ksort($data);

		$graphHeight=$h-$borderTop-$borderBottom;
		$starti = $time-($totalSteps*5*60);
	
		// Horizontale Linien und Gr?ssen
		for ($i=0;$i<($max/100);$i++)
		{
			$y = $h-$borderBottom-($graphHeight/($max/100)*$i);
			imagestring($im,2,$yLegend,$y-(imagefontheight(2)/2),$i*100,$colBlack);
			if ($i!=0)
			imageline($im,$borderLeftRight+1,$y,$w-$borderLeftRight-1,$y,$colGrey);
		}
		
		$c = count($data);
		if ($c>0)
		{
			$step = ($w-$borderLeftRight-$borderLeftRight)*60/($time-$starti);
	
			$x=$borderLeftRight;
			$y=$h-$borderBottom;
			$lastx=$borderLeftRight;
			$lastyo=$h-$borderBottom;// - ($graphHeight/$max*$data[$index0]['o']);
			$lastyr=$h-$borderBottom;// - ($graphHeight/$max*$data[$index0]['r']);;
	
			$ic=0;
			foreach ($data as $i => $d)
			{
				
				$x=$borderLeftRight + ($ic*$step);
				// Vertikale Stundenlinien
				if (date("i",$i)=="00")			
				{
					if (date("H",$i)=="00")			
						imageline($im,$x,$borderTop+1,$x,$h-$borderBottom-1,$colRed);
					else
						imageline($im,$x,$borderTop+1,$x,$h-$borderBottom-1,$colGrey);
					imagestring($im,2,$x-(imagefontheight(2)/2),$h-$bottomLegend,date("H",$i),$colBlack);
				}
				$t = date("dmyHi",$i);
				// User-Diagramm
				if (count($d)>0)
				{
					if ($max>0)
					{
						$yo=$h - $borderBottom - ($graphHeight/$max*$d['o']);
						$yr=$h - $borderBottom - ($graphHeight/$max*$d['r']);
					}
					else
					{
						$yo=$h - $borderBottom;
						$yr=$h - $borderBottom;
					}
					imageline($im,$lastx,$lastyo,$x,$yo,$colGreen);
					imageline($im,$lastx,$lastyr,$x,$yr,$colBlue);
					$lastyo=$yo;
					$lastyr=$yr;
					$lastx=$x;
				}
				elseif ($lastyo==$h-$borderBottom)
					$lastx=$x;
				$ic++;
			}
		}
	
		
		// Renderzeit
		$cfg = Config::getInstance();
		$render_time = explode(" ",microtime()); $rtime = $render_time[1]+$render_time[0]-$render_starttime; 
		imagestring($im,6,10,5,$cfg->game_name->v." ".$cfg->game_name->p1." - ".ROUNDID,$colBlack);
		imagestring($im,6,10,20,"Userstatistik der letzten 24 Stunden",$colBlack);	
		imagestring($im,2,10,40,"Erstellt: ".date("d.m.Y, H:i").", Renderzeit: ".round($rtime,3)." sec",$colBlack);	
	
		imagestring($im,3,110,$h-40,"Max    Durchschnitt   Aktuell",$colBlack);	
		imagestring($im,3,50,$h-25,"Online",$colGreen);	
		imagestring($im,2,110,$h-25,$maxo,$colBlack);	
		imagestring($im,2,160,$h-25,$avgo,$colBlack);	
		imagestring($im,2,265,$h-25,$acto,$colBlack);	
	
		imagestring($im,3,450,$h-40,"Max    Durchschnitt   Aktuell",$colBlack);	
		imagestring($im,3,350,$h-25,"Registriert",$colBlue);	
		imagestring($im,2,450,$h-25,$max,$colBlack);	
		imagestring($im,2,500,$h-25,$avgr,$colBlack);	
		imagestring($im,2,605,$h-25,$actr,$colBlack);	
	
	
		unlink($file);		
		imagepng($im,$file);

		}
		
	}
	
	static function generateXml($file)
	{

			/**
			* Gameinfo XML
			*/ 
			$pres = dbquery("SELECT COUNT(id) FROM planets;");
			$presh = dbquery("SELECT COUNT(id) FROM planets WHERE planet_user_id>0;");
			$parr = mysql_fetch_row($pres);
			$parrh = mysql_fetch_row($presh);

			$res=dbquery("SELECT
				stats_count,
				stats_regcount,
				stats_timestamp
			FROM
				user_onlinestats
			ORDER BY
				stats_timestamp DESC
			LIMIT
				1;");
			$mnr = mysql_num_rows($res);
			if ($mnr>0)
			{
				$arr=mysql_fetch_array($res);
				$acto = $arr['stats_count'];
				$actr = $arr['stats_regcount'];
			}

			$d = fopen($file,"w");
			$text = "<gameserver>
			<users>
				<online>".$acto."</online>
				<registered>".$actr."</registered>
			</users>
			<galaxy>
				<planets>
					<inhabited>".$parrh[0]."</inhabited>
					<total>".$parr[0]."</total>
				</planets>
			</galaxy>
		</gameserver>";
			fwrite($d,$text);
			fclose($d);
		}
	}
?>