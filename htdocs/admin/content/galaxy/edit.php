<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\AsteroidsRepository;
use EtoA\Universe\EmptySpaceRepository;
use EtoA\Universe\EntityType;
use EtoA\Universe\NebulaRepository;
use EtoA\Universe\StarRepository;
use EtoA\Universe\WormholeRepository;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

/** @var StarRepository */
$starRepo = $app['etoa.universe.star.repository'];

/** @var AsteroidsRepository */
$asteroidsRepo = $app['etoa.universe.asteroids.repository'];

/** @var NebulaRepository */
$nebulaRepo = $app['etoa.universe.nebula.repository'];

/** @var WormholeRepository */
$wormholeRepo = $app['etoa.universe.wormhole.repository'];

/** @var EmptySpaceRepository */
$emptySpaceRepo = $app['etoa.universe.empty_space.repository'];

$id = $_GET['id'];
if (isset($id) && $id > 0)
{
    $eres = dbQuerySave("
        SELECT
            e.id,
    c.id as cid,
            code,
            pos,
            sx,
            sy,
            cx,
            cy
        FROM
            entities e
        INNER JOIN
            cells c
        ON
            e.cell_id=c.id
            AND e.id=?
        LIMIT 1;", [
            $id
        ]);
        if (mysql_num_rows($eres)>0)
        {
            $earr = mysql_fetch_array($eres);

            echo "<h2>Raumobjekt ".$earr['sx']."/".$earr['sy']." : ".$earr['cx']."/".$earr['cy']." : ".$earr['pos']." bearbeiten</h2>";
            if ($id>1)
                echo button("&lt;&lt; Vorheriges Objekt","?page=$page&amp;sub=$sub&id=".($id-1)."");
            echo " &nbsp; Objekt ".$earr['id']." &nbsp; ";
            echo button("Nächstes Objekt &gt;&gt;","?page=$page&amp;sub=$sub&id=".($id+1)."")."<br/><br/>
            ".button("Alle Objekte dieser Zelle/dieses Systems anzeigen","?page=$page".searchQueryUrl("cell_s:=:".$earr['sx']."_".$earr['sy'].";cell_c:=:".$earr['cx']."_".$earr['cy']))."
    ".button("System dieses Objekts auf der Karte anzeigen","?page=$page&amp;sub=map&amp;cell=".$earr['cid']);
            echo "<br/><br/>";

            if ($earr['code']==EntityType::PLANET)
            {
                if (isset($_POST['save']))
                {
                    $pl = Planet::getById($id);

                    if (isset($_POST['planet_user_main']))
                    {
                        if ($pl->setMain())
                            success_msg("Hauptplanet gesetzt; ursprüngliche Hautpplanet-Zuordnung entfernt!");
                    }
                    else
                    {
                        if ($pl->isMain)
                        {
                            $pl->unsetMain();
                            success_msg("Hauptplanet-Zuordnung entfernt. Denke daran, einen neuen Hautplanet festzulegen!");
                        }
                    }

                    $addsql = "";
                    if (isset($_POST['rst_user_changed']))
                    {
                        $addsql.= ",planet_user_changed=0";
                    }
                    if ($pl->typeId != $_POST['planet_type_id'])
                    {
                        $addsql.=",planet_image='".intval($_POST['planet_type_id'])."_1'";
                    }
                    else
                        $addsql.=",planet_image='".mysql_real_escape_string($_POST['planet_image'])."'";

                    //Daten Speichern
                    dbquery("
                    UPDATE
                        planets
                    SET
            planet_name='".mysql_real_escape_string($_POST['planet_name'])."',
            planet_type_id=".intval($_POST['planet_type_id']).",
            planet_fields=".intval($_POST['planet_fields']).",
            planet_fields_extra=".intval($_POST['planet_fields_extra']).",
            planet_temp_from=".intval($_POST['planet_temp_from']).",
            planet_temp_to=".intval($_POST['planet_temp_to']).",
            planet_res_metal='".intval($_POST['planet_res_metal'])."',
            planet_res_crystal='".intval($_POST['planet_res_crystal'])."',
            planet_res_plastic='".intval($_POST['planet_res_plastic'])."',
            planet_res_fuel='".intval($_POST['planet_res_fuel'])."',
            planet_res_food='".intval($_POST['planet_res_food'])."',
            planet_res_metal=planet_res_metal+'".intval($_POST['planet_res_metal_add'])."',
            planet_res_crystal=planet_res_crystal+'".intval($_POST['planet_res_crystal_add'])."',
            planet_res_plastic=planet_res_plastic+'".intval($_POST['planet_res_plastic_add'])."',
            planet_res_fuel=planet_res_fuel+'".intval($_POST['planet_res_fuel_add'])."',
            planet_res_food=planet_res_food+'".intval($_POST['planet_res_food_add'])."',
            planet_wf_metal='".intval($_POST['planet_wf_metal'])."',
            planet_wf_crystal='".intval($_POST['planet_wf_crystal'])."',
            planet_wf_plastic='".intval($_POST['planet_wf_plastic'])."',
            planet_people='".intval($_POST['planet_people'])."',
            planet_people=planet_people+'".intval($_POST['planet_people_add'])."',
            planet_desc='".mysql_real_escape_string($_POST['planet_desc'])."'
            $addsql
                    WHERE
                        id='".$id."';");
                    if (mysql_affected_rows()>0)
                    {
                        success_msg("Änderungen übernommen");
                    }
                }
                if (isset($_POST['calcres'])) {
                    BackendMessage::updatePlanet($id);
                    sleep(2);
                    success_msg("Resourcen neu berechnet");
                }
                else if(count($_POST)>0 && !isset($_POST['save']))
                {
                    //Wenn der Besitzer wechseln soll
                    if($_POST['planet_user_id']!=$_POST['planet_user_id_old'])
                    {
                        //Planet dem neuen User übergeben (Schiffe und Verteidigung werden vom Planeten gelöscht!)
                        $pl = Planet::getById($id);
                        $pl->chown($_POST['planet_user_id']);

                        if ($_POST['planet_user_id']==0)
                        {
                            $pl->reset();
                        }

                        //Log Schreiben
                        Log::add(Log::F_GALAXY,Log::INFO,$cu->nick." wechselt den Besitzer vom Planeten: [page galaxy sub=edit id=".$id."][B]".$id."[/B][/page]\nAlter Besitzer: [page user sub=edit user_id=".$_POST['planet_user_id_old']."][B]".$_POST['planet_user_id_old']."[/B][/page]\nNeuer Besitzer: [page user sub=edit user_id=".$_POST['planet_user_id']."][B]".$_POST['planet_user_id']."[/B][/page]");

                        success_msg("Der Planet wurde dem User mit der ID: [b]".$_POST['planet_user_id']."[/b] &uuml;bergeben!");
                    }
                    else
                    {
                        error_msg("Es wurde kein neuer Besitzer gew&auml;hlt!");
                    }
                }

                $res = dbquery("
                SELECT
                    *
                FROM
                    planets
                WHERE
                    id=".$id."
                LIMIT 1;");
                $arr = mysql_fetch_array($res);

                echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
                tableStart("<span style=\"color:".Entity::$entityColors[$earr['code']]."\">Planet</span>","auto");


                echo "<tr><th>Name</t>
                <td><input type=\"text\" name=\"planet_name\" value=\"".$arr['planet_name']."\" size=\"20\" maxlength=\"250\" /></td>";
                echo "<th>Typ</th>
                <td>
                <select name=\"planet_type_id\">";
                /** @var \EtoA\Universe\PlanetTypeRepository $planetTypeRepository */
                $planetTypeRepository = $app['etoa.universe.planet_type.repository'];
                $planetTypeNames = $planetTypeRepository->getPlanetTypeNames(true);
                $selectedPlanetTypeName = null;
                foreach ($planetTypeNames as $planetTypeId => $planetTypeName){
                    echo "<option value=\"".$planetTypeId."\"";
                    if ($arr['planet_type_id']==$planetTypeId)
                    {
                        echo " selected=\"selected\"";
                        $selectedPlanetTypeName = $planetTypeName;
                    }
                    echo ">".$planetTypeName."</option>\n";
                }
                echo "</select></td></tr>";

                echo "<tr><td style=\"height:2px;\" colspan=\"4\"></td></tr>";

                //Listet alle User der Spiels auf
                $users = get_user_names();
                echo "<tr><th>Besitzer</th><td colspan=\"3\"><select name=\"planet_user_id\">";
                echo "<option value=\"0\">(niemand)</option>";
                foreach ($users as $uid=>$udata)
                {
                    echo "<option value=\"$uid\"";
                    if ($arr['planet_user_id']==$uid)
                        {echo " selected=\"selected\"";$planet_user_id=$uid;}
                    echo ">".$udata['nick']."</option>";
                }
                echo "</select> ";
                if ($arr['planet_user_id']>0 && $users[$planet_user_id]['alliance_id']>0)
                {
                    $ally = new Alliance($users[$planet_user_id]['alliance_id']);
                    echo $ally." &nbsp; ";
                    unset($ally);
                }
                echo "<input type=\"hidden\" name=\"planet_user_id_old\" value=\"".$arr['planet_user_id']."\">";
                echo "<input tabindex=\"29\" type=\"button\" name=\"change_owner\" value=\"Planet &uuml;bergeben\" class=\"button\" onclick=\"if( confirm('Dieser Planet soll einem neuen Besitzer geh&ouml;ren. Alle Schiffs- und Verteidigungsdaten vom alten Besitzer werden komplett gel&ouml;scht.')) document.getElementById('editform').submit()\"/>&nbsp;";
                echo "</td></tr>";

                echo "<tr>
                <th>Hauptplanet</th>
                <td>";
                if ($arr['planet_user_id']>0)
                {
                    echo "<input type=\"checkbox\" name=\"planet_user_main\" ".($arr['planet_user_main']==1 ? " checked=\"checked\"" : "")." value=\"1\"/> Ist Hauptplanet";
                }
                else
                    echo "-";
                echo "</td>
                <th>Letzer Besitzerwechsel</th>
                <td>
                ".($arr['planet_user_changed']>0 ? df($arr['planet_user_changed'])." <input type=\"checkbox\" name=\"rst_user_changed\" value=\"1\" /> Reset" : '-')."
                </td>
                </tr>";

                echo "<tr><td style=\"height:2px;\" colspan=\"4\"></td></tr>";

                echo "<tr><th>Felder / Extra-Felder</th>
                <td><input type=\"text\" name=\"planet_fields\" value=\"".$arr['planet_fields']."\" size=\"10\" maxlength=\"250\" />
                <input type=\"text\" name=\"planet_fields_extra\" value=\"".$arr['planet_fields_extra']."\" size=\"10\" maxlength=\"250\" /></td>";
                echo "<th>Felder benutzt</th>
                <td>".nf($arr['planet_fields_used'])."</td></tr>";

                echo "<tr><th>Temperatur</th>
                <td>
                    <input type=\"text\" name=\"planet_temp_from\" value=\"".$arr['planet_temp_from']."\" size=\"4\" maxlength=\"5\" />
                    bis <input type=\"text\" name=\"planet_temp_to\" value=\"".$arr['planet_temp_to']."\" size=\"4\" maxlength=\"5\" /> &deg;C
                </td>";
                $imPath = IMAGE_PATH."/planets/planet";
                $imPathPost = "_small.".IMAGE_EXT;
                echo "<th>Bild</th>
                <td>
                <img id=\"pimg\" src=\"".$imPath.$arr['planet_image'].$imPathPost."\" style=\"float:left;\" />
                <select name=\"planet_image\" onchange=\"document.getElementById('pimg').src='$imPath'+this.value+'$imPathPost'\">";
                echo "<option value=\"\">Undefiniert</option>";

                for ($x = 1; $x <= $config->getInt('num_planet_images'); $x++)
                {
                    echo "<option value=\"".$arr['planet_type_id']."_".$x."\"";
                    if ($arr['planet_image']==$arr['planet_type_id']."_".$x)
                        echo " selected=\"selected\"";
                    echo ">".$selectedPlanetTypeName." $x</option>\n";
                }
                echo "</select>

                </td>";

                echo "</tr>";

                echo "<td style=\"height:2px;\" colspan=\"4\"></td></tr>";

                echo "<tr><th class=\"resmetalcolor\">Titan</th>
                <td><input type=\"text\" name=\"planet_res_metal\" value=\"".intval($arr['planet_res_metal'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"planet_res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th class=\"rescrystalcolor\">Silizium</th>
                <td><input type=\"text\" name=\"planet_res_crystal\" value=\"".intval($arr['planet_res_crystal'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"planet_res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "<tr><th class=\"resplasticcolor\">PVC</th>
                <td><input type=\"text\" name=\"planet_res_plastic\" value=\"".intval($arr['planet_res_plastic'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"planet_res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th class=\"resfuelcolor\">Tritium</th>
                <td><input type=\"text\" name=\"planet_res_fuel\" value=\"".intval($arr['planet_res_fuel'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"planet_res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "<tr><th class=\"resfoodcolor\">Nahrung</th>
                <td><input type=\"text\" name=\"planet_res_food\" value=\"".intval($arr['planet_res_food'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"planet_res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th class=\"respeoplecolor\">Bevölkerung</th>
                <td><input type=\"text\" name=\"planet_people\" value=\"".intval($arr['planet_people'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"planet_people_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "<td style=\"height:2px;\" colspan=\"4\"></td></tr>";

                echo "<tr><th>Produktion ".RES_METAL."</th>
                <td>".nf($arr['planet_prod_metal'])."</td>";
                echo "<th>Speicher ".RES_METAL.":</th>
                <td>".nf($arr['planet_store_metal'])."</td></tr>";

                echo "<tr><th>Produktion ".RES_CRYSTAL."</th>
                <td>".nf($arr['planet_prod_crystal'])."</td>";
                echo "<th>Speicher ".RES_CRYSTAL.":</th>
                <td>".nf($arr['planet_store_crystal'])."</td></tr>";

                echo "<tr><th>Produktion ".RES_PLASTIC."</th>
                <td>".nf($arr['planet_prod_plastic'])."</td>";
                echo "<th>Speicher ".RES_PLASTIC.":</th>
                <td>".nf($arr['planet_store_plastic'])."</td></tr>";

                echo "<tr><th>Produktion ".RES_FUEL."</th>
                <td>".nf($arr['planet_prod_fuel'])."</td>";
                echo "<th>Speicher ".RES_FUEL.":</th>
                <td>".nf($arr['planet_store_fuel'])."</td></tr>";

                echo "<tr><th>Produktion ".RES_FOOD."</th>
                <td>".nf($arr['planet_prod_food'])."</td>";
                echo "<th>Speicher ".RES_FOOD.":</th>
                <td>".nf($arr['planet_store_food'])."</td></tr>";

                echo "<tr><th>Verbrauch Energie:</th>
                <td>".nf($arr['planet_use_power'])."</td>";
                echo "<th>Produktion Energie:</th>
                <td>".nf($arr['planet_prod_power'])."</td></tr>";

                echo "<tr><th>Wohnraum</th>
                <td>".nf($arr['planet_people_place'])."</td>";
                echo "<th>Bevölkerungswachstum</th>
                <td>".nf($arr['planet_prod_people'])."</td></tr>";

                echo "<td style=\"height:2px;\" colspan=\"4\"></td></tr>";

                echo "<tr><th>Tr&uuml;mmerfeld Titan</th>
                <td><input type=\"text\" name=\"planet_wf_metal\" value=\"".$arr['planet_wf_metal']."\" size=\"20\" maxlength=\"250\" /></td>";
                echo "<th>Tr&uuml;mmerfeld Silizium</th>
                <td><input type=\"text\" name=\"planet_wf_crystal\" value=\"".$arr['planet_wf_crystal']."\" size=\"20\" maxlength=\"250\" /></td></tr>";

                echo "<tr><th>Tr&uuml;mmerfeld PVC</th>
                <td><input type=\"text\" name=\"planet_wf_plastic\" value=\"".$arr['planet_wf_plastic']."\" size=\"20\" maxlength=\"250\" /></td>";
                echo "<th>Updated</th>
                <td>".date("d.m.Y H:i",$arr['planet_last_updated'])."</th></tr>";


                echo "<tr><th>Beschreibung</td>
                <td colspan=\"3\"><textarea name=\"planet_desc\" rows=\"2\" cols=\"50\" >".stripslashes($arr['planet_desc'])."</textarea></td></tr>";
                echo "</table>";
                echo "<br/>";
                echo "<p>";
                echo "<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
                echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page&amp;newsearch'\" value=\"Neue Suche\" /> ";
                echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
                echo "</p><hr/><p>";
                echo "<input type=\"submit\" name=\"calcres\" value=\"Neu berechnen\" class=\"button\" />&nbsp;";
                echo "<input type=\"button\" value=\"Gebäude\" onclick=\"document.location='?page=buildings&action=search&query=".searchQuery(array("entity_id"=>$arr['id']))."'\" /> &nbsp;";
                echo "</p>";
                echo "</form>";
            }
            elseif ($earr['code']==EntityType::STAR)
            {
                if (isset($_POST['save']))
                {
                    if ($starRepo->update($id, (int) $_POST['type_id'], $_POST['name']))
                    {
                        success_msg("Änderungen übernommen");
                    }
                }

                $star = $starRepo->find($id);

                echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
                tableStart("<span style=\"color:".Entity::$entityColors[$earr['code']]."\">Stern</span>","auto");
                echo "<tr><th>Name</th>
                <td><input type=\"text\" name=\"name\" value=\"".$star['name']."\" size=\"20\" maxlength=\"250\" /></td>";
                echo "<th>Typ</th>
                <td>
                <img src=\"".IMAGE_PATH."/stars/star".$star['type_id']."_small.".IMAGE_EXT."\" style=\"float:left;\" />
                <select name=\"type_id\">";
                /** @var \EtoA\Universe\SolarTypeRepository $solarTypeRepository */
                $solarTypeRepository = $app['etoa.universe.solar_type.repository'];
                $solarTypeNames = $solarTypeRepository->getSolarTypeNames(true);
                foreach ($solarTypeNames as $solarTypeId => $solarTypeName) {
                    echo "<option value=\"".$solarTypeId."\"";
                    if ($star['type_id']==$solarTypeId) {
                        echo " selected=\"selected\"";
                    }
                    echo ">".$solarTypeName."</option>\n";
                }
                echo "</select></td></tr>";
                echo "</table>";
                echo "<br/>
                            <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
                echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
                echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
                echo "</form>";
            }
            elseif ($earr['code']==EntityType::ASTEROIDS)
            {
                if (isset($_POST['save']))
                {
                    //Daten Speichern
                    $affected = $asteroidsRepo->update(
                        $id,
                        intval($_POST['res_metal']),
                        intval($_POST['res_crystal']),
                        intval($_POST['res_plastic']),
                        intval($_POST['res_fuel']),
                        intval($_POST['res_food']),
                        intval($_POST['res_power'])
                    );
                    $affectedAdd = $asteroidsRepo->addResources(
                        $id,
                        intval($_POST['res_metal_add']),
                        intval($_POST['res_crystal_add']),
                        intval($_POST['res_plastic_add']),
                        intval($_POST['res_fuel_add']),
                        intval($_POST['res_food_add']),
                        intval($_POST['res_power_add'])
                    );
                    if ($affected || $affectedAdd)
                    {
                        success_msg("Änderungen übernommen");
                    }
                }

                $asteroid = $asteroidsRepo->find($id);

                echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
                tableStart("<span style=\"color:".Entity::$entityColors[$earr['code']]."\">Asteroidenfeld</span>","auto");

                echo "<tr><th>".RES_METAL."</th>
                <td><input type=\"text\" name=\"res_metal\" value=\"".intval($asteroid['res_metal'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th>".RES_CRYSTAL."</th>
                <td><input type=\"text\" name=\"res_crystal\" value=\"".intval($asteroid['res_crystal'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "<tr><th>".RES_PLASTIC."</th>
                <td><input type=\"text\" name=\"res_plastic\" value=\"".intval($asteroid['res_plastic'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th>".RES_FUEL."</th>
                <td><input type=\"text\" name=\"res_fuel\" value=\"".intval($asteroid['res_fuel'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "<tr><th>".RES_FOOD."</th>
                <td><input type=\"text\" name=\"res_food\" value=\"".intval($asteroid['res_food'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th>".RES_POWER."</th>
                <td><input type=\"text\" name=\"res_power\" value=\"".intval($asteroid['res_power'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_power_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "</table>";
                echo "<br/>
                            <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
                echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
                echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
                echo "</form>";
            }
            elseif ($earr['code']==EntityType::NEBULA)
            {
                if (isset($_POST['save']))
                {
                    //Daten Speichern
                    $affected = $nebulaRepo->update(
                        $id,
                        intval($_POST['res_metal']),
                        intval($_POST['res_crystal']),
                        intval($_POST['res_plastic']),
                        intval($_POST['res_fuel']),
                        intval($_POST['res_food']),
                        intval($_POST['res_power'])
                    );
                    $affectedAdd = $nebulaRepo->addResources(
                        $id,
                        intval($_POST['res_metal_add']),
                        intval($_POST['res_crystal_add']),
                        intval($_POST['res_plastic_add']),
                        intval($_POST['res_fuel_add']),
                        intval($_POST['res_food_add']),
                        intval($_POST['res_power_add'])
                    );
                    if ($affected || $affectedAdd)
                    {
                        success_msg("Änderungen übernommen");
                    }
                }

                $nebula = $nebulaRepo->find($id);

                echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
                tableStart("<span style=\"color:".Entity::$entityColors[$earr['code']]."\">Interstellarer Nebel</span>","auto");

                echo "<tr><th>".RES_METAL."</th>
                <td><input type=\"text\" name=\"res_metal\" value=\"".intval($nebula['res_metal'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th>".RES_CRYSTAL."</th>
                <td><input type=\"text\" name=\"res_crystal\" value=\"".intval($nebula['res_crystal'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "<tr><th>".RES_PLASTIC."</th>
                <td><input type=\"text\" name=\"res_plastic\" value=\"".intval($nebula['res_plastic'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th>".RES_FUEL."</th>
                <td><input type=\"text\" name=\"res_fuel\" value=\"".intval($nebula['res_fuel'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "<tr><th>".RES_FOOD."</th>
                <td><input type=\"text\" name=\"res_food\" value=\"".intval($nebula['res_food'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
                echo "<th>".RES_POWER."</th>
                <td><input type=\"text\" name=\"res_power\" value=\"".intval($nebula['res_power'])."\" size=\"12\" maxlength=\"20\" /><br/>
                +/-: <input type=\"text\" name=\"res_power_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

                echo "</table>";
                echo "<br/>
                            <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
                echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
                echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
                echo "</form>";
            }
            elseif ($earr['code']==EntityType::WORMHOLE)
            {
                $wormhole = $wormholeRepo->find($id);

                //Daten Speichern
                if (isset($_POST['save']))
                {
                    $persistent = $_POST['wormhole_persistent'] == 1;

                    $wormholeRepo->setPersistent($id, $persistent);
                    $wormholeRepo->updateTarget((int) $wormhole['target_id'], $persistent);

                    success_msg("Änderungen übernommen");
                }

                echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
                tableStart("<span style=\"color:".Entity::$entityColors[$earr['code']]."\">Wurmloch</span>","auto");
                echo "<tr><th>Entstanden</th><td>".df($wormhole['changed'])."</td><tr/>";
                echo "<tr><th>Ziel</th>
                <td>";
                $ent = Entity::createFactoryById($wormhole['target_id']);
                echo "<a href=\"?page=$page&amp;sub=$sub&amp;id=".$ent->id()."\">".$ent."</a>";
                echo "</td></tr>";
                echo "<tr><th>Persistent</th><td>";
                echo "<input type=\"radio\" name=\"wormhole_persistent\" id=\"wormhole_persistent_0\" value=\"0\" ".($wormhole['persistent'] == 0 ? " checked=\"checked\"" : "")."> <label for=\"wormhole_persistent_0\">Nein</label> ";
                echo "<input type=\"radio\" name=\"wormhole_persistent\" id=\"wormhole_persistent_1\" value=\"1\" ".($wormhole['persistent'] == 1 ? " checked=\"checked\"" : "")."> <label for=\"wormhole_persistent_1\">Ja</label> ";
                echo "</td><tr/>";
                echo "</table>";
                echo "<br/>
                            <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
                echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
                echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
                echo "</form>";
            }
            elseif ($earr['code']==EntityType::EMPTY_SPACE)
            {
                $space = $emptySpaceRepo->find($id);

                echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
                tableStart("<span style=\"color:".Entity::$entityColors[$earr['code']]."\">Leerer Raum</span>","auto");
                echo "<tr><th>Zuletzt besucht</th>
                <td>";
                if ($space['lastvisited']>0)
                    df($space['lastvisited']);
                else
                    echo "Nie";
                echo "</td></tr>";
                echo "</table>";
                echo "<br/>
                            <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
                echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
                echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
                echo "</form>";
            }
            else
            {
                error_msg("Für diesen Entitätstyp (".$earr['code'].") existiert noch kein Bearbeitungsformular!");
                echo "<br/><br/><input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
            }

        }
        else
        {
            echo "Entität nicht vorhanden!";
        }
    }
    else
    {
        echo "Ungültige ID!";
    }
