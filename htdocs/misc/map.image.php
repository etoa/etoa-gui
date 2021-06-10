<?PHP

	include("image.inc.php");

	define('IMG_DIR',"images/imagepacks/Discovery");

	$sx_num=$conf['num_of_sectors']['p1'];
	$sy_num=$conf['num_of_sectors']['p2'];
	$cx_num=$conf['num_of_cells']['p1'];
	$cy_num=$conf['num_of_cells']['p2'];
	$p_num_min=$conf['num_planets']['p1'];
	$p_num_max=$conf['num_planets']['p2'];

  $size = min(isset($_GET['size']) ? intval($_GET['size']) : GALAXY_MAP_WIDTH, 3000);

  $legend = isset($_GET['legend']);
  $legendHeight = $legend ? GALAXY_MAP_LEGEND_HEIGHT : 0;

	define('GALAXY_IMAGE_SCALE', $size /((($sx_num-1)*10)+$cx_num));

	$w = $size;
	$h = $sy_num*$cy_num*GALAXY_IMAGE_SCALE + $legendHeight;
	$im = imagecreatetruecolor($w,$h);

	$colBlack = imagecolorallocate($im,0,0,0);
	$colGrey = imagecolorallocate($im,120,120,120);
	$colYellow = imagecolorallocate($im,255,255,0);
	$colOrange = imagecolorallocate($im,255,100,0);
	$colWhite = imagecolorallocate($im,255,255,255);
	$colGreen = imagecolorallocate($im,0,255,0);
	$colBlue = imagecolorallocate($im,150,150,240);
	$colViolett = imagecolorallocate($im,200,0,200);
	$colRe = imagecolorallocate($im,200,0,200);

	$admin = isset($_SESSION['adminsession']) ? true : false;

	if (isset($_SESSION) || $admin)
	{
		if (isset($_SESSION))
		{
			$s = $_SESSION;
		}
		if ($admin || (isset($s['user_id']) && $s['user_id'] > 0))
		{

			if ($admin && !empty($_GET['user']))
			{
				$user = new User($_GET['user']);
			}
			else if (!$admin && isset($s))
			{
				$user = new CurrentUser($s['user_id']);
			}

			$starImageSrc = imagecreatefrompng(IMG_DIR."/stars/star4_small.png");
			$starImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($starImage,$starImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($starImageSrc),imagesy($starImageSrc));

			$nebulaImageSrc = imagecreatefrompng(IMG_DIR."/nebulas/nebula2_small.png");
			$nebulaImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($nebulaImage,$nebulaImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($nebulaImageSrc),imagesy($nebulaImageSrc));

			$asteroidImageSrc = imagecreatefrompng(IMG_DIR."/asteroids/asteroids1_small.png");
			$asteroidImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($asteroidImage,$asteroidImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($asteroidImageSrc),imagesy($asteroidImageSrc));

			$spaceImageSrc = imagecreatefrompng(IMG_DIR."/space/space1_small.png");
			$spaceImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($spaceImage,$spaceImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($spaceImageSrc),imagesy($spaceImageSrc));

			$wormholeImageSrc = imagecreatefrompng(IMG_DIR."/wormholes/wormhole1_small.png");
			$wormholeImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($wormholeImage,$wormholeImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($wormholeImageSrc),imagesy($wormholeImageSrc));

			$persistentWormholeImageSrc = imagecreatefrompng(IMG_DIR."/wormholes/wormhole_persistent1_small.png");
			$persistentWormholeImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($persistentWormholeImage,$persistentWormholeImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($persistentWormholeImageSrc),imagesy($persistentWormholeImageSrc));

      $unexploredImages = array();
      for ($i=1;$i<7;$i++) {
        $unexploredImageSrc = imagecreatefrompng(IMG_DIR."/unexplored/fog$i.png");
        $unexploredImages[$i] = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
          imagecopyresampled($unexploredImages[$i],$unexploredImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($unexploredImageSrc),imagesy($unexploredImageSrc));
      }

      $fogborderImages = array();
      for ($i=1;$i<16;$i++) {
        $fogborderImageSrc = imagecreatefrompng(IMG_DIR."/unexplored/fogborder$i.png");
        $fogborderImages[$i] = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
          imagecopyresampled($fogborderImages[$i],$fogborderImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($fogborderImageSrc),imagesy($fogborderImageSrc));
      }

			if (isset($_GET['type']) && $_GET['type']=="alliance")
			{
				$uid=$_SESSION['user_id'];
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
                AND u.user_alliance_id > 0
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
				$col = [];
				for ($x=1;$x<=$p_num_max;$x++)
				{
					$col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
				}
				while ($arr=mysql_fetch_array($res))
				{
					$x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$y = $h-$legendHeight+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$arr['cnt']]);
				}
        if ($legend) {
          imagestring($im,3,10,$h-$legendHeight+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
          imagefilledellipse ($im,80,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$p_num_max]);
          imagefilledellipse ($im,135,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[floor($p_num_max/2)]);
          imagefilledellipse ($im,205,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[3]);
        }
			}
			elseif (isset($_GET['type']) && $_GET['type']=="own")
			{
				$uid=$_SESSION['user_id'];
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
				$col = [];
				for ($x=1;$x<=$p_num_max;$x++)
				{
					$col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
				}
				while ($arr=mysql_fetch_array($res))
				{
					$x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$y = $h-$legendHeight+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$arr['cnt']]);
				}
        if ($legend) {
          imagestring($im,3,10,$h-$legendHeight+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
          imagefilledellipse ($im,80,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$p_num_max]);
          imagefilledellipse ($im,135,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[floor($p_num_max/2)]);
          imagefilledellipse ($im,205,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[3]);
        }
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
				$col = [];
				for ($x=1;$x<=$p_num_max;$x++)
				{
					$col[$x] = imagecolorallocate($im,(255/$p_num_max*$x),(255/$p_num_max*$x),0);
				}
				while ($arr=mysql_fetch_assoc($res))
				{
					$x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					$y = $h-$legendHeight+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
					imagefilledellipse ($im,$x,$y,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[max(3,$arr['cnt'])]);
				}
        if ($legend) {
          imagestring($im,3,10,$h-$legendHeight+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
          imagefilledellipse ($im,80,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[$p_num_max]);
          imagefilledellipse ($im,135,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[floor($p_num_max/2)]);
          imagefilledellipse ($im,205,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$col[3]);
        }
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
				if (mysql_num_rows($res)>0)
				{
					while ($arr=mysql_fetch_array($res))
					{
            $x = ((($arr['sx']-1)*$cx_num + $arr['cx']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
            $y = $h-$legendHeight+GALAXY_IMAGE_SCALE-((($arr['sy']-1)*$cy_num + $arr['cy']) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE/2);
            $xe = $x-(GALAXY_IMAGE_SCALE/2);
            $ye = $y-(GALAXY_IMAGE_SCALE/2);

            $sx = $arr['sx'];
            $sy = $arr['sy'];
            $xcoords = $arr['cx'];
            $ycoords = $arr['cy'];

            if (($admin && !isset($user)) || $user->discovered((($arr['sx'] - 1) * $cx_num) + $arr['cx'],(($arr['sy'] - 1) * $cy_num) + $arr['cy']))
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
                imagecopyresampled($im,$starImageSrc,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($starImageSrc),imagesy($starImageSrc));
              }
              elseif ($arr['code']=='w')
              {
				$wh = new Wormhole($arr['id']);
				if ($wh->isPersistent())
				{
                    imagecopyresampled($im,$persistentWormholeImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
				} else {
                    imagecopyresampled($im,$wormholeImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
				}
              }
              elseif ($arr['code']=='a')
              {
                  imagecopyresampled($im,$asteroidImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
              }
              elseif ($arr['code']=='n')
              {
                  imagecopyresampled($im,$nebulaImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
              }
              elseif ($arr['code']=='e' || $arr['code']=='m')
              {
                  imagecopyresampled($im,$spaceImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
              }
              else
                continue;
            }
            else
            {
              $fogCode = 0;
              // Bottom
              $fogCode += $ycoords > 1 && $user->discovered((($sx - 1) * $cx_num) + $xcoords  , (($sy - 1) * $cy_num) + $ycoords-1) ? 1 : 0;
              // Left
              $fogCode += $xcoords > 1 && $user->discovered((($sx - 1) * $cx_num) + $xcoords-1, (($sy - 1) * $cy_num) + $ycoords  ) ? 2 : 0;
              // Right
              $fogCode += $xcoords < $cx_num && $user->discovered((($sx - 1) * $cx_num) + $xcoords+1, (($sy - 1) * $cy_num) + $ycoords  ) ? 4 : 0;
              // Top
              $fogCode += $ycoords < $cy_num && $user->discovered((($sx - 1) * $cx_num) + $xcoords  , (($sy - 1) * $cy_num) + $ycoords+1) ? 8 : 0;
              if ($fogCode > 0) {
                  imagecopyresampled($im,$fogborderImages[$fogCode],$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
              } else {
                  imagecopyresampled($im,$unexploredImages[mt_rand(1,6)],$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
              }
            }
					}
				}
				else
				{
					imagestring($im,3,20,20,"Universum existiert noch nicht!",$colWhite);
				}
				/*
				imagestring($im,3,10,$h-$legendHeight+10,"Legende:    Stern    Asteroidenfeld    Nebel    Wurmloch",$colWhite);
				imagefilledellipse ($im,80,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colWhite);
				imagefilledellipse ($im,145,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colGrey);
				imagefilledellipse ($im,270,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colOrange);
				imagefilledellipse ($im,335,$h-$legendHeight+10+GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,GALAXY_MAP_DOT_RADIUS*2,$colViolett);
				*/
        if ($legend) {
          imagestring($im,3,10,$h-$legendHeight+10,"Galaxiekarte",$colWhite);
        }
			}

			for ($x=($cx_num*GALAXY_IMAGE_SCALE);$x<$w;$x+=($cx_num*GALAXY_IMAGE_SCALE))
			{
				MDashedLine($im,$x,0,$x,$h-$legendHeight,$colGrey,$colBlack);
			}
			for ($y=($cy_num*GALAXY_IMAGE_SCALE);$y<$h;$y+=($cy_num*GALAXY_IMAGE_SCALE))
			{
				MDashedLine($im,0,$y,$w,$y,$colGrey,$colBlack);
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
