<?PHP
$xajax->register(XAJAX_FUNCTION, "reqInfo");


function reqInfo($id, $cat = 'b')
{
    global $app;
    $or = new xajaxResponse();
    ob_start();

    defineImagePaths();

    /** @var \EtoA\Building\BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[\EtoA\Building\BuildingDataRepository::class];
    $buildingNames = $buildingRepository->getBuildingNames();

    /** @var \EtoA\Technology\TechnologyDataRepository $technologyRepository */
    $technologyRepository = $app[\EtoA\Technology\TechnologyDataRepository::class];
    $technologyNames = $technologyRepository->getTechnologyNames();

    /** @var \EtoA\Ship\ShipDataRepository $shipRepository */
    $shipRepository = $app[\EtoA\Ship\ShipDataRepository::class];
    $shipNames = $shipRepository->getShipNames();

    /** @var \EtoA\Defense\DefenseDataRepository $defenseRepository */
    $defenseRepository = $app[\EtoA\Defense\DefenseDataRepository::class];
    $defenseNames = $defenseRepository->getDefenseNames();

    /** @var \EtoA\Missile\MissileDataRepository $missileRepository */
    $missileRepository = $app[\EtoA\Missile\MissileDataRepository::class];
    $missileNames = $missileRepository->getMissileNames();

    //
    // Required objects
    //

    if ($cat == 'b') {
        $req_tbl = "building_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 't') {
        $req_tbl = "tech_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 's') {
        $req_tbl = "ship_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 'd') {
        $req_tbl = "def_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 'm') {
        $req_tbl = "missile_requirements";
        $req_field = "obj_id";
    } else {
        throw new \InvalidArgumentException('Unknown category:' . $cat);
    }

    $items = array();
    $res = dbquery("SELECT * FROM $req_tbl WHERE obj_id=" . $id . " AND req_building_id>0 AND req_level>0 ORDER BY req_level;");
    $nr = mysql_num_rows($res);
    if ($nr > 0) {
        while ($arr = mysql_fetch_assoc($res)) {
            $items[] = array($arr['req_building_id'], $buildingNames[$arr['req_building_id']], $arr['req_level'], IMAGE_PATH . "/buildings/building" . $arr['req_building_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['req_building_id'] . ",'b')");
        }
    }
    $res = dbquery("SELECT * FROM $req_tbl WHERE $req_field=" . $id . " AND req_tech_id>0 AND req_level>0 ORDER BY req_level;");
    $nr2 = mysql_num_rows($res);
    if ($nr2 > 0) {
        while ($arr = mysql_fetch_assoc($res)) {
            $items[] = array($arr['req_tech_id'], $technologyNames[$arr['req_tech_id']], $arr['req_level'], IMAGE_PATH . "/technologies/technology" . $arr['req_tech_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['req_tech_id'] . ",'t')");
        }
    }

    if (count($items) > 0) {
        echo "<div class=\"techtreeItemContainer\">";
        foreach ($items as $i) {
            echo "<div class=\"techtreeItem\" style=\"background:url('" . $i[3] . "');\">
            <div class=\"techtreeItemLevel\">Lvl <b>" . $i[2] . "</b></div>
            <a href=\"javascript:;\" onclick=\"" . $i[4] . "\" style=\"height:100%;display:block;\"></a>
            <div class=\"techtreeItemName\">" . $i[1] . "</div>
            </div>";
        }
        echo "<br style=\"clear:both;\"";
        echo "</div>";

        echo "<div style=\"margin:0px auto;\">wird benötigt für</div>";
    }

    //
    // Current object
    //

    if ($cat == 'b') {
        $img = IMAGE_PATH . "/buildings/building" . $id . "_middle." . IMAGE_EXT;
        $name = $buildingNames[$id];
    } elseif ($cat == 't') {
        $img = IMAGE_PATH . "/technologies/technology" . $id . "_middle." . IMAGE_EXT;
        $name = $technologyNames[$id];
    } elseif ($cat == 's') {
        $img = IMAGE_PATH . "/ships/ship" . $id . "_middle." . IMAGE_EXT;
        $name = $shipNames[$id];
    } elseif ($cat == 'd') {
        $img = IMAGE_PATH . "/defense/def" . $id . "_middle." . IMAGE_EXT;
        $name = $defenseNames[$id];
    } elseif ($cat == 'm') {
        $img = IMAGE_PATH . "/missiles/missile" . $id . "_middle." . IMAGE_EXT;
        $name = $missileNames[$id];
    } else {
        throw new \InvalidArgumentException('Unknown category:' . $cat);
    }
    echo "<div class=\"techtreeMainItem\" style=\"background:url('" . $img . "');\">";
    echo "<div class=\"techtreeItemName\">" . $name . "</div>";
    echo "</div>";

    //
    // Allowed objects
    //

    if ($cat == 'b' || $cat == 't') {
        if ($cat == 'b') {
            $req_field = "req_building_id";
            $req_level_field = "req_level";
        } elseif ($cat == 't') {
            $req_field = "req_tech_id";
            $req_level_field = "req_level";
        } else {
            throw new \InvalidArgumentException('Unknown category:' . $cat);
        }


        $items = array();
        $res = dbquery("SELECT * FROM building_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($buildingNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $buildingNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/buildings/building" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'b')");
                }
            }
        }
        $res = dbquery("SELECT * FROM tech_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($technologyNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $technologyNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/technologies/technology" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'t')");
                }
            }
        }
        $res = dbquery("SELECT * FROM ship_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($shipNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $shipNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/ships/ship" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'s')");
                }
            }
        }
        $res = dbquery("SELECT * FROM def_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($defenseNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $defenseNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/defense/def" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'d')");
                }
            }
        }
        $res = dbquery("SELECT * FROM missile_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($missileNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $missileNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/missiles/missile" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'m')");
                }
            }
        }

        if (count($items) > 0) {
            echo "<div style=\"margin:10px auto;\">ermöglicht</div>";

            echo "<div class=\"techtreeItemContainer\">";
            $cnt = 0;
            foreach ($items as $i) {
                echo "<div class=\"techtreeItem\" style=\"background:url('" . $i[3] . "');\">
                <div class=\"techtreeItemLevel\">Ab Lvl <b>" . $i[2] . "</b></div>
                <a href=\"javascript:;\" onclick=\"" . $i[4] . "\" style=\"height:100%;display:block;\"></a>
                <div class=\"techtreeItemName\">" . $i[1] . "</div>
                </div>";
                $cnt++;
            }
            echo "<br style=\"clear:both;\"";
            echo "</div>";
        }
    }


    $out = ob_get_clean();
    $or->assign('reqInfo', 'innerHTML', $out);
    return $or;
}
