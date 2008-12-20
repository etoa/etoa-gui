<?PHP
	session_start();
	include("../conf.inc.php");
	include("../functions.php");
	define("CLASS_ROOT","../classes");
	dbconnect();
	$conf=get_all_config();
	include("../def.inc.php");

	define('GALAXY_MAP_DOT_RADIUS',3);
	define('GALAXY_MAP_WIDTH',500);
	define('GALAXY_MAP_LEGEND_HEIGHT',40);

	define('IMG_DIR',"../images/imagepacks/Discovery");

	$sx_num=$conf['num_of_sectors']['p1'];
	$sy_num=$conf['num_of_sectors']['p2'];
	$cx_num=$conf['num_of_cells']['p1'];
	$cy_num=$conf['num_of_cells']['p2'];
	$p_num_min=$conf['num_planets']['p1'];
	$p_num_max=$conf['num_planets']['p2'];

	define('GALAXY_IMAGE_SCALE',GALAXY_MAP_WIDTH/((($sx_num-1)*10)+$cx_num));

	
	$w = GALAXY_MAP_WIDTH;
	$h = $sy_num*$cy_num*GALAXY_IMAGE_SCALE+GALAXY_MAP_LEGEND_HEIGHT;
	$im = imagecreatetruecolor($w,$h);
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1	
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit	
	header ("Content-type: image/png");
	$colBlack = imagecolorallocate($im,0,0,0);
	$colGrey = imagecolorallocate($im,120,120,120);
	$colYellow = imagecolorallocate($im,255,255,0);
	$colOrange = imagecolorallocate($im,255,100,0);
	$colWhite = imagecolorallocate($im,255,255,255);
	$colGreen = imagecolorallocate($im,0,255,0);
	$colBlue = imagecolorallocate($im,150,150,240);
	$colViolett = imagecolorallocate($im,200,0,200);
	$colRe = imagecolorallocate($im,200,0,200);



	if (isset($_SESSION[ROUNDID]))
	{
		$s = $_SESSION[ROUNDID];
		if ($s['user_id'] > 0)
		{
			$cu = new CurrentUser($s['user_id']);
		
			$starImageSrc = imagecreatefrompng(IMG_DIR."/stars/star4_small.png");
			$starImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
			ImageCopyResampled($starImage,$starImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($starImageSrc),imagesy($starImageSrc));
			
			$nebulaImageSrc = imagecreatefrompng(IMG_DIR."/nebulas/nebula2_small.png");
			$nebulaImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
			ImageCopyResampled($nebulaImage,$nebulaImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($nebulaImageSrc),imagesy($nebulaImageSrc));
		
			$asteroidImageSrc = imagecreatefrompng(IMG_DIR."/asteroids/asteroids1_small.png");
			$asteroidImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
			ImageCopyResampled($asteroidImage,$asteroidImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($asteroidImageSrc),imagesy($asteroidImageSrc));
		
			$spaceImageSrc = imagecreatefrompng(IMG_DIR."/space/space1_small.png");
			$spaceImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
			ImageCopyResampled($spaceImage,$spaceImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($spaceImageSrc),imagesy($spaceImageSrc));
		
			$wormholeImageSrc = imagecreatefrompng(IMG_DIR."/wormholes/wormhole1_small.png");
			$wormholeImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
			ImageCopyResampled($wormholeImage,$wormholeImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($wormholeImageSrc),imagesy($wormholeImageSrc));
				
			$unexploredImageSrc = imagecreatefrompng(IMG_DIR."/unexplored/fog1.png");
			$unexploredImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
			ImageCopyResampled($unexploredImage,$unexploredImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($unexploredImageSrc),imagesy($unexploredImageSrc));
			
		
			if (isset($_GET['type']) && $_GET['type']=="alliance")
			{                        
				$uid=$_SESSION[ROUNDID]['user_id'];
				$res=dbquery("
							SELECT 
								cells.sx,
								cells.cx,
								cells.sy,
								cells.cy,
								COUNT(planets.id) AS cnt
							FROM
								users as u
							INNER JOIN
								users as a
							ON
								a.user_alliance_id=u.user_alliance_id
								AND u.user_id='$uid'
							INNER JOIN
								planets
							ON
								planets.planet_user_id=a.user_id
							INNER JOIn
								entities
							ON
								entities.id=planets.id
							INNER JOIN
								cells
							ON
								cells.id=entities.cell_id
							GROUP BY
								cells.id;
				");
				for ($x=1;$x<=$p_num_max;$x++)
				{
					$col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
				}			
				while ($arr=mysql_fetch_array($res))
				{
					$x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$y = $h-GALAXY_MAP_LEGEND_HEIGHT+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$arr['cnt']]);
				}		
				imagestring($im,3,10,$h-GALAXY_MAP_LEGEND_HEIGHT+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
				imagefilledellipse ($im,80,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$p_num_max]);
				imagefilledellipse ($im,135,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[floor($p_num_max/2)]);
				imagefilledellipse ($im,205,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[3]);
			}
			elseif (isset($_GET['type']) && $_GET['type']=="own")
			{
				$uid=$_SESSION[ROUNDID]['user_id'];
				$res=dbquery("
							SELECT 
								cells.sx, 
								cells.cx, 
								cells.sy, 
								cells.cy,
								COUNT(planets.id) AS cnt
							FROM 
								planets
							INNER JOIN
								entities
							ON 
								entities.id=planets.id
								AND planets.planet_user_id='$uid'
							INNER JOIN
								cells
							ON
								cells.id=entities.cell_id
							GROUP BY
								cells.id;
				");
				for ($x=1;$x<=$p_num_max;$x++)
				{
					$col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
				}		
				while ($arr=mysql_fetch_array($res))
				{
					$x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$y = $h-GALAXY_MAP_LEGEND_HEIGHT+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$arr['cnt']]);
				}		
				imagestring($im,3,10,$h-GALAXY_MAP_LEGEND_HEIGHT+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
				imagefilledellipse ($im,80,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$p_num_max]);
				imagefilledellipse ($im,135,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[floor($p_num_max/2)]);
				imagefilledellipse ($im,205,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[3]);
			}
			elseif (isset($_GET['type']) && $_GET['type']=="populated")
			{
				$res=dbquery("		
				SELECT 
					c.sx, 
					c.cx, 
					c.sy,
					c.cy,
					COUNT(p.id) AS cnt
				FROM 
					cells c,
					planets p,
					entities e
				WHERE
					p.id=e.id
					AND e.cell_id=c.id
					AND p.planet_user_id>0
				GROUP BY 
					e.cell_id
				");
				for ($x=1;$x<=$p_num_max;$x++)
				{
					$col[$x] = imagecolorallocate($im,(255/$p_num_max*$x),(255/$p_num_max*$x),0);
				}
				while ($arr=mysql_fetch_assoc($res))
				{
					$x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$y = $h-GALAXY_MAP_LEGEND_HEIGHT+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[max(3,$arr['cnt'])]);
				}		
				imagestring($im,3,10,$h-GALAXY_MAP_LEGEND_HEIGHT+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
				imagefilledellipse ($im,80,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$p_num_max]);
				imagefilledellipse ($im,135,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[floor($p_num_max/2)]);
				imagefilledellipse ($im,205,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[3]);
			}
			else
			{
				$res=dbquery("
				SELECT 
					cells.sx, 
					cells.cx, 
					cells.sy,
					cells.cy,
					entities.id,
					entities.code
				FROM 
					cells
				INNER JOIN
					entities 
					ON entities.cell_id = cells.id
					AND entities.pos=0
				");
				while ($arr=mysql_fetch_array($res))
				{
					$x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$y = $h-GALAXY_MAP_LEGEND_HEIGHT+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$xe = $x-(GALAXY_IMAGE_SCALE/2);
					$ye = $y-(GALAXY_IMAGE_SCALE/2);
		
					if ($cu->discovered((($arr['sx'] - 1) * $cx_num) + $arr['cx'],(($arr['sy'] - 1) * $cy_num) + $arr['cy']))
					{
						if ($arr['code']=='s')
						{
							$sres = dbquery("
							SELECT
								type_id
							FROM 
								stars
							WHERE
								id=".$arr['id']."
							LIMIT 1;
							");
							$sarr = mysql_fetch_row($sres);
							$starImageSrc = imagecreatefrompng(IMG_DIR."/stars/star".$sarr[0]."_small.png");
							ImageCopyResampled($im,$starImageSrc,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($starImageSrc),imagesy($starImageSrc));
						}
						elseif ($arr['code']=='w')
						{
							ImageCopyResampled($im,$wormholeImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
						}
						elseif ($arr['code']=='a')
						{
							ImageCopyResampled($im,$asteroidImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
						}
						elseif ($arr['code']=='n')
						{
							ImageCopyResampled($im,$nebulaImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
						}
						elseif ($arr['code']=='e' || $arr['code']=='m')
						{
							ImageCopyResampled($im,$spaceImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
						}
						else
							continue;
					}
					else
					{
						ImageCopyResampled($im,$unexploredImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
					}
				}
				imagestring($im,3,10,$h-GALAXY_MAP_LEGEND_HEIGHT+10,"Legende:    Stern    Asteroidenfeld    Nebel    Wurmloch",$colWhite);
				imagefilledellipse ($im,80,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colWhite);
				imagefilledellipse ($im,145,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colGrey);
				imagefilledellipse ($im,270,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colOrange);
				imagefilledellipse ($im,335,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colViolett);
			}
			
			for ($x=($cx_num*GALAXY_IMAGE_SCALE);$x<$w;$x+=($cx_num*GALAXY_IMAGE_SCALE))
			{
				imageline($im,$x,0,$x,$h-GALAXY_MAP_LEGEND_HEIGHT,$colBlue);
			}
			for ($y=($cy_num*GALAXY_IMAGE_SCALE);$y<$h;$y+=($cy_num*GALAXY_IMAGE_SCALE))
			{
				imageline($im,0,$y,$w,$y,$colBlue);
			}
		}
		else
		{
			imagestring($im,5,10,10,"Nicht eingeloggt!",$colWhite);
		}
	}
	else
	{
		imagestring($im,5,10,10,"Nicht eingeloggt!",$colWhite);		
	}
	echo imagepng($im);
	
	
	dbclose();
?>