<?PHP
	$tpl->setView('galaxy/exploration');
	$tpl->assign('title', "Erkundung");

	$res=dbquery("
	SELECT 
		user_id,
		user_nick 
	FROM 
		users 
	ORDER BY 
		user_nick
	;");
	if (mysql_num_rows($res)>0)
	{
		$users = [];
		while ($arr = mysql_fetch_assoc($res))
		{
			$users[$arr['user_id']] = $arr['user_nick'];
		}
		$tpl->assign("users", $users);
	}
	else
	{
		$tpl->assign("errmsg", "Keine Benutzer vorhanden!");
	}

	if (isset($_GET['user_id']) && $_GET['user_id']>0)
	{
		$uid = $_GET['user_id'];
		$tpl->assign("uid", $uid);
		
		$user = new User($uid);
		$tpl->assign("user", $user);

		$sx = 1;
		$sy = 1;
		$cx = 1;
		$cy = 1;
		$radius = 1;
		
		// Discover selected cell
		if (isset($_POST['discover_selected'])) 
		{
			$sx = intval($_POST['sx']);
			$sy = intval($_POST['sy']);
			$cx = intval($_POST['cx']);
			$cy = intval($_POST['cy']);
			$radius = abs(intval($_POST['radius']));
		
			$res = dbQuerySave("
			SELECT
				id
			FROM 
				cells
			WHERE 
			 	sx=? 
				AND sy=? 
				AND cx=? 
				AND cy=?;", 
			array(
				$sx,	
				$sy,	
				$cx,	
				$cy	
			));
			if (mysql_num_rows($res))	
			{
				$arr = mysql_fetch_row($res);
				$cell = new Cell($arr[0]);
				if ($cell->isValid())
				{
					$user->setDiscovered($cell->absX(), $cell->absY(), $radius);
					$tpl->assign("msg", "Koordinaten erkundet!");
				}
			}
			else
			{
				$tpl->assign("errmsg", "Ungültige Koordinate!");
			}
		}
		// Reset discovered coordinates
		else if (isset($_POST['discover_reset'])) 
		{
			$user->setDiscoveredAll(false);
			$tpl->assign("msg", "Erkundung zurückgesetzt!");
		}
		// Discover all coordinates
		else if (isset($_POST['discover_all'])) 
		{
			$user->setDiscoveredAll(true);
			$tpl->assign("msg", "Alles erkundet!");
		}
		
		$tpl->assign("sx", $sx);
		$tpl->assign("sy", $sy);
		$tpl->assign("cx", $cx);
		$tpl->assign("cy", $cy);
		$tpl->assign("radius", $radius);
		
		$tpl->assign("discoveredPercent", $user->getDiscoveredPercent());
	}
?>