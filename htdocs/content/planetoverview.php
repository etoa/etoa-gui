<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

/** @var ShipRepository */
$shipRepo = $app[ShipRepository::class];

/** @var FleetRepository */
$fleetRepo = $app[FleetRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

// BEGIN SKRIPT //

/** @var ?Planet $cp - The current Planet */
/** @var User $cu - The current User */

if (isset($cp))
{
    $planet = $planetRepo->find($cp->id);

    // Kolonie aufgeben
    if ($request->query->has('action') && $request->query->get('action') == "remove")
    {
        if (!$planet->mainPlanet)
        {
            echo "<h2>:: Kolonie auf diesem Planeten aufheben ::</h2>";

            $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
            if ($threshold < time())
            {
                echo "<form action=\"?page=$page\" method=\"POST\">";
                iBoxStart("Sicherheitsabfrage");
                echo "Willst du die Kolonie auf dem Planeten <b>".$planet->name."</b> wirklich löschen?";
                iBoxEnd();
                echo "<input type=\"submit\" name=\"submit_noremove\" value=\"Nein, Vorgang abbrechen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_remove\" value=\"Ja, die Kolonie soll aufgehoben werden\">";
                echo "</form>";
            }
            else
            {
                echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                erst ab <b>".df($threshold)."</b> gelöscht werden!<br/><br/>
                <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
            }
        }
        else
            error_msg("Dies ist ein Hauptplanet! Hauptplaneten können nicht aufgegeben werden!");
    }

    // Kolonie aufheben ausführen
    elseif ($request->request->get('submit_remove', '') != '')
    {
        if (!$planet->mainPlanet)
        {
            $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
            if ($threshold < time())
            {
                if (!$shipRepo->hasShipsOnEntity($planet->id))
                {
                    if (!$fleetRepo->hasFleetsRelatedToEntity($planet->id))
                    {
                        if ($cu->id == $planet->userId && !$planet->mainPlanet)
                        {
                            if (reset_planet($planet->id))
                            {
                                $mainPlanetId = $planetRepo->getUserMainId($cu->id);

                                echo "<br>Die Kolonie wurde aufgehoben!<br>";
                                echo "<a href=\"?page=overview&planet_id=".$mainPlanetId."\">Zur Übersicht</a>";

                                $planet = null;
                            }
                            else {
                                error_msg("Beim Aufheben der Kolonie trat ein Fehler auf! Bitte wende dich an einen Game-Admin!");
                            }
                        }
                        else {
                            error_msg("Der Planet ist aktuell nicht ausgewählt, er gehört nicht dir oder er ist ein Hauptplanet!");
                        }
                    }
                    else {
                        error_msg("Kolonie kann nicht gelöscht werden da Schiffe von/zu diesem Planeten unterwegs sind!");
                    }
                }
                else {
                    error_msg("Kolonie kann nicht gelöscht werden da noch Schiffe auf dem Planeten stationiert sind oder Schiffe noch im Bau sind!");
                }
            }
            else
            {
                echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                erst ab <b>".df($threshold)."</b> gelöscht werden!<br/><br/>
                <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
            }
        }
        else {
            error_msg("Dies ist ein Hauptplanet! Hauptplaneten können nicht aufgegeben werden!");
        }
    }
    // Kolonie zum Hauptplaneten machen
    if ($request->query->has('action') && $request->query->get('action') == "change_main")
    {
        if (!$planet->mainPlanet)
        {
            if(!$cu->changedMainPlanet())
            {
                echo "<h2>:: Kolonie zum Hauptplaneten machen ::</h2>";

                $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
                if ($threshold < time())
                {
                    echo "<form action=\"?page=$page\" method=\"POST\">";
                    iBoxStart("Sicherheitsabfrage");
                    echo "Willst du die Kolonie auf dem Planeten <b>".$planet->name."</b> wirklich zu deinem Hauptplaneten machen?<br>"
                        . "Du kannst deinen Hauptplaneten nur ein einziges Mal ändern.";
                    iBoxEnd();
                    echo "<input type=\"submit\" name=\"submit_nochange_main\" value=\"Nein, Vorgang abbrechen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_change_main\" value=\"Ja, Hauptplanet wechseln\">";
                    echo "</form>";
                }
                else
                {
                    echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                    erst ab <b>".df($threshold)."</b> zu deinem Hauptplaneten gemacht werden!<br/><br/>
                    <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
                }
            }
            else {
                error_msg("Du kannst deinen Hauptplaneten nur ein Mal ändern!");
            }
        }
        else {
            error_msg("Dies ist bereits dein Hauptplanet!");
        }
    }

    // Kolonie zum Hauptplaneten machen ausführen
    elseif ($request->request->get('submit_change_main', '') != '')
    {
        if (!$planet->mainPlanet)
        {
            $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
            if ($threshold < time())
            {
                if(!$cu->changedMainPlanet())
                {
                    if ($planetRepo->setMain($planet->id, $cu->id))
                    {
                        $cu->setChangedMainPlanet(true);
                        $cu->addToUserLog("planets", "{nick} wählt [b]".$cp."[/b] als neuen Hauptplanet aus.", 0);
                        echo "<br><b>".$planet->name."</b> ist nun dein Hauptplanet!<br/><br/>
                        <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
                    }
                    else {
                        error_msg("Beim Aufheben der Kolonie trat ein Fehler auf! Bitte wende dich an einen Game-Admin!");
                    }
                }
                else {
                    error_msg("Du kannst deinen Hauptplaneten nur ein Mal ändern!");
                }
            }
            else
            {
                echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                erst ab <b>".df($threshold)."</b> zu deinem Hauptplaneten gemacht werden!<br/><br/>
                <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
            }
        }
        else {
            error_msg("Dies ist bereits ein Hauptplanet!");
        }
    }

    //
    // Planeteninfo anzeigen
    //
    elseif (isset($planet))
    {
        if ($request->request->get('submit_change', '') != '')
        {
            if ($request->request->get('planet_name', '') != '')
            {
                // setNameAndComment() escapes strings
                $initialName = $planet->name;
                $cp->setNameAndComment($request->request->get('planet_name'), $request->request->get('planet_desc'));
                if ($initialName !== $cp->name) {
                    $app['dispatcher']->dispatch(new \EtoA\Planet\Event\PlanetRename(), \EtoA\Planet\Event\PlanetRename::RENAME_SUCCESS);
                }
            }
        }

        $sl = new ShipList($planet->id, $cu->id, 1);
        $dl = new DefList($planet->id, $cu->id, 1);

        echo "<h1>Übersicht über den Planeten ".$planet->name."</h1>";
        echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);

        if ($request->query->has('sub') && $request->query->get('sub') == "ships") {
            $sub="ships";
        } elseif ($request->query->has('sub') && $request->query->get('sub') == "defense") {
            $sub="defense";
        } elseif ($request->query->has('sub') && $request->query->get('sub') == "fields") {
            $sub="fields";
        } elseif ($request->query->has('sub') && $request->query->get('sub') == "name") {
            $sub="name";
        } else {
            $sub="";
        }
        echo "<script type=\"text/javascript\">
        // Wechselt zwischen den Verschiedenen Tabs
        function showTab(idx)
        {
            document.getElementById('tabOverview').style.display='none';
            document.getElementById('tabName').style.display='none';
            document.getElementById('tabFields').style.display='none';
            document.getElementById('tabShips').style.display='none';
            document.getElementById('tabDefense').style.display='none';
            if (document.getElementById(idx))
                document.getElementById(idx).style.display='';
        }
        </script>";
        $ddm = new DropdownMenu(1);
        $ddm->add('b','Übersicht',"showTab('tabOverview');");
        $ddm->add('n','Name &amp; Beschreibung',"showTab('tabName');");
        $ddm->add('r','Felder',"showTab('tabFields');");
        $ddm->add('f','Schiffe',"showTab('tabShips');");
        $ddm->add('d','Verteidigung',"showTab('tabDefense');");
        echo $ddm;

        echo "<div id=\"tabOverview\" style=\"".($sub=="" ? '' : 'display:none')."\">";

        iBoxStart("Übersicht");
        echo "<div style=\"position:relative;height:320px;padding:0px;background:#000 url('images/stars_middle.jpg');\">
        <div style=\"position:absolute;right:20px;top:20px;\">
        <img src=\"".$cp->imagePath('b')."\" style=\"width:220px;height:220px;\" alt=\"Planet\" /></div>";
        echo "<div class=\"planetOverviewName\"><a href=\"javascript:;\" onclick=\"showTab('tabName')\">".$planet->name."</a></div>";
        echo "<div class=\"planetOverviewList\">
        <div class=\"planetOverviewItem\">Grösse</div> ".nf($config->getInt('field_squarekm')*$planet->fields)." km&sup2;<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Temperatur</div>	".$planet->tempFrom." &deg;C bis ".$planet->tempTo." &deg;C <br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">System</div> <a href=\"?page=cell&amp;id=".$cp->cellId()."&amp;hl=".$planet->id."\">".$cp->getSectorSolsys()."</a> (Position ".$cp->pos.")<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Kennung</div> <a href=\"?page=entity&amp;id=".$planet->id."\">".$planet->id."</a><br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Stern</div> ".helpLink("stars",$cp->starTypeName)."<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Planetentyp</div> ".helpLink("planets",$cp->type())."<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Felder</div> <a href=\"javascript:;\" onclick=\"showTab('tabFields')\">".nf($planet->fieldsUsed)." von ".(nf($planet->fields))." benutzt</a> (".round($planet->fieldsUsed/$planet->fields*100)."%)<br style=\"clear:left;\"/>";
        if ($cp->debrisField)
        {
            echo "<div class=\"planetOverviewItem\">Trümmerfeld</div>
            <span class=\"resmetal\">".nf($cp->debrisMetal,0,1)."</span>
            <span class=\"rescrystal\">".nf($cp->debrisCrystal,0,1)."</span>
            <span class=\"resplastic\">".nf($cp->debrisPlastic,0,1)."</span>
            <br style=\"clear:left;\"/>";
        }
        if (filled($planet->description)) {
            if (strlen($planet->description) > 90) {
                echo "<div class=\"planetOverviewItem\">Beschreibung</div><span ".mTT('Beschreibung', $planet->description)."> ".substr($planet->description, 0, 90)." ...</span><br style=\"clear:left;\"/>";
            } else {
                echo "<div class=\"planetOverviewItem\">Beschreibung</div> ".$planet->description."<br style=\"clear:left;\"/>";
            }
        }
        if ($planet->mainPlanet) {
            echo "<div class=\"planetOverviewItem\">Hauptplanet</div> Dies ist dein Hauptplanet. Hauptplaneten können nicht invasiert oder aufgegeben werden!<br style=\"clear:left;\"/>";
        }
        echo "</div>";
        echo "</div>";
        iBoxEnd();
        echo "</div>";


        echo "<div id=\"tabName\" style=\"".($sub=="name" ? '' : 'display:none;')."\">";
        echo '<script type="text/javascript" src="web/js/vendor/planetname.js"></script>';
        echo "<form action=\"?page=$page\" method=\"POST\" style=\"text-align:center;\">";
        tableStart("Name und Beschreibung ändern:");
        echo "<tr><th>Name:</th><td>
        <input type=\"text\" name=\"planet_name\" id=\"planet_name\" value=\"".($planet->name)."\" length=\"25\" maxlength=\"15\" />
        &nbsp; <a href=\"javascript:;\" onclick=\"GenPlot();\">Name generieren</a></td></tr>";
        echo "<tr><th>Beschreibung:</th><td><textarea name=\"planet_desc\" rows=\"2\" cols=\"30\">".($cp->getNoBrDesc())."</textarea></td></tr>";
        tableEnd();
        echo "<input type=\"submit\" name=\"submit_change\" value=\"Speichern\" /> &nbsp; ";
        echo "</form>";
        echo "</div>";


        //
        // Felder
        //

        echo "<div id=\"tabFields\" style=\"".($sub=="fields" ? '' : 'display:none;')."\">";
        tableStart("Felderbelegung");
        echo "<tr>
        <tr><td colspan=\"2\">
        <img src=\"misc/progress.image.php?r=1&w=650&p=".round($planet->fieldsUsed/$planet->fields*100)."\" alt=\"progress\" style=\"width:100%;\"/>
        <br/>Benutzt: ".$planet->fieldsUsed.", Total: ".nf($planet->fields)." = ".nf($cp->fieldsBase)." Basisfelder + ".nf($planet->fieldsExtra)." zusätzliche Felder<br/></td></tr>
        <tr><td style=\"width:50%;vertical-align:top;padding:5px;\">";
        tableStart("Gebäude",'100%');
        $bl = new BuildList($planet->id, $cu->id, 1);
        if ($bl->count() > 0)
        {
            $fcnt=0;
            echo "<tr>
                <th>Name</th>
                <th>Stufe</th>
                <th>Felder</th></tr>";
            foreach ($bl as $k => &$v)
            {
                echo "<tr><th>".$v."</th>";
                echo "<td>".$bl->getLevel($k)."</td>";
                echo "<td>".nf($bl->getLevel($k) * $v->building->fields)."</td></tr>";
                $fcnt += $bl->getLevel($k) * $v->building->fields;
            }
            unset($v);
            echo "<tr><th colspan=\"2\">Total</th><td>".nf($fcnt)."</td></tr>";
        }
        else
            echo "<tr><td><i>Keine Gebäude vorhanden!</i></td></tr>";
        tableEnd();
        echo "</td><td style=\"width:50%;vertical-align:top;padding:5px;\">";
        tableStart("Verteidigungsanlagen",'100%');
        if ($dl->count() > 0)
        {
            $dfcnt=0;
            echo "<tr><th>Name</th><th>Anzahl</th><th>Felder</th></tr>";
            foreach ($dl as $k => &$v)
            {
                echo "<tr><th>".$v."</th>";
                echo "<td>".$dl->count($k)."</td>";
                echo "<td>".nf($dl->count($k)*$v->fieldsUsed)."</td></tr>";
                $dfcnt+=$dl->count($k)*$v->fieldsUsed;
            }
            unset($v);
            echo "<tr><th colspan=\"2\">Total</th><td>".nf($dfcnt)."</td></tr>";
        }
        else
            echo "<tr><td><i>Keine Verteidigungsanlagen vorhanden!</i></td></tr>";
        tableEnd();
        echo "</table>";
        echo "</div>";

        //
        // Schiffe
        //

        echo "<div id=\"tabShips\" style=\"".($sub=="ships" ? '' : 'display:none;')."\">";
        tableStart("Kampfstärke");
        if ($sl->count() > 0)
        {
                // Forschung laden und bonus dazu rechnen
            // Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (att)
                $weapon_tech_a=1;
                $structure_tech_a=1;
            $shield_tech_a=1;
            $heal_tech_a=1;

            $techres_a = dbquery("
                SELECT
                    techlist_tech_id,
                    techlist_current_level,
                    tech_name
                FROM
                    techlist
                INNER JOIN
                    technologies
                ON
                    techlist_tech_id=tech_id
                AND
                    techlist_user_id='".$cu->id."'
                    AND
                    (
                        techlist_tech_id='".STRUCTURE_TECH_ID."'
                        OR techlist_tech_id='".SHIELD_TECH_ID."'
                        OR techlist_tech_id='".WEAPON_TECH_ID."'
                        OR techlist_tech_id='".REGENA_TECH_ID."'
                    )
                ;");

            while ($techarr_a = mysql_fetch_array($techres_a))
            {
                if ($techarr_a['techlist_tech_id']==SHIELD_TECH_ID)
                        {
                    $shield_tech_a+=($techarr_a['techlist_current_level']/10);
                                $shield_tech_name = $techarr_a["tech_name"];
                                $shield_tech_level = $techarr_a["techlist_current_level"];
                        }
                if ($techarr_a['techlist_tech_id']==STRUCTURE_TECH_ID)
                        {
                    $structure_tech_a+=($techarr_a['techlist_current_level']/10);
                                $structure_tech_name = $techarr_a["tech_name"];
                                $structure_tech_level = $techarr_a["techlist_current_level"];
                        }
                if ($techarr_a['techlist_tech_id']==WEAPON_TECH_ID)
                        {
                    $weapon_tech_a+=($techarr_a['techlist_current_level']/10);
                                $weapon_tech_name = $techarr_a["tech_name"];
                                $weapon_tech_level = $techarr_a["techlist_current_level"];
                        }
                if ($techarr_a['techlist_tech_id']==REGENA_TECH_ID)
                        {
                    $heal_tech_a+=($techarr_a['techlist_current_level']/10);
                                $heal_tech_name = $techarr_a["tech_name"];
                                $heal_tech_level = $techarr_a["techlist_current_level"];
                        }
            }

            echo "<tr><th><b>Einheit</b></th><th>Grundwerte</th><th>Aktuelle Werte</th></tr>";
            echo "<tr>
                    <td><b>Struktur:</b></td>
                    <td>".nf($sl->getTotalStrucure())."</td>
                    <td>".nf($sl->getTotalStrucure()*($structure_tech_a+$sl->getBStructure()));
                    if ($structure_tech_a>1)
                    {
                        echo " (".get_percent_string($structure_tech_a,1)." durch ".$structure_tech_name." ".$structure_tech_level;
                        if ($sl->getBStructure()>0)
                            echo ", ".get_percent_string((1+$sl->getBStructure()),1)." durch Spezialschiffe";
                        echo ")";
                    }
                    echo "</td></tr>";
            echo "<tr><td><b>Schilder:</b></td>
                    <td>".nf($sl->getTotalShield())."</td>
                    <td>".nf($sl->getTotalShield()*($shield_tech_a+$sl->getBShield()));
                    if ($shield_tech_a>1)
                    {
                        echo " (".get_percent_string($shield_tech_a,1)." durch ".$shield_tech_name." ".$shield_tech_level;
                        if ($sl->getBShield()>0)
                            echo ", ".get_percent_string((1+$sl->getBShield()),1)." durch Spezialschiffe";
                        echo ")";
                    }
                    echo "</td></tr>";
            echo "<tr><td><b>Waffen:</b></td>
                    <td>".nf($sl->getTotalWeapon())."</td>
                    <td>".nf($sl->getTotalWeapon()*($weapon_tech_a+$sl->getBWeapon()));
                    if ($weapon_tech_a>1)
                    {
                        echo " (".get_percent_string($weapon_tech_a,1)." durch ".$weapon_tech_name." ".$weapon_tech_level;
                        if ($sl->getBWeapon()>0)
                            echo ", ".get_percent_string((1+$sl->getBWeapon()),1)." durch Spezialschiffe";
                        echo ")";
                    }
                    echo "</td></tr>";
            echo "<tr><td><b>Reparatur:</b></td>
                    <td>".nf($sl->getTotalHeal())."</td>
                    <td>".nf($sl->getTotalHeal()*($heal_tech_a+$sl->getBHeal()));
                    if ($heal_tech_a>1)
                    {
                        echo " (".get_percent_string($heal_tech_a,1)." durch ".$heal_tech_name." ".$heal_tech_level;
            if ($sl->getBHeal()>0)
                            echo ", ".get_percent_string((1+$sl->getBHeal()),1)." durch Spezialschiffe";
                        echo ")";
                    }
                    echo "</td></tr>";
            echo "<tr><td><b>Anzahl Schiffe:</b></td>
            <td colspan=\"2\">".nf($sl->count())."</td></tr>";
        }
        else
        {
            echo "<tr><td><i>Keine Schiffe vorhanden!</i></td></tr>";
        }
        tableEnd();

        tableStart("Details");
        echo "<tr><th>Typ</th><th>Anzahl</th><th>Eingebunkert</th></tr>";
        foreach ($sl as $k => &$v)
        {
            echo "<tr>
                <td>".$v."</td>
                <td>".nf($sl->count($k))."</td>
                <td>".nf($sl->countBunkered($k))."</td>
                </tr>";
        }
        unset($v);
        tableEnd();
        echo "</div>";

        //
        // Defense overview
        //

        echo "<div id=\"tabDefense\" style=\"".($sub=="defense" ? '' : 'display:none;')."\">";
        tableStart("Kampfstärke");
        if ($dl->count() >0)
        {
                // Forschung laden und bonus dazu rechnen
            // Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (att)
                $weapon_tech_a=1;
                $structure_tech_a=1;
            $shield_tech_a=1;
            $heal_tech_a=1;

            $techres_a = dbquery("
                SELECT
                    techlist_tech_id,
                    techlist_current_level,
                    tech_name
                FROM
                    techlist
                INNER JOIN
                    technologies
                ON
                    techlist_tech_id=tech_id
                AND
                    techlist_user_id='".$cu->id."'
                    AND
                    (
                        techlist_tech_id='".STRUCTURE_TECH_ID."'
                        OR techlist_tech_id='".SHIELD_TECH_ID."'
                        OR techlist_tech_id='".WEAPON_TECH_ID."'
                        OR techlist_tech_id='".REGENA_TECH_ID."'
                    )
                ;");

            while ($techarr_a = mysql_fetch_array($techres_a))
            {
                if ($techarr_a['techlist_tech_id']==SHIELD_TECH_ID)
                            {
                    $shield_tech_a+=($techarr_a['techlist_current_level']/10);
                                    $shield_tech_name = $techarr_a["tech_name"];
                                    $shield_tech_level = $techarr_a["techlist_current_level"];
                            }
                if ($techarr_a['techlist_tech_id']==STRUCTURE_TECH_ID)
                            {
                    $structure_tech_a+=($techarr_a['techlist_current_level']/10);
                                    $structure_tech_name = $techarr_a["tech_name"];
                                    $structure_tech_level = $techarr_a["techlist_current_level"];
                            }
                if ($techarr_a['techlist_tech_id']==WEAPON_TECH_ID)
                            {
                    $weapon_tech_a+=($techarr_a['techlist_current_level']/10);
                                    $weapon_tech_name = $techarr_a["tech_name"];
                                    $weapon_tech_level = $techarr_a["techlist_current_level"];
                            }
                if ($techarr_a['techlist_tech_id']==REGENA_TECH_ID)
                            {
                    $heal_tech_a+=($techarr_a['techlist_current_level']/10);
                                    $heal_tech_name = $techarr_a["tech_name"];
                                    $heal_tech_level = $techarr_a["techlist_current_level"];
                            }
            }

                echo "<tr><th><b>Einheit</b></th><th>Grundwerte</th><th>Aktuelle Werte</th></tr>";
            echo "<tr>
                    <td><b>Struktur:</b></td>
                    <td>".nf($dl->getTotalStrucure())."</td>
                    <td>".nf($dl->getTotalStrucure()*($structure_tech_a+$sl->getBStructure()));
                    if ($structure_tech_a>1)
                    {
                        echo " (".get_percent_string($structure_tech_a,1)." durch ".$structure_tech_name." ".$structure_tech_level;
            if ($sl->getBStructure()>0)
                            echo ", ".get_percent_string((1+$sl->getBStructure()),1)." durch Spezialschiffe";
                        echo ")";
                    }
                    echo "</td></tr>";
            echo "<tr><td><b>Schilder:</b></td>
                    <td>".nf($dl->getTotalShield())."</td>
                    <td>".nf($dl->getTotalShield()*($shield_tech_a+$sl->getBShield()));
                    if ($shield_tech_a>1)
                    {
                        echo " (".get_percent_string($shield_tech_a,1)." durch ".$shield_tech_name." ".$shield_tech_level;
            if ($sl->getBShield()>0)
                            echo ", ".get_percent_string((1+$sl->getBShield()),1)." durch Spezialschiffe";
                        echo ")";
                    }
                    echo "</td></tr>";
            echo "<tr><td><b>Waffen:</b></td>
                    <td>".nf($dl->getTotalWeapon())."</td>
                    <td>".nf($dl->getTotalWeapon()*($weapon_tech_a+$sl->getBWeapon()));
                    if ($weapon_tech_a>1)
                    {
                        echo " (".get_percent_string($weapon_tech_a,1)." durch ".$weapon_tech_name." ".$weapon_tech_level;
            if ($sl->getBWeapon()>0)
                            echo ", ".get_percent_string((1+$sl->getBWeapon()),1)." durch Spezialschiffe";
                        echo ")";
                    }
                    echo "</td></tr>";
            echo "<tr><td><b>Reparatur:</b></td>
                    <td>".nf($dl->getTotalHeal())."</td>
                    <td>".nf($dl->getTotalHeal()*($heal_tech_a+$sl->getBHeal()));
                    if ($heal_tech_a>1)
                    {
                        echo " (".get_percent_string($heal_tech_a,1)." durch ".$heal_tech_name." ".$heal_tech_level;
                    if ($sl->getBHeal()>0)
                            echo ", ".get_percent_string((1+$sl->getBHeal()),1)." durch Spezialschiffe";
                        echo ")";
        }
                    echo "</td></tr>";
            echo "<tr><td><b>Anzahl Anlagen:</b></td>
            <td colspan=\"2\">".nf($dl->count())."</td></tr>";
        }
        else
        {
            echo "<tr><td><i>Keine Verteidigung vorhanden!</i></td></tr>";
        }
        tableEnd();

        tableStart("Details");
        echo "<tr><th>Typ</th><th>Anzahl</th><th>Felder</th></tr>";
        foreach ($dl as $k => &$v)
        {
            echo "<tr>
                <td>".$v."</td>
                <td>".nf($dl->count($k))."</td>
                <td>".nf($dl->count($k)*$v->fieldsUsed)."</td>
                </tr>";
        }
        unset($v);
        tableEnd();

        echo "</div>";



        if (!$planet->mainPlanet)
        {
            echo "&nbsp;<input type=\"button\" value=\"Kolonie aufheben\" onclick=\"document.location='?page=$page&action=remove'\" />";
            if(!$cu->changedMainPlanet()){
                echo "&nbsp;<input type=\"button\" value=\"Zum Hauptplaneten machen\" onclick=\"document.location='?page=$page&action=change_main'\" />";
            }
        }

    }
} else {
    error_msg("Dieser Planet existiert nicht oder er gehlört nicht dir!");
}
