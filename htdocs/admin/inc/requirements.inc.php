<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

	//
	// Anforderungen
	//
$twig->addGlobal("title", TITLE);

	// Lade Gebäude- & Technologienamen
	$bures = dbquery("SELECT building_id,building_name FROM buildings;");
	while ($buarr = mysql_fetch_array($bures))
	{
		$bu[$buarr['building_id']]=$buarr['building_name'];
	}
	$teres = dbquery("SELECT tech_id,tech_name FROM technologies;");
	while ($tearr = mysql_fetch_array($teres))
	{
		$te[$tearr['tech_id']]=$tearr['tech_name'];
	}

	$res = dbquery("
	SELECT 
		`".ITEM_ID_FLD."` as id,
		`".ITEM_NAME_FLD."` as name
	FROM 
		`".ITEMS_TBL."`
	WHERE
		".ITEM_ENABLE_FLD."=1
	ORDER BY 
		".ITEM_ORDER_FLD.";");
	if (mysql_num_rows($res)>0)
	{
		echo "<table><tr>
			<th colspan=\"".(defined('ITEM_IMAGE_PATH')?2:1)."\">Name</th>
			<th>Voraussetzungen</th>
		</tr>";
		while ($arr = mysql_fetch_assoc($res))
		{
			$id = $arr['id'];
			echo "<tr>";
			if (defined('ITEM_IMAGE_PATH'))
			{
				$path = preg_replace('/<DB_TABLE_ID>/',$id,ITEM_IMAGE_PATH);
				if (is_file($path))
				{
					$imsize = getimagesize($path);
					echo "<td style=\"background:#000;width:".$imsize[0]."px;\"><img src=\"".$path."\"/></td>";
				}
				else
				{
					echo "<td style=\"background:#000;width:40px;\"><img src=\"../images/blank.gif\" style=\"width:40px;height:40px;\" /></td>";
				}
			}
			echo "<td>".$arr['name']."</td><td>";

			echo "<div id=\"item_container_".$id."\">";
			drawTechTreeForSingleItem(REQ_TBL,$id);
			echo "</div>";
			echo "<br/><select id=\"reqid_".$id."\">
			<option value=\"\">Anforderung wählen...</option>";
			foreach ($bu as $k=>$v)
			{
				echo "<option value=\"b:$k\">$v</option>";
			}
			echo "<option value=\"\">----------------------</option>";
			foreach ($te as $k=>$v)
			{
				echo "<option value=\"t:$k\">$v</option>";
			}
			echo "</select><input type=\"text\" id=\"reqlvl_".$id."\" size=\"2\" maxlength=\"2\" value=\"1\" />
			<input type=\"button\" onclick=\"xajax_addToTechTree('".REQ_TBL."',".$id.",document.getElementById('reqid_".$id."').value,document.getElementById('reqlvl_".$id."').value);\" value=\"Hinzufügen\" />";


			echo "</td></tr>";
		}
		tableEnd();
	}
	else
	{
		echo "<i>Keine Objekte vorhanden!</i>";
	}


?>
