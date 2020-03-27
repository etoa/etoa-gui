<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: galaxy.php
	// 	Topic: Verwaltung der Galaxie
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Beistzerprüfung
	//
	if ($sub=="map")
	{
 		require("galaxy/map.inc.php");
	}

	//
	// Exploration
	//
	elseif ($sub=="exploration")
	{
		require("galaxy/exploration.inc.php");
	}

	//
	// Universe Maintenance
	//
	elseif ($sub=="uni")
	{
    require("galaxy/universe.php");
	}

	//
	// Integrity check
	//
	elseif ($sub=="galaxycheck")
	{
    require("galaxy/galaxycheck.php");
	}

	elseif ($sub=="planet_types")
	{
		advanced_form("planet_types", $twig);
	}
	elseif ($sub=="sol_types")
	{
		advanced_form("sol_types", $twig);
	}

	else
	{
		$order_array=array();
		$order_array['id']="Objekt-ID";
		$order_array['planet_name']="Objekt-Name";
		$order_array['type_name']="Objekt-Subtyp";
		$order_array['user_nick']="Besitzer-Name";

		echo "<h1>Raumobjekte (Entitäten)</h1>";

		$sa = array();
		$so = array();

    // Create search query if cell id is requested
    if (isset($_GET['cell_id'])) {
      $_GET['sq'] = base64_encode("cell_id:=:".intval($_GET['cell_id']));
    }

		//
		// Details bearbeiten
		//
		if ($sub=="edit")
		{
			require("galaxy/edit.php");
		}

		//
		// Search query and result
		//
		elseif (searchQueryArray($sa,$so))
		{
			$table = "entities e";
			$joins = " INNER JOIN cells c ON c.id=e.cell_id ";
			$selects = "";
			$sql = "";

			if (isset($sa['id']))
			{
				$sql.= " AND e.id ".searchFieldSql($sa['id']);
			}
			if (isset($sa['code']))
			{
				$sql.= " AND (";
				$i=0;
				foreach ($sa['code'][1] as $code)
				{
					if ($i>0)
						$sql.=" OR";
					$sql .= " 
					e.code='".$code."'";
					$i++;
				}
				$sql.= ") ";
			}
			if (isset($sa['cell_id']))
			{
				$sql.= " AND c.id ".searchFieldSql($sa['cell_id']);
			}
			if (isset($sa['cell_cx']))
			{
				$sql.= " AND c.cx ".searchFieldSql($sa['cell_cx']);
			}
			if (isset($sa['cell_cy']))
			{
				$sql.= " AND c.cy ".searchFieldSql($sa['cell_cy']);
			}
			if (isset($sa['cell_c']))
			{
				$val = explode("_",$sa['cell_c'][1]);
				$sql.= " AND c.cx=".$val[0];
				$sql.= " AND c.cy=".$val[1];
			}
			if (isset($sa['cell_sx']))
			{
				$sql.= " AND c.sx ".searchFieldSql($sa['cell_sx']);
			}
			if (isset($sa['cell_sy']))
			{
				$sql.= " AND c.sy ".searchFieldSql($sa['cell_sy']);
			}
			if (isset($sa['cell_s']))
			{
				$val = explode("_",$sa['cell_s'][1]);
				$sql.= " AND c.sx=".$val[0];
				$sql.= " AND c.sy=".$val[1];
			}
			if (isset($sa['cell_pos']))
			{
				$sql.= " AND e.pos ".searchFieldSql($sa['cell_pos']);
			}

			if (isset($sa['name']))
			{
				$joins.= " INNER JOIN planets p ON p.id=e.id ";
				$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";

				$sql.= " AND p.planet_name ".searchFieldSql($sa['name']);
			}
			if (isset($sa['user_id']))
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}
				$sql.= " AND p.planet_user_id ".searchFieldSql($sa['user_id']);
			}
			if (isset($sa['user_main']) && $sa['user_main'][1]<2 )
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}
				$sql.= " AND p.planet_user_main='".intval($sa['user_main'][1])."'";
			}
			if (isset($sa['debris']) && $sa['debris'][1]<2)
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}
				if ($sa['debris'][1]==1)
					$sql.= " AND (p.planet_wf_metal>0 OR p.planet_wf_crystal>0 OR p.planet_wf_plastic>0)";
				else
					$sql.= " AND (p.planet_wf_metal=0 AND p.planet_wf_crystal=0 AND p.planet_wf_plastic=0)";
			}
			if (isset($sa['user_nick']))
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}

				$sql.= " AND users.user_nick ".searchFieldSql($sa['user_nick']);
				$joins.= " INNER JOIN users ON p.planet_user_id=user_id ";
			}
			if (isset($sa['desc']) && $sa['desc'][1]<2)
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}

				if ($sa['desc'][1]==1)
				{
					$sql.= " AND p.planet_desc!='' ";
				}
				else
				{
					$sql.= " AND p.planet_desc='' ";
				}
			}

			// Build ordering
			if (count($so)>1)
			{
				$sql.=" ORDER BY ";
				foreach ($so as $k=> $v)
				{
					if ($k!="limit")
					{
						$sql.=" ".$k." ".($v == "d" ? "DESC" : "ASC")." ";
					}
				}
			}

			// Build limit
			$sql.=" LIMIT ".$so['limit'];

			// Build query
			$sql = "SELECT   
				SQL_CALC_FOUND_ROWS
				e.id,
				e.code, 
				e.pos,
				c.sx,c.sy,c.cx,c.cy ".
				$selects
				."
			FROM ".$table." 
			".$joins." 
			WHERE 1 ".$sql;

			// Execute query
			$res = dbquery($sql);
			$nr = mysql_num_rows($res);

			// Save query
			searchQuerySave($sa,$so);

			// Select total found rows
			$ares = dbquery("SELECT FOUND_ROWS()");
			$aarr = mysql_fetch_row($ares);
			$enr =$aarr[0];

			echo "<h2>Suchresultate</h2>";
			echo "<form acton=\"?page=".$page."\" method=\"post\">";

			echo "<b>Abfrage:</b> ";
			$cnt=0;
			$n = count($sa);
			foreach ($sa as $k => $v)
			{
				echo "<i>$k</i> ".searchFieldOptionsName($v[0])." ";
				if (is_array($v[1]))
				{
					$scnt=0;
					$sn = count($v[1]);
					foreach ($v[1] as $sv)
					{
						echo "'$sv'";
						$scnt++;
						if ($scnt< $sn)
							echo " oder ";
					}
				}
				else
					echo "'".$v[1]."'";
				$cnt++;
				if ($cnt<$n)
					echo ", ";
			}
			echo "<br/><b>Ergebnis:</b> ".$nr." Datens&auml;tze (".$enr." total)<br/>";
			//, Sortierung: ".$order_array[$_POST['order']]."<br/>";
			echo "<b>Anzeigen:</b> <select name=\"search_limit\">";
			for ($x=100;$x<=2000;$x+=100)
			{
				echo "<option value=\"$x\"";
				if ($so['limit']==$x)
					echo " selected=\"selected\"";
				echo ">$x</option>";
			}
			echo "</select> Datensätze sortiert nach <select name=\"search_order\">";
			foreach ($order_array as $k=>$v)
			{
				echo "<option value=\"".$k."\"";
				if (isset($so[$k]))
					echo " selected=\"selected\"";
				echo ">".$v."</option>";
			}
			echo "</select> <input type=\"submit\" value=\"Anzeigen\" name=\"search_resubmit\" /></form><br/>";

			if ($nr > 0)
			{
				if ($nr > 20)
				{
					echo button("Neue Suche","?page=$page&amp;newsearch")."<br/><br/>";
				}

				echo "<table class=\"tb\">";
				echo "<tr>";
				echo "<th style=\"width:40px;\">ID</th>";
				echo "<th style=\"width:90px;\">Koordinaten</th>";
				echo "<th>Entitätstyp</th>";
				echo "<th>Subtyp</th>";
				echo "<th>Name</th>";
				echo "<th>Besitzer</th>";
				echo "<th style=\"width:20px;\">&nbsp;</th>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					$ent = Entity::createFactory($arr['code'],$arr['id']);

					echo "<tr>";
					echo "<td>
						<a href=\"?page=$page&sub=edit&id=".$arr['id']."\">
						".$arr['id']."
						</a></td>";
					echo "<td>
						<a href=\"?page=$page&sub=edit&id=".$arr['id']."\">
						".$arr['sx']."/".$arr['sy']." : ".$arr['cx']."/".$arr['cy']." : ".$arr['pos']."
						</a>  </td>";
					echo "<td style=\"color:".Entity::$entityColors[$arr['code']]."\">";
					echo $ent->entityCodeString();
					echo " ".($ent->ownerMain() ? "(Hauptplanet)": '')."";
					echo "</td>";
					echo "<td>".$ent->type()."</td>";
					echo "<td>".$ent->name()."</td>";
					echo "<td>";
					if ($ent->ownerId()>0)
					{
						echo "<a href=\"?page=user&amp;sub=edit&amp;user_id=".$ent->ownerId()."\" title=\"Spieler bearbeiten\">
							".$ent->owner()."</a>";
					}
					echo "
					</td>";
  				echo "<td>".edit_button("?page=$page&sub=edit&id=".$arr['id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/>".button("Neue Suche","?page=$page&amp;newsearch");
			}
			else
			{
				searchQueryReset();
				echo "Die Suche lieferte keine Resultate!<br/><br/>
				".button("Neue Suche","?page=$page&amp;newsearch");
			}
		}

		//
		// Suchmaske
		//

		else
		{
			echo "<h2>Suchmaske</h2>";
			echo "<form action=\"?page=$page\" method=\"post\" name=\"dbsearch\" autocomplete=\"off\">";
			echo "<table class=\"tb\" style=\"width:auto;margin:0px\">";
			echo "<tr>
				<th>ID:</th>
				<td><input type=\"text\" name=\"search_id\" value=\"\" size=\"5\" maxlength=\"10\" /></td></tr>";
			echo "<tr>
				<th style=\"width:160px\">Name:</th>
				<td>".searchFieldTextOptions('name')." <input type=\"text\" name=\"search_name\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr>
				<th>Koordinaten:</th>
				<td><select name=\"search_cell_s\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				for ($y=1;$y<=$conf['num_of_sectors']['p2'];$y++)
				{
					echo "<option value=\"".$x."_".$y."\">$x / $y</option>";
				}
			}
			echo "</select> : <select name=\"search_cell_c\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				for ($y=1;$y<=$conf['num_of_cells']['p2'];$y++)
				{
					echo "<option value=\"".$x."_".$y."\">$x / $y</option>";
				}
			}
			echo "</select> : <select name=\"search_cell_pos\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			echo "<tr>
				<th style=\"width:160px\">Entitätstyp:<br/><br/>
				<a href=\"javascript:;\" onclick=\"if (this.innerHTML=='Alles auswählen') { this.innerHTML='Auswahl aufheben';for(i=0;i<=7;i++) {document.getElementById('code_'+i).checked=true} } else {for(i=0;i<=7;i++) {document.getElementById('code_'+i).checked=false};this.innerHTML='Alles auswählen';}\">Alles auswählen</a>
				</tH>
				<td>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_0\" value=\"p\" /> Planet<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_1\" value=\"s\" /> Stern<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_2\" value=\"n\" /> Nebel<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_3\" value=\"a\" /> Asteroidenfeld<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_4\" value=\"w\" /> Wurmloch<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_5\" value=\"m\" /> Marktplanet<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_6\" value=\"x\" /> Allianzplanet<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_7\" value=\"e\" /> Leerer Raum
				</td></tr>";
			echo "<tr>
				<th>Besitzer-ID:</th>
				<td><input type=\"text\" name=\"search_user_id\" value=\"\" size=\"5\" maxlength=\"10\" /></td>";
			echo "<tr>
				<th>Besitzer:</th>
				<td>".searchFieldTextOptions('user_nick')." <input type=\"text\" name=\"search_user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\"  />&nbsp;";
				echo "</td></tr>";
			echo "<tr>
				<td style=\"height:2px\" colspan=\"2\"></td></tr>";
			echo "<tr>
				<th>Hauptplanet:</th>
				<td><input type=\"radio\" name=\"search_user_main\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"search_user_main\" value=\"0\" /> Nein &nbsp;
				<input type=\"radio\" name=\"search_user_main\" value=\"1\" /> Ja</td>";
			echo "<tr>
				<th>Tr&uuml;mmerfeld:</th>
				<td><input type=\"radio\" name=\"search_debris\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"search_debris\" value=\"0\" /> Nein &nbsp;
				<input type=\"radio\" name=\"search_debris\" value=\"1\"  /> Ja </td>";
			echo "<tr><th>Bemerkungen:</th>
				<td><input type=\"radio\" name=\"search_desc\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"search_desc\" value=\"0\" /> Keine &nbsp;
				<input type=\"radio\" name=\"search_desc\" value=\"1\"  /> Vorhanden</td></tr>";
			echo "</table>";
			echo "<br/>
			<select name=\"search_limit\">";
			for ($x=100;$x<=2000;$x+=100)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> Datensätze sortiert nach <select name=\"search_order\">";
				foreach ($order_array as $k=>$v)
				{
					echo "<option value=\"".$k."\">".$v."</option>";
				}
				echo "
			</select> <input type=\"submit\" name=\"search_submit\" value=\"Suchen\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(id) FROM planets;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";

			echo "<script type=\"text/javascript\">document.forms['dbsearch'].elements[2].focus();</script>";
		}
	}
?>

