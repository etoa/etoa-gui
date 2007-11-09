<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: planet.php														//
	// Topic: Planetendetailsanzeige								//
	// Version: 0.1																	//
	// Letzte Änderung: 01.10.2004									//
	//////////////////////////////////////////////////

	// DATEN LADEN

	$sol_type = get_sol_types_array();
	$planet_type = get_planet_types_array();

	// DEFINITIONEN //

	define(TBL_SPACING,$conf['general_table_offset']['v']);
	define(TBL_PADDING,$conf['general_table_offset']['p1']);

	// BEGIN SKRIPT //

	if (intval($_GET['planet_info_id'])>0)
	{
		$res = dbquery("
		SELECT 
			p.planet_user_id,
			p.planet_name,
			p.planet_image,
			p.planet_solsys_pos,
			p.planet_fields,
            p.planet_fields_used,
            p.planet_temp_from,
            p.planet_temp_to,
            p.planet_desc,
            sp.cell_id,
            sp.cell_sx,
            sp.cell_sy,
            sp.cell_cx,
            sp.cell_cy,
            pt.type_name as ptype,
            s.type_name as stype
		FROM 
            ".$db_table['planets']." AS p,
            ".$db_table['space_cells']." AS sp,
            ".$db_table['sol_types']." AS s,
            ".$db_table['planet_types']." AS pt
		WHERE
            sp.cell_solsys_solsys_sol_type=s.type_id
            AND p.planet_type_id=pt.type_id
            AND p.planet_solsys_id=sp.cell_id
            AND p.planet_id='".intval($_GET['planet_info_id'])."'
		GROUP BY 
			p.planet_id;");
		$arr = mysql_fetch_array($res);

		echo "<h1>&Uuml;bersicht &uuml;ber den Planeten ".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos'];
		if ($arr['planet_name']!="") echo " (".$arr['planet_name'].")";
		echo "</h1>";

		$p_img = IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['planet_image'].".gif";

		infobox_start("Planetendaten",1);
		echo "<tr><td width=\"320\" class=\"tbldata\" style=\"background:#000;\" rowspan=\"8\"><img src=\"$p_img\" width=\"310\" height=\"310\"/></td>";
		echo "<td width=\"100\" class=\"tbltitle\">Besitzer</td><td class=\"tbldata\">";
		if ($arr['planet_user_id']>0)
		{
			$user = get_user_nick($arr['planet_user_id']);
			if ($user!="") echo $user." [<a href=\"?page=userinfo&id=".$arr['planet_user_id']."\" title=\"Info\">Info</a>]";
		}
		else
			echo "<i>Unbewohnter Planet</i>";
		echo "</td></tr>";
		echo "<tr><td width=\"100\" class=\"tbltitle\">Sonnentyp</td><td class=\"tbldata\">".$arr['stype']."</td></tr>";
		echo "<tr><td width=\"100\" class=\"tbltitle\">Planettyp</td><td class=\"tbldata\">".$arr['ptype']."</td></tr>";
		echo "<tr><td width=\"100\" class=\"tbltitle\">Felder</td><td class=\"tbldata\">".$arr['planet_fields_used']." benutzt, ".$arr['planet_fields']." total</td></tr>";
		echo "<tr><td width=\"100\" class=\"tbltitle\">Gr&ouml;sse</td><td class=\"tbldata\">".nf($conf['field_squarekm']['v']*$arr['planet_fields'])." km&sup2;</td></tr>";
		echo "<tr><td width=\"100\" class=\"tbltitle\">Temperatur</td><td class=\"tbldata\">".$arr['planet_temp_from']."&deg;C bis ".$arr['planet_temp_to']."&deg;C</td></tr>";
		echo "<tr><td width=\"100\" class=\"tbltitle\">Beschreibung</td><td class=\"tbldata\">".$arr['planet_desc']."</td></tr>";
		infobox_end(1);
	}
	else
		echo "<b>Fehler:</b> Keine Planeten-ID angegeben!<br/><br/>";
	echo "<input type=\"button\" value=\"Zur&uuml;ck zum Sonnensystem\" onclick=\"document.location='?page=solsys&id=".$arr['cell_id']."'\" />";



?>

