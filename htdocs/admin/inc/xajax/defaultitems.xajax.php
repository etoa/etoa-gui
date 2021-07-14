<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;

$xajax->register(XAJAX_FUNCTION, "loadItemSelector");
$xajax->register(XAJAX_FUNCTION, "addItemToSet");
$xajax->register(XAJAX_FUNCTION, "loadItemSet");
$xajax->register(XAJAX_FUNCTION, "removeFromItemSet");
$xajax->register(XAJAX_FUNCTION, "showObjCountChanger");
$xajax->register(XAJAX_FUNCTION, "changeItem");

function changeItem($id, $value, $setid)
{
    global $app;

    /** @var DefaultItemRepository $defaultItemRepository */
    $defaultItemRepository = $app[DefaultItemRepository::class];
    $defaultItemRepository->updateItemCount((int) $id, (int) $value);

    $or = new xajaxResponse();
    $or->script("xajax_loadItemSet(" . $setid . ");");
    return $or;
}


function removeFromItemSet($id, $setid)
{
    global $app;

    /** @var DefaultItemRepository $defaultItemRepository */
    $defaultItemRepository = $app[DefaultItemRepository::class];
    $defaultItemRepository->removeItem((int) $id);

    $or = new xajaxResponse();
    $or->script("xajax_loadItemSet(" . $setid . ");");
    return $or;
}

function showObjCountChanger($id, $setid)
{
    global $app;

    /** @var DefaultItemRepository $defaultItemRepository */
    $defaultItemRepository = $app[DefaultItemRepository::class];
    $count = $defaultItemRepository->getItemCount((int) $id);

    $or = new xajaxResponse();
    ob_start();
    echo "<input type=\"text\" id=\"countchanger_" . $id . "\" value=\"" . $count . "\" size=\"3\" />
    <input type=\"button\" onclick=\"xajax_changeItem(" . $id . ",document.getElementById('countchanger_" . $id . "').value," . $setid . ")\" value=\"Speichern\"/>
    <input type=\"button\" onclick=\"xajax_loadItemSet(" . $setid . ")\" value=\"Abbrechen\"/>
    <input type=\"button\"onclick=\"xajax_removeFromItemSet(" . $id . "," . $setid . ")\" value=\"Entfernen\" />";
    $out = ob_get_contents();
    ob_end_clean();
    $or->assign("details_" . $id, "innerHTML", $out);
    $or->script("document.getElementById('countchanger_" . $id . "').select();");
    return $or;
}

function loadItemSet($setid)
{
    global $app;

    /** @var DefaultItemRepository $defaultItemRepository */
    $defaultItemRepository = $app[DefaultItemRepository::class];
    $defaultItems = $defaultItemRepository->getItemsGroupedByCategory((int) $setid);

    $or = new xajaxResponse();
    ob_start();

    if (isset($defaultItems['b'])) {
        /** @var BuildingDataRepository $buildingRepository */
        $buildingRepository = $app[BuildingDataRepository::class];
        $buildingNames = $buildingRepository->getBuildingNames(true);
        echo "<br/><b>Gebäude:</b><br/>";
        foreach ($defaultItems['b'] as $defaultItem) {
            echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(" . $defaultItem->id . "," . $setid . ")\">" . $buildingNames[$defaultItem->objectId] . "</span>
            <span id=\"details_" . $defaultItem->id . "\">(" . $defaultItem->count . ")</span><br/>";
        }
    }

    if (isset($defaultItems['t'])) {
        /** @var TechnologyDataRepository $technologyDataRepository */
        $technologyDataRepository = $app[TechnologyDataRepository::class];
        $technologyNames = $technologyDataRepository->getTechnologyNames(true);
        echo "<br/><b>Technologien:</b><br/>";
        foreach ($defaultItems['t'] as $defaultItem) {
            echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(" . $defaultItem->id . "," . $setid . ")\">" . $technologyNames[$defaultItem->objectId] . "</span>
            <span id=\"details_" . $defaultItem->id . "\">(" . $defaultItem->count . ")</span><br/>";
        }
    }

    if (isset($defaultItems['s'])) {
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);
        echo "<br/><b>Schiffe:</b><br/>";
        foreach ($defaultItems['s'] as $defaultItem) {
            echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(" . $defaultItem->id . "," . $setid . ")\">" . $shipNames[$defaultItem->objectId] . "</span>
            <span id=\"details_" . $defaultItem->id . "\">(" . $defaultItem->count . ")</span><br/>";
        }
    }

    if (isset($defaultItems['d'])) {
        /** @var DefenseDataRepository $defenseRepository */
        $defenseRepository = $app[DefenseDataRepository::class];
        $defenseNames = $defenseRepository->getDefenseNames(true);
        echo "<br/><b>Verteidigung:</b><br/>";
        foreach ($defaultItems['d'] as $defaultItem) {
            echo "<span onmouseover=\"this.style.color='#0f0'\" onmouseout=\"this.style.color=''\" onclick=\"xajax_showObjCountChanger(" . $defaultItem->id . "," . $setid . ")\">" . $defenseNames[$defaultItem->objectId] . "</span>
            <span id=\"details_" . $defaultItem->id . "\">(" . $defaultItem->count . ")</span><br/>";
        }
    }
    if (count($defaultItems) === 0) {
        echo "Keine Objekte definiert!<br/>";
    }

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign("setcontent_" . $setid, "innerHTML", $out);
    return $or;
}




function addItemToSet($setid, $form)
{
    global $app;
    $or = new xajaxResponse();
    ob_start();
    $cnt = intval($form['new_item_count']);
    if ($cnt > 0) {
        /** @var DefaultItemRepository $defaultItemRepository */
        $defaultItemRepository = $app[DefaultItemRepository::class];
        $success = $defaultItemRepository->addItemToSet($setid, $form['new_item_cat'], $form['new_item_object_id'], $cnt);
        if ($success) {
            $or->script("xajax_loadItemSet(" . $setid . ");");
            ob_end_clean();
            return $or;
        }

        $or->alert("Bereits vorhanden!");
        ob_end_clean();
        return $or;
    }

    $or->alert("Ungültige Anzahl/Stufe!");
    ob_end_clean();
    return $or;
}

function loadItemSelector($cat, $setid)
{
    global $app;
    $or = new xajaxResponse();
    ob_start();
    if ($cat == "b") {
        /** @var BuildingDataRepository $buildingDataRepository */
        $buildingDataRepository = $app[BuildingDataRepository::class];
        $buildingNames = $buildingDataRepository->getBuildingNames(true);
        echo "<select name=\"new_item_object_id\">";
        foreach ($buildingNames as $buildingId => $buildingName) {
            echo "<option value=\"" . $buildingId . "\">" . $buildingName . "</option>";
        }
        echo "</select> Stufe <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
        &nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(" . $setid . ",xajax.getFormValues('set_" . $setid . "'))\" value=\"Hinzufügen\" />";
    } elseif ($cat == "t") {
        /** @var TechnologyDataRepository $technologyDataRepository */
        $technologyDataRepository = $app[TechnologyDataRepository::class];
        $technologyNames = $technologyDataRepository->getTechnologyNames(true);
        echo "<select name=\"new_item_object_id\">";
        foreach ($technologyNames as $technologyId => $technologyName) {
            echo "<option value=\"" . $technologyId . "\">" . $technologyName . "</option>";
        }
        echo "</select> Stufe <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
        &nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(" . $setid . ",xajax.getFormValues('set_" . $setid . "'))\" value=\"Hinzufügen\" />";
    } elseif ($cat == "s") {
        /** @var ShipDataRepository $shipDateRepository */
        $shipDateRepository = $app[ShipDataRepository::class];
        $shipNames = $shipDateRepository->getShipNames(true);
        echo "<select name=\"new_item_object_id\">";
        foreach ($shipNames as $shipId => $shipName) {
            echo "<option value=\"" . $shipId . "\">" . $shipName . "</option>";
        }
        echo "</select> Anzahl <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
        &nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(" . $setid . ",xajax.getFormValues('set_" . $setid . "'))\" value=\"Hinzufügen\" />";
    } elseif ($cat == "d") {
        /** @var DefenseDataRepository $defenseDateRepository */
        $defenseDateRepository = $app[DefenseDataRepository::class];
        $defenseNames = $defenseDateRepository->getDefenseNames(true);
        echo "<select name=\"new_item_object_id\">";
        foreach ($defenseNames as $defenseId => $defenseName) {
            echo "<option value=\"" . $defenseId . "\">" . $defenseName . "</option>";
        }
        echo "</select> Anzahl <input type=\"text\" name=\"new_item_count\" value=\"1\" size=\"3\" />
        &nbsp; <input type=\"button\" onclick=\"xajax_addItemToSet(" . $setid . ",xajax.getFormValues('set_" . $setid . "'))\" value=\"Hinzufügen\" />";
    } else {
        echo "Bitte Kategorie wählen!";
    }

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign("itemlist_" . $setid, "innerHTML", $out);
    return $or;
}
