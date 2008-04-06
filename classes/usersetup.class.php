<?PHP

	class Usersetup
	{
	/**
	* Add an item setlist to a given planet
	*/
	static function addItemSetListToPlanet($planetid,$userid,$setid)
	{
		// Add buildings
		$ires = dbquery("
		SELECT 
			item_object_id as id,
			item_count as count
		FROM 
			default_items
		WHERE
			item_set_id=".$setid." 
			AND item_cat='b';");
		if (mysql_num_rows($ires)>0)
		{
			while($iarr = mysql_fetch_assoc($ires))
			{
				dbquery("INSERT INTO
					buildlist
					(
						buildlist_building_id,
						buildlist_user_id,
						buildlist_planet_id,
						buildlist_current_level						
					)
					VALUES
					(
						".$iarr['id'].",
						".$userid.",
						".$planetid.",
						".$iarr['count']."
					);");						
			}		
		}		
		
		// Add technologies
		$ires = dbquery("
		SELECT 
			item_object_id as id,
			item_count as count
		FROM 
			default_items
		WHERE
			item_set_id=".$setid." 
			AND item_cat='t';");
		if (mysql_num_rows($ires)>0)
		{
			while($iarr = mysql_fetch_assoc($ires))
			{
				dbquery("INSERT INTO
					techlist
					(
						techlist_tech_id,
						techlist_user_id,
						techlist_planet_id,
						techlist_current_level						
					)
					VALUES
					(
						".$iarr['id'].",
						".$userid.",
						".$planetid.",
						".$iarr['count']."
					);");						
			}		
		}

		// Add ships
		$ires = dbquery("
		SELECT 
			item_object_id as id,
			item_count as count
		FROM 
			default_items
		WHERE
			item_set_id=".$setid." 
			AND item_cat='s';");
		if (mysql_num_rows($ires)>0)
		{
			while($iarr = mysql_fetch_assoc($ires))
			{
				dbquery("INSERT INTO
					shiplist
					(
						shiplist_ship_id,
						shiplist_user_id,
						shiplist_planet_id,
						shiplist_count						
					)
					VALUES
					(
						".$iarr['id'].",
						".$userid.",
						".$planetid.",
						".$iarr['count']."
					);");						
			}		
		}
			
		// Add defense
		$ires = dbquery("
		SELECT 
			item_object_id as id,
			item_count as count
		FROM 
			default_items
		WHERE
			item_set_id=".$setid." 
			AND item_cat='d';");
		if (mysql_num_rows($ires)>0)
		{
			while($iarr = mysql_fetch_assoc($ires))
			{
				dbquery("INSERT INTO
					deflist
					(
						deflist_def_id,
						deflist_user_id,
						deflist_planet_id,
						deflist_count						
					)
					VALUES
					(
						".$iarr['id'].",
						".$userid.",
						".$planetid.",
						".$iarr['count']."
					);");						
			}		
		}		
	}
			
		
	}



?>