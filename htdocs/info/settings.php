<?php

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

echo "<h2>Einstellungen</h2>";
HelpUtil::breadCrumbs(array("Einstellungen", "settings"));

$items = [
    "Max. Spieler" => $config->param2Int('enable_register'),
    "Urlaubsmodus Mindestdauer" => $config->getInt('hmode_days'),
    "Einheiten/Userpunkte" => $config->param1Int('points_update'),
    "Userpunkte/Allianzpunkte" => $config->param2Int('points_update'),
    "Tage bis zur endgültigen Löschung eines Accounts" => $config->getInt('user_delete_days'),
    "Spieler werden inaktiv nach (in Tagen)" => $config->getInt('user_inactive_days'),
    "Löschung wegen Inaktivität nach (in Tagen)" => $config->param1Int('user_inactive_days'),
    "Timeout in Sekunden" => $config->getInt('user_timeout'),
    "Globaler Bauzeitfaktor" => $config->getInt('global_time'),
    "Startzeitfaktor" => $config->getFloat('flight_start_time'),
    "Landezeitfaktor" => $config->getFloat('flight_land_time'),
    "Flugzeitfaktor" => $config->getFloat('flight_flight_time'),
    "Verteidigungsbauzeitfaktor" => $config->getFloat('def_build_time'),
    "Gebäudebauzeitfaktor" => $config->getFloat('build_build_time'),
    "Forschungszeitfaktor" => $config->getFloat('res_build_time'),
    "Schiffbauzeitfaktor" => $config->getFloat('ship_build_time'),
    "Minimale Planetentemperatur" => $config->param1Int('planet_temp'),
    "Maximale Planetentemperatur" => $config->param2Int('planet_temp'),
    "Minimale Feldanzahl" => $config->param1Int('planet_fields'),
    "Maximale Feldanzahl" => $config->param2Int('planet_fields'),
    "Minimale Planetenanzahl" => $config->param1Int('num_planets'),
    "Maximale Planetenanzahl" => $config->param2Int('num_planets'),
    "Maximale Planeten/User" => $config->getInt('user_max_planets'),
    "Verteidigungswiederherstellung" => $config->getFloat('def_restore_percent'),
    "Verteidigung ins Trümmerfeld" => $config->getFloat('def_wf_percent'),
    "Schiffe ins Trümmerfeld" => $config->getFloat('ship_wf_percent'),
    "Noobschutz: Minimale Punkte" => $config->getInt('user_attack_min_points'),
    "Noobschutz: Verhältnis %" => $config->getInt('user_attack_percentage'),
    "Dauer eines Krieges (in Stunden)" => $config->getInt('alliance_war_time'),
    "Nahrungsverbrauch pro Arbeiter" => $config->getInt('people_food_require'),
    "Bevölkerungswachstum" => $config->getFloat('people_multiply'),
];

tableStart("Grundeinstellungen");
echo "<tr><th>Name</th>";
echo "<th>Wert</th></tr>";
echo "<tr><td>Spielversion</td>";
echo "<td>" . getAppVersion() . "</td></tr>";
foreach ($items as $key => $value) {
    echo "<tr><td>" . $key . "</td>";
    echo "<td>" . $value . "</td></tr>";
}
tableEnd();
