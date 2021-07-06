<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

if (isset($_GET['id']) && intval($_GET['id'])>0)
{
    $cellId = intval($_GET['id']);
}
else
{
    $cellId = $cp->cellId();
}

$_SESSION['currentEntity']=serialize($cp);

// Systemnamen updaten
if (isset($_POST['starname_submit']) && $_POST['starname']!="" && intval($_POST['starname_id'])>0 && checker_verify())
{
    $star = new Star($_POST['starname_id']);
    if ($star->isValid())
    {
        if ($star->setNewName($_POST['starname']))
        {
            success_msg("Der Stern wurde benannt!");

            $app['dispatcher']->dispatch(new \EtoA\Galaxy\Event\StarRename(), \EtoA\Galaxy\Event\StarRename::RENAME_SUCCESS);
        }
        else
        {
            error_msg("Es gab ein Problem beim Setzen des Namens!");
        }
    }
    unset($star);
}

    $cell = new Cell($cellId);
    if ($cell->isValid())
    {


        $entities = $cell->getEntities();

        echo "<h1>System ".$cell."</h1>";

        $sx_num = $config->param1Int('num_of_sectors');
        $sy_num = $config->param2Int('num_of_sectors');
        $cx_num = $config->param1Int('num_of_cells');
        $cy_num = $config->param2Int('num_of_cells');


        if ($cu->discovered($cell->absX(),$cell->absY()))
        {
        $ares = dbquery("SELECT
                            player_id
                        FROM
                            admin_users
                        WHERE
                            player_id<>0;");
        $admins = array();
        while ($arow = mysql_fetch_row($ares)) {
            $admins[] = (int)$arow[0];
        }

        //
        // Systamkarte
        //
        tableStart("Systemkarte");

        echo "<tr><td colspan=\"6\" style=\"text-align:center;vertical-align:middle;\">
        <a href=\"?page=galaxy\">Galaxie</a> &raquo;&nbsp;
        <a href=\"?page=sector&sector=".$cell->getSX().",".$cell->getSY()."\">Sektor ".$cell->getSX()."/".$cell->getSY()."</a> &raquo; &nbsp;";
        $cres = dbquery("
        SELECT
            id
        FROM
            cells
        WHERE
            sx=".$cell->getSX()."
            AND sy=".$cell->getSY()."
            AND cx=1
            AND cy=1;
        ");
        $carr = mysql_fetch_row($cres);
        $cid = $carr[0];
        echo "<select name=\"cell\" onchange=\"document.location='?page=$page&id='+this.value\">";
        for ($x=1;$x<=$cx_num;$x++)
        {
            for ($y=1;$y<=$cy_num;$y++)
            {
                echo "<option value=\"".$cid."\"";
                if ($cell->getCX()==$x && $cell->getCY()==$y)
                    echo " selected=\"selected\"";
                echo ">System $x/$y &nbsp;</option>";
                $cid++;
            }
        }
        echo "</select>";
        echo "</td></tr>";


            echo "<tr>
                <th colspan=\"2\" style=\"width:60px;\">Position</th>
                <th>Typ</th>
                <th>Name</th>
                <th>Besitzer</th>
                <th style=\"width:150px;\">Aktionen</th>
            </tr>";

            $hasPlanetInSystem = false;
            $starNameEmpty = false;
            foreach ($entities as $ent)
            {
                if ($ent->pos()==1)
                {
                    echo "<tr>
                        <td style=\"height:3px;background:#000;\" colspan=\"6\"></td>
                    </tr>";
                }
                $addstyle=" style=\"vertical-align:middle;";
                if (isset($_GET['hl']) && $_GET['hl']==$ent->id())
                {
                    $addstyle.="background:#003D6F;";
                }
                $addstyle.="\" ";

                $class = " class=\"";
                if ($ent->ownerId()>0)
                {
                    //Admin
                    if (in_array((int) $ent->ownerId(),$admins, true)) {
                        $class .= "adminColor";
                        $tm_info = "Admin/Entwickler";
                    }
                    // Krieg
                    elseif ($ent->owner->allianceId>0 && $cu->allianceId>0 && $cu->alliance->checkWar($ent->owner->allianceId))
                    {
                        $class .= "enemyColor";
                        $tm_info = "Krieg";
                    }
                    // Bündniss
                    elseif ($ent->owner->allianceId>0 && $cu->allianceId>0 && $cu->alliance->checkBnd($ent->owner->allianceId))
                    {
                        $class .= "friendColor";
                        $tm_info = "B&uuml;ndnis";
                    }
                    // Gesperrt
                    elseif ($ent->ownerLocked())
                    {
                        $class .= "userLockedColor";
                        $tm_info = "Gesperrt";
                    }
                    // Urlaub
                    elseif ($ent->ownerHoliday())
                    {
                        $class .= "userHolidayColor";
                        $tm_info = "Urlaubsmodus";
                    }
                    // Lange Inaktiv
                    elseif ($ent->owner->lastOnline<time()-USER_INACTIVE_LONG*86400)
                    {
                        $class .= "userLongInactiveColor";
                        $tm_info = "Lange Inaktiv";
                    }
                    // Inaktiv
                    elseif ($ent->owner->lastOnline<time()-USER_INACTIVE_SHOW*86400)
                    {
                        $class .= "userInactiveColor";
                        $tm_info = "Inaktiv";
                    }
                    // Eigener Planet
                    elseif($cu->id == $ent->ownerId())
                    {
                        $class .= "userSelfColor";
                        $tm_info = "";
                    }
                    // Allianzmitglied
                    elseif($cu->allianceId()==$ent->owner->allianceId() && $cu->allianceId())
                    {
                        $class .= "userAllianceMemberColor";
                        $tm_info = "Allianzmitglied";
                    }
                    // Alien/NPC
                    elseif ($ent->owner->isNPC()>0)
                    {
                        $class .= "alien";
                        $tm_info = "Alien";
                    }
                    // Noob
                    elseif (!$cu->canAttackPlanet($ent))
                    {
                        $class .= "noobColor";
                        $tm_info = "Anf&auml;ngerschutz";
                    }
                    else
                    {
                        $class .= "";
                        $tm_info="";
                    }
                }
                else
                {
                    $class .= "";
                    $tm_info="";
                }
                $class .="\" ";

                if ($ent->entityCode() === EntityType::PLANET)
                {
                    $planet = $planetRepo->find($ent->id());

                    $tm="";
                    $tm.= "<b>Felder</b>: ".nf($ent->fields);
                    $tm.= "<br/><b>Bewohnbar</b>: ";
                    if ($ent->habitable==1) $tm.= "Ja"; else $tm.= "Nein	";
                    if ($ent->typeMetal!=1)
                        $tm.="<br/><b>".RES_METAL.":</b> ".get_percent_string($ent->typeMetal,1);
                    if ($ent->typeCrystal!=1)
                        $tm.="<br/><b>".RES_CRYSTAL.":</b> ".get_percent_string($ent->typeCrystal,1);
                    if ($ent->typePlastic!=1)
                        $tm.="<br/><b>".RES_PLASTIC.":</b> ".get_percent_string($ent->typePlastic,1);
                    if ($ent->typeFuel!=1)
                        $tm.="<br/><b>".RES_FUEL.":</b> ".get_percent_string($ent->typeFuel,1);
                    if ($ent->typeFood!=1)
                        $tm.="<br/><b>".RES_FOOD.":</b> ".get_percent_string($ent->typeFood,1);
                    if ($ent->typePower!=1)
                        $tm.="<br/><b>Energie:</b> ".get_percent_string($ent->typePower,1);
                    if ($ent->typePopulation!=1)
                        $tm.="<br/><b>Bewohner:</b> ".get_percent_string($ent->typePopulation,1);
                    if ($ent->typeResearchtime!=1)
                        $tm.="<br/><b>Foschungszeit:</b> ".get_percent_string($ent->typeResearchtime,1,1);
                    if ($ent->typeBuildtime!=1)
                        $tm.="<br/><b>Bauzeit:</b> ".get_percent_string($ent->typeBuildtime,1,1);
                    $tm.= "<br /><br/><b>Wärmebonus</b>: ";
                    $solarProdBonus = $planet->solarPowerBonus();
                    $color = $solarProdBonus>=0 ? '#0f0' : '#f00';
                    $tm.= "<span style=\"color:".$color."\">".($solarProdBonus > 0 ? '+' : '').$solarProdBonus."</span>";
                    $tm.= " Energie pro Solarsatellit";
                    $tm.= "<br /><b>Kältebonus</b>: ";
                    $fuelProdBonus = $planet->fuelProductionBonus();
                    $color = $fuelProdBonus >= 0 ? '#0f0' : '#f00';
                    $tm.= "<span style=\"color:".$color."\">".($fuelProdBonus > 0 ? '+' : '').$fuelProdBonus."%</span>";
                    $tm.= " ".RES_FUEL."-Produktion";
                }

                echo "<tr>
                    <td $class style=\"width:40px;background:#000;\">
                        <a href=\"?page=entity&amp;id=".$ent->id()."\">
                            <img src=\"".$ent->imagePath()."\" alt=\"icon\" />
                        </a>
                    </td>
                    <td $class style=\"text-align:center;vertical-align:middle;background:#000\"><b>".$ent->pos()."</b></td>
                    <td $class $addstyle >";
                    if ($ent->entityCode()==='p')
                        echo "<span ".tm($ent->type(),$tm).">".$ent->type()."</span>";
                    else
                        echo $ent->entityCodeString();

                    if ($ent->entityCode()=='w')
                    {
                        if ($ent->isPersistent())
                        {
                            echo " [stabil]";
                        }
                        else
                        {
                            echo " [veränderlich]";
                        }
                        $tent = new Wormhole($ent->targetId());
                        echo "<br/>Ziel: <a href=\"?page=cell&amp;id=".$tent->cellId()."\">".$tent."</a>";
                    }
                    elseif ($ent->entityCode() == EntityType::PLANET)
                    {
                        $planet = $planetRepo->find($ent->id());
                        if ($planet->hasDebrisField()) {
                            echo "<br/><span style=\"color:#817339;font-weight:bold\" ".tm(
                                "Trümmerfeld",
                                RES_ICON_METAL.nf($planet->wfMetal)." ".
                                RES_METAL."<br style=\"clear:both\" />".
                                RES_ICON_CRYSTAL.nf($planet->wfCrystal)." ".
                                RES_CRYSTAL."<br style=\"clear:both\" />".
                                RES_ICON_PLASTIC.nf($planet->wfPlastic)." ".
                                RES_PLASTIC."<br style=\"clear:both\" />"
                            ).">Trümmerfeld</span> ";
                        }
                    }
                    echo "</td>
                    <td $addstyle><a $class href=\"?page=entity&amp;id=".$ent->id()."\">".text2html($ent->name())."</a></td>
                    <td $addstyle>";
                    if ($ent->ownerId()>0)
                    {
                        $header = $ent->owner();
                        $tm = "Punkte: ".nf($ent->owner->points)."<br style=\"clear:both\" />";
                        if ($ent->ownerAlliance()>0)
                            $tm .= "Allianz: ".$ent->owner->alliance."<br style=\"clear:both\" />";
                        if ($tm_info!="")
                            $header .= " (<span $class>".$tm_info."</span>)";
                        echo "<span style=\"color:#817339;font-weight:bold\" ".tm($header,$tm)."><a $class href=\"?page=userinfo&amp;id=".$ent->ownerId()."\">".$ent->owner()."</a></span> ";
                    }
                    else
                        echo $ent->owner();
                    echo "</td>
                    <td $addstyle>";

                        // Favorit
                    if ($cu->id!=$ent->ownerId())
                    {
                        echo "<a href=\"?page=bookmarks&amp;add=".$ent->id()."\" title=\"Zu den Favoriten hinzuf&uuml;gen\">".icon("favorite")."</a> ";
                    }

                    // Flotte
                    if ($ent->entityCode()=='p' || $ent->entityCode()=='a' || $ent->entityCode()=='w' || $ent->entityCode()=='n' || $ent->entityCode()=='e')
                    {
                        echo "<a href=\"?page=haven&amp;target=".$ent->id()."\" title=\"Flotte hinschicken\">".icon('fleet')."</a> ";
                    }

                    if ($ent->entityCode()=='s')
                    {
                        if (!$ent->named)
                        {
                            $starNameEmpty=true;
                            $starToBeNamed = $ent->id();
                        }
                    }
                    elseif ($ent->entityCode()=='p')
                    {
                        if ($ent->ownerId()>0 && $cu->id==$ent->ownerId())
                        {
                            $hasPlanetInSystem = true;
                        }

                        // Nachrichten-Link
                        if ($ent->ownerId()>0 && $cu->id!=$ent->ownerId())
                        {
                            echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$ent->ownerId()."\" title=\"Nachricht senden\">".icon("mail")."</a> ";
                        }

                        // Diverse Links
                        if ($cu->id!=$ent->ownerId())
                        {
                            // Besiedelte Planete
                            if($ent->ownerId() > 0)
                            {
                                echo "<a href=\"javascript:;\" onclick=\"xajax_launchSypProbe(".$ent->id().");\" title=\"Ausspionieren\">".icon("spy")."</a>";
                                echo "<a href=\"?page=missiles&amp;target=".$ent->id()."\" title=\"Raketenangriff starten\">".icon("missile")."</a> ";
                                echo "<a href=\"?page=crypto&amp;target=".$ent->id()."\" title=\"Flottenbewegungen analysieren\">".icon("crypto")."</a> ";
                            }
                        }
                    }

                    if (in_array("analyze",$ent->allowedFleetActions(), true))
                    {
                        if ($cu->properties->showCellreports)
                        {
                            $reports = Report::find(array("type"=>"spy","user_id"=>$cu->id, "entity1_id"=>$ent->id()),"timestamp DESC",1,0,true);
                            if (count($reports) > 0) {
                                $r = array_pop($reports);
                                echo "<span ".tm($r->subject,$r."<br style=\"clear:both\" />")."><a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(".$ent->id().");\" title=\"Analysieren\">".icon("spy")."</a></span>";
                            }
                            else
                                echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(".$ent->id().");\" title=\"Analysieren\">".icon("spy")."</a> ";
                        }
                        else
                            echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(".$ent->id().");\" title=\"Analysieren\">".icon("spy")."</a> ";
                    }


                    echo "</td></tr>";

            }

            tableEnd();


            // System benennen
            if ($hasPlanetInSystem && $starNameEmpty)
            {
            echo "<form action=\"?page=$page&amp;id=".intval($cellId)."\" method=\"post\">";
            checker_init();
            echo "Du darfst diesen Stern benennen:
            <input type=\"text\" name=\"starname\" value=\"\" maxlength=\"30\"/>
            <input type=\"hidden\" name=\"starname_id\" value=\"". $starToBeNamed."\" />
            <input type=\"submit\" name=\"starname_submit\" value=\"Speichern\" /><br/><br/></form>";
        }



            echo "<div id=\"spy_info_box\" style=\"display:none;\">";
            iBoxStart("Flotten");
            echo "<div id=\"spy_info\"></div>";
            iBoxEnd();
            echo "</div>";

            iBoxStart("Legende");
            echo "
            <span class=\"userSelfColor\">Eigener Planet</span>,
            <span class=\"userLockedColor\">Gesperrt</span>,
            <span class=\"userHolidayColor\">Urlaubsmodus</span>,
            <span class=\"userInactiveColor\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span>,
            <span class=\"userLongInactiveColor\">Inaktiv (".USER_INACTIVE_LONG." Tage)</span><br/>
            <span class=\"noobColor\">Anf&auml;ngerschutz</span>,
            <span class=\"friendColor\">B&uuml;ndnis</span>,
            <span class=\"enemyColor\">Krieg</span>,
            <span class=\"userAllianceMemberColor\">Allianzmitglied</span>,
            <span class=\"adminColor\" ".tm("Admin/Entwickler","Gemäss §14.2 ist es strengstens untersagt einen Adminaccount anzugreifen oder auszuspionieren. Wer dies tut ist selber schuld und kann mit einer Sperre von 24h bestraft werden!<br style=\"clear:both\" />").">Admin/Entwickler</span>";
            iBoxEnd();
        }
        else
        {
            iBoxStart("Fehler");
            echo "<div style=\"text-align:center;\">
            <a href=\"?page=galaxy\">Galaxie</a> &gt;&nbsp;
            <a href=\"?page=sector&sector=".$cell->getSX().",".$cell->getSY()."\">Sektor ".$cell->getSX()."/".$cell->getSY()."</a> &gt; &nbsp;";
            $cres = dbquery("
            SELECT
                id
            FROM
                cells
            WHERE
                sx=".$cell->getSX()."
                AND sy=".$cell->getSY()."
                AND cx=1
                AND cy=1;
            ");
            $carr = mysql_fetch_row($cres);
            $cid = $carr[0];
            echo "<select name=\"cell\" onchange=\"document.location='?page=$page&id='+this.value\">";
            for ($x=1;$x<=$cx_num;$x++)
            {
                for ($y=1;$y<=$cy_num;$y++)
                {
                    echo "<option value=\"".$cid."\"";
                    if ($cell->getCX()==$x && $cell->getCY()==$y)
                        echo " selected=\"selected\"";
                    echo ">System $x/$y &nbsp;</option>";
                    $cid++;
                }
            }
            echo "</select></div><br/>";
            echo "System noch nicht erkundet. Erforsche das System mit einer Erkundungsflotte um es sichtbar zu machen!<br/><br/>";
            echo "<input type=\"button\" onclick=\"xajax_launchExplorerProbe(".$cellId.");\" value=\"Erkundungsflotte senden\" />";
            iBoxEnd();

            echo "<div id=\"spy_info_box\" style=\"display:none;\">";
            iBoxStart("Flotten");
            echo "<div id=\"spy_info\"></div>";
            iBoxEnd();
            echo "</div>";

        }

        echo "<input type=\"button\" value=\"Zur Raumkarte\" onclick=\"document.location='?page=sector&amp;sx=".$cell->getSX()."&amp;sy=".$cell->getSY()."'\" /> &nbsp; ";

    }
    else
    {
        error_msg("System nicht gefunden!");
        echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=sector'\" />";
    }
