<?PHP
	//
	// EtoA-Punktdiagramm
	// (c) 2006 by Nicolas Perrenoud, mrcage@etoa.ch
	// Created 28.10.2006
	//

	session_start();
	include("../functions.php");
	include("../conf.inc.php");
	dbconnect();

	define(DETAIL_LIMIT,12);	// Maximale Anzahl Datensätze
	define(STEP,6);
	define(IM_W,600);	// Breite des Bildes
	define(IM_H,IM_W/3*2);	// Höhe des Bildes
	define(B_B,25);		// Randabstand
	define(SHADOW_L,5);	// Grösse des Schattens
	define(FONT_SIZE,1);	// Schriftgrösse
	define(BG_FAC_W,5/6);	// Schriftgrösse
	define(BG_FAC_H,0.41);	// Schriftgrösse
	
	Header("Content-Type: image/png");

	define(B_H,IM_H-(2*B_B));

	$im = ImageCreate(IM_W,IM_H);

	$bg = ImageColorAllocate($im,34,34,51);
	$white = ImageColorAllocate($im,255,255,255);
	$grey = ImageColorAllocate($im,187,187,187);
	$black = ImageColorAllocate($im,0,0,0);
	$grey = ImageColorAllocate($im,150,150,150);
	$green = ImageColorAllocate($im,0,200,0);
	$blue = ImageColorAllocate($im,34,34,85);
	$red = ImageColorAllocate($im,255,0,0);

	ImageFill($im,0,0, $white);
	$imh = imagecreatefromjpeg("../images/logo_trans.jpg");
	ImageCopyresized($im,$imh,(IM_W-(IM_W*BG_FAC_W))/2,(IM_H-(IM_H*BG_FAC_H))/2,0,0,IM_W*BG_FAC_W,IM_H*BG_FAC_H,imagesx($imh),imagesy($imh));
	ImageRectangle($im, 0, 0, IM_W-1, IM_H-1, $black);
	
	if ($_GET['user']>0 && count($_SESSION)>0)
	{
		$res=dbquery("
			SELECT 
				user_nick,
				user_rank_current 
			FROM 
				".$db_table['users']." 
			WHERE 
				user_id='".$_GET['user']."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			$pres=dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['user_points']." 
				WHERE 
					point_user_id='".$_GET['user']."' 
				ORDER BY 
					point_timestamp DESC LIMIT ".(DETAIL_LIMIT*6).";
			");
			if (mysql_num_rows($pres)>0)
			{
				define(B_W, (IM_W-B_B)/floor(mysql_num_rows($pres)/STEP)/2);
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
	
				imagestring($im,FONT_SIZE,B_B/3,B_B/3,"Statistiken für ".$arr['user_nick'].", Rang ".$arr['user_rank_current'].", letzes Update: ".date("d.m.Y H:i",$last_update).", Schrittweite: ".STEP." Stunden",$black);	
				$cnt=0;
				
				foreach ($points as $t=>$p)
				{
					$left =  B_B+($cnt*2*B_W);
					// Schatten
					for ($i=SHADOW_L;$i>0;$i--)
					{
						imageline($im, $left+$i, B_B+B_H-(B_H*$p/$pmax)-$i, $left+B_W+$i, B_B+B_H-(B_H*$p/$pmax)-$i, $s_col[$i]);
						imageline($im, $left+B_W+$i, B_B+B_H-(B_H*$p/$pmax)-$i, $left+B_W+$i, B_B+B_H-$i, $s_col[$i]);
					}
					// Balken
					for ($x=0;$x<B_W;$x++)
					{
						imageline($im, $left+$x, B_B+B_H-(B_H*$p/$pmax), $left+$x, B_B+B_H, $b_col[$x]);
					}
					// Rahmen
					imagerectangle($im, $left, B_B+B_H-(B_H*$p/$pmax), $left+B_W, B_B+B_H, $black);
					// Zeit
					imagestring($im,FONT_SIZE,$left+(B_W/2)-imagefontwidth(1)*5/2,B_B+B_H+5,date("H:i",$t),$black);
					imagestring($im,FONT_SIZE,$left-8+(B_W/2)-imagefontwidth(1)*5/2,B_B+B_H+13,date("d.m.y",$t),$black);
					// Punkte
					if ((B_H*$p/$pmax)-12 < imagefontwidth(1)*strlen(nf($p)))
						imagestringup($im,FONT_SIZE,$left+(B_W/2)-imagefontheight(1)/2,B_B+B_H-(B_H*$p/$pmax)-10,nf($p),$black);
					else
						imagestringup($im,FONT_SIZE,$left+(B_W/2)-imagefontheight(1)/2,B_H+B_B-10,nf($p),$white);
					$cnt++;
				}
			}
			else
				imagestring($im,3,10,10,"Keine Punktdaten vorhanden!",$black);			
		}		
		else
			imagestring($im,3,10,10,"Fehler! Benutzer nicht vorhanden!",$black);			
	}
	else
		imagestring($im,3,10,10,"Fehler! Keine ID angegeben oder du bist nicht eingeloggt!",$black);			
	ImagePNG($im);





	dbclose();		
?>