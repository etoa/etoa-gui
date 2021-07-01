<?PHP

use EtoA\Specialist\SpecialistDataRepository;

echo "<h2>Spezialisten</h2>";
	HelpUtil::breadCrumbs(array("Spezialisten","specialists"));

	iBoxStart("Info");
	echo "Spezialisten können für eine fixe Zeitdauer angestellt werden und verstärken während dieser Zeit
	dein Imperium in eine bestimmte Richtung. Sie können erst ab einer bestimmten Punktzahl eingestellt werden. Von jedem Typ
	ist nur eine gewisse Anzahl verfügbar. Ebenfalls steigt der Preis mit steigender Nachfrage, nach einem bestimmten Spezialisten. Die
	Einstellung geschieht per sofort, und nach Ablauf der Anstellung verlässt der Spezialist dein Imperium wieder.
	Es kann immer nur ein Spezialist gleichzeitig angestellt werden. Man kann aber einen Spezialisten vorzeitig entlassen,
	um Platz für einen neuen zu schaffen; man erhält in diesem Fall aber keine Ressourcen zurück.";
	iBoxEnd();

	/** @var SpecialistDataRepository */
	$speciaistRepository = $app[SpecialistDataRepository::class];
	$specialists = $speciaistRepository->getActiveSpecialists();

	tableStart("Verfügbare Spezialisten",'95%');
	echo "<tr>
	<th>Name</th>
	<th>Beschreibung</th>
	<th>Effekt</th>
	<th>Grundpreis</th>";
	echo "</tr>";

	foreach ($specialists as $specialist) {
		echo '<tr>';
		echo '<th style="width:140px;">'.$specialist->name.'<br/>
		<span style="font-size:8pt;font-weight:500;">Ab '.nf($specialist->pointsRequirement).' Punkten<br/>
		Anstellbar für '.$specialist->days.' Tage</span></th>';
		echo '<td>'.$specialist->description.'</td>';
		echo '<td style="width:220px;">';
		$bonus='';
		if ($specialist->prodMetal !== 1.0) {
			$bonus.= get_percent_string($specialist->prodMetal,1).' '.RES_METAL.'produktion<br/>';
		}
		if ($specialist->prodCrystal !== 1.0) {
			$bonus.= get_percent_string($specialist->prodCrystal,1).' '.RES_CRYSTAL.'produktion<br/>';
		}
		if ($specialist->prodPlastic !== 1.0) {
			$bonus.= get_percent_string($specialist->prodPlastic,1).' '.RES_PLASTIC.'produktion<br/>';
		}
		if ($specialist->prodFuel !== 1.0) {
			$bonus.= get_percent_string($specialist->prodFuel,1).' '.RES_FUEL.'produktion<br/>';
		}
		if ($specialist->prodFood !== 1.0) {
			$bonus.= get_percent_string($specialist->prodFood,1).' '.RES_FOOD.'sproduktion<br/>';
		}
		if ($specialist->prodPower !== 1.0) {
			$bonus.= get_percent_string($specialist->prodPower,1).' Stromerzeugung<br/>';
		}
		if ($specialist->prodPeople !== 1.0) {
			$bonus.= get_percent_string($specialist->prodPeople,1).' Bevölkerungswachstum<br/>';
		}
		if ($specialist->timeTechnologies !== 1.0) {
			$bonus.= get_percent_string($specialist->timeTechnologies,1,1).' Forschungszeit<br/>';
		}
		if ($specialist->timeBuildings !== 1.0) {
			$bonus.= get_percent_string($specialist->timeBuildings,1,1).' Gebäudebauzeit<br/>';
		}
		if ($specialist->timeDefense !== 1.0) {
			$bonus.= get_percent_string($specialist->timeDefense,1,1).' Verteidigungsbauzeit<br/>';
		}
		if ($specialist->timeShips !== 1.0) {
			$bonus.= get_percent_string($specialist->timeShips,1,1).' Schiffbauzeit<br/>';
		}
		if ($specialist->costsBuildings !== 1.0) {
			$bonus.= get_percent_string($specialist->costsBuildings,1,1).' Gebäudekosten<br/>';
		}
		if ($specialist->costsDefense !== 1.0) {
			$bonus.= get_percent_string($specialist->costsDefense,1,1).' Verteidigungskosten<br/>';
		}
		if ($specialist->costsShips !== 1.0) {
			$bonus.= get_percent_string($specialist->costsShips,1,1).' Schiffbaukosten<br/>';
		}
		if ($specialist->costsTechnologies !== 1.0) {
			$bonus.= get_percent_string($specialist->costsTechnologies,1,1).' Forschungskosten<br/>';
		}
		if ($specialist->fleetSpeed !== 1.0) {
			$bonus.= get_percent_string($specialist->fleetSpeed,1).' Flottengeschwindigkeit<br/>';
		}
		if ($specialist->fleetMax !== 0) {
			$bonus.= '<span style="color:#0f0;">+'.$specialist->fleetMax.'</span> zusätzliche Flotten<br/>';
		}
		if ($specialist->defenseRepair !== 1.0) {
			$bonus.= get_percent_string($specialist->defenseRepair,1).' Verteidigungswiederherstellung<br/>';
		}
		if ($specialist->spyLevel !== 0) {
			$bonus.= '<span style="color:#0f0;">+'.$specialist->spyLevel.'</span> zusätzliche Spionagelevel<br/>';
		}
		if ($specialist->tarnLevel !== 0) {
			$bonus.= '<span style="color:#0f0;">+'.$specialist->tarnLevel.'</span> zusätzliche Tarnlevel<br/>';
		}
		if ($specialist->tradeTime !== 1.0) {
			$bonus.= get_percent_string($specialist->tradeTime,1).' Handelsflottengeschwindigkeit<br/>';
		}
		if ($specialist->tradeBonus !== 1.0) {
			$bonus.= get_percent_string($specialist->tradeBonus,1,1).' Handelskosten<br/>';
		}

		echo $bonus;
		echo '</td>';
		echo '<td style="width:120px;">';
		echo nf($specialist->costsMetal).' '.RES_METAL.'<br/>';
		echo nf($specialist->costsCrystal).' '.RES_CRYSTAL.'<br/>';
		echo nf($specialist->costsPlastic).' '.RES_PLASTIC.'<br/>';
		echo nf($specialist->costsFuel).' '.RES_FUEL.'<br/>';
		echo nf($specialist->costsFood).' '.RES_FOOD.'<br/>';
		echo '</td>';
		echo '</tr>';
	}
	tableEnd();

