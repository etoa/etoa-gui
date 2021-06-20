<?PHP

$xajax->register(XAJAX_FUNCTION,"loadItemSelector");
$xajax->register(XAJAX_FUNCTION,"addItemToSet");
$xajax->register(XAJAX_FUNCTION,"loadItemSet");
$xajax->register(XAJAX_FUNCTION,"removeFromItemSet");
$xajax->register(XAJAX_FUNCTION,"showObjCountChanger");
$xajax->register(XAJAX_FUNCTION,"changeItem");

function changeItem($id,$value,$setid)
{
	$or = new xajaxResponse();
	dbquery("
	UPDATE
		default_items
	SET
		item_count='".$value."'
	WHERE
		item_id=".$id."
	");
	$or->script("xajax_loadItemSet(".$setid.");");
	return $or;
}


function removeFromItemSet($id,$setid)
{
	$or = new xajaxResponse();
	dbquery("
	DELETE FROM
		default_items
	WHERE
		item_id=".$id."
	");
	$or->script("xajax_loadItemSet(".$setid.");");
	return $or;
}

function showObjCountChanger($id,$setid)
{
	$or = new xajaxResponse();
	ob_start();
	$res = dbquery("
	SELECT
		item_count
	FROM
		default_items
	WHERE
		item_id=".$id."
	");
	$arr=mysql_fetch_array($res);
	echo "<input type=\"text\" id=\"countchanger_".$id."\" value=\"".$arr['item_count']."\" size=\"3\" />
	<input type=\"button\" onclick=\"xajax_changeItem(".$id.",document.getElementById('countchanger_".$id."').value,".$setid.")\" value=\"Speichern\"/>
	<input type=\"button\" onclick=\"xajax_loadItemSet(".$setid.")\" value=\"Abbrechen\"/>
	<input type=\"button\"onclick=\"xajax_removeFromItemSet(".$id.",".$setid.")\" value=\"Entfernen\" />";
	$out = ob_get_contents();
	ob_end_clean();
	$or->assign("details_".$id,"innerHTML",$out);
	$or->script("document.getElementById('countchanger_".$id."').select();");
	return $or;
}

function loadItemSet($setid)
{
	$or = new xajaxResponse();
	ob_start();
    $cnt = 0;

	$ires = dbquery("SELECT
		item_id as id,
		building_name as name,
		item_count as count
	FROM
		default_items
	INNER JOIN
		buildings
		ON building_id=item_object_id
		AND item_set_id=".$setid."
		AND item_cat='b'
	 ORDER BY building_type_id,building_order,building_name;");
	if (mysql_num_rows($ires)>0)
	{
		echo "<br/><b>Gebäude:</b><br/>";
		while($iarr = mysql_fetch_array($ires))
		{
			echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(".$iarr['id'].",".$setid.")\">".$iarr['name']."</span>
			<span id=\"details_".$iarr['id']."\">(".$iarr['count'].")</span><br/>";
		}
		$cnt++;
	}
	$ires = dbquery("SELECT
		item_id as id,
		tech_name as name,
		item_count as count
	FROM
		default_items
	INNER JOIN
		technologies
		ON tech_id=item_object_id
		AND item_set_id=".$setid."
		AND item_cat='t'
	 ORDER BY tech_type_id,tech_order,tech_name;");
	if (mysql_num_rows($ires)>0)
	{
		echo "<br/><b>Technologien:</b><br/>";
		while($iarr = mysql_fetch_array($ires))
		{
			echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(".$iarr['id'].",".$setid.")\">".$iarr['name']."</span>
			<span id=\"details_".$iarr['id']."\">(".$iarr['count'].")</span><br/>";
		}
		$cnt++;
	}
	$ires = dbquery("SELECT
		item_id as id,
		ship_name as name,
		item_count as count
	FROM
		default_items
	INNER JOIN
		ships
		ON ship_id=item_object_id
		AND item_set_id=".$setid."
		AND item_cat='s'
	 ORDER BY ship_name;");
	if (mysql_num_rows($ires)>0)
	{
		echo "<br/><b>Schiffe:</b><br/>";
		while($iarr = mysql_fetch_array($ires))
		{
			echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(".$iarr['id'].",".$setid.")\">".$iarr['name']."</span>
			<span id=\"details_".$iarr['id']."\">(".$iarr['count'].")</span><br/>";
		}
		$cnt++;
	}
	$ires = dbquery("SELECT
		item_id as id,
		def_name as name,
		item_count as count
	FROM
		default_items
	INNER JOIN
		defense
		ON def_id=item_object_id
		AND item_set_id=".$setid."
		AND item_cat='d'
	 ORDER BY def_name;");
	if (mysql_num_rows($ires)>0)
	{
		echo "<br/><b>Verteidigung:</b><br/>";
		while($iarr = mysql_fetch_array($ires))
		{
			echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(".$iarr['id'].",".$setid.")\">".$iarr['name']."</span>
			<span id=\"details_".$iarr['id']."\">(".$iarr['count'].")</span><br/>";
		}
		$cnt++;
	}
	if ($cnt==0)
	{
		echo "Keine Objekte definiert!<br/>";
	}

	$out = ob_get_contents();
	ob_end_clean();
	$or->assign("setcontent_".$setid,"innerHTML",$out);
	return $or;
}




function addItemToSet($setid,$form)
{
	$or = new xajaxResponse();
	ob_start();
	$cnt = intval($form['new_item_count']);
	if ($cnt>0)
	{
		$res = 		dbquery("
		SELECT
			item_id
		FROM
			default_items
		WHERE
			item_set_id=".$setid."
			AND item_cat='".$form['new_item_cat']."'
			AND item_object_id=".$form['new_item_object_id']."
		");
		if (mysql_num_rows($res)==0)
		{
			dbquery("
			INSERT INTO
				default_items
			(
				item_set_id,
				item_cat,
				item_object_id,
				item_count
			)
			VALUES
			(
				".$setid.",
				'".$form['new_item_cat']."',
				".$form['new_item_object_id'].",
				".$form['new_item_count']."
			);
			");
			$or->script("xajax_loadItemSet(".$setid.");");
		}
		else
		{
			$or->alert("Bereits vorhanden!");
			ob_end_clean();
			return $or;
		}
	}
	else
	{
		$or->alert("Ungültige Anzahl/Stufe!");
		ob_end_clean();
		return $or;
	}

	$out = ob_get_contents();
	ob_end_clean();
	return $or;
}

function loadItemSelector($cat,$setid)
{
	$or = new xajaxResponse();
	ob_start();
	if ($cat=="b")
	{
		$res = dbquery("
		SELECT
			building_name as name,
			building_id as id
		FROM
			buildings
		ORDER BY
			building_type_id,
			building_order,
			building_name;
		");
		echo "<select name=\"new_item_object_id\">";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<option value=\"".$arr['id']."\">".$arr['name']."</option>";
		}
		echo "</select> Stufe <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
		&nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(".$setid.",xajax.getFormValues('set_".$setid."'))\" value=\"Hinzufügen\" />";
	}
	elseif ($cat=="t")
	{
		$res = dbquery("
		SELECT
			tech_name as name,
			tech_id as id
		FROM
			technologies
		ORDER BY
			tech_type_id,
			tech_order,
			tech_name;
		");
		echo "<select name=\"new_item_object_id\">";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<option value=\"".$arr['id']."\">".$arr['name']."</option>";
		}
		echo "</select> Stufe <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
		&nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(".$setid.",xajax.getFormValues('set_".$setid."'))\" value=\"Hinzufügen\" />";
	}
	elseif ($cat=="s")
	{
		$res = dbquery("
		SELECT
			ship_name as name,
			ship_id as id
		FROM
			ships
		ORDER BY
			ship_name;
		");
		echo "<select name=\"new_item_object_id\">";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<option value=\"".$arr['id']."\">".$arr['name']."</option>";
		}
		echo "</select> Anzahl <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
		&nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(".$setid.",xajax.getFormValues('set_".$setid."'))\" value=\"Hinzufügen\" />";
	}
	elseif ($cat=="d")
	{
		$res = dbquery("
		SELECT
			def_name as name,
			def_id as id
		FROM
			defense
		ORDER BY
			def_name;
		");
		echo "<select name=\"new_item_object_id\">";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<option value=\"".$arr['id']."\">".$arr['name']."</option>";
		}
		echo "</select> Anzahl <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
		&nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(".$setid.",xajax.getFormValues('set_".$setid."'))\" value=\"Hinzufügen\" />";
	}
	else
	{
		echo "Bitte Kategorie wählen!";
	}

	$out=ob_get_contents();
	ob_end_clean();
	$or->assign("itemlist_".$setid,"innerHTML",$out);
	return $or;
}

?>
