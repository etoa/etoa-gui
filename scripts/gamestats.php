<?PHP

	/**
	* This file generates some stats of the current game
	*
	*/

	define ('GAMESTATS_FILE',"cache/out/gamestats.html");
	define ('GAMESTATS_ROW_LIMIT',15);
	
	/*********************
	* Statistik-Funktion *
	*********************/
	
	function generate_gamestats()
	{
		global $db_table;

		// Renderzeit-Start festlegen
		$render_time = explode(" ",microtime());
		$render_starttime=$render_time[1]+$render_time[0];
	
		$out.="<h2>Spieler-Auslastung</h2>";
		$out.="<img src=\"cache/out/userstats.png\" alt=\"User-Statistik\" />";

		//
		//Universum
		//

		$out.="<h2>Universum</h2>";
		$out.="<table width=\"95%\">";
		$out.="<tr>";


        /****************************************/
        /* Bewohnte Planetentypen               */
        /* Anzahl bewohnter Planeten pro Typ    */
        /****************************************/

            $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
            $out.="<tr><th class=\"tbltitle\" colspan=\"3\">Bewohnte Planetentypen</th></tr>";
            $res=dbquery("
            SELECT
                planet_types.type_name,
                COUNT(planets.planet_type_id) as cnt
            FROM
                ".$db_table['planet_types']."
            INNER JOIN
                (   
                    ".$db_table['planets']."
                INNER JOIN
                    ".$db_table['users']."
                ON
                    planet_user_id=user_id
                    AND user_show_stats=1
                )
            ON
                planet_type_id=type_id
            GROUP BY
                planet_types.type_id
            ORDER BY
                cnt DESC;");
            $rank=1;
            $total=0;
            while ($arr=mysql_fetch_array($res))
            {
                $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['type_name']."</td><td class=\"tbldata\">".$arr['cnt']."</td></tr>";
                $rank++;
                $total+=$arr['cnt'];
            }
            $out.="<tr><td class=\"tbldata\" colspan=\"2\"><b>Total</b></td><td class=\"tbldata\"><b>".$total."</b></td></tr>";
            $out.="</table></td>";



        /****************************************/
        /* Benannte Systeme                     */
        /* Anzahl benannter Systeme pro Typ     */
        /****************************************/

            $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
            $out.="<tr><th class=\"tbltitle\" colspan=\"3\">Benannte Systeme</th></tr>";
            $res=dbquery("
            SELECT
                type_name,
                COUNT(cell_id) as cnt
            FROM
                ".$db_table['space_cells']."
            INNER JOIN
                ".$db_table['sol_types']."
            ON
                cell_solsys_solsys_sol_type=type_id
                AND cell_solsys_name!=''
            GROUP BY
                type_id
            ORDER BY
                cnt DESC;");
            $rank=1;
            $total=0;
            while ($arr=mysql_fetch_array($res))
            {
                $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['type_name']."</td><td class=\"tbldata\">".$arr['cnt']."</td></tr>";
                $rank++;
                $total+=$arr['cnt'];
            }
            $out.="<tr><td class=\"tbldata\" colspan=\"2\"><b>Total</b></td><td class=\"tbldata\"><b>".$total."</b></td></tr>";
            $out.="</table></td>";



        /****************************/
        /* Rassen                   */
        /* Anzahl Rassen pro Typ    */
        /*********************** ****/

            $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
            $out.="<tr><th class=\"tbltitle\" colspan=\"3\">Rassen</th></tr>";
            $res=dbquery("
            SELECT
                races.race_name,
                COUNT(users.user_race_id) as cnt
            FROM
                ".$db_table['users']."
            INNER JOIN
                ".$db_table['races']."
            ON 
                users.user_race_id=races.race_id
                AND users.user_show_stats=1
            GROUP BY
                races.race_id
            ORDER BY
                cnt DESC;");
            $rank=1;
            $total=0;
            while ($arr=mysql_fetch_array($res))
            {
                $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['race_name']."</td><td class=\"tbldata\">".$arr['cnt']."</td></tr>";
                $rank++;
                $total+=$arr['cnt'];
            }
            $out.="<tr><td class=\"tbldata\" colspan=\"2\"><b>Total</b></td><td class=\"tbldata\"><b>".$total."</b></td></tr>";
            $out.="</table></td>";
            
	$out.="</tr>";
	$out.="</table>";
	
	
	

//
//Rohstoffe
//
	$out.="<h2>Rohstoffe</h2>";
	$out.="<table width=\"95%\">";
	$out.="<tr>";
	
        
        /****************************************/
        /* Max Ressourcen auf einem Planeten    */
        /*                                      */
        /****************************************/

            $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
            $out.="<tr><th class=\"tbltitle\" colspan=\"3\">Max Ressourcen auf einem Planeten</th></tr>";
            
            //Anzahl Titan
            $res=dbquery("
            SELECT
                planet_res_metal AS res,
                type_name AS type
            FROM
                ".$db_table['planet_types']."
            INNER JOIN
                (   
                    ".$db_table['planets']."
                INNER JOIN
                    ".$db_table['users']."
                ON
                    planet_user_id=user_id
                    AND user_show_stats=1
                )
            ON
                planet_type_id=type_id
            ORDER BY
                res DESC
            LIMIT 1;
                "); 
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_METAL."</td>
                <td class=\"tbldata\">".nf($arr['res'])."</td>
                <td class=\"tbldata\">".$arr['type']."</td>
            </tr>";
            
            //Anzahl Silizium
            $res=dbquery("
            SELECT
                planet_res_crystal AS res,
                type_name AS type
            FROM
                ".$db_table['planet_types']."
            INNER JOIN
                (   
                    ".$db_table['planets']."
                INNER JOIN
                    ".$db_table['users']."
                ON
                    planet_user_id=user_id
                    AND user_show_stats=1
                )
            ON
                planet_type_id=type_id
            ORDER BY
                res DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_CRYSTAL."</td>
                <td class=\"tbldata\">".nf($arr['res'])."</td>
                <td class=\"tbldata\">".$arr['type']."</td>
            </tr>";
            
            //Anzahl PVC
            $res=dbquery("
            SELECT
                planet_res_plastic AS res,
                type_name AS type
            FROM
                ".$db_table['planet_types']."
            INNER JOIN
                (   
                    ".$db_table['planets']."
                INNER JOIN
                    ".$db_table['users']."
                ON
                    planet_user_id=user_id
                    AND user_show_stats=1
                )
            ON
                planet_type_id=type_id
            ORDER BY
                res DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_PLASTIC."</td>
                <td class=\"tbldata\">".nf($arr['res'])."</td>
                <td class=\"tbldata\">".$arr['type']."</td>
            </tr>";
            
            //Anzahl Tritium
            $res=dbquery("
            SELECT
                planet_res_fuel AS res,
                type_name AS type
            FROM
                ".$db_table['planet_types']."
            INNER JOIN
                (   
                    ".$db_table['planets']."
                INNER JOIN
                    ".$db_table['users']."
                ON
                    planet_user_id=user_id
                    AND user_show_stats=1
                )
            ON
                planet_type_id=type_id
            ORDER BY
                res DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_FUEL."</td>
                <td class=\"tbldata\">".nf($arr['res'])."</td>
                <td class=\"tbldata\">".$arr['type']."</td>
            </tr>";
            
            //Anzahl Nahrung
            $res=dbquery("
            SELECT
                planet_res_food AS res,
                type_name AS type
            FROM
                ".$db_table['planet_types']."
            INNER JOIN
                (   
                    ".$db_table['planets']."
                INNER JOIN
                    ".$db_table['users']."
                ON
                    planet_user_id=user_id
                    AND user_show_stats=1
                )
            ON
                planet_type_id=type_id
            ORDER BY
                res DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_FOOD."</td>
                <td class=\"tbldata\">".nf($arr['res'])."</td>
                <td class=\"tbldata\">".$arr['type']."</td>
            </tr>";
            $out.="</table></td>";



        /****************************************/
        /* Total Ressourcen im Universum        */
        /*                                      */
        /****************************************/

            $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
            $out.="<tr><th class=\"tbltitle\" colspan=\"4\">Total Ressourcen im Universum</th></tr>";
            $out.="<tr><th class=\"tbltitle\">Ressource</th><th class=\"tbltitle\">Total</th><th class=\"tbltitle\">Durchschnitt</th><th class=\"tbltitle\">Planeten</th></tr>";
            
            //Anzahl Titan
            $res=dbquery("
            SELECT
                SUM(planet_res_metal) AS sum,
                AVG(planet_res_metal) AS avg,
                COUNT(planet_id) AS cnt
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                planet_user_id=user_id
                AND user_show_stats=1   
                AND planet_res_metal>0");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_METAL."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
                <td class=\"tbldata\">".nf($arr['avg'])."</td>
                <td class=\"tbldata\">".nf($arr['cnt'])."</td>
            </tr>";
            
            //Anzahl Silizium
            $res=dbquery("
            SELECT
                SUM(planet_res_crystal) AS sum,
                AVG(planet_res_crystal) AS avg,
                COUNT(planet_id) AS cnt
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                planet_user_id=user_id
                AND user_show_stats=1   
                AND planet_res_crystal>0");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_CRYSTAL."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
                <td class=\"tbldata\">".nf($arr['avg'])."</td>
                <td class=\"tbldata\">".nf($arr['cnt'])."</td>
            </tr>";
            
            //Anzahl PVC
            $res=dbquery("
            SELECT
                SUM(planet_res_plastic) AS sum,
                AVG(planet_res_plastic) AS avg,
                COUNT(planet_id) AS cnt
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                planet_user_id=user_id
                AND user_show_stats=1   
                AND planet_res_plastic>0");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_PLASTIC."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
                <td class=\"tbldata\">".nf($arr['avg'])."</td>
                <td class=\"tbldata\">".nf($arr['cnt'])."</td>
            </tr>";
            
            //Anzahl Tritium
            $res=dbquery("
            SELECT
                SUM(planet_res_fuel) AS sum,
                AVG(planet_res_fuel) AS avg,
                COUNT(planet_id) AS cnt
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                planet_user_id=user_id
                AND user_show_stats=1   
                AND planet_res_fuel>0");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_FUEL."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
                <td class=\"tbldata\">".nf($arr['avg'])."</td>
                <td class=\"tbldata\">".nf($arr['cnt'])."</td>
            </tr>";
            
            //Anzahl Nahrung
            $res=dbquery("
            SELECT
                SUM(planet_res_food) AS sum,
                AVG(planet_res_food) AS avg,
                COUNT(planet_id) AS cnt
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                planet_user_id=user_id
                AND user_show_stats=1   
                AND planet_res_food>0");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_FOOD."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
                <td class=\"tbldata\">".nf($arr['avg'])."</td>
                <td class=\"tbldata\">".nf($arr['cnt'])."</td>
            </tr>";
            $out.="</table></td>";



        /****************************************/
        /* Max Ressourcen eines Spielers        */
        /* Total Rohstoffe in einem Account     */
        /****************************************/

            $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
            $out.="<tr><th class=\"tbltitle\" colspan=\"3\">Max Ressourcen eines Spielers</th></tr>";
            
            //Anzahl Titan
            $res=dbquery("
            SELECT
                SUM(planet_res_metal) AS sum
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                user_id=planet_user_id
                AND user_show_stats=1
            GROUP BY
                planet_user_id
            ORDER BY
                sum DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_METAL."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
            </tr>";
            
            //Anzahl Silizium
            $res=dbquery("
            SELECT
                SUM(planet_res_crystal) AS sum
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                user_id=planet_user_id
                AND user_show_stats=1
            GROUP BY
                planet_user_id
            ORDER BY
                sum DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_CRYSTAL."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
            </tr>";
            
            //Anzahl PVC
            $res=dbquery("
            SELECT
                SUM(planet_res_plastic) AS sum
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                user_id=planet_user_id
                AND user_show_stats=1
            GROUP BY
                planet_user_id
            ORDER BY
                sum DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_PLASTIC."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
            </tr>";
            
            //Anzahl Tritium
            $res=dbquery("
            SELECT
                SUM(planet_res_fuel) AS sum
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                user_id=planet_user_id
                AND user_show_stats=1
            GROUP BY
                planet_user_id
            ORDER BY
                sum DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_FUEL."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
            </tr>";
            
            //Anzahl Nahrung
            $res=dbquery("
            SELECT
                SUM(planet_res_food) AS sum
            FROM
                ".$db_table['planets']."
            INNER JOIN
                ".$db_table['users']."
            ON
                user_id=planet_user_id
                AND user_show_stats=1
            GROUP BY
                planet_user_id
            ORDER BY
                sum DESC
            LIMIT 1;
                ");
            $arr=mysql_fetch_array($res);
            $out.="<tr>
                <td class=\"tbldata\">".RES_FOOD."</td>
                <td class=\"tbldata\">".nf($arr['sum'])."</td>
            </tr>";

            $out.="</table></td>";
            
        $out.="</tr>";
        $out.="</table>";





//
// Konstruktionen (von allen spielern)
//

	$out.="<h2>Konstruktionen (Gesamt Anzahl von allen Spielern)</h2>";
	$out.="<table width=\"95%\">";
	$out.="<tr>";



        /***************************************************/
        /* Schiffe						        		   */
        /* Gesamt Anzahl im Universum + Beste Leistung     */
        /***************************************************/
        
        $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
        $out.="<tr><th class=\"tbltitle\" colspan=\"4\">Schiffe ohne Flotten (Beste Leistung, Gesamt)</th></tr>";
        $res=dbquery("
        SELECT
            ships.ship_name,
            SUM(shiplist.shiplist_count) as cnt,
            MAX(shiplist.shiplist_count) as max
        FROM
            ".$db_table['ships']."
        INNER JOIN
            (   
                ".$db_table['shiplist']."
            INNER JOIN
                ".$db_table['users']."
            ON
                shiplist_user_id=user_id
                AND user_show_stats=1
            )
        ON
            shiplist_ship_id=ship_id
            AND ships.special_ship=0            
        GROUP BY
            ships.ship_id
        ORDER BY
            cnt DESC;");
        $rank=1;
        $total=0;
        while ($arr=mysql_fetch_array($res))
        {
            $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['ship_name']."</td><td class=\"tbldata\">".nf($arr['max'])."</td><td class=\"tbldata\">".nf($arr['cnt'])."</td></tr>";
            $rank++;
            $total+=$arr['cnt'];
        }
        $out.="<tr><td class=\"tbldata\" colspan=\"2\"><b>Total</b></td><td class=\"tbldata\">&nbsp;</td><td class=\"tbldata\"><b>".nf($total)."</b></td></tr>";
        $out.="</table></td>";



        /***************************************************/
        /* Verteidigung					        		   */
        /* Gesamt Anzahl im Universum + Beste Leistung     */
        /***************************************************/
        
        $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
        $out.="<tr><th class=\"tbltitle\" colspan=\"4\">Verteidigung</th></tr>";
        $res=dbquery("
        SELECT
            defense.def_name,
            SUM(deflist.deflist_count) as cnt,
            MAX(deflist.deflist_count) as max       
        FROM
            ".$db_table['defense']."
        INNER JOIN
            (   
                ".$db_table['deflist']."
            INNER JOIN
                ".$db_table['users']."
            ON
                deflist_user_id=user_id
                AND user_show_stats=1
            )
        ON
            deflist_def_id=def_id            
        GROUP BY
            defense.def_id
        ORDER BY
            cnt DESC;");
        $rank=1;
        $total=0;
        while ($arr=mysql_fetch_array($res))
        {
            $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['def_name']."</td><td class=\"tbldata\">".nf($arr['max'])."</td><td class=\"tbldata\">".nf($arr['cnt'])."</td></tr>";
            $rank++;
            $total+=$arr['cnt'];
        }
        $out.="<tr><td class=\"tbldata\" colspan=\"2\"><b>Total</b></td><td class=\"tbldata\">&nbsp;</td><td class=\"tbldata\"><b>".nf($total)."</b></td></tr>";
        $out.="</table></td>";



        /****************************************/
        /* Geb�ude						       	*/
        /* Gesamt Anzahl Level im Universum     */
        /****************************************/
        
        $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
        $out.="<tr><th class=\"tbltitle\" colspan=\"3\">Geb&auml;ude</th></tr>";
        $res=dbquery("
        SELECT
            buildings.building_name,
            SUM(buildlist.buildlist_current_level) as cnt
        FROM
            ".$db_table['buildings']."
        INNER JOIN
            (   
                ".$db_table['buildlist']."
            INNER JOIN
                ".$db_table['users']."
            ON
                buildlist_user_id=user_id
                AND user_show_stats=1
            )
        ON
            building_id=buildlist_building_id          
        GROUP BY
            buildings.building_id
        ORDER BY
            cnt DESC
        LIMIT ".GAMESTATS_ROW_LIMIT.";");
        $rank=1;
        $total=0;
        while ($arr=mysql_fetch_array($res))
        {
            $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['building_name']."</td><td class=\"tbldata\">".nf($arr['cnt'])."</td></tr>";
            $rank++;
            $total+=$arr['cnt'];
        }
        $out.="<tr><td class=\"tbldata\" colspan=\"2\"><b>Total</b></td><td class=\"tbldata\"><b>".nf($total)."</b></td></tr>";
        $out.="</table></td>";
    $out.="</tr>";
    $out.="</table>";



	//
	// Konstruktionen (von den besten Spielern)
	//

	$out.="<h2>Konstruktionen (Die beste Leistung eines Einzelnen)</h2>";
	$out.="<table width=\"95%\">";
	$out.="<tr>";


	/************************/
	/* Forschungen			*/
	/*					    */
	/************************/
	$out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
	$out.="<tr><th class=\"tbltitle\" colspan=\"3\">Forschungen</th></tr>";
	$res=dbquery("
	SELECT
	    technologies.tech_name,
	    MAX(techlist.techlist_current_level) as max
	FROM
	    ".$db_table['technologies']."
	INNER JOIN
	    (   
	        ".$db_table['techlist']."
	    INNER JOIN
	        ".$db_table['users']."
	    ON
	        techlist_user_id=user_id
	        AND user_show_stats=1
	    )
	ON
	    tech_id=techlist_tech_id              
	GROUP BY
	    technologies.tech_id
	ORDER BY
	    max DESC;");
	$rank=1;
	$total=0;
	while ($arr=mysql_fetch_array($res))
	{
	    $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['tech_name']."</td><td class=\"tbldata\">".nf($arr['max'])."</td></tr>";
	    $rank++;
	}
	$out.="</table></td>";
	
	
	
	/************************/
	/* Geb�ude			    */
	/*					    */
	/************************/
	
	$out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
	$out.="<tr><th class=\"tbltitle\" colspan=\"3\">Geb&auml;ude</th></tr>";
	$res=dbquery("
	SELECT
	    buildings.building_name,
	    MAX(buildlist.buildlist_current_level) as max
	FROM
	    ".$db_table['buildings']."
	INNER JOIN
	    (   
	        ".$db_table['buildlist']."
	    INNER JOIN
	        ".$db_table['users']."
	    ON
	        buildlist_user_id=user_id
	        AND user_show_stats=1
	    )
	ON
	    building_id=buildlist_building_id          
	GROUP BY
	    buildings.building_id
	ORDER BY
	    max DESC;");
	$rank=1;
	$total=0;
	while ($arr=mysql_fetch_array($res))
	{
	    $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['building_name']."</td><td class=\"tbldata\">".nf($arr['max'])."</td></tr>";
	    $rank++;
	}
	$out.="</table></td>";
	
	
	
	/************************/
	/* Spezialschiffe		*/
	/* Level + EXP			*/
	/************************/  
	
	$out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
	$out.="<tr><th class=\"tbltitle\" colspan=\"4\">Spezialschiffe (Level, EXP)</th></tr>";
	$res=dbquery("
	SELECT
	    ships.ship_name,
	    MAX(shiplist.shiplist_special_ship_level) as level,
	    MAX(shiplist.shiplist_special_ship_exp) as exp
	FROM
	    ".$db_table['ships']."
	INNER JOIN
	    (   
	        ".$db_table['shiplist']."
	    INNER JOIN
	        ".$db_table['users']."
	    ON
	        shiplist_user_id=user_id
	        AND user_show_stats=1
	    )
	ON
	    shiplist_ship_id=ship_id
	    AND ships.special_ship=1
	GROUP BY
	    ships.ship_id
	ORDER BY
	    exp DESC;");
	$rank=1;
	$total=0;
	while ($arr=mysql_fetch_array($res))
	{
	    $out.="<tr><td class=\"tbldata\">".$rank."</td><td class=\"tbldata\">".$arr['ship_name']."</td><td class=\"tbldata\">".nf($arr['level'])."</td><td class=\"tbldata\">".nf($arr['exp'])."</td></tr>";
	    $rank++;
	}
	$out.="</table></td>";
        
		$out.="</tr>";
		$out.="</table>";


		//
		//Sonstiges
		//

		$limit=5;
		$out.="<h2>Sonstiges</h2>";
		$out.="<table width=\"95%\"><tr>";



		/****************/
		/* Design				*/
		/****************/
		
		$rplc=array("css_style/"=>"");
		$out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
		$out.="<tr><th class=\"tbltitle\" colspan=\"4\">Design</th></tr>";
		$res=dbquery("
		SELECT 
		    user_css_style,
		    COUNT(user_id) as cnt 
		FROM 
		    ".$db_table['users']." 
		GROUP BY 
		    user_css_style 
		ORDER BY 
		    cnt DESC 
		LIMIT $limit;");
		$rank=1;
		$total=0;
		$i = array();
		$num=mysql_num_rows($res);
		while ($arr=mysql_fetch_array($res))
		{
		    array_push($i,$arr);
		    $total+=$arr['cnt'];
		}
		foreach ($i as $arr)
		{
		    $out.="<tr><td class=\"tbldata\">".$rank."</td>";
		    if ($arr['user_css_style']!="")
		        $out.="<td class=\"tbldata\">".strtr($arr['user_css_style'],$rplc)."</td>";
		    else
		        $out.="<td class=\"tbldata\"><i>Standard</i></td>";
		    $out.="<td class=\"tbldata\">".nf($arr['cnt'])."</td>";
		    $out.="<td class=\"tbldata\">".round(100/$total*$arr['cnt'],2)."%</td></tr>";
		    $rank++;
		}
		$out.="</table></td>";
		
		
		
		/******************/
		/* Bildpaket			*/
		/******************/
		
		$rplc=array("images/themes/"=>"");
		$out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
		$out.="<tr><th class=\"tbltitle\" colspan=\"4\">Bildpaket</th></tr>";
		$res=dbquery("
		SELECT 
		    user_image_url,
		    COUNT(user_id) as cnt 
		FROM 
		    ".$db_table['users']." 
		GROUP BY 
		    user_image_url 
		ORDER BY 
		    cnt DESC 
		LIMIT $limit;");
		$rank=1;
		$total=0;
		$i = array();
		$num=mysql_num_rows($res);
		while ($arr=mysql_fetch_array($res))
		{
		    array_push($i,$arr);
		    $total+=$arr['cnt'];
		}
		foreach ($i as $arr)
		{
		    $out.="<tr><td class=\"tbldata\">".$rank."</td>";
		    if ($arr['user_image_url']!="")
		        $out.="<td class=\"tbldata\">".strtr($arr['user_image_url'],$rplc)."</td>";
		    else
		        $out.="<td class=\"tbldata\"><i>Standard</i></td>";
		    $out.="<td class=\"tbldata\">".nf($arr['cnt'])."</td>";
		    $out.="<td class=\"tbldata\">".round(100/$total*$arr['cnt'],2)."%</td></tr>";
		    $rank++;
		}
		$out.="</table></td>";



    /**********************/
    /* Bilderweiterung		*/
    /**********************/
    
    $out.="<td style=\"width:33%;vertical-align:top;\"><table width=\"100%\">";
    $out.="<tr><th class=\"tbltitle\" colspan=\"4\">Bild-Erweiterung</th></tr>";
    $res=dbquery("
    SELECT 
        user_image_ext,
        COUNT(user_id) as cnt 
    FROM 
        ".$db_table['users']." 
    GROUP BY 
        user_image_ext 
    ORDER BY 
        cnt DESC 
    LIMIT $limit;");
    $rank=1;
    $total=0;
    $i = array();
    $num=mysql_num_rows($res);
    while ($arr=mysql_fetch_array($res))
    {
        array_push($i,$arr);
        $total+=$arr['cnt'];
    }
    foreach ($i as $arr)
    {
        $out.="<tr><td class=\"tbldata\">".$rank."</td>";
        if ($arr['user_image_ext']!="")
            $out.="<td class=\"tbldata\">".$arr['user_image_ext']."</td>";
        else
            $out.="<td class=\"tbldata\"><i>Standard</i></td>";
        $out.="<td class=\"tbldata\">".nf($arr['cnt'])."</td>";
        $out.="<td class=\"tbldata\">".round(100/$total*$arr['cnt'],2)."%</td></tr>";
        $rank++;
    }
    $out.="</table></td>";
        
		$out.="</tr><tr>";




		// Browser
	$out.="<td style=\"width:33%;vertical-align:top;\" colspan=\"3\"><table width=\"100%\" class=\"tb\">";
	$out.="<th colspan=\"5\">Browser & Betriebssystem</th></tr>";
	$res=dbquery("
	SELECT 
		user_client, 
		COUNT(user_id) as cnt 
	FROM 
		".$db_table['users']." 
	WHERE 
		user_client!='' 
	GROUP BY 
		user_client 
	ORDER BY 
		cnt DESC 
	LIMIT 30;");
	$rank=1;
	$total=0;
	$i = array();
	$num=mysql_num_rows($res);
	while ($arr=mysql_fetch_array($res))
	{
		array_push($i,$arr);
		$total+=$arr['cnt'];
	}
	foreach ($i as $arr)
	{
		$out.="<tr><td>$rank</td>";
		if (stristr($arr['user_client'],"Firefox"))
			$client="Firefox ".substr($arr['user_client'],strpos($arr['user_client'],"Firefox/")+8,7);
		elseif (stristr($arr['user_client'],"MSIE"))
			$client="Internet Explorer ".substr($arr['user_client'],strpos($arr['user_client'],"MSIE")+5,3);
		elseif (stristr($arr['user_client'],"Opera"))
			$client="Opera ".substr($arr['user_client'],strpos($arr['user_client'],"Opera/")+6,4);
		elseif (stristr($arr['user_client'],"Opera"))
			$client="Opera ".substr($arr['user_client'],strpos($arr['user_client'],"Opera/")+6,4);
		elseif (stristr($arr['user_client'],"Safari"))
			$client="Safari ".substr($arr['user_client'],strpos($arr['user_client'],"Safari/")+7);
		elseif (stristr($arr['user_client'],"Mozilla"))
			$client="Mozilla";
		else
			$client="-";

		if (stristr($arr['user_client'],"Windows NT 4.0"))
			$os="Windows NT 4.0";
		elseif (stristr($arr['user_client'],"Windows NT 5.0"))
			$os="Windows 2000";
		elseif (stristr($arr['user_client'],"Windows NT 5.1"))
			$os="Windows XP";
		elseif (stristr($arr['user_client'],"Windows NT 5.2"))
			$os="Windows Server 2003";
		elseif (stristr($arr['user_client'],"Windows NT 6.0"))
			$os="Windows Vista";
		elseif (stristr($arr['user_client'],"Windows 95"))
			$os="Windows 95";
		elseif (stristr($arr['user_client'],"Windows 98"))
			$os="Windows 98";
		elseif (stristr($arr['user_client'],"Windows ME"))
			$os="Windows ME";
		elseif (stristr($arr['user_client'],"Windows CE"))
			$os="Windows CE";
		elseif (stristr($arr['user_client'],"Max OS X"))
			$os="Max OS X";
		elseif (stristr($arr['user_client'],"Macintosh"))
			$os="Mac OS";
		elseif (stristr($arr['user_client'],"Linux"))
			$os="Linux";
		elseif (stristr($arr['user_client'],"SunOS"))
			$os="SunOS";
		else
			$os="-";

		$out.="<td ".tm("User Agent String",$arr['user_client']).">".$client."</td>";
		$out.="<td ".tm("User Agent String",$arr['user_client']).">".$os."</td>";
		$out.="<td>".nf($arr['cnt'])."</td>";
		$out.="<td>".round(100/$total*$arr['cnt'],2)."%</td></tr>";
		$rank++;
	}
	$out.="</table></td>";

	$out.="</tr>";












		$out.="</table>";
		
		$out.= "<br/>Erstellt am ".date("d.m.Y")." um ".date("H:i")." Uhr ";

		// Renderzeit
		$render_time = explode(' ',microtime());
		$rtime = $render_time[1]+$render_time[0]-$render_starttime;
		$out.= " in ".round($rtime,3)." Sekunden";	
		
		return $out;
	}


	// Gamepfad feststellen
	if ($_SERVER['argv'][1]!="")
	{
		$grd = $_SERVER['argv'][1];
	}
	else
	{
		$c=strrpos($_SERVER["SCRIPT_FILENAME"],"scripts/");
		if (stristr($_SERVER["SCRIPT_FILENAME"],"./")&&$c==0)
			$grd = "../";
		elseif ($c==0)
			$grd = ".";
		else
			$grd = substr($_SERVER["SCRIPT_FILENAME"],0,$c-1);
	}

	define("GAME_ROOT_DIR",$grd);

	/*******
	* Main *
	*******/

	// Initialisieren
	if (include(GAME_ROOT_DIR."/functions.php"))
	{
		include(GAME_ROOT_DIR."/conf.inc.php");
		dbconnect();
		if (!defined('CLASS_ROOT'))	
			define('CLASS_ROOT',GAME_ROOT_DIR.'/classes');
		
		$conf = get_all_config();
		include(GAME_ROOT_DIR."/def.inc.php");
		$nohtml=true;

		// Statistiken generieren und speichern
		if ($f=fopen(GAME_ROOT_DIR."/".GAMESTATS_FILE,"w+"))
		{
			$str = generate_gamestats();
			if (!fwrite($f,$str))
			{
				echo "Error! Could not write file!";
			}
			fclose($f);	
		}
		else
		{
			echo "Error! Could not open file!";
		}
		
		// DB schliessen
		dbclose();
	}
	else
	{
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";
	}
?>