<?php

echo "<h1>Tools</h1>";

	//
	// Time Tester
	//
	if ($sub=="timetester")
	{
			
		echo "<a href=\"?page=$page&amp;sub=$sub\">Nochmal</a><br>";
		
		echo "<br>Test welches echo schneller ist, mit \"text\" oder 'text'<br><br>";
		$start1 = microtime();
		for ($i = 0; $i < 10000; $i++) { $test = "Dies ist ein Test $i"; }
		$ende1 = microtime();
		echo "Verbrauchte Zeit mit \" : ".($ende1 - $start1);
		
		$start2 = microtime();
		for ($i = 0; $i < 10000; $i++) { $test = 'Dies ist ein Test'.$i; }
		$ende2 = microtime();
		echo "<br>Verbrauchte Zeit mit ' : ".($ende2 - $start2);
		
		
		echo "<br><br><br>Mysql Test<br>";
		$start3 = microtime();
		for ($i = 0; $i < 1; $i++)
		{
			$res = mysql_query("SELECT planet_name, user_nick FROM ".$db_table['planets'].", ".$db_table['users']." ORDER BY planet_name;");
		}
		$ende3 = microtime();
		
		echo "<br>Verbrauchte Zeit mit radikaler Auslesung (SELECT * FROM): ".($ende3 - $start3);
		$start4 = microtime();
		for ($i = 0; $i < 10; $i++)
		{
			$res = mysql_query("SELECT id FROM ".$db_table['planets'].";");
		}
		$ende4 = microtime();
		echo "<br>Verbrauchte Zeit mit rationioneller Auslesung ( ".$i."x SELECT xy FROM): ".($ende4 - $start4);
	}

	//
	// IP-Resolver
	//
	elseif ($sub=="ipresolver")
	{
		$ip = "";
		$host = "";
		
		if (isset($_POST['resolve']))
		{
			if ($_POST['address']!="")
			{
				$ip = $_POST['address'];
				$host = gethostbyaddr($_POST['address']);
				echo "Die IP <b>".$ip."</b> hat den Hostnamen <b>".$host."</b><br/>";
				
			}
			elseif ($_POST['hostname']!="")
			{
				$ip = gethostbyname($_POST['hostname']);
				$host = $_POST['hostname'];
				echo "Die Host <b>".$host."</b> hat die IP <b>".$ip."</b><br/>";
			}			
		}
		if (isset($_POST['whois']))
		{
			echo "<div style=\"border:1px solid #fff;background:#000;padding:3px;\">";
			$cmd = "whois ".$_POST['hostname'];
			$out = array();
			exec($cmd,$out);
			foreach ($out as $o)
			{
				echo "$o <br/>";
			}
			echo "</div>";
		}		
		echo "<h2>IP-Resolver</h2>";
		echo '<form action="?page='.$page.'&amp;sub='.$sub.'" method="post">';
		echo "IP-Adresse: <input type=\"text\" name=\"address\" value=\"$ip\" /><br/>";
		echo "oder Hostname: <input type=\"text\" name=\"hostname\" value=\"$host\" /><br/><br/>";
		echo "<input type=\"submit\" name=\"resolve\" value=\"Auflösen\" /> &nbsp; ";
		echo "<input type=\"submit\" name=\"whois\" value=\"WHOIS\" /><br/>";		
		echo "</form>";
	}

	//
	// PHP
	//
	elseif ($sub=="php")
	{
		echo "<h2>PHP-Infos</h2>";
		echo '<iframe src="phpinfo.php" style="width:850px;height:650px;" ></iframe>';
	}
	
		
	//
	// Battle-Sim
	//
	elseif ($sub=="battle_simulation")
	{
	echo "<h2>Kampfsimulator</h2>";
	global $db_table;



	if($_POST['submit_simulation']!="" && checker_verify())
	{
        $ships_a = array();
        $special_ships_a = array();
        $ships_d = array();
        $special_ships_d = array();
        $tech_a = array();
        $tech_d = array();
        $def_d = array();

		$simulade_att=0;
		$simulade_def=0;

        //infobox_start("Ausgewählte Schiffe Angreifer",1);
        foreach ($_POST['ship_count_a'] as $id_a=> $count_a)
        {

        	if($count_a>0)
        	{
                $att_res = dbquery("SELECT
                    ship_id,
                    ship_name,
                    ship_structure,
                    ship_shield,
                    ship_weapon,
                    ship_heal,
                    ship_costs_metal,
                    ship_costs_crystal,
                    ship_costs_plastic,
                    ship_costs_fuel,
                    ship_costs_food
                FROM
                	".$db_table['ships']."
                WHERE
                	ship_id='$id_a'");

                $att_arr=mysql_fetch_array($att_res);

				array_push(
                $ships_a,
                array("id"=>$att_arr['ship_id'],
                "cnt"=>$count_a,
                "name"=>$att_arr['ship_name'],
                "structure"=>$att_arr['ship_structure'],
                "shield"=>$att_arr['ship_shield'],
                "weapon"=>$att_arr['ship_weapon'],
                "heal"=>$att_arr['ship_heal'],
                "costs_metal"=>$att_arr['ship_costs_metal'],
                "costs_crystal"=>$att_arr['ship_costs_crystal'],
                "costs_plastic"=>$att_arr['ship_costs_plastic'],
                "costs_fuel"=>$att_arr['ship_costs_fuel'],
                "costs_food"=>$att_arr['ship_costs_food'])
                );

                //echo "<tr><td class=\"tbldata\">".$att_arr['ship_name']."</td><td class=\"tbldata\">$count_a</td></tr>";

                $simulade_att=1;

        	}
        }
        //tableEnd();


        //infobox_start("Ausgewählte Spezialschiffe Angreifer",1);
        foreach ($_POST['special_ship_count_a'] as $special_id_a=> $special_count_a)
        {

        	if($special_count_a>0)
        	{
                $special_att_res = dbquery("SELECT
                    ship_id,
                    ship_name,
                    ship_structure,
                    ship_shield,
                    ship_weapon,
                    ship_costs_metal,
                    ship_costs_crystal,
                    ship_costs_plastic,
                    ship_costs_fuel,
                    ship_costs_food,
                    special_ship_bonus_weapon,
                    special_ship_bonus_structure,
                    special_ship_bonus_shield,
                    special_ship_bonus_heal,
                    special_ship_need_exp,
                    special_ship_exp_factor
                FROM
                	".$db_table['ships']."
                WHERE
                	ship_id='$special_id_a'");

                $special_att_arr=mysql_fetch_array($special_att_res);

				array_push(
                $special_ships_a,
                array("id"=>$special_att_arr['ship_id'],
                "cnt"=>$special_count_a,
                "name"=>$special_att_arr['ship_name'],
                "structure"=>$special_att_arr['ship_structure'],
                "shield"=>$special_att_arr['ship_shield'],
                "weapon"=>$special_att_arr['ship_weapon'],
                "costs_metal"=>$special_att_arr['ship_costs_metal'],
                "costs_crystal"=>$special_att_arr['ship_costs_crystal'],
                "costs_plastic"=>$special_att_arr['ship_costs_plastic'],
                "costs_fuel"=>$special_att_arr['ship_costs_fuel'],
                "costs_food"=>$special_att_arr['ship_costs_food'],
                "bonus_weapon"=>$special_att_arr['special_ship_bonus_weapon'],
                "bonus_structure"=>$special_att_arr['special_ship_bonus_structure'],
                "bonus_shield"=>$special_att_arr['special_ship_bonus_shield'],
                "bonus_heal"=>$special_att_arr['special_ship_bonus_heal'],
                "need_exp"=>$special_att_arr['special_ship_need_exp'],
                "exp_factor"=>$special_att_arr['special_ship_exp_factor'],
                "level"=>$_POST['special_ship_level_a'][$special_id_a])
                );

               	// "bonus_heal"=>$special_att_arr['special_ship_bonus_heal']
                //echo "<tr><td class=\"tbldata\">".$special_att_arr['ship_name']."</td><td class=\"tbldata\">$special_count_a</td><td class=\"tbldata\">".$_POST['special_ship_level_a'][$special_id_a]."</td></tr>";

				$simulade_att=1;
        	}
        }
        //tableEnd();



        //infobox_start("Ausgewählte Tech Angreifer",1);
        foreach ($_POST['tech_a'] as $id_tech_a=> $level_tech_a)
        {

        	if($level_tech_a>0)
        	{
                $att_tech_res = dbquery("SELECT
                    tech_id,
                    tech_name
                FROM
                	".$db_table['technologies']."
                WHERE
                	tech_id='$id_tech_a'");

                $att_tech_arr=mysql_fetch_array($att_tech_res);

				array_push(
                $tech_a,
                array("id"=>$att_tech_arr['tech_id'],
                "name"=>$att_tech_arr['tech_name'],
                "level"=>$level_tech_a)
                );

                //echo "<tr><td class=\"tbldata\">".$att_tech_arr['tech_name']."</td><td class=\"tbldata\">$level_tech_a</td></tr>";

        	}

        }
        //tableEnd();

        //infobox_start("Ausgewählte Schiffe Verteidiger",1);
        foreach ($_POST['ship_count_d'] as $id_d=> $count_d)
        {

        	if($count_d>0)
        	{
                $def_res = dbquery("SELECT
                    ship_id,
                    ship_name,
                    ship_structure,
                    ship_shield,
                    ship_weapon,
                    ship_costs_metal,
                    ship_costs_crystal,
                    ship_costs_plastic,
                    ship_costs_fuel,
                    ship_costs_food
                FROM
                	".$db_table['ships']."
                WHERE
                	ship_id='$id_d'");

                $def_arr=mysql_fetch_array($def_res);

				array_push(
                $ships_d,
                array("id"=>$def_arr['ship_id'],
                "cnt"=>$count_d,
                "name"=>$def_arr['ship_name'],
                "structure"=>$def_arr['ship_structure'],
                "shield"=>$def_arr['ship_shield'],
                "weapon"=>$def_arr['ship_weapon'],
                "costs_metal"=>$def_arr['ship_costs_metal'],
                "costs_crystal"=>$def_arr['ship_costs_crystal'],
                "costs_plastic"=>$def_arr['ship_costs_plastic'],
                "costs_fuel"=>$def_arr['ship_costs_fuel'],
                "costs_food"=>$def_arr['ship_costs_food'])
                );

                //echo "<tr><td class=\"tbldata\">".$def_arr['ship_name']."</td><td class=\"tbldata\">$count_d</td></tr>";
				$simulade_def=1;
        	}

        }
        //tableEnd();



        //infobox_start("Ausgewählte Spezialschiffe Verteidiger",1);
        foreach ($_POST['special_ship_count_d'] as $special_id_d=> $special_count_d)
        {

        	if($special_count_d>0)
        	{
                $special_def_res = dbquery("SELECT
                    ship_id,
                    ship_name,
                    ship_structure,
                    ship_shield,
                    ship_weapon,
                    ship_costs_metal,
                    ship_costs_crystal,
                    ship_costs_plastic,
                    ship_costs_fuel,
                    ship_costs_food,
                    special_ship_bonus_weapon,
                    special_ship_bonus_structure,
                    special_ship_bonus_shield,
                    special_ship_bonus_heal,
                    special_ship_need_exp,
                    special_ship_exp_factor
                FROM
                	".$db_table['ships']."
                WHERE
                	ship_id='$special_id_d'");

                $special_def_arr=mysql_fetch_array($special_def_res);

				array_push(
                $special_ships_d,
                array("id"=>$special_def_arr['ship_id'],
                "cnt"=>$special_count_d,
                "name"=>$special_def_arr['ship_name'],
                "structure"=>$special_def_arr['ship_structure'],
                "shield"=>$special_def_arr['ship_shield'],
                "weapon"=>$special_def_arr['ship_weapon'],
                "costs_metal"=>$special_def_arr['ship_costs_metal'],
                "costs_crystal"=>$special_def_arr['ship_costs_crystal'],
                "costs_plastic"=>$special_def_arr['ship_costs_plastic'],
                "costs_fuel"=>$special_def_arr['ship_costs_fuel'],
                "costs_food"=>$special_def_arr['ship_costs_food'],
                "bonus_weapon"=>$special_def_arr['ship_bonus_weapon'],
                "bonus_structure"=>$special_def_arr['special_ship_bonus_structure'],
                "bonus_shield"=>$special_def_arr['special_ship_bonus_shield'],
                "bonus_heal"=>$special_def_arr['special_ship_bonus_heal'],
                "need_exp"=>$special_def_arr['special_ship_need_exp'],
                "exp_factor"=>$special_def_arr['special_ship_exp_factor'],
                "level"=>$_POST['special_ship_level_d'][$special_id_d])
                );

               	// "bonus_heal"=>$special_def_arr['special_ship_bonus_heal']
                //echo "<tr><td class=\"tbldata\">".$special_def_arr['ship_name']."</td><td class=\"tbldata\">$special_count_d</td><td class=\"tbldata\">".$_POST['special_ship_level_d'][$special_id_d]."</td></tr>";

                $simulade_def=1;


        	}
        }
        //tableEnd();


        //infobox_start("Ausgewählte Verteidigung Verteidiger",1);
        foreach ($_POST['def_count_d'] as $def_id_d=> $def_count_d)
        {

        	if($def_count_d>0)
        	{
                $def_res = dbquery("
                SELECT
                    def_id,
                    def_name,
                    def_structure,
                    def_shield,
                    def_weapon,
                    def_costs_metal,
                    def_costs_crystal,
                    def_costs_plastic,
                    def_costs_fuel,
                    def_costs_food
                FROM
                	".$db_table['defense']."
                WHERE
                	def_id='$def_id_d'");

                $def_arr=mysql_fetch_array($def_res);

				array_push(
                $def_d,
                array("id"=>$def_arr['def_id'],
                "cnt"=>$def_count_d,
                "name"=>$def_arr['def_name'],
                "structure"=>$def_arr['def_structure'],
                "shield"=>$def_arr['def_shield'],
                "weapon"=>$def_arr['def_weapon'],
                "costs_metal"=>$def_arr['def_costs_metal'],
                "costs_crystal"=>$def_arr['def_costs_crystal'],
                "costs_plastic"=>$def_arr['def_costs_plastic'],
                "costs_fuel"=>$def_arr['def_costs_fuel'],
                "costs_food"=>$def_arr['def_costs_food'])
                );

                //echo "<tr><td class=\"tbldata\">".$def_arr['def_name']."</td><td class=\"tbldata\">$def_count_d</td></tr>";

        	}

        }
        //tableEnd();


        //infobox_start("Ausgewählte Tech Verteidiger",1);
        foreach ($_POST['tech_d'] as $id_tech_d=> $level_tech_d)
        {

        	if($level_tech_d>0)
        	{
                $def_tech_res = dbquery("
                SELECT
                    tech_id,
                    tech_name
                FROM
                	".$db_table['technologies']."
                WHERE
                	tech_id='$id_tech_d'");

                $def_tech_arr=mysql_fetch_array($def_tech_res);

				array_push(
                $tech_d,
                array("id"=>$def_tech_arr['tech_id'],
                "name"=>$def_tech_arr['tech_name'],
                "level"=>$level_tech_d)
                );

                //echo "<tr><td class=\"tbldata\">".$def_tech_arr['tech_name']."</td><td class=\"tbldata\">$level_tech_d</td></tr>";

				$simulade_def=1;
        	}

        }
        //tableEnd();


		if($simulade_att==1 && $simulade_def==1)
		{
            include ("../inc/battle.inc.php");
            battle_simulation($ships_a,$special_ships_a,$ships_d,$special_ships_d,$tech_a,$tech_d,$def_d);
        }
        else
        {
        	echo "Du musst eine Angreifer- UND Verteidigerflotte angeben<br>";
        }


	}
	else
	{

        // Schiffe anzeigen
        $res = dbquery("
        SELECT
            ship_id,
            ship_name
        FROM
        	".$db_table['ships']."
        WHERE
        	ship_buildable=1
        	AND special_ship=0
        ORDER BY
        	ship_name;");
        if (mysql_num_rows($res)>0)
        {
            echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
            checker_init();
            infobox_start("Schiffswahl",1,0);
            echo "
            <tr>
                <td class=\"tbltitle\" colspan=\"2\">Typ</td>
                <td class=\"tbltitle\" valign=\"top\">Angreifer</td>
                <td class=\"tbltitle\" valign=\"top\">Verteidiger</td>
            </tr>\n";

            $tabulator=1;
            while ($arr = mysql_fetch_array($res))
            {

                echo "
                <tr>
                    <td class=\"tbldata\" width=\"30\"><a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['ship_id']."\"><img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" width=\"30\" height=\"30\" alt=\"Ship\" border=\"0\"/></a></td>
                    <td class=\"tbldata\">".$arr['ship_name']."</td>
                    <td class=\"tbldata\" width=\"100\" title=\"Anzahl Angreifer\"><input type=\"text\" name=\"ship_count_a[".$arr['ship_id']."]\" size=\"10\" maxlength=\"20\" value=\"0\"  title=\"Anzahl Schiffe eingeben, die den Angreifer unterstützen\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\"></td>
                    <td class=\"tbldata\" width=\"100\" title=\"Anzahl Angreifer\"><input type=\"text\" name=\"ship_count_d[".$arr['ship_id']."]\" size=\"10\" maxlength=\"20\" value=\"0\"  title=\"Anzahl Schiffe eingeben, die den Verteidiger unterstützen\" tabindex=\"".($tabulator+1)."\" onKeyPress=\"return nurZahlen(event)\"></td>
                </tr>\n";
                $tabulator+=2;
            }

            tableEnd();
        }



        // Spezial Schiffe anzeigen
        $ssres = dbquery("
        SELECT
            ship_id,
            ship_name,
            ship_max_count
        FROM
        	".$db_table['ships']."
        WHERE
        	ship_buildable=1
        	AND special_ship=1
        ORDER BY
        	ship_name;");
        if (mysql_num_rows($ssres)>0)
        {
            infobox_start("Spezial-Schiffe Auswahl",1,0);
            echo "
            <tr>
                <td class=\"tbltitle\" valign=\"top\">Typ</td>
                <td class=\"tbltitle\" valign=\"top\">Max.</td>
                <td class=\"tbltitle\" valign=\"top\">Angreifer</td>
                <td class=\"tbltitle\" valign=\"top\">LvL</td>
                <td class=\"tbltitle\" valign=\"top\">Verteidiger</td>
                <td class=\"tbltitle\" valign=\"top\">LvL</td>
            </tr>\n";

            $tabulator=1;
            while ($ssarr = mysql_fetch_array($ssres))
            {
				if($ssarr['ship_max_count']==0)
					$max_count = "-";
				else
					$max_count = $ssarr['ship_max_count'];

                echo "
                <tr>
                    <td class=\"tbldata\">".$ssarr['ship_name']."</td>
                    <td class=\"tbldata\">$max_count</td>
                    <td class=\"tbldata\" width=\"100\" title=\"Anzahl Angreifer\"><input type=\"text\" name=\"special_ship_count_a[".$ssarr['ship_id']."]\" size=\"10\" maxlength=\"20\" value=\"0\"  title=\"Anzahl Spezialschiffe eingeben, die den Angreifer unterstützen\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\"></td>
                    <td class=\"tbldata\" title=\"Level des Schiffes\"><input type=\"text\" name=\"special_ship_level_a[".$ssarr['ship_id']."]\" size=\"3\" maxlength=\"3\" value=\"0\"  title=\"Level des Schiffes\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\"></td>

                    <td class=\"tbldata\" width=\"100\" title=\"Anzahl Verteidiger\"><input type=\"text\" name=\"special_ship_count_d[".$ssarr['ship_id']."]\" size=\"10\" maxlength=\"20\" value=\"0\"  title=\"Anzahl Spezialschiffe eingeben, die den Verteidiger unterstützen\" tabindex=\"".($tabulator+1)."\" onKeyPress=\"return nurZahlen(event)\"></td>
                    <td class=\"tbldata\" title=\"Level des Schiffes\"><input type=\"text\" name=\"special_ship_level_d[".$ssarr['ship_id']."]\" size=\"3\" maxlength=\"3\" value=\"0\"  title=\"Level des Schiffes\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\"></td>
                </tr>\n";
                $tabulator+=2;
            }

            tableEnd();
        }

        // Verteidigung anzeigen
        $dres = dbquery("
        SELECT
        	*
        FROM
        	".$db_table['defense']."
        WHERE
        	def_buildable=1
        ORDER BY
        	def_name;");
        if (mysql_num_rows($dres)>0)
        {
            echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
            checker_init();
            infobox_start("Verteidigung",1,0);
            echo "
            <tr>
                <td class=\"tbltitle\" colspan=\"2\">Typ</td>
                <td class=\"tbltitle\" valign=\"top\">Verteidiger</td>
            </tr>\n";

            $tabulator=1;
            while ($darr = mysql_fetch_array($dres))
            {

                echo "
                <tr>
                    <td class=\"tbldata\" width=\"30\"><a href=\"?page=help&amp;site=defense&amp;id=".$darr['def_id']."\"><img src=\"".IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$darr['def_id']."_small.".IMAGE_EXT."\" width=\"30\" height=\"30\" alt=\"def\" border=\"0\"/></a></td>
                    <td class=\"tbldata\">".$darr['def_name']."</td>
                    <td class=\"tbldata\" width=\"100\" title=\"Anzahl Angreifer\"><input type=\"text\" name=\"def_count_d[".$darr['def_id']."]\" size=\"10\" maxlength=\"20\" value=\"0\"  title=\"Anzahl Schiffe eingeben, die den Verteidiger unterstützen\" tabindex=\"".($tabulator+1)."\" onKeyPress=\"return nurZahlen(event)\"></td>
                </tr>\n";
                $tabulator++;
            }

            tableEnd();
        }


        // Technologien anzeigen
        $tres = dbquery("
        SELECT
        	*
        FROM
        	".$db_table['technologies']."
        WHERE
        	tech_show=1
        	AND (tech_id=".STRUCTURE_TECH_ID." OR tech_id=".SHIELD_TECH_ID." OR tech_id=".WEAPON_TECH_ID." OR tech_id=".REGENA_TECH_ID.")");
        if (mysql_num_rows($tres)>0)
        {

            infobox_start("Technologien",1,0);
            echo "
            <tr>
                <td class=\"tbltitle\" valign=\"top\">Forschung</td>
                <td class=\"tbltitle\" valign=\"top\">Angreifer</td>
                <td class=\"tbltitle\" valign=\"top\">Verteidiger</td>
            </tr>\n";

            while ($techarr = mysql_fetch_array($tres))
            {
            	echo "
            	<tr>
            		<td class=\"tbldata\">".$techarr['tech_name']."</td>
            		<td class=\"tbldata\" width=\"100\" title=\"Gib hier das Level der Forschung vom Angreifer ein\"><input type=\"text\" name=\"tech_a[".$techarr['tech_id']."]\" size=\"10\" maxlength=\"20\" value=\"0\" title=\"\" onKeyPress=\"return nurZahlen(event)\"></td>
            		<td class=\"tbldata\" width=\"100\" title=\"Gib hier das Level der Forschung vom Angreifer ein\"><input type=\"text\" name=\"tech_d[".$techarr['tech_id']."]\" size=\"10\" maxlength=\"20\" value=\"0\" title=\"\" onKeyPress=\"return nurZahlen(event)\"></td>
            	</tr>";

            }
            tableEnd();
            echo "<input type=\"submit\" name=\"submit_simulation\" value=\"Simulieren\" title=\"Kampf simulieren\"/> &nbsp;";
            echo "</form>";
        }
	}

	}	
		
	else
	{
		echo "Wähle ein Tool aus dem Menü!";
	}

?>