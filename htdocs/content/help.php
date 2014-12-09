<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//
	
	/**
	* Online-Help and info tables
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	echo '<h1>Hilfe</h1>';
	
	$link = "page=".$page;
	
	// Help page
	if (isset($_GET['site']) && ctype_alsc($_GET['site']) && $_GET['site']!="")
	{
		$site = $_GET['site'];
		if ($site!="")
		{
			if (file_exists(RELATIVE_ROOT."info/$site.php"))
			{
				include (RELATIVE_ROOT."info/$site.php");
			}
			else
			{
				err_msg("Hilfedatei nicht gefunden!");
			}
		}
		return_btn();
	}
	
	// Overview
	else
	{
		echo '<h2>&Uuml;bersicht</h2>';
		
		echo 'Hier findest du Informationen zu verschiedenen Objekten des Spiels:<br/><br/>';

		if (!ADMIN_MODE)
		{
			// Internal pages
			$links = [
				[
					'label' => 'Supportticket eröffnen',
					'url' => '?page=ticket'
				],
				[
					'label' => 'Admin kontaktieren',
					'url' => '?page=contact'
				],
				[
					'label' => 'Changelog',
					'url' => '?page=changelog'
				],
				[
					'label' => 'Credits',
					'url' => '?page=credits'
				]
			];
			tableStart("Resourcen");
			echo '<tr>';
			foreach ($links as $l)
			{
				echo '<td style="text-align:center;width:'.floor(100/count($links)).'%">';
				echo '<a href="'.$l['url'].'">'.$l['label'].'</a>';
				echo '</td>';
			}
			echo '</tr>';
			tableEnd();
			
			// External resources
			$links = [
				[
					'label' => 'Häufig gestellte Fragen',
					'onclick' => HELPCENTER_ONCLICK
				],
				[
					'label' => 'Regeln',
					'onclick' => RULES_ONCLICK
				],
				[
					'label' => 'Forum',
					'url' => FORUM_URL
				],
				[
					'label' => 'Fehler melden',
					'url' => DEVCENTER_PATH
				]
			];
			tableStart("Externe Resourcen");
			echo '<tr>';
			foreach ($links as $l)
			{
				echo '<td style="text-align:center;width:'.floor(100/count($links)).'%">';
				if (isset($l['onclick'])) {
					echo '<a href="javascript:;" onclick="'.$l['onclick'].'">'.$l['label'].'</a>';
				} else {
					echo '<a href="'.$l['url'].'" target="_blank">'.$l['label'].'</a>';
				}
				echo '</td>';
			}
			echo '</tr>';
			tableEnd();
		}
		
		$helpNav = [
			"Datenbank" => [
				"Einstellungen" => array('settings','Grundlegende Einstellungen dieser Runde'),
				"Gebäude" => array('buildings','Liste aller Geb&auml;ude'),
				"Planeten" => array('planets','Liste aller Planeten'),
				"Raketen" => array('missiles','Liste aller Raketen'),
				"Rassen" => array('races','Liste aller Rassen'),
				"Ressourcen" => array('resources','Liste aller Ressourcen'),
				"Rohstoffkurse" => array('rates','Welche Werte die Rohstoffe akuell haben'),
				"Schiffe" => array('shipyard','Liste aller Schiffe'),
				"Schiffsaktionen" => array('action','Die verschiedenen Aktionen in der &Uuml;bersicht'),
				"Spezialisten" => array('specialists','Was man mit Spezialisten machen kann'),
				"Sterne" => array('stars','Liste aller Sterne'),
				"Technologien" => array('research','Liste aller Technologien'),
				"Verteidigung" => array('defense','Liste aller Verteidigungsanlagen')
			],
			"Spielmechanismen" => [
				"Bewohner" => array('population','Wie arbeite ich mit Bewohnern und was muss ich beachten?'),
				"Energie" => array('power','Alles über die Energieproduktion'),
				"Kryptocenter" => array('crypto','Wie man fremde Flottenbewegungen scannt?'),
				"Markt" => array('market','Wie der Marktplatz funktioniert?'),
				"Multis und Sitting" => array('multi_sitting','Wie wir Mehrfachaccounts handhaben und wie Sitting funktioniert?'),
				"Raketen" => array('missile_system','Wie das Raketensystem funktioniert?'),
				"Raumkarte" => array('space','Wie ist das Universum aufgebaut?'),
				"Spezialpunkte" => array('specialpoints','Wie man Spezialpunkte und Titel erwerben kann?'),
				"Spionage" => array('spy_info','Wie das Spionagesystem funktioniert?'),
				"Statistik" => array('stats','Was sind Statistiken und wie werden sie berechnet?'),
				"Technikbaum" => array('techtree','Wie lese ich daraus die Voraussetzungen ab?'),
				"Textformatierung" => array('textformat','Wie man Text formatieren kann (BBcode)?'),
				"Urlaubsmodus" => array('u_mod','Was das ist und wie es funktioniert?'),
				"Wärme- und Kältebonus" => array('tempbonus','Welche Auswirkungen hat die Planetentemperatur?')
			]
		];  
		foreach ($helpNav as $cat => $data)
		{
			tableStart($cat);
			foreach ($data as $title=>$item)
			{
				echo '<tr><td width="35%"><b><a href="?'.$link.'&site='.$item[0].'">'.$title.'</b></td><td>'.$item[1].'</td></tr>';
			}
			tableEnd();
		}
	}
?>