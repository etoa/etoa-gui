<?PHP

	echo "<h2>Raketen</h2>";

//
//Detail
//

	if (isset($_GET['id']))
	{
		$mid = intval($_GET['id']);

		$res = dbquery("SELECT * FROM missiles WHERE missile_id='".$mid."';");
		if ($arr = mysql_fetch_array($res))
		{
			HelpUtil::breadCrumbs(array("Raketen","missiles"),array(text2html($arr['missile_name']),$arr['missile_id']),1);
			echo "<select onchange=\"document.location='?$link&amp;site=missiles&id='+this.options[this.selectedIndex].value\">";
			$bres=dbquery("SELECT
				missile_id,
				missile_name
			FROM
				missiles
			WHERE
				missile_show=1
			ORDER BY
				missile_name;");
			while ($barr=mysql_fetch_array($bres))
			{
				echo "<option value=\"".$barr['missile_id']."\"";
				if ($barr['missile_id']==$mid) echo " selected=\"selected\"";
				echo ">".$barr['missile_name']."</option>";
			}
			echo "</select><br/><br/>";

		 	tableStart($arr['missile_name']);

	   	echo "<tr><td width=\"220\" class=\"tbltitle\"><img src=\"".IMAGE_PATH."/missiles/missile".$arr['missile_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" alt=\"Raketen\" /></td>";
	   	echo "<td class=\"tbldata\">".text2html($arr['missile_ldesc'])."</td></tr>";

	   	echo "<tr><td colspan=\"2\" style=\"height:30px;\"></td></tr>";

	   	echo "<tr><th class=\"tbltitle\" colspan=\"2\" style=\"text-align:center\">Kosten und technische Daten</th></tr>";

	    echo "<tr><td class=\"tbltitle\">".RES_ICON_METAL."".RES_METAL."</td><td class=\"tbldata\">".nf($arr['missile_costs_metal'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td><td class=\"tbldata\">".nf($arr['missile_costs_crystal'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</td><td class=\"tbldata\">".nf($arr['missile_costs_plastic'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</td><td class=\"tbldata\">".nf($arr['missile_costs_fuel'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</td><td class=\"tbldata\">".nf($arr['missile_costs_food'])."</td></tr>";

	    echo "<tr><td class=\"tbltitle\">Geschwindigkeit</td><td class=\"tbldata\">".nf($arr['missile_speed'])." AE/h</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Reichweite</td><td class=\"tbldata\">".nf($arr['missile_range'])." AE</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Schaden</td>";
	    	if (($arr['missile_damage'])==0)
	    	{
	    		echo "<td class=\"tbldata\">Nein</td></tr>";
	    	}
	    	else
	    	{
	    		echo "<td class=\"tbldata\">".nf($arr['missile_damage'])."</td></tr>";
	    	}
	    echo "<tr><td class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\">";
	    	if (($arr['missile_def'])>0)
	    	{
	    		echo "Ja, ".nf($arr['missile_def'])."";
	    			if (($arr['missile_def'])==1)
	    			{
	    				echo " Sprengkopf</td></tr>";
	    			}
	    			else
	    			{
	    				echo " Sprengk&ouml;pfe</td></tr>";
	    			}
	  		}
	  		else
	  		{
	  			echo "Nein</td></tr>";
	  		}
	  	echo "<tr><td class=\"tbltitle\">EMP-Schaden</td><td class=\"tbldata\">";
	  		if (($arr['missile_deactivate'])>0)
	  		{
	  			echo "Ja, deaktiviert Geb&auml;ude f&uuml;r ".tf($arr['missile_deactivate'])."</td></tr>";
	  		}
	  		else
	  		{
	  			echo "Kein EMP-Angriff m&ouml;glich</td></tr>";
	  		}
	    tableEnd();
		}
		else
		  echo "Raketendaten nicht gefunden!";
		echo "<input type=\"button\" value=\"Raketen&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=defense'\" /> &nbsp; ";
		if ($_SESSION['lastpage']=="missiles")
			echo "<input type=\"button\" value=\"Zur&uuml;ck zum Silo\" onclick=\"document.location='?page=missiles'\" /> &nbsp; ";
	}

//
//Übersicht
//

	else
	{
		HelpUtil::breadCrumbs(array("Raketen","missiles"));

		if (isset($_GET['order']) && ctype_alpha($_GET['order']))
		{
			$order="missile_".$_GET['order'];
			if ($_SESSION['help']['orderfield']==$_GET['order'])
			{
				if ($_SESSION['help']['ordersort']=="DESC")
					$sort="ASC";
				else
					$sort="DESC";
			}
			else
			{
				if ($_GET['order']=="name")
					$sort="ASC";
				else
					$sort="DESC";
			}
			$_SESSION['help']['orderfield']=$_GET['order'];
			$_SESSION['help']['ordersort']=$sort;
		}
		else
		{
			$order="missile_name";
			$sort="ASC";
		}

		$res = dbquery("SELECT * FROM missiles WHERE missile_show=1 ORDER BY missile_name;");
		if (mysql_num_rows($res)>0)
		{
			tableStart("&Uuml;bersicht");

			echo "<tr></th><th class=\"tbltitle\" colspan=\"2\">Name</th>";
			echo "<th class=\"tbltitle\">Kurzbeschreibung</th>";
			echo "<th class=\"tbltitle\">Geschwindigkeit</th>";
			echo "<th class=\"tbltitle\">Reichweite</th>";
			echo "<th class=\"tbltitle\">Schaden</th>";
			echo "<th class=\"tbltitle\">Verteidigung</th>";
			while ($arr = mysql_fetch_array($res))
			{
				echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000\">
							<a href=\"?$link&amp;site=$site&amp;id=".$arr['missile_id']."\">
							<img src=\"".IMAGE_PATH."/missiles/missile".$arr['missile_id']."_small.".IMAGE_EXT."\" alt=\"Raketen\" width=\"40\" height=\"40\" border=\"0\"/></a></td>";
				echo "<td class=\"tbltitle\">".$arr['missile_name']."</td>";
				echo "<td class=\"tbldata\">".$arr['missile_sdesc']."</td>";
				echo "<td class=\"tbldata\">".nf($arr['missile_speed'])." AE/h</td>";
				echo "<td class=\"tbldata\">".nf($arr['missile_range'])." AE</td>";
				echo "<td class=\"tbldata\">";
					if (($arr['missile_def'])>0)
					{
						echo "".nf($arr['missile_def'])."</td>";
					}
					else
					{
						echo "Kein</td>";
					}
				echo "<td class=\"tbldata\">".nf($arr['missile_damage'])."</td></tr>";
			}
			tableEnd();
		}
		else
			echo "<i>Keine Daten vorhanden!</i>";

	}
?>
