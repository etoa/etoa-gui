<?PHP

use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;

echo "<h2>Spezialisten</h2>";
HelpUtil::breadCrumbs(array("Spezialisten", "specialists"));

iBoxStart("Info");
echo "Spezialisten können für eine fixe Zeitdauer angestellt werden und verstärken während dieser Zeit
    dein Imperium in eine bestimmte Richtung. Sie können erst ab einer bestimmten Punktzahl eingestellt werden. Von jedem Typ
    ist nur eine gewisse Anzahl verfügbar. Ebenfalls steigt der Preis mit steigender Nachfrage, nach einem bestimmten Spezialisten. Die
    Einstellung geschieht per sofort, und nach Ablauf der Anstellung verlässt der Spezialist dein Imperium wieder.
    Es kann immer nur ein Spezialist gleichzeitig angestellt werden. Man kann aber einen Spezialisten vorzeitig entlassen,
    um Platz für einen neuen zu schaffen; man erhält in diesem Fall aber keine Ressourcen zurück.";
iBoxEnd();

/** @var SpecialistDataRepository $speciaistRepository */
$speciaistRepository = $app[SpecialistDataRepository::class];
$specialists = $speciaistRepository->getActiveSpecialists();

tableStart("Verfügbare Spezialisten", '95%');
echo "<tr>
    <th>Name</th>
    <th>Beschreibung</th>
    <th>Effekt</th>
    <th>Grundpreis</th>";
echo "</tr>";

foreach ($specialists as $specialist) {
    echo '<tr>';
    echo '<th style="width:140px;">' . $specialist->name . '<br/>
        <span style="font-size:8pt;font-weight:500;">Ab ' . StringUtils::formatNumber($specialist->pointsRequirement) . ' Punkten<br/>
        Anstellbar für ' . $specialist->days . ' Tage</span></th>';
    echo '<td>' . $specialist->description . '</td>';
    echo '<td style="width:220px;">';
    $bonus = '';
    if ($specialist->prodMetal !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->prodMetal, true) . ' ' . ResourceNames::METAL . 'produktion<br/>';
    }
    if ($specialist->prodCrystal !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->prodCrystal, true) . ' ' . ResourceNames::CRYSTAL . 'produktion<br/>';
    }
    if ($specialist->prodPlastic !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->prodPlastic, true) . ' ' . ResourceNames::PLASTIC . 'produktion<br/>';
    }
    if ($specialist->prodFuel !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->prodFuel, true) . ' ' . ResourceNames::FUEL . 'produktion<br/>';
    }
    if ($specialist->prodFood !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->prodFood, true) . ' ' . ResourceNames::FOOD . 'sproduktion<br/>';
    }
    if ($specialist->prodPower !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->prodPower, true) . ' Stromerzeugung<br/>';
    }
    if ($specialist->prodPeople !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->prodPeople, true) . ' Bevölkerungswachstum<br/>';
    }
    if ($specialist->timeTechnologies !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->timeTechnologies, true, true) . ' Forschungszeit<br/>';
    }
    if ($specialist->timeBuildings !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->timeBuildings, true, true) . ' Gebäudebauzeit<br/>';
    }
    if ($specialist->timeDefense !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->timeDefense, true, true) . ' Verteidigungsbauzeit<br/>';
    }
    if ($specialist->timeShips !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->timeShips, true, true) . ' Schiffbauzeit<br/>';
    }
    if ($specialist->costsBuildings !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->costsBuildings, true, true) . ' Gebäudekosten<br/>';
    }
    if ($specialist->costsDefense !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->costsDefense, true, true) . ' Verteidigungskosten<br/>';
    }
    if ($specialist->costsShips !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->costsShips, true, true) . ' Schiffbaukosten<br/>';
    }
    if ($specialist->costsTechnologies !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->costsTechnologies, true, true) . ' Forschungskosten<br/>';
    }
    if ($specialist->fleetSpeed !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->fleetSpeed, true) . ' Flottengeschwindigkeit<br/>';
    }
    if ($specialist->fleetMax !== 0) {
        $bonus .= '<span style="color:#0f0;">+' . $specialist->fleetMax . '</span> zusätzliche Flotten<br/>';
    }
    if ($specialist->defenseRepair !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->defenseRepair, true) . ' Verteidigungswiederherstellung<br/>';
    }
    if ($specialist->spyLevel !== 0) {
        $bonus .= '<span style="color:#0f0;">+' . $specialist->spyLevel . '</span> zusätzliche Spionagelevel<br/>';
    }
    if ($specialist->tarnLevel !== 0) {
        $bonus .= '<span style="color:#0f0;">+' . $specialist->tarnLevel . '</span> zusätzliche Tarnlevel<br/>';
    }
    if ($specialist->tradeTime !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->tradeTime, true) . ' Handelsflottengeschwindigkeit<br/>';
    }
    if ($specialist->tradeBonus !== 1.0) {
        $bonus .= StringUtils::formatPercentString($specialist->tradeBonus, true, true) . ' Handelskosten<br/>';
    }

    echo $bonus;
    echo '</td>';
    echo '<td style="width:120px;">';
    echo StringUtils::formatNumber($specialist->costsMetal) . ' ' . ResourceNames::METAL . '<br/>';
    echo StringUtils::formatNumber($specialist->costsCrystal) . ' ' . ResourceNames::CRYSTAL . '<br/>';
    echo StringUtils::formatNumber($specialist->costsPlastic) . ' ' . ResourceNames::PLASTIC . '<br/>';
    echo StringUtils::formatNumber($specialist->costsFuel) . ' ' . ResourceNames::FUEL . '<br/>';
    echo StringUtils::formatNumber($specialist->costsFood) . ' ' . ResourceNames::FOOD . '<br/>';
    echo '</td>';
    echo '</tr>';
}
tableEnd();
