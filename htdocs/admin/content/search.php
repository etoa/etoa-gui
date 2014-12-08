<?PHP
	if (isset($_POST['search_query']) && $_POST['search_query']!="")
	{
		$search = $_POST['search_query'];
		echo "<h1>Suche nach <i>".$search."</i></h1>";

		// Users		
		$res=dbquery("
		SELECT
			user_id,
			user_nick
		FROM 
			users
		WHERE
			user_nick LIKE '%".$search."%'		
			OR user_name LIKE '%".$search."%'		
			OR user_email LIKE '%".$search."%'		
			OR user_email_fix LIKE '%".$search."%'
		ORDER BY
			user_nick
		LIMIT 30;			
			
		");
		if (mysql_num_rows($res)>0)
		{
			echo "<h2>Spieler</h2><ul>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<li><a href=\"?page=user&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['user_nick']."</a></li>";
			}
			echo "</ul>";
		}

		// Alliances		
		$res=dbquery("
		SELECT
			alliance_id,
			alliance_name,
			alliance_tag
		FROM 
			alliances
		WHERE
			alliance_name LIKE '%".$search."%'		
			OR alliance_tag LIKE '%".$search."%'		
		ORDER BY
			alliance_tag
		");
		if (mysql_num_rows($res)>0)
		{
			echo "<h2>Allianzen</h2><ul>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<li><a href=\"?page=alliances&amp;sub=edit&amp;alliance_id=".$arr['alliance_id']."\">[".$arr['alliance_tag']."] ".$arr['alliance_name']."</a></li>";
			}
			echo "</ul>";
		}
		
		// Planets		
		$res=dbquery("
		SELECT
			id
		FROM 
			planets
		WHERE
			planet_name LIKE '%".$search."%'	
		ORDER BY planet_name	
		LIMIT 30;
		");
		if (mysql_num_rows($res)>0)
		{
			echo "<h2>Planeten</h2><ul>";
			while ($arr=mysql_fetch_array($res))
			{
				$pl = Planet::getById($arr['id']);
				echo "<li><a href=\"?page=galaxy&sub=edit&id=".$arr['id']."\">".$pl."</a></li>";
			}
			echo "</ul>";
		}
		
	}
	else
	{
		echo "<h1>Suche</h1>";
		echo err_msg("Kein Suchbegriff eingegeben!");
	}

	
	

?>