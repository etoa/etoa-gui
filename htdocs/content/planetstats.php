<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

echo '<h1>Wirtschaftsübersicht</h1>';

$planets = $planetRepo->getUserPlanets($cu->id);

$cnt_res = 0;
$max_res = array(0, 0, 0, 0, 0, 0);
$min_res = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
$tot_res = array(0, 0, 0, 0, 0, 0);

$cnt_prod = 0;
$max_prod = array(0, 0, 0, 0, 0, 0);
$min_prod = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
$tot_prod = array(0, 0, 0, 0, 0, 0);
$val_res = [];
$val_prod = [];
$val_store = [];
$val_time = [];
foreach ($planets as $planet) {
    //Speichert die aktuellen Rohstoffe in ein Array
    $val_res[$planet->id][0] = floor($planet->resMetal);
    $val_res[$planet->id][1] = floor($planet->resCrystal);
    $val_res[$planet->id][2] = floor($planet->resPlastic);
    $val_res[$planet->id][3] = floor($planet->resFuel);
    $val_res[$planet->id][4] = floor($planet->resFood);
    $val_res[$planet->id][5] = floor($planet->people);

    for ($x = 0; $x < 6; $x++) {
        $max_res[$x] = max($max_res[$x], $val_res[$planet->id][$x]);
        $min_res[$x] = min($min_res[$x], $val_res[$planet->id][$x]);
        $tot_res[$x] += $val_res[$planet->id][$x];
    }

    //Speichert die aktuellen Rohstoffproduktionen in ein Array
    $val_prod[$planet->id][0] = floor($planet->prodMetal);
    $val_prod[$planet->id][1] = floor($planet->prodCrystal);
    $val_prod[$planet->id][2] = floor($planet->prodPlastic);
    $val_prod[$planet->id][3] = floor($planet->prodFuel);
    $val_prod[$planet->id][4] = floor($planet->prodFood);
    $val_prod[$planet->id][5] = floor($planet->prodPeople);

    for ($x = 0; $x < 6; $x++) {
        $max_prod[$x] = max($max_prod[$x], $val_prod[$planet->id][$x]);
        $min_prod[$x] = min($min_prod[$x], $val_prod[$planet->id][$x]);
        $tot_prod[$x] += $val_prod[$planet->id][$x];
    }

    //Speichert die aktuellen Speicher in ein Array
    $val_store[$planet->id][0] = floor($planet->storeMetal);
    $val_store[$planet->id][1] = floor($planet->storeCrystal);
    $val_store[$planet->id][2] = floor($planet->storePlastic);
    $val_store[$planet->id][3] = floor($planet->storeFuel);
    $val_store[$planet->id][4] = floor($planet->storeFood);
    $val_store[$planet->id][5] = floor($planet->peoplePlace);

    //Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

    //Titan
    if ($planet->prodMetal > 0) {
        if ($planet->storeMetal - $planet->resMetal > 0) {
            $val_time[$planet->id][0] = ceil(($planet->storeMetal - $planet->resMetal) / $planet->prodMetal * 3600);
        } else {
            $val_time[$planet->id][0] = 0;
        }
    } else {
        $val_time[$planet->id][0] = 0;
    }

    //Silizium
    if ($planet->prodCrystal > 0) {
        if ($planet->storeCrystal - $planet->resCrystal > 0) {
            $val_time[$planet->id][1] = ceil(($planet->storeCrystal - $planet->resCrystal) / $planet->prodCrystal * 3600);
        } else {
            $val_time[$planet->id][1] = 0;
        }
    } else {
        $val_time[$planet->id][1] = 0;
    }

    //PVC
    if ($planet->prodPlastic > 0) {
        if ($planet->storePlastic - $planet->resPlastic > 0) {
            $val_time[$planet->id][2] = ceil(($planet->storePlastic - $planet->resPlastic) / $planet->prodPlastic * 3600);
        } else {
            $val_time[$planet->id][2] = 0;
        }
    } else {
        $val_time[$planet->id][2] = 0;
    }

    //Tritium
    if ($planet->prodFuel > 0) {
        if ($planet->storeFuel - $planet->resFuel > 0) {
            $val_time[$planet->id][3] = ceil(($planet->storeFuel - $planet->resFuel) / $planet->prodFuel * 3600);
        } else {
            $val_time[$planet->id][3] = 0;
        }
    } else {
        $val_time[$planet->id][3] = 0;
    }

    //Nahrung
    if ($planet->prodFood > 0) {
        if ($planet->storeFood - $planet->resFood > 0) {
            $val_time[$planet->id][4] = ceil(($planet->storeFood - $planet->resFood) / $planet->prodFood * 3600);
        } else {
            $val_time[$planet->id][4] = 0;
        }
    } else {
        $val_time[$planet->id][4] = 0;
    }

    //Bewohner
    if ($planet->prodPeople > 0) {
        if ($planet->peoplePlace - $planet->people > 0) {
            $val_time[$planet->id][5] = ceil(($planet->peoplePlace - $planet->people) / $planet->prodPeople * 3600);
        } else {
            $val_time[$planet->id][5] = 0;
        }
    } else {
        $val_time[$planet->id][5] = 0;
    }
}

//
// Rohstoffe/Bewohner und Speicher
//

tableStart("Rohstoffe und Bewohner");
echo '<tr><th>Name:</th>
<th>' . RES_METAL . '</th>
<th>' . RES_CRYSTAL . '</th>
<th>' . RES_PLASTIC . '</th>
<th>' . RES_FUEL . '</th>
<th>' . RES_FOOD . '</th>
<th>Bewohner</th></tr>';
foreach ($planets as $planet) {
    echo '<tr><td><a href="?page=economy&amp;change_entity=' . $planet->id . '">' . $planet->name . '</a></td>';
    for ($x = 0; $x < 6; $x++) {
        echo '<td';
        if ($max_res[$x] == $val_res[$planet->id][$x]) {
            echo ' style="color:#0f0"';
        } elseif ($min_res[$x] == $val_res[$planet->id][$x]) {
            echo ' style="color:#f00"';
        } else {
            echo ' ';
        }

        //Der Speicher ist noch nicht gefüllt
        if ($val_res[$planet->id][$x] < $val_store[$planet->id][$x] && $val_time[$planet->id][$x] != 0) {
            $capacity = $cp->people_place;
            if ($capacity < 200) {
                $capacity = 200;
            }

            $people_div = $cp->people * (($config->getFloat('people_multiply')  + $cp->typePopulation + $cu->race->population + $cp->starPopulation + $cu->specialist->population - 4) * (1 - ($cp->people / ($capacity + 1))) / 24);

            if ($x < 5) {
                echo ' ' . tm("Speicher", "Speicher voll in " . StringUtils::formatTimespan($val_time[$planet->id][$x]) . "") . '> ';
            } else {
                echo ' ' . tm("Wachstum", "Wachstum pro Stunde: " . round($people_div) . "") . '> ';
            }

            if ($val_time[$planet->id][$x] < 43200) {
                echo '<i>';
            }
            echo StringUtils::formatNumber($val_res[$planet->id][$x]);
            if ($val_time[$planet->id][$x] < 43200) {
                echo '</i>';
            }
            echo '</td>';
        }
        //Speicher Gefüllt
        else {
            echo ' ' . tm("Speicher", "Speicher voll!") . '';
            echo ' style="" ';
            echo '><b>' . StringUtils::formatNumber($val_res[$planet->id][$x]) . '</b></td>';
        }
    }
    echo '</tr>';
    $cnt_res++;
}
echo '<tr><td colspan="7"></td></tr>';
echo '<tr><th>Total</th>';
for ($x = 0; $x < 6; $x++)
    echo '<td>' . StringUtils::formatNumber($tot_res[$x]) . '</td>';
echo '</tr><tr><th>Durchschnitt</th>';
for ($x = 0; $x < 6; $x++)
    echo '<th>' . StringUtils::formatNumber($tot_res[$x] / $cnt_res) . '</th>';
echo '</tr>';
tableEnd();

//
// Rohstoffproduktion inkl. Energie
//

// Ersetzt Bewohnerwerte durch Energiewerte
$max_prod[5] = 0;
$min_prod[5] = 9999999999;
$tot_prod[5] = 0;
foreach ($planets as $planet) {
    //Speichert die aktuellen Energieproduktionen in ein Array (Bewohnerproduktion [5] wird überschrieben)
    $val_prod[$planet->id][5] = floor($planet->prodPower - $planet->usePower);

    // Gibt Min. / Max. aus
    $max_prod[5] = max($max_prod[5], $val_prod[$planet->id][5]);
    $min_prod[5] = min($min_prod[5], $val_prod[$planet->id][5]);
    $tot_prod[5] += $val_prod[$planet->id][5];
}

tableStart("Produktion");
echo '<tr><th>Name:</th>
<th>' . RES_METAL . '</th>
<th>' . RES_CRYSTAL . '</th>
<th>' . RES_PLASTIC . '</th>
<th>' . RES_FUEL . '</th>
<th>' . RES_FOOD . '</th>
<th>Energie</th></tr>';
foreach ($planets as $planet) {
    echo '<tr><td><a href="?page=economy&amp;change_entity=' . $planet->id . '">' . $planet->name . '</a></td>';
    for ($x = 0; $x < 6; $x++) {
        echo '<td';
        if ($max_prod[$x] == $val_prod[$planet->id][$x]) {
            echo '  style="color:#0f0"';
        } elseif ($min_prod[$x] == $val_prod[$planet->id][$x]) {
            echo '  style="color:#f00"';
        } else {
            echo ' ';
        }
        echo '>' . StringUtils::formatNumber($val_prod[$planet->id][$x]) . '</td>';
    }
    echo '</tr>';
    $cnt_prod++;
}
echo '<tr><td colspan="7"></td></tr>';
echo '<tr><th>Total</th>';
for ($x = 0; $x < 6; $x++)
    echo '<td>' . StringUtils::formatNumber($tot_prod[$x]) . '</td>';
echo '</tr><tr><th>Durchschnitt</th>';
for ($x = 0; $x < 6; $x++)
    echo '<th>' . StringUtils::formatNumber($tot_prod[$x] / $cnt_prod) . '</th>';
echo '</tr>';
tableEnd();

tableStart("Legende");
echo '<tr>
<td style="color:#f00">Minimum</td>
<td style="color:#0f0">Maximum</td>
<td style="font-style:italic">Speicher bald voll</td>
<td style="font-weight:bold">Speicher voll</td>
</tr>';
tableEnd();

echo '<div><br/>
<input type="button" onclick="document.location=\'?page=economy\'" value="Wirtschaft des aktuellen Planeten anzeigen" />
</div>';
