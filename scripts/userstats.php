<?PHP

	define('USERSTATS_OUTFILE',"cache/out/userstats.png");
	define('XML_INFO_FILE',"cache/xml/info.xml");
	define('RSS_TOWNHALL_FILE',"cache/rss/townhall.rss");


	// Gamepfad feststellen
	if ($_SERVER['argv'][1]!="")
	{
		$grd = $_SERVER['argv'][1];
	}
	else
	{
		$c=strrpos($_SERVER["SCRIPT_FILENAME"],"scripts/");
		if (stristr($_SERVER["SCRIPT_FILENAME"],"./")&&$c==0)
			$grd = "../";
		elseif ($c==0)
			$grd = ".";
		else
			$grd = substr($_SERVER["SCRIPT_FILENAME"],0,$c-1);
	}
	define("GAME_ROOT_DIR",$grd);

	// Initialisieren
	if (include(GAME_ROOT_DIR."/functions.php"))
	{
		include(GAME_ROOT_DIR."/../conf.inc.php");
		include(GAME_ROOT_DIR."/classes.php");
		dbconnect();
		$conf = get_all_config();
		include(GAME_ROOT_DIR."/def.inc.php");
		$nohtml=true;
		
		$w = 700;
		$h = 400;
		$borderLeftRight=50;
		$borderTop=70;
		$borderBottom=80;
		$yLegend = 20;
		$bottomLegend = 65;
			
		$totalSteps = 288;
		
		$im = imagecreate($w,$h);
		$imh = imagecreatefromjpeg(GAME_ROOT_DIR."/images/logo_trans.jpg");
		ImageCopyresized($im,$imh,($w-imagesx($imh))/2,($h-imagesy($imh))/2,0,0,imagesx($imh),imagesy($imh),imagesx($imh),imagesy($imh));
		
		
		
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1	
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit	
		header ("Content-type: image/png");
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
		$res=dbquery("SELECT stats_count,stats_regcount,stats_timestamp FROM user_onlinestats ORDER BY stats_timestamp DESC LIMIT $totalSteps;");
		$mnr = mysql_num_rows($res);
		if ($mnr>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$t = date("dmyHi",$arr['stats_timestamp']);
				$data[$t]['o']=$arr['stats_count'];
				$data[$t]['r']=$arr['stats_regcount'];
				$max = max($max,$arr['stats_regcount']);
				$maxo = max($maxo,$arr['stats_count']);
				if ($acto==false) $acto = $arr['stats_count'];
				if ($actr==false) $actr = $arr['stats_regcount'];
				$sumo+=$arr['stats_count'];
				$sumr+=$arr['stats_regcount'];
				$index0=$arr['stats_timestamp'];
			}
			$avgo = round($sumo / $mnr,2);
			$avgr = round($sumr / $mnr,2);
		}
	
		ksort($data);
		$graphHeight=$h-$borderTop-$borderBottom;
		$starti = $time-($totalSteps*5*60);
	
		// Horizontale Linien und Grössen
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
			for ($i=$starti;$i<$time;$i+=60)
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
				if (count($data[$t])>0)
				{
					$yo=$h - $borderBottom - ($graphHeight/$max*$data[$t]['o']);
					$yr=$h - $borderBottom - ($graphHeight/$max*$data[$t]['r']);
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
		$render_time = explode(" ",microtime()); $rtime = $render_time[1]+$render_time[0]-$render_starttime; 
		imagestring($im,6,10,5,$conf['game_name']['v']." ".$conf['game_name']['p1']." - ".GAMEROUND_NAME,$colBlack);
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
	
	
		unlink(GAME_ROOT_DIR."/".USERSTATS_OUTFILE);		
		imagepng($im,GAME_ROOT_DIR."/".USERSTATS_OUTFILE);



	/**
	* Gameinfo XML
	*/ 
	$pres = dbquery("SELECT COUNT(planet_id) FROM planets;");
	$presh = dbquery("SELECT COUNT(planet_id) FROM planets WHERE planet_user_id>0;");
	$parr = mysql_fetch_row($pres);
	$parrh = mysql_fetch_row($presh);

	$d = fopen(GAME_ROOT_DIR."/".XML_INFO_FILE,"w");
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
	

	/**
	* Townhall RSS
	*/

	$rssValue = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
	$rssValue .= "<rss version=\"2.0\">\r\n";
	
	// Build the channel tag
	$rssValue .= "<channel>\r\n";
	$rssValue .= "<title>EtoA Rathaus ".GAMEROUND_NAME."</title>\r\n";
	$rssValue .= "<link>http://www.etoa.ch</link>\r\n";
	$rssValue .= "<description>Rathaus der EtoA ".GAMEROUND_NAME."</description>\r\n";
	$rssValue .= "<language>de</language>\r\n";
	
	// Build the image tag
	$rssValue .= "<image>\r\n";
	$rssValue .= "<title>EtoA Rathaus</title>\r\n";
	$rssValue .= "<url>http://www.etoa.ch/images/game_logo.gif</url>\r\n";
	$rssValue .= "<link>http://www.etoa.ch</link>\r\n";
	$rssValue .= "</image>\r\n";
	
	$res=dbquery("
	SELECT 
		alliance_news_title,
		alliance_news_text
	FROM
		alliance_news
	WHERE
		alliance_news_alliance_to_id = 0
	ORDER BY
		alliance_news_date DESC
	
	;");	
	
	// The records were retrieved OK, let's start building the item tags
	while($arr = mysql_fetch_array($res))
	{
		$rssValue .= "<item>\r\n";
		$rssValue .= "<title>".text2html($arr['alliance_news_title'])."</title>\r\n";
		$rssValue .= "<description>".text2html($arr['alliance_news_text'])."</description>\r\n";
		$rssValue .= "<link>http://www.etoa.ch</link>\r\n";
		$rssValue .= "</item>\r\n";
	}

	$rssValue .= "</channel>\r\n";
	$rssValue .= "</rss>";

	$d = fopen(GAME_ROOT_DIR."/".RSS_TOWNHALL_FILE,"w");
	fwrite($d,$rssValue);
	fclose($d);



		// DB schliessen
		dbclose();
	}
	else
	{
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";
	}

?>
