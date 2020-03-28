<?PHP
	//
	// EtoA-Punktdiagramm
	// (c) 2006 by Nicolas Perrenoud, mrcage@etoa.ch
	// Created 28.10.2006
	//

	include("image.inc.php");

	define('DETAIL_LIMIT',48);	// Maximale Anzahl Datensätze
	define('STEP',6);
	define('IM_W',600);	// Breite des Bildes
	define('IM_H',IM_W/3*2);	// Höhe des Bildes
	define('B_B',25);		// Randabstand
	define('SHADOW_L',5);	// Grösse des Schattens
	define('FONT_SIZE',1);	// Schriftgrösse
	define('BG_FAC_W',5/6);	// Schriftgrösse
	define('BG_FAC_H',0.41);	// Schriftgrösse

	define('B_H',IM_H-(2*B_B));

	$im = ImageCreate(IM_W,IM_H);

	$bg = ImageColorAllocate($im,34,34,51);
	$white = ImageColorAllocate($im,255,255,255);
	$grey = ImageColorAllocate($im,187,187,187);
	$black = ImageColorAllocate($im,0,0,0);
	$grey = ImageColorAllocate($im,150,150,150);
	$green = ImageColorAllocate($im,0,200,0);
	$blue = ImageColorAllocate($im,34,34,85);
	$red = ImageColorAllocate($im,255,0,0);
	$yellow = ImageColorAllocate($im,255,255,0);
	$lblue = ImageColorAllocate($im,34,34,200);

	ImageFill($im,0,0, $white);
	$imh = imagecreatefromjpeg("images/logo_trans.jpg");
	ImageCopyresized($im,$imh,(IM_W-(IM_W*BG_FAC_W))/2,(IM_H-(IM_H*BG_FAC_H))/2,0,0,IM_W*BG_FAC_W,IM_H*BG_FAC_H,imagesx($imh),imagesy($imh));
	ImageRectangle($im, 0, 0, IM_W-1, IM_H-1, $black);

	if (isset($_GET['alliance']))
	{
		$aid = intval($_GET['alliance']);
	}

	if ($aid>0 && count($_SESSION)>0)
	{
		$res=dbquery("
			SELECT 
				alliance_tag,
				alliance_name,
				alliance_rank_current 
			FROM 
				alliances
			WHERE 
				alliance_id='".$aid."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			if (intval($_GET['start'])>0)
				$sql1 = " AND point_timestamp > ".intval($_GET['start'])." ";
			if (intval($_GET['end'])>0)
				$sql2 = " AND point_timestamp < ".intval($_GET['end'])." ";
			$pres=dbquery("
				SELECT 
					* 
				FROM 
					alliance_points
				WHERE 
					point_alliance_id='".$aid."' 
					AND point_points>0
					$sql1
					$sql2
				ORDER BY 
					point_timestamp DESC LIMIT ".(DETAIL_LIMIT*6).";
			");
			if (mysql_num_rows($pres)>0)
			{
				if (floor(mysql_num_rows($pres)/STEP) > 0) {
					define('B_W', (IM_W-B_B)/floor(mysql_num_rows($pres)/STEP)/2);
				} else {
					define('B_W', 0);
				}
				// Bar colors
				for ($x=0;$x<B_W;$x++)
				{
					$b_col[$x]=ImageColorAllocate($im,34/B_W*$x,34/B_W*$x,85/B_W*$x);
				}
				// Shadow colors
				for ($i=SHADOW_L;$i>0;$i--)
				{
					$s_col[$i]=ImageColorAllocate($im,5+($i*250/SHADOW_L),5+($i*250/SHADOW_L),5+($i*250/SHADOW_L));
				}

				$pmax=0;
				$last_update=0;
				$cnt=0;
				while ($parr=mysql_fetch_array($pres))
				{
					if ($last_update==0) $last_update=$parr['point_timestamp'];
					if ($cnt==0)
					{
						$points[$parr['point_timestamp']]=$parr['point_points'];
						$pmax=max($pmax,$parr['point_points']);
					}
					$cnt++;
					if ($cnt==STEP) $cnt=0;
				}
				ksort ($points);

				imagestring($im,FONT_SIZE,B_B/3,B_B/3,"Statistiken von [".$arr['alliance_tag']."] ".$arr['alliance_name'].", Rang ".$arr['alliance_rank_current'].", letzes Update: ".date("d.m.Y H:i",$last_update)."",$black);
				imagestring($im,FONT_SIZE,B_B/3,B_B/3+9,"Schrittweite: ".STEP." Stunden, Zeitraum: ".(DETAIL_LIMIT*STEP/24)." Tage",$black);
				$cnt=0;

				$last_x = -1;
				$last_y = -1;
				foreach ($points as $t=>$p)
				{
					$left =  B_B-15+($cnt*2*B_W);

					$x0 = $left+($x/2);
					$y0 = B_B+B_H-(B_H*$p/$pmax);

					if ($last_x==-1)
					{
						$last_x=$x0;
					}
					if ($last_y==-1)
					{
						$last_y=$y0;
					}


					imageline($im, $x0+1, $y0+2,$last_x+1,$last_y+2,$grey);
					imageline($im, $x0, $y0+2,$last_x,$last_y+2,$grey);

					imageline($im, $x0, $y0,$last_x,$last_y,$lblue);
					imageline($im, $x0, $y0+1,$last_x,$last_y+1,$lblue);

					imageline($im, $left+(B_W/2), B_B+B_H-(B_H*$p/$pmax), $left+(B_W/2), B_B+B_H, $grey);

					$last_x = $x0;
					$last_y = $y0;

					/*
					// Schatten
					for ($i=SHADOW_L;$i>0;$i--)
					{
						imageline($im, $left+$i, B_B+B_H-(B_H*$p/$pmax)-$i, $left+B_W+$i, B_B+B_H-(B_H*$p/$pmax)-$i, $s_col[$i]);
						imageline($im, $left+B_W+$i, B_B+B_H-(B_H*$p/$pmax)-$i, $left+B_W+$i, B_B+B_H-$i, $s_col[$i]);
					}*/
					/*
					// Balken
					for ($x=0;$x<B_W;$x++)
					{
						imageline($im, $left+$x, B_B+B_H-(B_H*$p/$pmax), $left+$x, B_B+B_H, $b_col[$x]);
					}
					// Rahmen
					imagerectangle($im, $left, B_B+B_H-(B_H*$p/$pmax), $left+B_W, B_B+B_H, $black);
					*/
					// Zeit
					if ($cnt%3==1)
					{
						imagestring($im,FONT_SIZE,$left+(B_W/2)-imagefontwidth(1)*5/2,B_B+B_H+5,date("H:i",$t),$black);
						imagestring($im,FONT_SIZE,$left-8+(B_W/2)-imagefontwidth(1)*5/2,B_B+B_H+13,date("d.m.y",$t),$black);
					}
					// Punkte

						imagestringup($im,FONT_SIZE,$left+(B_W/2)-imagefontheight(1),B_H+B_B-10,nf($p),$black);

					$cnt++;
				}
			}
			else
				imagestring($im,3,10,10,"Keine Punktdaten (Punkte > 0) vorhanden!",$black);
		}
		else
			imagestring($im,3,10,10,"Fehler! Allianz nicht vorhanden!",$black);
	}
	else
		imagestring($im,3,10,10,"Fehler! Keine ID angegeben oder du bist nicht eingeloggt!",$black);
	ImagePNG($im);




	dbclose();
?>
