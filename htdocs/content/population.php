<?PHP
    //////////////////////////////////////////////////
    //             ____    __           ______      //
    //            /\  _`\ /\ \__       /\  _  \     //
    //            \ \ \L\_\ \ ,_\   ___\ \ \L\ \    //
    //             \ \  _\L\ \ \/  / __`\ \  __ \   //
    //              \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \  //
    //               \ \____/\ \__\ \____/\ \_\ \_\ //
    //                \/___/  \/__/\/___/  \/_/\/_/ //
    //                                              //
    //////////////////////////////////////////////////
    // The Andromeda-Project-Browsergame            //
    // Ein Massive-Multiplayer-Online-Spiel         //
    // Programmiert von Nicolas Perrenoud           //
    // als Maturaarbeit '04 am Gymnasium Oberaargau //
    // www.etoa.ch | mail@etoa.ch                   //
    //////////////////////////////////////////////////
    //
    //
    
    /**
    * Shows information about the planetar population
    *
    * @author MrCage <mrcage@etoa.ch>
    * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
    */

    // BEGIN SKRIPT //

    if ($cp)
    {

        echo '<h1>Bev&ouml;lkerungs&uuml;bersicht des Planeten '.$cp->name.'</h1>';
        echo '<div id="population_info"></div>'; // Nur zu testzwecken
        echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);

        $res = dbquery("
        SELECT
            buildings.building_store_factor,
            buildings.building_name,
            buildings.building_people_place,
            buildlist.buildlist_current_level
        FROM
            buildlist
        INNER JOIN
            buildings
        ON
            buildlist.buildlist_building_id=buildings.building_id
        AND buildlist.buildlist_entity_id=".$cp->id."
        AND buildings.building_people_place>0
        AND buildlist.buildlist_current_level>0;");
        if (mysql_num_rows($res)>0)
        {
            //
            // Wohnfläche
            //
            tableStart("Wohnfl&auml;che",400);
            echo '<tr>
            <th style="width:150px">Grundwohnfl&auml;che</th>
            <td>'.nf($cfg->p1('user_start_people')).'</td>
            </tr>';
            $pcnt=$cfg->p1('user_start_people');
            while ($arr=mysql_fetch_array($res))
            {
                $place = round($arr['building_people_place'] * pow($arr['building_store_factor'],$arr['buildlist_current_level']-1));
                echo '<tr><th>'.$arr['building_name'].'</th>
                <td>'.nf($place).'</td></tr>';
                $pcnt+=$place;
            }
            echo '<tr><th>TOTAL</b></th><td><b>'.nf($pcnt).'</b></td></tr>';
            tableEnd();

        //
        // Arbeiter zuteilen
        //
            if (isset($_POST['submit_people_work']) && checker_verify())
            {
                //zählt gesperrte Arbeiter auf dem aktuellen Planet
                $check_res = dbquery("
                SELECT
                    SUM(buildlist_people_working)
                FROM
                    buildlist
                WHERE
                    buildlist_entity_id=".$cp->id."
                AND buildlist_people_working_status='1';");

                //Check workers for gen
                $check_res_gen=dbquery("
                SELECT
                    buildlist_gen_people_working
                FROM
                    buildlist
                WHERE
                    buildlist_entity_id=".$cp->id."
                AND buildlist_people_working_status='1';");

                $working = 0;
                $check_arr = mysql_fetch_array($check_res);
                $check_arr_gen = mysql_fetch_array($check_res_gen);
                // Frei = total auf Planet - gesperrt auf Planet
                $free_people=floor($cp->people)-$check_arr[0]-$check_arr_gen[0];

                foreach ($_POST['people_work'] as $id=>$num)
                {
                    $working+=nf_back($num);
                }

                //add ppl from genlab
                $working += nf_back($_POST['gen']);
                
                $available = min($free_people,$working);

                foreach ($_POST['people_work'] as $id=>$num)
                {
                    $num = nf_back($num);
                    if ($available>0)
                        $work = min($num,$available);
                    else
                        $work = 0;
                    $available-=$num;
                    dbquery("
                    UPDATE
                        buildlist
                    SET
                        buildlist_people_working='".$work."'
                    WHERE
                        buildlist_building_id='".intval($id)."'
                    AND buildlist_entity_id=".$cp->id);
                }

                if(!$gen_research) {
                    $num = nf_back($_POST['gen']);
                    if ($available>0)
                        $work = min($num,$available);
                    else
                        $work = 0;
                    $available-=$num;

                    dbquery("
                    UPDATE
                        buildlist
                    SET
                        buildlist_gen_people_working = $work
                    WHERE
                        buildlist_user_id =".$cu->id."
                        AND buildlist_entity_id =".$cp->id()."
                        AND buildlist_building_id =".TECH_BUILDING_ID);
                }

            }

            //überprüft tätigkeit des Schiffswerftes
            $sql = "
            SELECT
                COUNT(queue_id)
            FROM
                ship_queue
            WHERE
                queue_entity_id='".$cp->id."'
            AND queue_user_id='".$cu->id."'
            AND queue_starttime>'0'
            AND queue_endtime>'0';";
            $tres = dbquery($sql);
            $tarr=mysql_fetch_row($tres);
            $w[SHIP_BUILDING_ID]=$tarr[0];

            //überprüft tätigkeit der waffenfabrik
            $sql = "
            SELECT
                COUNT(queue_id)
            FROM
                def_queue
            WHERE
                queue_entity_id='".$cp->id."'
            AND queue_user_id='".$cu->id."'
            AND queue_starttime>'0'
            AND queue_endtime>'0';";
            $tres = dbquery($sql);
            $tarr=mysql_fetch_row($tres);
            $w[DEF_BUILDING_ID]=$tarr[0];

            //überprüft tätigkeit des forschungslabors
            $sql = "
            SELECT
                COUNT(techlist_id)
            FROM
            techlist
            WHERE
                techlist_entity_id='".$cp->id."'
            AND techlist_user_id='".$cu->id."'
            AND techlist_build_type>'2'
            AND techlist_tech_id <>".GEN_TECH_ID;
            $tres = dbquery($sql);
            $tarr=mysql_fetch_row($tres);
            $w[TECH_BUILDING_ID]=$tarr[0];

            //überprüft tätigkeit des bauhofes
            $sql = "
            SELECT
                COUNT(buildlist_id)
            FROM
                buildlist
            WHERE
                buildlist_entity_id='".$cp->id."'
            AND buildlist_user_id='".$cu->id."'
            AND buildlist_build_start_time>'0'
            AND buildlist_build_end_time>'0';";
            $tres = dbquery($sql);
            $tarr=mysql_fetch_row($tres);
            $w[BUILD_BUILDING_ID]=$tarr[0];

            // Alle Arbeiter freistellen (solange sie nicht noch an einer Arbeit sind)
            if (isset($_POST['submit_people_free']) && checker_verify())
            {
                foreach ($w as $id=>$v)
                {
                    if ($v==0)
                    {
                        dbquery("
                        UPDATE
                            buildlist
                        SET
                            buildlist_people_working='0',
                            buildlist_gen_people_working ='0'
                        WHERE
                            buildlist_building_id='".$id."'
                        AND buildlist_user_id='".$cu->id."'
                        AND buildlist_entity_id='".$cp->id."'");
                    }
                }
            }
            echo '<form action="?page='.$page.'" method="post">';
            checker_init();
            tableStart("Arbeiter zuteilen");
            echo '<tr><th>Geb&auml;ude</th><th>Arbeiter</th><th>Zus&auml;tzliche Nahrung</th></tr>';

            // Gebäudede mit Arbeitsplätzen auswählen
            $sp_res = dbquery("
            SELECT
                buildlist.buildlist_people_working,
                buildings.building_name,
                buildings.building_people_place,
                buildings.building_id
            FROM
                buildlist,
                buildings
            WHERE
                buildlist.buildlist_building_id=buildings.building_id
            AND buildings.building_workplace='1'
            AND buildlist.buildlist_entity_id='".$cp->id."'
            ORDER BY
                buildings.building_id;");
            $work_available=false;
            if (mysql_num_rows($sp_res)>0)
            {
                $work_available=true;
                while ($sp_arr=mysql_fetch_array($sp_res))
                {
                    echo '<tr><td style="width:150px">';
                    if (BUILD_BUILDING_ID==$sp_arr['building_id'])
                        echo 'Bauhof';
                    else
                        echo $sp_arr['building_name'];
                    echo '</td><td>';

                    if ($w[$sp_arr['building_id']]>0)
                    {
                        echo $sp_arr['buildlist_people_working'];

                        //Sperrt arbeiter
                        dbquery("
                        UPDATE
                            buildlist
                        SET
                            buildlist_people_working_status='1'
                        WHERE
                            buildlist_building_id='".$sp_arr['building_id']."'
                            AND buildlist_user_id='".$cu->id."'
                            AND buildlist_entity_id='".$cp->id."'");
                    }
                    else
                    {   
                        
                        echo '<input type="text" id="'.$sp_arr['building_id'].'" name="people_work['.$sp_arr['building_id'].']" value="'.$sp_arr['buildlist_people_working'].'" size="8" maxlength="20" onKeyUp="FormatNumber(this.id,this.value, '.$cp->people.', \'\', \'\');"/>';

                        //onKeyPress=\"return nurZahlen(event)\"
                        //FormatNumber
                        //xajax_formatNumbers(this.id,this.value,1,".$cp->people.");

                        //Entsperrt arbeiter
                        dbquery("
                        UPDATE
                            buildlist
                        SET
                            buildlist_people_working_status='0'
                        WHERE
                            buildlist_building_id='".$sp_arr['building_id']."'
                            AND buildlist_user_id='".$cu->id."'
                            AND buildlist_entity_id='".$cp->id."'");
                    }
                    echo '</td><td>'.(nf($sp_arr['buildlist_people_working']*$cfg->get('people_food_require'))).' t</td></tr>';
                }
            }

            //Spezialfall Gentech
            
            $requirements_passed = true;
            $rres = dbquery("
            SELECT 
                * 
            FROM 
                tech_requirements where obj_id=".GEN_TECH_ID);
            
            $bl = new BuildList($cp->id(),$cu->id);
            $tl = new TechList($cu->id);

            while ($rarr = mysql_fetch_array($rres)) {
                if ($rarr['req_tech_id']>0) {
                    if (($rarr['req_level']) > ($tl->getLevel($rarr['req_tech_id']))) {
                        $requirements_passed = false;
                    }
                }
                if ($rarr['req_building_id']>0) {

                    if (($rarr['req_level']) > ($bl->getLevel($rarr['req_building_id']))) {
                        $requirements_passed = false;
                    }
                }
            }



            if ($requirements_passed) {
                echo'<tr><td>Genlabor</td>';    
 
                $rres = dbquery("
                SELECT 
                    buildlist_gen_people_working 
                FROM 
                    buildlist 
                WHERE  
                    buildlist.buildlist_user_id =".$cu->id."
                    AND buildlist.buildlist_entity_id =".$cp->id()."
                    AND buildlist.buildlist_building_id =".TECH_BUILDING_ID);

                $gen_workers = mysql_result($rres,0);
                               
                $rres = dbquery("
                SELECT 
                    * 
                FROM 
                    techlist 
                WHERE  
                    techlist.techlist_user_id =".$cu->id."
                    AND techlist.techlist_entity_id =".$cp->id()."
                    AND techlist.techlist_tech_id = ".GEN_TECH_ID."
                    AND techlist.techlist_build_type = 3");
                if(mysql_num_rows($rres) >0) {
                    $gen_research = true;
                    echo '<td>'.$gen_workers.'</td>';
                }
                else
                {    
                    $gen_research = false;
                    echo '<td><input type="text" id="gen" name="gen" value="'.$gen_workers.'" size="8" maxlength="20" onKeyUp="FormatNumber(this.id,this.value, '.$cp->people.', \'\', \'\');"/></td>';    
                }
                echo '</td><td>'.nf($gen_workers*$cfg->get('people_food_require')).' t</td></tr>';

            }


            if ($work_available)
            {
                echo '<tr><td>&nbsp;</td>
                <td><input type="submit" name="submit_people_work" value="Speichern" /></td>
                <td><input type="submit" name="submit_people_free" value="Alle Arbeiter freigeben" /></td></tr>';
            }
            echo '<tr><td colspan="3">
            Wenn einem Geb&auml;ude Arbeiter zugeteilt werden, wird es entsprechend schneller gebaut. Die Arbeiter ben&ouml;tigen jedoch Nahrung. ';
            echo 'Die Zuteilung der Arbeiter kann erst ge&auml;ndert werden, wenn entsprechende Bauauftr&auml;ge abgeschlossen sind. ';
            echo 'Die gesamte Nahrung f&uuml;r die Arbeiter wird beim Start eines Bauvorgangs sofort vom Planetenkonto abgezogen.
            </td></tr>';
            tableEnd();
            echo '</form>';


        // Zählt alle arbeiter die eingetragen snid (besetzt oder nicht) für die anszeige!
            $bres = dbquery("
            SELECT
                SUM(buildlist_people_working)
            FROM
                buildlist
            WHERE
                buildlist_entity_id=".$cp->id.";");
            $barr = mysql_fetch_array($bres);
            $people_working = $barr[0]+$gen_workers;

        // Infodaten
            $people_free = floor($cp->people)-$people_working;
            $people_div = $cp->people/2 * ($cfg->get('people_multiply') + $cp->typePopulation + $cu->race->population + $cp->starPopulation -3);
            if($people_div<=3) $people_div=3;
            tableStart("Daten",500);
            echo '<tr><th style="width:300px">Bev&ouml;lkerung total</th><td>'.nf(floor($cp->people)).'</td></tr>';
            echo '<tr><th>Arbeiter</th><td>'.nf($people_working).'</td></tr>';
            echo '<tr><th>Freie Leute</th><td>'.nf($people_free).'</td></tr>';
            echo '<tr><th>Zeitreduktion pro Arbeiter und Auftrag</th><td>'.tf($cfg->get('people_work_done')).'</td></tr>';
            echo '<tr><th>Nahrung pro Arbeiter und Auftrag</th><td>'.nf($cfg->get('people_food_require')).' t</td></tr>';
            echo '<tr><th>Grundwachstumsrate</th><td>'.get_percent_string($cfg->get('people_multiply'))."</td></tr>";
            echo '<tr><th>Wachstumsbonus '.$cp->typeName.'</th><td>'.get_percent_string($cp->typePopulation,1)."</td></tr>";
            echo '<tr><th>Wachstumsbonus '.$cu->race->name.'</th><td>'.get_percent_string($cu->race->population,1)."</td></tr>";
            echo '<tr><th>Wachstumsbonus '.$cp->starTypeName.'</th><td>'.get_percent_string($cp->starPopulation,1).'</td></tr>';
            echo '<tr><th>Wachstumsbonus '.$cu->specialist->name.'</th><td>'.get_percent_string($cu->specialist->population,1).'</td></tr>';
            echo '<tr><th>Wachstumsbonus total</th><td>'.get_percent_string(array($cp->typePopulation,$cu->race->population,$cp->starPopulation,$cu->specialist->population),1).'</td></tr>';
            echo '<tr><th>Bev&ouml;lkerungszuwachs pro Stunde</th><td>'.nf($people_div).'</td></tr>';
            tableEnd();
        }
        else
            error_msg("Es sind noch keine Geb&auml;ude gebaut, in denen deine Bev&ouml;lkerung wohnen oder arbeiten kann!");
    }
?>