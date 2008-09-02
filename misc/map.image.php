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

	$sx_num=$conf['num_of_sectors']['p1'];
	$sy_num=$conf['num_of_sectors']['p2'];
	$cx_num=$conf['num_of_cells']['p1'];
	$cy_num=$conf['num_of_cells']['p2'];
	$p_num_min=$conf['num_planets']['p1'];
	$p_num_max=$conf['num_planets']['p2'];

	define('GALAXY_IMAGE_SCALE',GALAXY_MAP_WIDTH/((($sx_num-1)*10)+$cx_num));
	
	$w = GALAXY_MAP_WIDTH;
	$h = $sy_num*$cy_num*GALAXY_IMAGE_SCALE+GALAXY_MAP_LEGEND_HEIGHT;
	$im = imagecreate($w,$h);
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


	if (isset($_GET['type']) && $_GET['type']=="alliance")
	{                        
		$aid=$_SESSION[ROUNDID]['user']['alliance_id'];
		$res=dbquery("SELECT cell_sx, cell_cx, cell_sy, cell_cy,
		COUNT(id) AS cnt
		FROM space_cells,
		planets,
		users
		WHERE
			planet_solsys_id=cell_id
			AND user_alliance_id=$aid
			AND planet_user_id=user_id
			AND user_alliance_id!=0
		GROUP BY
			cell_id;
		");
		for ($x=1;$x<=$p_num_max;$x++)
		{
			$col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
		}			
		while ($arr=mysql_fetch_array($res))
		{
			$x = ((($arr['cell_sx']-1)*$cx_num + $arr['cell_cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
			$y = $h-GALAXY_MAP_LEGEND_HEIGHT+GALAXY_IMAGE_SCALE-((($arr['cell_sy']-1)*$cy_num + $arr['cell_cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
			imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$arr['cnt']]);
		}		
		imagestring($im,3,10,$h-GALAXY_MAP_LEGEND_HEIGHT+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
		imagefilledellipse ($im,80,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$p_num_max]);
		imagefilledellipse ($im,135,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[floor($p_num_max/2)]);
		imagefilledellipse ($im,205,$h-GALAXY_MAP_LEGEND_HEIGHT+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[3]);
	}
	elseif (isset($_GET['type']) && $_GET['type']=="own")
	{
		$uid=$_SESSION[ROUNDID]['user']['id'];
		$res=dbquery("SELECT cell_sx, cell_cx, cell_sy, cell_cy,
		COUNT(id) AS cnt
		FROM space_cells,
		planets
		WHERE
			planet_solsys_id=cell_id
			AND planet_user_id=$uid
		GROUP BY
			cell_id;
		");
		for ($x=1;$x<=$p_num_max;$x++)
		{
			$col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
		}		
		while ($arr=mysql_fetch_array($res))
		{
			$x = ((($arr['cell_sx']-1)*$cx_num + $arr['cell_cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
			$y = $h-GALAXY_MAP_LEGEND_HEIGHT+GALAXY_IMAGE_SCALE-((($arr['cell_sy']-1)*$cy_num + $arr['cell_cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
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
				if ($arr['code']=='s')
					$col = $colWhite;
				elseif ($arr['code']=='w')
					$col = $colViolett;
				elseif ($arr['code']=='a')
					$col = $colGrey;
				elseif ($arr['code']=='m')
					$col = $colOrange;
				else
					continue;
				imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col);
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

	echo imagepng($im);
	dbclose();
?>