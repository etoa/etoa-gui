-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 18. Oktober 2012 um 20:48
-- Server Version: 5.1.63
-- PHP-Version: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Daten für Tabelle `alliance_buildings`
--

INSERT INTO `alliance_buildings` (`alliance_building_id`, `alliance_building_name`, `alliance_building_shortcomment`, `alliance_building_longcomment`, `alliance_building_costs_metal`, `alliance_building_costs_crystal`, `alliance_building_costs_plastic`, `alliance_building_costs_fuel`, `alliance_building_costs_food`, `alliance_building_build_time`, `alliance_building_costs_factor`, `alliance_building_last_level`, `alliance_building_show`, `alliance_building_needed_id`, `alliance_building_needed_level`) VALUES
(1, 'Zentrale', '', 'Die Zentrale ist das Hauptgebäude der Allianzbasis. Baut dieses aus um weitere Objekte zu erhalten.', 100000, 100000, 70000, 35000, 50000, 3600, '2.00', 4, 1, 0, 0),
(2, 'Handelszentrum', '', 'Das Handelszentrum ermöglicht den risikofreien Handel unter den Allianzmitgliedern. Dieser erlaubt es die Angebote auf einem abgeschotteten Markt anzubieten, auf welchen nur Allianzmitglieder zutritt haben.', 300000, 250000, 350000, 35000, 0, 18000, '2.00', 10, 1, 1, 1),
(3, 'Schiffswerft', '', 'Die Allianzschiffswerft produziert einzelne Schiffsteile, mit welchen ein ganzes Schiff hergestellt werden kann. Je weiter die Werft ausgebaut ist, desto schneller können die Teile hergestellt werden und desto mehr Baupläne für Schiffstypen werden konstruiert.', 145000, 102000, 117000, 80000, 0, 15000, '2.50', 99, 1, 4, 1),
(4, 'Flottenkontrolle', '', 'Flottenkontrolletext hierrein', 100000, 75000, 50000, 25000, 0, 15000, '2.01', 99, 1, 1, 1),
(5, 'Forschungslabor', '', 'Bau dir was', 60000, 90000, 45000, 35000, 0, 15000, '2.00', 99, 1, 1, 1),
(6, 'Kryptocenter', '', '', 250000, 2250000, 250000, 3250000, 0, 20000, '3.00', 10, 1, 1, 2);

--
-- Daten für Tabelle `alliance_rights`
--

INSERT INTO `alliance_rights` (`right_id`, `right_key`, `right_desc`) VALUES
(1, 'editdata', 'Allianzdaten (Name, Tag, Beschreibung, Bild, Link) ändern'),
(2, 'viewmembers', 'Mitglieder anschauen'),
(3, 'applicationtemplate', 'Bewerbungsvorlage bearbeiten'),
(4, 'history', 'Allianzgeschichte betrachten'),
(5, 'massmail', 'Allianzinternes Rundmail versenden'),
(6, 'ranks', 'Allianzränge bearbeiten'),
(7, 'alliancenews', 'Allianznews (Rathaus) verfassen'),
(8, 'relations', 'Allianzbeziehungen (Bündnisse / Kriege) verwalten'),
(10, 'allianceboard', 'Forum verwalten'),
(11, 'editmembers', 'Mitglieder verwalten'),
(12, 'applications', 'Bewerbungen bearbeiten'),
(13, 'polls', 'Umfrage erstellen'),
(14, 'fleetminister', 'Allianzflotten bearbeiten'),
(15, 'wings', 'Wings hinzufügen und entfernen'),
(16, 'buildminister', 'Allianzbasis ausbauen (Gebäude, Technologien)'),
(17, 'cryptominister', 'Kryptocenter benutzen');

--
-- Daten für Tabelle `alliance_technologies`
--

INSERT INTO `alliance_technologies` (`alliance_tech_id`, `alliance_tech_name`, `alliance_tech_shortcomment`, `alliance_tech_longcomment`, `alliance_tech_costs_metal`, `alliance_tech_costs_crystal`, `alliance_tech_costs_plastic`, `alliance_tech_costs_fuel`, `alliance_tech_costs_food`, `alliance_tech_build_time`, `alliance_tech_costs_factor`, `alliance_tech_last_level`, `alliance_tech_show`, `alliance_tech_needed_id`, `alliance_tech_needed_level`) VALUES
(4, 'Tarntechnik', 'In Zeiten einer neuen Ära mit grösseren Flottenverbänden bestehend aus mehreren Teilflotten, reichte die gewöhnliche Tarntechnik nicht mehr aus. So setzten sich Spieler zusammen und teilten ihr Wissen und ihre Ressourcen, um auch diese Hürde zu überwinden.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben Allianzverbände für den Gegner unentdeckt.', 'In Zeiten einer neuen Ära mit grösseren Flottenverbänden bestehend aus mehreren Teilflotten, reichte die gewöhnliche Tarntechnik nicht mehr aus. So setzten sich Spieler zusammen und teilten ihr Wissen und ihre Ressourcen, um auch diese Hürde zu überwinden.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben Allianzverbände für den Gegner unentdeckt.', 75000, 25000, 50000, 50000, 50000, 900, '1.60', 50, 1, 0, 0),
(5, 'Waffentechnik', '', '', 0, 0, 0, 0, 0, 0, '1.00', 50, 1, 5, 2),
(6, 'Schutzschilder', '', '', 0, 0, 0, 0, 0, 0, '1.00', 50, 1, 5, 2),
(7, 'Panzerung', '', '', 0, 0, 0, 0, 0, 0, '1.00', 50, 1, 5, 2),
(8, 'Spionagetechnik', '', '', 0, 0, 0, 0, 0, 0, '1.00', 0, 1, 5, 1),
(9, 'Antriebstechnologie', '', '', 0, 0, 0, 0, 0, 0, '1.00', 0, 1, 5, 11);

--
-- Daten für Tabelle `buildings`
--

INSERT INTO `buildings` (`building_id`, `building_name`, `building_type_id`, `building_shortcomment`, `building_longcomment`, `building_costs_metal`, `building_costs_crystal`, `building_costs_fuel`, `building_costs_plastic`, `building_costs_food`, `building_costs_power`, `building_build_costs_factor`, `building_demolish_costs_factor`, `building_power_use`, `building_power_req`, `building_fuel_use`, `building_prod_metal`, `building_prod_crystal`, `building_prod_plastic`, `building_prod_fuel`, `building_prod_food`, `building_prod_power`, `building_production_factor`, `building_store_metal`, `building_store_crystal`, `building_store_plastic`, `building_store_fuel`, `building_store_food`, `building_store_factor`, `building_people_place`, `building_last_level`, `building_fields`, `building_show`, `building_order`, `building_fieldsprovide`, `building_workplace`, `building_bunker_res`, `building_bunker_fleet_count`, `building_bunker_fleet_space`) VALUES
(1, 'Titanmine', 2, 'Produziert Titan.', 'Produziert Titan.', 100, 45, 0, 0, 0, 0, '1.90', '0.20', 10, 0, 0, 65, 0, 0, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 0, 0, 0, 0, 0, 0),
(2, 'Siliziummine', 2, 'Produziert Silizium.', 'Produziert Silizium.', 150, 50, 0, 0, 0, 0, '1.90', '0.20', 20, 0, 0, 0, 50, 0, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 1, 0, 0, 0, 0, 0),
(3, 'Chemiefabrik', 2, 'Produziert PVC.', 'Produziert PVC.', 100, 80, 0, 0, 0, 0, '1.90', '0.20', 20, 0, 0, 0, 0, 40, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 3, 1, 3, 0, 0, 0, 0, 0),
(4, 'Tritiumsynthesizer', 2, 'Produziert Tritium.', 'Produziert Tritium.', 160, 110, 0, 50, 0, 0, '2.00', '0.20', 50, 0, 0, 0, 0, 0, 28, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 3, 1, 4, 0, 0, 0, 0, 0),
(5, 'Gewächshaus', 2, 'Produziert Nahrung.', 'Produziert Nahrung.', 80, 100, 0, 0, 0, 0, '1.90', '0.20', 5, 0, 0, 0, 0, 0, 0, 40, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 5, 0, 0, 0, 0, 0),
(6, 'Planetenbasis', 1, 'Das Grundgebäude jedes Planeten bietet Platz für Bewohner, Lagerräume und produziert Rohstoffe.', 'Die Planetenbasis ist die Schaltzentrale aller Aktivitäten auf deinem Planeten. Du musst zuerst eine Planetenbasis bauen, danach kannst du alle weiteren Gebäude errichten. Die Planetenbasis liefert ein Grundeinkommen an Rohstoffen und eine minimale Energieversorgung durch ein integriertes Erdwärmekraftwerk. Es ist jedoch sinnvoll, Minen und Fabriken zu bauen, um die Rohstoffproduktion zu steigern.', 500, 250, 0, 300, 0, 0, '2.00', '0.00', 50, 0, 0, 50, 20, 10, 5, 15, 200, '1.00', 100000, 100000, 100000, 100000, 100000, '1.00', 300, 1, 5, 1, 0, 0, 1, 0, 0, 0),
(7, 'Wohnmodul', 1, 'Mit einem Wohnmodul wird die Kapazität für Bewohner erhöht.', 'Mit steigendem Wachstum eines Planeten werden immer mehr Gebäude errichtet und ausgebaut, wofür mehr Arbeiter benötigt werden.\r\nEin Ausbau des Wohnmoduls ist deshalb wichtig, welches die Kapazität der Bewohner erhöht und so potenzielle Arbeiter freigibt.', 50, 30, 0, 150, 0, 0, '2.00', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '1.80', 300, 50, 1, 1, 1, 0, 0, 0, 0, 0),
(8, 'Forschungslabor', 1, 'Im Labor werden neue Techniken entwickelt. Höhere Stufen senken die Forschungszeit.', 'Damit Schiffe und Spezialgebäude errichten werden können, braucht es ein Forschungslabor, in dem die Wissenschaftler neue Technologien entwickeln. Je höher das Forschungslabor ausgebaut ist, desto mehr Technologien können entwickelt werden. Erforschte Technologien gelten automatisch auf allen Planeten deines Reiches.\r\nAusserdem senkt das Forschungslabor die Forschungszeit, jedoch erst ab einer bestimmten Stufe!\r\nUm zur Elite auf dem Gebiet der Technologien zu gehören, ist ein guter Ausbau des Forschungslabors unverzichtbar. ', 500, 700, 210, 350, 0, 0, '2.00', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 4, 1, 2, 0, 1, 0, 0, 0),
(9, 'Schiffswerft', 1, 'In der Werft werden alle Raumschiffe gebaut.Höhere Stufen senken die Bauzeit.', 'In der Schiffswerft werden Schiffe gebaut, die im Krieg oder für den Handel mit anderen Völkern eingesetzt werden können. Je höher die Werft, desto mehr Schiffe können gebaut werden.\r\nAusserdem senkt die Schiffswerft die Bauzeit der Schiffe, jedoch erst ab einer bestimmten Stufe!', 900, 680, 510, 780, 0, 0, '1.80', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 6, 1, 3, 0, 1, 0, 0, 0),
(10, 'Waffenfabrik', 1, 'In der Waffenfabrik werden Verteidigungsanlagen gebaut. Höhere Stufen senken die Bauzeit.', 'Die Waffenfabrik bietet jedem Volk die Möglichkeit, Verteidigungsanlagen gegen feindliche Angriffe zu errichten.\r\nVerteidigungsanlagen funktionieren, wenn sie mal gebaut sind, selbstständig und eröffnen das Feuer gegen angreifende Flotten. \r\nAusserdem senkt der Ausbau der Waffenfabrik die Bauzeit der Verteidigungsanlagen, jedoch erst ab einer bestimmten Stufe!', 750, 480, 320, 500, 0, 0, '1.80', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 5, 1, 4, 0, 1, 0, 0, 0),
(11, 'Flottenkontrolle', 1, 'Koordiniert deine Flotten. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können starten.', 'Die Flottenkontrolle ist ein Gebäude voller Überwachungscomputer, Leitsystemen, Empfänger- sowie Sendeanlagen. Mit Hilfe der Flottenkontrolle werden Flotten gesteuert. Sie ist ebenfalls Voraussetzung für den Bau von Schiffen. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können vom Planeten gestartet werden.', 1100, 750, 0, 500, 0, 0, '1.80', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 5, 1, 5, 0, 0, 0, 0, 0),
(12, 'Windkraftwerk', 3, 'Nicht sehr effizientes und relativ teures Kraftwerk, welches Energie mit Hilfe des Windes gewinnt.', 'Windenergieanlagen wandeln mit Hilfe des Rotors die Windenergie in eine Drehbewegung um. Mit Hilfe von Generatoren wird diese Drehbewegung in eine elektrische Energie umgewandelt, welche dann in das Stromnetz des Planeten eingespeist wird.\r\nWindenergie ist eine alternative Energie, jedoch noch nicht sehr effizient. Der Bau ist relativ teuer und die Produktion nur mittelmässig.', 250, 50, 5, 80, 0, 0, '1.90', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 80, '1.65', 0, 0, 0, 0, 0, '0.00', 0, 50, 1, 1, 0, 0, 0, 0, 0, 0),
(13, 'Solarkraftwerk', 3, 'Solarkraftwerke gewinnen Energie durch Sonnenlicht. ', 'In einer Solarstromanlage findet die Umwandlung von Sonnenenergie in elektrische Energie statt. Eine Solarstromanlage besteht aus mehreren Komponenten. Der Generator empfängt und wandelt die Lichtenergie in elektrische Energie um. Als Empfänger dient die Solarzelle. Hierbei kommen Spiegel oder Linsensysteme zum Einsatz, die die Strahlung auf die Zellen umleiten und konzentrieren.\r\nEiner der wichtigsten Bestandteile einer Solarzelle ist das Metal Silizium. Dieses hat die Eigenschaft, unter Bestrahlung von Licht eine elektrische Spannung erzeugen zu können.\r\nDiese Methode für die Energieerzeugung ist noch sehr jung und unerforscht. Wegen den grossen Mengen an Silizium die es benötigt, wird das Solarkraftwerk oft als unrentabel bezeichnet, jedoch kann sich die Energiegewinnung daraus sehen lassen.', 150, 250, 0, 160, 0, 0, '1.90', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 100, '1.70', 0, 0, 0, 0, 0, '0.00', 0, 50, 1, 1, 2, 0, 0, 0, 0, 0),
(14, 'Fusionskraftwerk', 3, 'Durch die Fusion von Tritium und Deuterium werden im Fusionskraftwerk riesige Energiemengen gewonnen. ', 'Als Kernfusion wird der Prozess des Verschmelzens zweier Atomkerne zu einem schwereren Kern bezeichnet. Besonders viel Energie wird frei, wenn Deuterium und Tritium miteinander verschmelzen. Hier beträgt der Massendefekt fast 4 Promille. Die fehlende Masse wird aufgrund der Äquivalenz von Masse und Energie aus Einsteins Gleichung E=mc^2 als kinetische Energie auf die Reaktionsprodukte übertragen. Da c^2 eine sehr grosse Zahl ist, setzt schon die Fusion kleiner Mengen von Deuterium und Tritium gewaltige Energiemengen frei.\r\nDie Effizienz dieses Kraftwerkes wird pro Stufe immer wie grösser! Die Energie, welche das Kraftwerk in den ersten Stufen freisetzt, wird oft als normal angesehen, jedoch stellt sich schon sehr früh heraus, dass beim weiteren Ausbau des Fusionskraftwerkes die Effizient beachtlich gesteigert wird!', 3000, 4900, 8300, 1500, 0, 0, '1.90', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 1500, '1.95', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 5, 0, 0, 0, 0, 0),
(15, 'Gezeitenkraftwerk', 3, 'Dieses Kraftwerk gewinnt Energie durch den Hubunterschied der Gezeiten.', 'Ein Gezeitenkraftwerk ist ein Kraftwerk zur Produktion von elektrischem Strom, das durch die Tide angetrieben wird. Sie sind eine Sonderform der Wasserkraftwerke.\r\nGezeitenkraftwerke werden an Meeresbuchten und in Ästuaren errichtet, die einen besonders hohen Tidenhub haben. Dazu wird die entsprechende Bucht durch einen Deich abgedämmt. Dadurch kann das Wasser der Tidenströme durch die Turbinen strömen, die aufgrund der Gezeitenströme, welche viermal am Tag die Richtung wechseln, auf Zweirichtungsbetrieb eingestellt sind.', 2100, 1000, 500, 2000, 0, 0, '1.85', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 750, '1.75', 0, 0, 0, 0, 0, '0.00', 0, 50, 3, 1, 3, 0, 0, 0, 0, 0),
(16, 'Titanspeicher', 4, 'Lagert Titan.', 'Lagert Titan. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 4000, 100, 0, 100, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 100000, 0, 0, 0, 0, '1.80', 0, 50, 1, 1, 0, 0, 0, 0, 0, 0),
(17, 'Siliziumspeicher', 4, 'Lagert Silizium.', 'Lagert Silizium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 100, 3500, 0, 100, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 100000, 0, 0, 0, '1.80', 0, 50, 1, 1, 1, 0, 0, 0, 0, 0),
(18, 'Lagerhalle', 4, 'Lagert Plastik.', 'Lagert Plastik. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 50, 50, 0, 3750, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 100000, 0, 0, '1.80', 0, 50, 1, 1, 2, 0, 0, 0, 0, 0),
(19, 'Nahrungssilo', 4, 'Lagert Nahrung.', 'Lagert Nahrung.Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 1000, 1000, 0, 1000, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 100000, '1.80', 0, 50, 1, 1, 4, 0, 0, 0, 0, 0),
(20, 'Tritiumsilo', 4, 'Lagert Tritium.', 'Lagert Tritium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 500, 500, 3000, 0, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 100000, 0, '1.80', 0, 50, 1, 1, 3, 0, 0, 0, 0, 0),
(21, 'Marktplatz', 1, 'Auf dem Marktplatz können Schiffe und Rohstoffe gehandelt und ersteigert werden.', 'Der Marktplatz bildet das Zentrum aller Händler in Andromeda.\r\nHand mit Schiffen, Rohstoffen, \r\nJe höher der Marktplatz ausgebaut ist, desto mehr Waren können gleichzeitig angeboten werden.\r\nAusserdem werden mehr Waren zurück erstattet, wenn ein Angebot zurückgezogen wird.\r\nDer Markt kann aber nicht beliebig weit ausgebaut werden, sondern ist durch ein Maximallevel beschränkt.', 30000, 25000, 3500, 35000, 0, 0, '1.50', '1.50', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 10, 4, 1, 6, 0, 0, 0, 0, 0),
(22, 'Orbitalplattform', 1, 'Die Orbitalplattform erhöht den Platz auf einem Planeten und bietet Lagerräume für Ressourcen.', 'Die Orbitalplattform erhöht die Anzahl verfügbarer Felder auf einem Planeten. Dies wird besonders wichtig, wenn ein Planet nicht allzu viele Felder besitzt, oder viele Verteidigungsanlagen errichtet wurden. Ebenfalls befinden sich auf der Plattform zusätzliche Lagerräume für diverse Ressourcen.\r\nPro Ausbaustufe erhöht sich die Anzahl der Felder, ebenso die Grösse der Lagerräume.', 30000, 60000, 50000, 55000, 0, 0, '1.90', '0.00', 100, 0, 0, 0, 0, 0, 0, 0, 0, '1.80', 10000, 15000, 20000, 0, 0, '2.00', 0, 50, 0, 1, 7, 60, 0, 0, 0, 0),
(23, 'Multimine', 2, 'Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie!', 'Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie! Da sie so enorm gross ist, braucht sie viele Felder und kann nur bis zu Stufe 15 gebaut werden.', 5100, 7200, 160, 1100, 0, 0, '2.00', '0.00', 100, 0, 0, 100, 70, 0, 0, 0, 0, '1.80', 50000, 50000, 0, 0, 0, '1.50', 0, 15, 8, 0, 20, 0, 0, 0, 0, 0),
(24, 'Kryptocenter', 1, 'Das Kryptocenter analysiert Kommunikationskanäle um Infos über fremde Flottenbewegungen zu erhalten. ', 'Das Kryptocenter analysiert Kommunikationskanäle zwischen Flotten und Bodenstationen, um Aufschluss über fremde Flottenbewegungen zu erhalten. Mit Hilfe eines riesigen unterirdischen Rechenzentrums werden die gewonnenen Daten analysiert, entschlüsselt und ausgewertet, deshalb braucht diese Anlage enorm viel Energie zum Bau und zum  Betrieb. Je höher der Level dieser Anlage, desto grösser ist auch die Reichweite des Scanners.', 50000, 450000, 650000, 50000, 0, 1000000, '1.50', '0.10', 0, 1000000, 0, 0, 0, 0, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 10, 5, 0, 11, 0, 0, 0, 0, 0),
(25, 'Raketensilo', 1, 'Im Raketensilo werden Raketen gebaut unt gestartet, um gegnerische Verteidigungsanlagen zu beschädigen.', 'Im Raketensilo werden Raketen gelagert und gestartet, mit denen man gegnerische Verteidigungsanlagen beschädigen oder ausser Gefecht setzen kann, sowie Raketen um gegnerische Raketen abzufangen. Je grösser das Silo ist, desto mehr Raketen können darin gelagert werden.', 100000, 50000, 70000, 20000, 0, 20000, '1.40', '0.00', 50000, 0, 300, 0, 0, 0, 0, 0, 0, '1.10', 0, 0, 0, 0, 0, '0.00', 0, 20, 2, 1, 10, 0, 0, 0, 0, 0),
(26, 'Rohstoffbunker', 1, 'In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden.', 'In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden, so dass sie nicht geklaut werden können. Das Verstecken geschieht automatisch. Auf Stufe 1 können 5000 Resourcen versteckt werden, pro Stufe verdoppelt sich diese Anzahl.', 5000, 1000, 0, 2000, 0, 0, '2.00', '0.50', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '2.00', 0, 10, 0, 1, 8, 0, 0, 5000, 0, 0),
(27, 'Flottenbunker', 1, '', '', 20000, 10000, 0, 5000, 0, 0, '2.00', '0.50', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '2.00', 0, 10, 0, 1, 9, 0, 0, 0, 5, 2500);

--
-- Daten für Tabelle `building_requirements`
--

INSERT INTO `building_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 1, 6, 0, 1),
(2, 2, 6, 0, 1),
(5, 5, 6, 0, 1),
(6, 7, 6, 0, 1),
(11, 12, 6, 0, 1),
(12, 3, 1, 0, 2),
(13, 3, 12, 0, 3),
(14, 4, 12, 0, 4),
(15, 4, 2, 0, 3),
(16, 8, 12, 0, 5),
(17, 10, 8, 0, 2),
(18, 9, 10, 0, 2),
(19, 9, 8, 0, 4),
(20, 11, 9, 0, 1),
(21, 13, 0, 3, 3),
(22, 14, 0, 3, 8),
(23, 15, 0, 3, 5),
(24, 16, 1, 0, 3),
(25, 17, 2, 0, 3),
(26, 18, 3, 0, 4),
(27, 19, 5, 0, 3),
(28, 20, 4, 0, 4),
(29, 13, 6, 0, 1),
(30, 14, 6, 0, 1),
(31, 15, 6, 0, 1),
(32, 4, 1, 0, 1),
(33, 21, 8, 0, 7),
(35, 22, 0, 3, 10),
(36, 22, 10, 10, 8),
(37, 23, 1, 0, 10),
(38, 23, 2, 0, 9),
(39, 23, 14, 0, 6),
(40, 23, 0, 3, 8),
(41, 23, 0, 16, 3),
(50, 25, 0, 24, 1),
(51, 25, 11, 0, 12),
(52, 25, 10, 0, 10),
(53, 24, 11, 0, 11),
(54, 24, 14, 0, 5),
(55, 24, 0, 7, 13),
(56, 24, 0, 25, 7),
(57, 26, 16, 0, 1),
(58, 26, 17, 0, 1),
(59, 26, 18, 0, 1),
(60, 26, 19, 0, 1),
(61, 26, 20, 0, 1),
(62, 27, 11, 0, 5),
(63, 27, 0, 3, 5),
(64, 27, 0, 25, 3),
(65, 27, 0, 11, 5);

--
-- Daten für Tabelle `building_types`
--

INSERT INTO `building_types` (`type_id`, `type_name`, `type_order`, `type_color`) VALUES
(1, 'Allgemeine Gebäude', 1, '#ffffff'),
(2, 'Rohstoffgebäude', 2, '#ffffff'),
(3, 'Kraftwerke', 3, '#ffffff'),
(4, 'Speicher', 4, '#ffffff');

--
-- Daten für Tabelle `config`
--

INSERT INTO `config` (`config_id`, `config_name`, `config_value`, `config_param1`, `config_param2`) VALUES
(1, 'roundname', 'Runde X', '', ''),
(2, 'roundurl', 'http://test.dev.etoa.net', '', ''),
(3, 'loginurl', 'http://etoa.ch', '', ''),
(4, 'debug', '0', '', ''),
(5, 'accesslog', '0', '', ''),
(6, 'num_of_sectors', '', '2', '2'),
(7, 'num_of_cells', '', '10', '10'),
(8, 'space_cell_size', '', '40', '40'),
(9, 'num_planets', '', '5', '20'),
(10, 'space_percent_solsys', '50', '', ''),
(11, 'space_percent_asteroids', '12', '', ''),
(12, 'space_percent_nebulas', '12', '', ''),
(13, 'space_percent_wormholes', '12', '', ''),
(14, 'num_planet_images', '5', '', ''),
(15, 'planet_fields', '', '500', '2500'),
(16, 'planet_temp', '20', '-155', '166'),
(17, 'user_timeout', '2400', '', ''),
(18, 'password_minlength', '6', '30', ''),
(20, 'global_time', '12', '', ''),
(21, 'field_squarekm', '11694', '', ''),
(22, 'general_table_offset', '0', '3', ''),
(23, 'res_update', '300', '', ''),
(24, 'cell_length', '300', '', ''),
(25, 'mail_sender', 'no-reply@etoa.ch', '', ''),
(26, 'mail_reply', 'mail@etoa.ch', '', ''),
(27, 'enable_register', '1', '1190446200', '15'),
(28, 'enable_login', '1', '1205485200', ''),
(29, 'round_end', '0', '1205485200', ''),
(30, 'points_update', '3600', '1000', '100'),
(31, 'map_init_sector', '', '1', '1'),
(32, 'statsupdate', '1329645602', '', ''),
(33, 'hmode_days', '2', '', ''),
(34, 'def_store_capacity', '200000', '', ''),
(35, 'user_start_metal', '4000', '', ''),
(36, 'user_start_crystal', '3000', '', ''),
(37, 'user_start_plastic', '2500', '', ''),
(38, 'user_start_fuel', '200', '', ''),
(39, 'user_start_food', '500', '', ''),
(40, 'user_planet_name', 'Startplanet', '', ''),
(41, 'user_attack_min_points', '1', '', ''),
(42, 'user_attack_percentage', '0.001', '', ''),
(43, 'invade_possibility', '1', '1', '1'),
(44, 'invade_ship_destroy', '0.3', '', ''),
(45, 'def_restore_percent', '0.4', '', ''),
(46, 'def_wf_percent', '0.3', '', ''),
(47, 'ship_wf_percent', '0.4', '', ''),
(48, 'shipdefbuild_cancel_time', '15', '', ''),
(49, 'user_min_fields', '1200', '', ''),
(50, 'user_start_people', '200', '250', ''),
(51, 'people_food_require', '12', '', ''),
(52, 'info', '[color=yellow][b]Dies ist ein Testserver und die Spielwiese der Entwickler![/b] \r\nEs gibt weder Admin-Support noch werden irgendwelche Sachen erstattet. Rechnet damit dass eure Accs unter Umständen ohne Vorankündigung gelöscht oder angegriffen werden können, falls wir etwas testen müssen.[/color]\r\n\r\n[color=limegreen]Alle Infos zur Entwicklung: [url]https://dev.etoa.net[/url]\r\n\r\n[color orange][b]Fehler melden:[/b]\r\nDas es scheinbar noch das eine oder andere Missverständnis gibt: Rechtschreibefehler und Derartiges bitte unter [url]http://www.etoa.ch/forum/thread.php?postid=67971[/url] melden und Bugs unter [url]http://dev.etoa.ch:8000/game/newticket[/url] und bei Milestone bitte Beta V3 auswählen und wenn möglich kurz schauen, ob der entsprechende Bug schon gemeldet wurde.\r\n\r\nViele Dank ihr macht somit uns, wie auch euch das Ganze einiges einfacher.[/color]\r\n\r\n[color blue][b]Flottenupdates:[/b]\r\nBezüglich des Flottenscripts ist es momentan noch möglich, dass Aktionen nicht korrekt ausgeführt werden oder dass das Script aufgrund einer Aktion blockiert wird. Vollen Support diesbezüglich wird es ab Donnerstag Abend geben.\r\n\r\nBesten Dank für euer Verständnis\r\n[/color]\r\n', '', ''),
(53, 'url_rules', 'http://etoa.ch/rules', '', ''),
(54, 'url_bugs', 'http://dev.etoa.ch/net', '', ''),
(55, 'url_teamspeak', 'http://etoa.ch/teamspeak', '', ''),
(57, 'user_max_planets', '15', '', ''),
(58, 'wordbanlist', '', '', ''),
(59, 'msg_flood_control', '10', '', ''),
(60, 'msg_ban_hours', '0', '', ''),
(61, 'user_inactive_days', '7', '21', '14'),
(62, 'stats_num_rows', '50', '', ''),
(63, 'messages_threshold_days', '28', '14', ''),
(64, 'reports_threshold_days', '42', '42', ''),
(65, 'user_ban_min_length', '1', '', ''),
(66, 'user_umod_min_length', '3', '', ''),
(67, 'user_sitting_days', '12', '2', ''),
(68, 'people_multiply', '1.1', '', ''),
(69, 'online_threshold', '5', '', ''),
(70, 'deactivate_fleet', '', '', ''),
(71, 'default_image_path', 'images/imagepacks/Discovery', '', ''),
(72, 'people_work_done', '3', '', ''),
(73, 'default_css_style', 'Discovery', '', ''),
(74, 'build_time_boni_forschungslabor', '5', '10', '0.2'),
(75, 'build_time_boni_schiffswerft', '5', '10', ''),
(76, 'build_time_boni_waffenfabrik', '5', '10', ''),
(77, 'ship_bomb_factor', '5', '10', ''),
(78, 'ship_build_time', '0.8', '', ''),
(79, 'def_build_time', '0.8', '', ''),
(80, 'build_build_time', '1', '', ''),
(81, 'flight_flight_time', '1', '', ''),
(82, 'flight_start_time', '1', '', ''),
(83, 'flight_land_time', '1', '', ''),
(84, 'res_build_time', '1', '', ''),
(85, 'asteroid_ress', '', '1000', '1000000'),
(86, 'nebula_ress', '', '100000', '3000000'),
(87, 'contact_message', 'Die folgenden Angaben betreffen nur die aktuelle Spielrunde. Bitte bei Problemen im Zusammenhang mit dem Spielablauf (Namenswechsel, Cheater/Buguser melden etc) die Game-Admins kontaktieren. Bei schweren Bugs oder Fragen bitte zuerst im Forum nachschauen, dann erst die Entwickler kontaktieren. Bitte nur ganz wichtige E-Mails an die Projektleitung senden, belanglose Mails und Spam werden stillschweigend ignoriert!', '', ''),
(88, 'admininfo', '', '', ''),
(89, 'admin_timeout', '1200', '', ''),
(90, 'htaccess', 'htpasswd2', '.htaccess', 'cache/security/.htpasswd'),
(91, 'admin_htaccess', 'Administration-', 'etoa', ''),
(93, 'admin_dateformat', 'Y-m-d H:i:s', '', ''),
(94, 'wh_update', '172800', '1', ''),
(95, 'referers', 'http://test.dev.etoa.net\r\nhttp://dev.etoa.net\r\nhttp://etoa.ch\r\nhttp://www.etoa.ch\r\nhttp://localhost\r\nhttp://127.0.0.1', '', ''),
(96, 'under_construction', '', '', ''),
(97, 'flightban', '0', '', ''),
(98, 'battleban', '0', '', ''),
(99, 'mailqueue', '50', '', ''),
(100, 'nick_length', '', '3', '15'),
(101, 'imagepack_zip_format', 'zip', '', ''),
(102, 'imagepack_predirectory', '', '', ''),
(103, 'main_planet_changetime', '7', '', ''),
(104, 'battleban_time', '', '1234165500', '1234174500'),
(105, 'flightban_time', '', '1199293080', '1199552280'),
(106, 'gasplanet', '7', '3600', '500'),
(107, 'system_message', '', '', ''),
(108, 'crypto_enable', '1', '', ''),
(109, 'update_enabled', '1', '', ''),
(110, 'msg_max_store', '200', '20', ''),
(111, 'offline', '0', '89.236.149.247', ''),
(112, 'name_length', '30', '', ''),
(113, 'townhall_ban', '86400', 'Nichtbeachtung der Rathaus-Regeln', ''),
(114, 'welcome_message', 'Seid gegrüsst, Imperator!\r\n\r\nIch beglückwünsche Euch zum Antritt Eurer Regentschaft. Die Zukunft Eurer Rasse liegt nun in Euren Händen. Eure Heimatwelt hat sich soweit entwickelt dass ihre Bewohner sich danach sehnen die Galaxie um sie herum zu erkunden und fremde Welten zu besiedeln.\r\n\r\nLinks seht ihr die Navigation, mit der ihr Euer Reich verwalten könnt. Baut zuerst einige Gebäude um Rohstoffe zu fördern. Danach solltet ihr Forschungslabors und Werften errichten, damit ihr Raumschiffe bauen könnt um die Weiten von Andromeda zu erkunden. Bedenkt dass einige Gebäude Energie benötigen, vernachlässigt also den Bau von geeigneten Kraftwerken nicht.\r\n\r\nAnsonsten schaut Euch einfach um, zweifellos werdet Ihr Euch rasch zurechtfinden.\r\n\r\nWeitere Hilfen und Tipps findet ihr hier:\r\n\r\nHilfe: [url ?page=help]Umfangreiche InGame-Hilfe[/url]\r\nKontakt: [url ?page=contact]Game-Admin kontaktieren[/url]\r\nForum: [url http://www.etoa.ch/forum]Offizielles Forum[/url]\r\nFAQ: [url http://www.etoa.ch/faq]Häufig gestellte Fragen und Antworten dazu[/url]\r\n\r\nIch wünsche Euch nun viel Erfolg in der Galaxie von Andromeda. Möge Euer Imperium gross und Eure Schlachten erfolgreich sein!\r\n\r\nAnmerkung: Eine Kopie dieser Nachricht wird in Eurer Nachrichten-Box hinterlegt. Klickt auf das grüne Briefsymbol im der linken unteren Bildschirmhälfte um die Nachrichtenbox anzuzeigen.', '', ''),
(115, 'user_delete_days', '5', '', ''),
(116, 'battleban_arrival_text', '', 'Die ankommenden Schiffe sind auf dem Planeten gelandet. Nach einer kurzen Kaffeepause der Piloten kehrten sie wieder um und machten sich auf den Rückflug.', 'Auf dem Weg zu ihrem Ziel flogen deine Raketen in ein intergalaktisches Warpfeld. Sie wurden deaktiviert und in ihr Lager gebeamt.'),
(117, 'register_key', '', '', ''),
(118, 'random_event_hits', '0', '', ''),
(119, 'random_event_misses', '0', '', ''),
(120, 'profileimagecheck_done', '1209569797', '', ''),
(121, 'userrank_total', 'Imperator von Andromeda', '', ''),
(122, 'userrank_buildings', 'Grossbaumeister von Andromeda', '', ''),
(123, 'userrank_tech', 'Hochtechnokrat von Andromeda', '', ''),
(124, 'userrank_fleet', 'Flottenadmiral von Andromeda', '', ''),
(125, 'userrank_battle', 'Generalfeldmarschall von Andromeda', '', ''),
(126, 'userrank_trade', 'Handelsfürst von Andromeda', '', ''),
(127, 'userrank_diplomacy', 'Botschafter von Andromeda', '', ''),
(128, 'cryptocenter', '86400', '7200', '21600'),
(129, 'asteroid_action', '30', '50', '0'),
(130, 'gascollect_action', '20', '10', '1000'),
(131, 'nebula_action', '30', '50', '1000'),
(132, 'battle_rounds', '5', '', ''),
(133, 'antrax_action', '30', '90', ''),
(134, 'gasattack_action', '25', '95', ''),
(135, 'userrank_exp', 'Kriegsheld von Andromeda', '', ''),
(136, 'alliance_membercosts_factor', '0.9', '', ''),
(137, 'discoverymask', '0.4', '10', '5'),
(138, 'discover_percent_pirates', '10', 'a,e,n,s,w', '7'),
(139, 'discover_percent_aliens', '5', 'a,e,n,s,w', '1'),
(140, 'discover_percent_resources', '35', 'a,e,n,s,w', ''),
(141, 'discover_percent_ships', '25', 'a,e,n,s,w', ''),
(142, 'discover_percent_total_lost', '1', 'a,e,n,s,w', ''),
(143, 'discover_percent_fast_flight', '8', 'a,e,n,s,w', '80'),
(144, 'discover_percent_slow_fight', '5', 'a,e,n,s,w', '1.80'),
(145, 'discover_percent_sheet', '3', 'a,e,n,s,w', ''),
(146, 'discover_pirates', '1', '5', '1.5'),
(147, 'discover_aliens', '5', '10', '1.75'),
(148, 'discover_resources', '5000', '50', '5'),
(149, 'discover_fleet', '5', '1', '15'),
(150, 'alliance_shippoints_per_hour', '5', '', ''),
(151, 'imagesize', '220', '120', '40'),
(152, 'num_nebula_images', '9', '', ''),
(153, 'num_asteroid_images', '5', '', ''),
(154, 'num_space_images', '10', '', ''),
(155, 'num_wormhole_images', '1', '', ''),
(156, 'spyattack_action', '3', '1', '10'),
(157, 'alliance_shipcosts_factor', '1.02', '', ''),
(158, 'alliance_tech_bonus', '10', '', ''),
(159, 'alliance_war_time', '48', '48', ''),
(161, 'solsys_percent_planet', '85', '', ''),
(162, 'solsys_percent_asteroids', '5', '', ''),
(164, 'specialistconfig', '0.3', '10', '100000'),
(165, 'market_enabled', '1', '', ''),
(166, 'market_response_time', '14', '', ''),
(167, 'market_ship_action_ress', 'market', '', ''),
(168, 'market_ship_action_ship', 'market', '', ''),
(169, 'market_ship_flight_time', '', '15', '180'),
(170, 'market_auction_delay_time', '24', '', ''),
(171, 'market_rate_0', '1', '', ''),
(172, 'market_rate_1', '1', '', ''),
(173, 'market_rate_2', '1', '', ''),
(174, 'market_rate_3', '1', '', ''),
(175, 'market_rate_4', '1', '', ''),
(177, 'bot_max_count', '5', '', ''),
(178, 'log_threshold_days', '28', '', ''),
(179, 'sessionlog_store_days', '', '30', '60'),
(180, 'elorating', '1600', '15', ''),
(181, 'battle_rebuildable', '0', '0.75', ''),
(182, 'rebuildable_costs', '0.25', '', ''),
(183, 'invade_active_users', '0', '', ''),
(184, 'alliance_shippoints_base', '1.4', '', '');
(185, 'backup_dir', '/home/etoa/backup/', '', ''),
(186, 'backup_retention_time', '14', '', ''),
(187, 'backup_use_gzip', '0', '', ''),
(188, 'daemon_logfile', '/var/log/etoad/round12.log', '', ''),
(189, 'daemon_pidfile', '/var/run/etoad/round12.pid', '', ''),
(190, 'daemon_ipckey', '1090553408', '', ''),
(191, 'backup_time_interval', '6', '', ''),
(192, 'backup_time_hour', '0', '', ''),
(193, 'backup_time_minute', '47', '', ''),
(194, 'offline_message', '', '', ''),
(195, 'offline_ips_allow', '', '', ''),
(196, 'daemon_exe', '/usr/local/bin/etoad', '', ''),
(197, 'backend_status', '0', '', ''),
(198, 'backend_offline_message', 'Der EtoA-Updatedienst ist momentan ausser Betrieb. Die Entwickler sind informiert. Es ist nicht notwendig ein Ticket zu eröffnen. Unterdessen werden weder Gebäude, Technologien, Schiffe, Verteidigungen, Planeten noch Flotten aktualisiert.', '', ''),
(199, 'backend_offline_mail', 'glaubinix@etoa.ch;mrcage@etoa.ch', '', '');

--
-- Daten für Tabelle `default_items`
--

INSERT INTO `default_items` (`item_id`, `item_set_id`, `item_cat`, `item_object_id`, `item_count`) VALUES
(132, 7, 's', 71, 10),
(131, 7, 'd', 1, 10),
(130, 7, 'd', 11, 1),
(129, 7, 'd', 10, 1),
(128, 7, 's', 69, 20),
(127, 7, 's', 24, 20),
(126, 7, 's', 31, 20),
(125, 7, 's', 68, 20),
(124, 7, 's', 46, 20),
(123, 7, 's', 20, 10),
(122, 7, 's', 13, 1),
(121, 7, 's', 36, 20),
(120, 7, 's', 42, 20),
(119, 7, 's', 27, 20),
(118, 7, 's', 8, 20),
(117, 7, 's', 4, 20),
(116, 7, 's', 60, 1),
(115, 7, 's', 9, 10),
(69, 7, 'b', 6, 1),
(70, 7, 'b', 7, 15),
(71, 7, 'b', 8, 20),
(72, 7, 'b', 21, 10),
(73, 7, 'b', 9, 20),
(74, 7, 'b', 10, 20),
(75, 7, 'b', 11, 20),
(76, 7, 'b', 22, 1),
(77, 7, 'b', 24, 1),
(78, 7, 'b', 25, 10),
(79, 7, 'b', 1, 25),
(80, 7, 'b', 2, 25),
(81, 7, 'b', 3, 25),
(82, 7, 'b', 4, 25),
(83, 7, 'b', 5, 25),
(84, 7, 'b', 12, 12),
(85, 7, 'b', 13, 12),
(86, 7, 'b', 15, 6),
(87, 7, 'b', 14, 20),
(88, 7, 'b', 16, 25),
(89, 7, 'b', 17, 25),
(90, 7, 'b', 18, 25),
(91, 7, 'b', 20, 25),
(92, 7, 'b', 19, 25),
(93, 7, 't', 4, 20),
(94, 7, 't', 5, 20),
(95, 7, 't', 14, 20),
(96, 7, 't', 6, 15),
(97, 7, 't', 21, 15),
(98, 7, 't', 20, 15),
(99, 7, 't', 8, 20),
(100, 7, 't', 9, 20),
(101, 7, 't', 10, 20),
(102, 7, 't', 11, 20),
(103, 7, 't', 15, 10),
(104, 7, 't', 17, 10),
(105, 7, 't', 18, 10),
(106, 7, 't', 19, 10),
(107, 7, 't', 22, 1),
(108, 7, 't', 23, 1),
(109, 7, 't', 24, 1),
(110, 7, 't', 3, 20),
(111, 7, 't', 7, 20),
(112, 7, 't', 16, 10),
(113, 7, 't', 25, 10),
(114, 7, 't', 12, 10);

--
-- Daten für Tabelle `default_item_sets`
--

INSERT INTO `default_item_sets` (`set_id`, `set_name`, `set_active`) VALUES
(5, 'Standard', 1),
(7, 'All Objects', 0);

--
-- Daten für Tabelle `defense`
--

INSERT INTO `defense` (`def_id`, `def_name`, `def_shortcomment`, `def_longcomment`, `def_costs_metal`, `def_costs_crystal`, `def_costs_fuel`, `def_costs_plastic`, `def_costs_food`, `def_costs_power`, `def_power_use`, `def_fuel_use`, `def_prod_power`, `def_fields`, `def_show`, `def_buildable`, `def_order`, `def_structure`, `def_shield`, `def_weapon`, `def_heal`, `def_jam`, `def_race_id`, `def_cat_id`, `def_max_count`, `def_points`) VALUES
(1, 'SPICA Flakkanone', 'Einfache und billige Abwehrwaffe.', 'Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen.', 800, 475, 0, 425, 0, 0, 1, 0, 0, 0, 1, 1, 0, 300, 150, 250, 0, 0, 0, 2, 1000000, '1.700'),
(2, 'POLARIS Raketengeschütz', 'Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung.', 'Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe.', 1000, 700, 300, 500, 0, 0, 3, 0, 0, 0, 1, 1, 2, 450, 325, 350, 0, 0, 0, 2, 1000000, '2.500'),
(3, 'ZIBAL Laserturm', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel.', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann.', 3900, 3100, 2100, 1500, 0, 0, 8, 0, 0, 0, 1, 1, 3, 1500, 2000, 1800, 0, 0, 0, 2, 100000, '10.600'),
(4, 'OMEGA Geschütz', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen.', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen. Da es aber eine starke Waffe ist, können maximal 1\\''000 Stück gebaut werden.', 750000, 525000, 165000, 325000, 0, 0, 15, 0, 0, 1, 1, 1, 4, 300000, 350000, 275000, 0, 0, 0, 2, 1000, '1765.000'),
(5, 'VEGA Hochenergieschild', 'Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 'Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss. Es ist jedoch nicht sehr gut und kann nur wenig Beschuss abhalten.', 3000, 1200, 1800, 600, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1200, 3500, 0, 0, 0, 0, 1, 1, '6.600'),
(6, 'CASTOR Hochenergieschild', 'Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 'Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 95000, 40000, 45000, 25000, 0, 0, 0, 0, 0, 2, 1, 1, 1, 52500, 105000, 0, 0, 0, 0, 1, 1, '205.000'),
(7, 'NEKKAR Plasmawerfer', 'Die stärkste Verteidigung in ganz Andromeda.', 'Die stärkste Verteidigung in ganz Andromeda. Dieser Plasmawerfer kann es sogar mit einem Andromeda Kampfstern aufnehmen! Dabei schiesst er hochenergetische Teilchen auf das Ziel.\r\nBedingt durch seine Grösse und Stärke ist die maximale Anzahl pro Planet auf 15 limitiert.', 25000000, 20000000, 11500000, 12000000, 0, 0, 0, 0, 0, 2, 1, 1, 5, 14000000, 9500000, 14500000, 0, 0, 0, 2, 15, '68500.000'),
(8, 'SIGMA Hochenergieschild', 'Dies ist der grösste Schild in ganz Andromeda.', 'Dies ist der grösste Schild in ganz Andromeda. Dieser Schild nutzt hochenergetische Teilchen, um die Angriffe der Gegner abzufangen. Beim Bau dieses Schildes wird gleich noch ein Kraftwerk nur für diesen Schild gebaut, damit die Energieversorgung gesichert ist. Deshalb ist er so unglaublich teuer.', 250000000, 20000000, 25000000, 5000000, 0, 0, 0, 0, 0, 100, 1, 1, 3, 25000000, 225000000, 0, 0, 0, 0, 1, 1, '300000.000'),
(9, 'KAPPA Minen', 'Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören.', 'Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören. Sie sind mit Tritium gefüllt und explodieren bei einer Kollision mit feindlichen Schiffen. Da ein kleiner Korridor für eigene Schiffe und Handelsschiffe frei bleiben muss, kann maximal eine Million dieser Minen gebaut werden.', 25, 10, 18, 5, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 20, 0, 0, 0, 3, 1000000, '0.058'),
(11, 'PHOENIX Reparaturplattform', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer.', 6500, 3500, 3000, 1900, 0, 0, 0, 0, 0, 1, 1, 1, 10, 3750, 2500, 2500, 1000, 0, 10, 3, 1000000, '14.900'),
(12, 'SAGITTARIUS Plasmaschild', 'Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.', 'Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.', 1350000, 1000000, 1050000, 625000, 0, 0, 0, 0, 0, 20, 1, 1, 2, 1400000, 2100000, 0, 0, 0, 10, 1, 1, '4025.000'),
(10, 'MAGNETRON Störsender', 'Diese defensive Anlage kann zufällige Signale in den Raum abgeben und so das Auffinden und Entschlüsseln der eigenen Flottenkommunikation durch gegnerische Spione erschweren.', 'Durch die Verfügbarkeit von grossen Rechenzentren ist in letzter Zeit die Bedrohung durch kryptographische Angriffe auf die eigenen Flottenfunkverbindungen stark angestiegen. Viele Generäle fühlten sich nicht mehr sicher, da ihre Feinde anscheinend plötzlich sehr genau wussten, wann und wo ihre Flotten landen würden. Dies führte zur Erfindung des MAGNETRON Störsenders. Die riesigen Sendeanlagen erzeugen zufällige Funksignale, die sie in den Raum abgeben. Eine gegnerische Analyse der Funksignale eines Planeten findet so viel zu viele Signale und hat Mühe, die richtigen herauszufiltern. ', 20000, 50000, 10000, 15000, 0, 0, 0, 0, 0, 5, 1, 1, 10, 15000, 1200, 0, 0, 1, 0, 3, 10, '95.000'),
(14, 'ZIBAL Laserturm M', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Mobile Version.', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann. Kann auf andere Planeten transportiert werden.', 3900, 3100, 2100, 1500, 0, 0, 8, 0, 0, 0, 1, 1, 3, 1500, 2000, 1800, 0, 0, 10, 2, 100000, '10.600'),
(15, 'POLARIS Raketengeschütz M', 'Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung. Mobile Version.', 'Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe. Kann auf andere Planeten transportiert werden.', 1000, 700, 300, 500, 0, 0, 3, 0, 0, 0, 1, 1, 2, 450, 325, 350, 0, 0, 10, 2, 1000000, '2.500'),
(16, 'SPICA Flakkanone M', 'Einfache und billige Abwehrwaffe. Mobile Version.', 'Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen. Kann auf andere Planeten transportiert werden.', 800, 475, 0, 425, 0, 0, 1, 0, 0, 0, 1, 1, 0, 300, 150, 250, 0, 0, 10, 2, 1000000, '1.700'),
(17, 'PHOENIX Reparaturplattform M', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden. Mobile Version.', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer. Kann auf andere Planeten transportiert werden.', 6500, 3500, 3000, 1900, 0, 0, 0, 0, 0, 1, 1, 1, 10, 3750, 2500, 2500, 1000, 0, 10, 3, 1000000, '14.900');

--
-- Daten für Tabelle `def_cat`
--

INSERT INTO `def_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_color`) VALUES
(1, 'Schilder', 1, '#0080FF'),
(2, 'Geschütze', 0, '#00ff00'),
(3, 'Spezialanlagen', 2, '#B048F8');

--
-- Daten für Tabelle `def_requirements`
--

INSERT INTO `def_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(35, 12, 0, 16, 8),
(2, 2, 10, 0, 3),
(3, 3, 10, 0, 6),
(4, 3, 0, 3, 5),
(5, 4, 0, 3, 7),
(6, 4, 10, 0, 8),
(7, 4, 8, 0, 5),
(8, 5, 10, 0, 3),
(11, 5, 0, 3, 4),
(10, 5, 8, 0, 2),
(12, 6, 10, 0, 6),
(13, 6, 0, 3, 6),
(14, 6, 8, 0, 5),
(15, 7, 10, 0, 10),
(16, 7, 0, 8, 11),
(17, 7, 0, 3, 10),
(18, 8, 10, 0, 10),
(19, 8, 0, 3, 8),
(20, 8, 10, 10, 12),
(21, 8, 0, 9, 6),
(22, 8, 22, 0, 3),
(23, 3, 0, 8, 5),
(24, 4, 8, 8, 7),
(25, 9, 10, 0, 4),
(26, 9, 0, 8, 3),
(27, 9, 0, 4, 2),
(28, 11, 10, 0, 8),
(29, 11, 0, 25, 3),
(30, 11, 0, 16, 4),
(31, 11, 0, 19, 3),
(32, 12, 10, 0, 9),
(33, 12, 8, 0, 7),
(34, 12, 0, 3, 10),
(36, 10, 0, 25, 5),
(37, 10, 0, 11, 8),
(38, 10, 0, 3, 10),
(39, 10, 13, 0, 5),
(41, 14, 10, 0, 6),
(42, 14, 0, 3, 5),
(43, 14, 0, 8, 5),
(44, 14, 0, 12, 9),
(72, 15, 0, 12, 7),
(71, 15, 10, 0, 3),
(69, 16, 10, 0, 1),
(75, 17, 0, 16, 4),
(68, 1, 10, 0, 1),
(73, 17, 10, 0, 8),
(70, 16, 0, 12, 5),
(74, 17, 0, 25, 3),
(76, 17, 0, 19, 3),
(77, 17, 0, 12, 11);

--
-- Daten für Tabelle `events`
--

INSERT INTO `events` (`event_id`, `event_execrate`, `event_title`, `event_text`, `event_ask`, `event_answer_pos`, `event_answer_neg`, `event_reward_p_rate`, `event_reward_p_metal_min`, `event_reward_p_metal_max`, `event_reward_p_crystal_min`, `event_reward_p_crystal_max`, `event_reward_p_plastic_min`, `event_reward_p_plastic_max`, `event_reward_p_fuel_min`, `event_reward_p_fuel_max`, `event_reward_p_food_min`, `event_reward_p_food_max`, `event_reward_p_people_min`, `event_reward_p_people_max`, `event_reward_p_ship_id`, `event_reward_p_ship_min`, `event_reward_p_ship_max`, `event_reward_p_def_id`, `event_reward_p_def_min`, `event_reward_p_def_max`, `event_reward_p_building_id`, `event_reward_p_building_level`, `event_reward_p_tech_id`, `event_reward_p_tech_level`, `event_costs_p_rate`, `event_costs_p_metal_min`, `event_costs_p_metal_max`, `event_costs_p_crystal_min`, `event_costs_p_crystal_max`, `event_costs_p_plastic_min`, `event_costs_p_plastic_max`, `event_costs_p_fuel_min`, `event_costs_p_fuel_max`, `event_costs_p_food_min`, `event_costs_p_food_max`, `event_costs_p_people_min`, `event_costs_p_people_max`, `event_costs_p_ship_id`, `event_costs_p_ship_min`, `event_costs_p_ship_max`, `event_costs_p_def_id`, `event_costs_p_def_min`, `event_costs_p_def_max`, `event_costs_p_building_id`, `event_costs_p_building_level`, `event_costs_p_tech_id`, `event_costs_p_tech_level`, `event_reward_n_rate`, `event_reward_n_metal_min`, `event_reward_n_metal_max`, `event_reward_n_crystal_min`, `event_reward_n_crystal_max`, `event_reward_n_plastic_min`, `event_reward_n_plastic_max`, `event_reward_n_fuel_min`, `event_reward_n_fuel_max`, `event_reward_n_food_min`, `event_reward_n_food_max`, `event_reward_n_people_min`, `event_reward_n_people_max`, `event_reward_n_ship_id`, `event_reward_n_ship_min`, `event_reward_n_ship_max`, `event_reward_n_def_id`, `event_reward_n_def_min`, `event_reward_n_def_max`, `event_reward_n_building_id`, `event_reward_n_building_level`, `event_reward_n_tech_id`, `event_reward_n_tech_level`, `event_costs_n_rate`, `event_costs_n_metal_min`, `event_costs_n_metal_max`, `event_costs_n_crystal_min`, `event_costs_n_crystal_max`, `event_costs_n_plastic_min`, `event_costs_n_plastic_max`, `event_costs_n_fuel_min`, `event_costs_n_fuel_max`, `event_costs_n_food_min`, `event_costs_n_food_max`, `event_costs_n_people_min`, `event_costs_n_people_max`, `event_costs_n_ship_id`, `event_costs_n_ship_min`, `event_costs_n_ship_max`, `event_costs_n_def_id`, `event_costs_n_def_min`, `event_costs_n_def_max`, `event_costs_n_building_id`, `event_costs_n_building_level`, `event_costs_n_tech_id`, `event_costs_n_tech_level`, `event_alien_rate`, `event_alien_ship1_id`, `event_alien_ship1_min`, `event_alien_ship1_max`, `event_alien_ship2_id`, `event_alien_ship2_min`, `event_alien_ship2_max`, `event_alien_ship3_id`, `event_alien_ship3_min`, `event_alien_ship3_max`) VALUES
(1, 100, 'Bruchlandung Marauder', 'Ein Flotte mit {reward:p:ship} der Handelsföderation ist auf deinem Planeten {planet} abgestürzt, es würde dich {costs:p:metal}, {costs:p:crystal} und {costs:p:plastic} kosten das Schiff zu bergen. Möchtest du es bergen?', 1, 'Deine Bergungsmannschaft konnte {reward:p:shipcnt} {reward:p:ship} bergen!', 'Leider hast du deiner Bergungsmannschaft keine Ressourcen gegeben um {reward:p:shipcnt} {reward:p:ship} zu bergen!', '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 29, 1, 10, 0, 0, 0, 0, 0, 0, 0, '0.0100', 200, 400, 150, 300, 150, 300, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.9999', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 100, 'Intergalaktischer Sturm', 'Ein intergalaktischer Sturm, ist über deinen Planeten {planet} gefegt, und hat dabei {reward:p:metal} und {reward:p:crystal} auf deinem Planeten hinterlassen. ', 0, '', '', '0.0100', 1, 200, 1, 200, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.9999', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 100, 'Vulkanausbruch', 'Ein Vulkan ist auf deinem Planeten {planet} ausgebrochen, und hat {reward:p:metal} aus dem Erdinneren hervor gebracht. Das Abbauen kostet dich {costs:p:fuel} und {costs:p:food}. Sollen das Titan abgebaut werden?', 1, 'Es wurde {reward:p:metal} abgebaut!', '', '0.0100', 200, 400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 20, 200, 30, 150, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 100, 'Zusammenstoss von Gasplaneten', 'Nach dem Zusammenstoss von zwei Gasplaneten sind {reward:p:fuel} auf deinen Planeten {planet} gefallen.', 0, '', '', '0.0100', 0, 0, 0, 0, 0, 0, 10, 80, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(5, 100, 'Supernova', 'Bei einer Supernova sind {reward:p:crystal} ins Weltall geschleudert worden und nun auf deinem Planeten {planet} angekommen.', 0, '', '', '0.0100', 0, 0, 20, 70, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(6, 100, 'Unabhängige Bürger', 'Eine Gruppe von {reward:p:people} unabhängigen Bürgern ist auf deinem Planeten gelandet, möchtes du ihnen eine Unterkunft anbieten?', 1, '{reward:p:people} Bürger schliessen sich deiner Zivilisation an!', 'Die unabhängigen Bürger sind empört dass du ihnen keine Unterkunft gewährt hast und stürmen deine {costs:n:building}, dabei geht ein Teil kaputt und die Stufe des Silos verringert sich um {costs:n:buildinglevel}.', '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 19, 1, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(7, 100, 'Defekter Antrieb', 'Ein Imperialisches Schlachtschiff hat einen defekten Antrieb, möchtest du der Besatzung {costs:p:crystal} geben, damit sie ihren Antrieb wieder benutzen können?', 0, 'Die Besatzung des Schlachtschiffes ist auf ihren Planeten zurück geflogen und hat sich dann entschieden, dir ein Geschenk zu machen: {reward:p:shipcnt} {reward:p:ship}', 'Die Piloten des fremden Schiffes versucht, das Silizium zu stehlen, dabei kommt es zu Aueinandersetzungen mit deiner Armee. Du verlierst {costs:n:people} Bürger und {costs:n:crystal}', '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 1, 11, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 350, 841, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0);

--
-- Daten für Tabelle `fleet_ships`
--

INSERT INTO `fleet_ships` (`fs_id`, `fs_fleet_id`, `fs_ship_id`, `fs_ship_cnt`, `fs_ship_faked`, `fs_special_ship`, `fs_special_ship_level`, `fs_special_ship_exp`, `fs_special_ship_bonus_weapon`, `fs_special_ship_bonus_structure`, `fs_special_ship_bonus_shield`, `fs_special_ship_bonus_heal`, `fs_special_ship_bonus_capacity`, `fs_special_ship_bonus_speed`, `fs_special_ship_bonus_pilots`, `fs_special_ship_bonus_tarn`, `fs_special_ship_bonus_antrax`, `fs_special_ship_bonus_forsteal`, `fs_special_ship_bonus_build_destroy`, `fs_special_ship_bonus_antrax_food`, `fs_special_ship_bonus_deactivade`) VALUES
(73, 67, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(58, 57, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(57, 0, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(56, 55, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(55, 0, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(54, 53, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(44, 44, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(43, 43, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(53, 0, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(49, 49, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(48, 48, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(62, 56, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(63, 57, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(64, 58, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(65, 59, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(66, 60, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(67, 61, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(69, 63, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(71, 65, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(75, 69, 16, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(76, 70, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(77, 71, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(78, 72, 16, 23, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(79, 73, 16, 33, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(80, 74, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(81, 75, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(82, 76, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(83, 77, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(85, 79, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(87, 81, 16, 58, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(93, 87, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(89, 83, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(92, 86, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(91, 85, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(94, 88, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(95, 89, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(96, 90, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(97, 91, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(98, 92, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(99, 93, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(100, 94, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(101, 95, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(103, 97, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(111, 105, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(113, 107, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(114, 108, 16, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(115, 109, 16, 506, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(231, 216, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(232, 217, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(315, 291, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(312, 288, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(316, 292, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(310, 286, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(317, 293, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(311, 287, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(313, 289, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(318, 294, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(319, 295, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(324, 300, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(323, 299, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(322, 298, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(321, 297, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(320, 296, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(325, 301, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(326, 302, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(327, 303, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(328, 304, 16, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

--
-- Daten für Tabelle `message_cat`
--

INSERT INTO `message_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_desc`, `cat_sender`) VALUES
(1, 'Persönliche Nachrichten', 0, '', ''),
(2, 'Spionageberichte', 1, '', 'Flottenkontrolle'),
(3, 'Kriegsberichte', 2, '', 'Flottenkontrolle'),
(4, 'Überwachungsberichte', 3, '', 'Raumüberwachung'),
(5, 'Sonstige Nachrichten', 5, '', 'System'),
(6, 'Allianz', 4, '', 'Allianzverwaltung'),
(7, 'Account', 5, '', 'EtoA Administration');

--
-- Daten für Tabelle `minimap_field_types`
--

INSERT INTO `minimap_field_types` (`field_typ_id`, `field_name`, `field_image`, `field_blocked`) VALUES
(1, 'Gras', 'gras01.jpg', 0),
(2, 'Felsen', 'rock01.jpg', 1);

--
-- Daten für Tabelle `missiles`
--

INSERT INTO `missiles` (`missile_id`, `missile_name`, `missile_sdesc`, `missile_ldesc`, `missile_costs_metal`, `missile_costs_crystal`, `missile_costs_plastic`, `missile_costs_fuel`, `missile_costs_food`, `missile_damage`, `missile_speed`, `missile_range`, `missile_deactivate`, `missile_def`, `missile_launchable`, `missile_show`) VALUES
(1, 'PHOBOS Rakete', 'Zerstört gegnerische Verteidigung.', 'Diese Rakete kann auf Verteidigungsanlagen eines feindlichen Planeten abgefeuert werden und verursacht an diesen einen gewissen Schaden, so dass einige Anlagen unter Umständen zerstört werden. Diese Raketen haben eine begrenzte Reichweite, treffen ihr Ziel aber immer.', 18000, 6000, 5000, 15000, 0, 25000, 100000, 3000, 0, 0, 1, 1),
(2, 'GEMINI Abfangrakete', 'Abfangraketen schiessen selbstständig gegnerische Raketen ab, die diesen Planeten anfliegen.', 'Bei einem Raketenangriff können diese Raketen jeweils eine fremde Rakete zerstören. Sie lösen sich selbständig aus und bieten so einen guten Schutz gegen anfliegende Raketen. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.', 9000, 18000, 6000, 4000, 2000, 0, 0, 0, 0, 1, 0, 1),
(3, 'VEGA EMP-Rakete', 'Kann ein gegnerisches Gebäude temporär deaktivieren.', 'Diese Rakete kann angreifen um ein gegnerisches Gebäude temporär ausser Kraft zu setzen. Sie richtet an der Verteidigung aber keinen Schaden an und kann ein Gebäude auch nicht vollständig zerstören! Die Rakete wird beim EMP-Angriff verbraucht und hat auch nur eine begrenzte Reichweite.', 18000, 6000, 5000, 15000, 0, 0, 90000, 3000, 300, 0, 1, 1),
(4, 'VIRGO Abfangrakete', 'Verbesserte Abfangrakete; schiesst selbstständig zwei gegnerische Raketen ab.', 'Bei einem Raketenangriff können diese Raketen jeweils zwei fremde Rakete zerstören. Sie lösen sich  selbständig aus und bieten so einen guten Schutz. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.', 15000, 23000, 9000, 4000, 2000, 0, 0, 0, 0, 2, 0, 1);

--
-- Daten für Tabelle `missile_requirements`
--

INSERT INTO `missile_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 2, 25, 0, 1),
(2, 2, 0, 25, 1),
(4, 1, 25, 0, 3),
(5, 1, 0, 24, 3),
(6, 3, 25, 0, 4),
(7, 3, 0, 24, 5),
(8, 4, 25, 0, 5),
(9, 4, 0, 24, 6),
(10, 4, 0, 25, 5),
(11, 1, 0, 25, 2),
(12, 3, 0, 25, 4),
(13, 3, 0, 17, 8),
(14, 1, 0, 8, 8);

--
-- Daten für Tabelle `multifire`
--

INSERT INTO `multifire` (`id`, `source_ship_id`, `source_def_id`, `target_ship_id`, `value`, `target_def_id`) VALUES
(1, 6, 0, 2, 30, 0),
(2, 6, 0, 3, 50, 0);

--
-- Daten für Tabelle `obj_transforms`
--

INSERT INTO `obj_transforms` (`id`, `def_id`, `ship_id`, `costs_metal`, `costs_crystal`, `costs_plastic`, `costs_fuel`, `costs_food`, `costs_power`, `costs_factor_sd`, `costs_factor_ds`, `num_def`) VALUES
(1, 14, 79, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1),
(2, 15, 81, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1),
(3, 16, 80, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1),
(4, 17, 82, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1);

--
-- Daten für Tabelle `planet_types`
--

INSERT INTO `planet_types` (`type_id`, `type_name`, `type_habitable`, `type_comment`, `type_f_metal`, `type_f_crystal`, `type_f_plastic`, `type_f_fuel`, `type_f_food`, `type_f_power`, `type_f_population`, `type_f_researchtime`, `type_f_buildtime`, `type_collect_gas`, `type_consider`) VALUES
(1, 'Erdähnlicher Planet', 1, 'Dieser Planet hat eine sehr ausgeglichene Umwelt und ähnelt unseren ehemaligen Erde am meisten. Da der Mensch ein Gewohnheitstier ist, sind erdähnliche Planeten ideal für das Heranwachsen einer Zivilisation geeignet, da die notwendigen Voraussetzungen für alle Bereiche gegeben sind.', '1.15', '1.00', '1.20', '1.00', '1.50', '1.10', '1.20', '1.00', '1.00', 0, 1),
(2, 'Wasserplanet', 1, 'Die Oberfläche dieses Planeten besteht zum grössten Teil aus Ozeanen. Die wenigen Landteile sind nicht wirklich geeignet für grossflächigen Abbau von Mineralen, dafür kann aus dem Wasser Tritium gewonnen werden. Ebenfalls ist durch das viele vorhandene Wasser die Hauptgrundlage für Nahrungsabbau gelegt, ausserdem ist der Planet bestens geeignet, mit Hilfe von Wasserkraftwerken grosse Mengen an Energie zu erzeugen.', '0.90', '0.90', '1.00', '1.15', '1.15', '1.60', '0.80', '1.00', '0.85', 0, 1),
(3, 'Wüstenplanet', 1, 'Wüste, Sand, Trockenheit und ein unwirtliches Klima zeichnet diesen Planetentyp aus. Der allgegenwärtige Sand hat aber auch etwas positives, denn aus ihm können grosse Mengen von wertvollem Silizium gewonnen werden.', '1.00', '1.70', '1.00', '0.90', '0.80', '0.90', '0.85', '0.85', '1.10', 0, 1),
(4, 'Eisplanet', 1, 'Auf diesem unwirtlichen Planeten lockt einzig der Abbau von Tritium, welches sich aus den Eisschichen herausextrahieren lässt.\r\nVor kurzem haben Forscher eine neue chemische Methode entwickelt, aus Eismassen Silizium zu gewinnen. Diese neuartige Abbaumöglichkeit macht die Eisplaneten für Silizium-Anwender interessanter.', '1.00', '1.20', '1.00', '1.60', '0.90', '1.00', '0.50', '1.00', '1.00', 0, 1),
(5, 'Dschungelplanet', 1, 'Riesige Wälder wachsen auf diesem Planeten, dessen Klima sehr gut für das Wachstum der Umwelt ist. Daher kann viel Nahrung für die Bevölkerung geerntet werden, welche sich auf einem Dschungelplaneten auch sonst sehr wohl fühlt.', '1.00', '1.00', '1.20', '1.15', '1.40', '1.00', '1.50', '1.10', '1.00', 0, 1),
(6, 'Gebirgsplanet', 1, 'Den Namen hat dieser Planetentyp durch seine felsige Oberfläche erhalten. Ein Abbau von Erzen bietet sich optimalerweise an, hingegen ist der Abbau von Nahrung und die Herstellung von PVC mit Aufwand verbunden, da die Umgebung deren Anforderungen nicht gerecht wird.', '1.30', '1.00', '0.90', '1.00', '0.90', '1.10', '0.90', '1.10', '0.90', 0, 1),
(7, 'Gasplanet', 0, 'Dieser Planet ist unbewohnbar, da er keine feste Oberfläche hat, sondern aus lauter gasartigen Nebeln besteht. Seine Gase lassen sich jedoch mit Hilfe von Gassaugern zu Tritium umwandeln.', '0.50', '0.60', '0.40', '3.00', '0.30', '1.20', '0.30', '1.20', '1.20', 1, 1),
(8, 'Alienplanet', 1, 'Sagen und Mythen ranken sich um diesen Planeten. ', '1.50', '1.50', '1.50', '1.50', '1.50', '1.50', '1.50', '1.00', '1.00', 0, 1);

--
-- Daten für Tabelle `races`
--

INSERT INTO `races` (`race_id`, `race_name`, `race_comment`, `race_short_comment`, `race_adj1`, `race_adj2`, `race_adj3`, `race_leadertitle`, `race_f_researchtime`, `race_f_buildtime`, `race_f_fleettime`, `race_f_metal`, `race_f_crystal`, `race_f_plastic`, `race_f_fuel`, `race_f_food`, `race_f_power`, `race_f_population`, `race_active`) VALUES
(1, 'Terraner', 'Die Terraner sind eine eher jüngere Rasse, deren Vorfahren ursprünglich vom Planeten Erde kamen. Die Menschen sind besonders gut in Forschung, der Herstellung von Plastik und dem Anbau von Nahrung. Ihre Schwächen liegen im Abbau von Erzen und Kristallen. Da sie ihre ganzen Ressourcen in die Forschung steckten, sind ihre Schiffe relativ langsam.', '', 'terranischer', 'terranisches', 'terranische', 'Präsident der Terraner', '0.85', '1.00', '0.95', '0.90', '0.90', '1.30', '1.00', '1.30', '1.00', '1.00', 1),
(2, 'Andorianer', 'Die Andorianer sind zugleich humanoid und insektoid. Sie haben graublaue Haut und weisses Haar. Auf ihrem Kopf haben sie zwei Fühler, die ihnen zur feinfühligen sinnlichen Wahrnehmung dienen. Ihre Stärke ist die Produktion künstlicher Stoffe wie Plastik. Ihre Schwäche ist der schlechte Umgang mit Energie.', '', '', '', '', 'Schwarmführer der Andorianer', '1.00', '1.00', '1.00', '1.00', '1.10', '1.60', '1.00', '1.00', '0.90', '1.00', 1),
(3, 'Rigelianer', 'Die Rigelianer stammen aus dem Rigel-System. Ihre Stärke liegt im Abbau von Kristallen, die für Steuereinheiten in Gebäuden und Schiffen verwendet werden. Da sie lange nur auf den Handel mit Silizium gesetzt haben, sind ihre Kenntnisse beim Abbau anderer Stoffe eher schlecht.', '', '', '', '', 'Kaiser der Rigelianer', '1.00', '1.00', '1.00', '0.80', '1.80', '0.90', '0.90', '0.90', '1.00', '1.10', 1),
(4, 'Orioner', 'Die Orioner sind eine humanoide Rasse aus der Nähe des Orions. Die Gesellschaft der Orioner besteht hauptsächlich aus Schmugglern und Piraten. Ihre Schiffe sind bekannt für ihre Schnelligkeit.', '', '', '', '', 'Kapitän der Orioner', '1.00', '1.00', '2.00', '1.10', '1.10', '0.80', '0.90', '1.00', '1.00', '1.10', 1),
(5, 'Minbari', 'Die Minbari sind eine humanoide Rasse. Dadurch, dass sie den Rohstoff Erdöl nie gekannt haben, sind sie seit Ewigkeiten auf den Abbau von Tritium spezialisiert. Durch ihre enormen Treibstoffreserven und ihre grossen Anwendungskenntnisse von Tritium haben sie relativ schnelle Raumschiffe.', 'Eine Rasse mit schnellen Schiffen und grossem Wissen über Tritium.', 'minbarischer', '', 'minbarische', 'Vorsteher des Minbarikonzils', '1.00', '1.00', '1.20', '0.90', '0.90', '1.10', '1.80', '1.00', '1.00', '1.00', 1),
(8, 'Centauri', 'Die Centauri haben die besten Wissenschaftler des Universums, darum können sie auch schneller Technologien erforschen. Allerdings verbrauchen sie für ihre Labore sehr viel Strom.', '', '', '', '', 'Professor der Centauri', '0.60', '1.00', '1.00', '0.90', '0.90', '1.10', '0.90', '1.00', '0.85', '1.00', 1),
(6, 'Ferengi', 'Die Ferengi sind eine humanoide Rasse. Sie sind etwas kleinwüchsiger als Menschen und  haben grosse Ohren. Die Stärke der Ferengi liegt beim Abbau von Metall.', '', '', '', '', 'Grosser Nagus der Ferengi', '1.00', '1.00', '1.20', '1.60', '0.90', '1.00', '0.90', '1.00', '1.00', '1.00', 1),
(7, 'Vorgonen', 'Die Vorgonen sind eine Rasse, die vor allem gut bauen kann. Sie können ihre Gebäude viel schneller fertig stellen als alle Anderen. Dafür lassen ihre Schiffe und ihre Produktionsrate zu wünschen übrig.', '', '', '', '', 'Architekt der Vorgonen', '1.00', '0.60', '0.95', '0.90', '0.80', '0.80', '1.10', '0.70', '1.20', '0.90', 1),
(9, 'Cardassianer', 'Seit einer grossen Hungersnot haben sich die Cardassianer auf die Nahrungsherstellung spezialisiert, haben aber den Abbau von Erzen vernachlässigt.\r\nIhre andere Stärke liegt in der Fähigkeit der Mutterschiffe zur Regeneration von ganzen Flottenverbänden.', '', '', '', '', 'Zentralrat der Cardassianer', '1.00', '1.00', '1.00', '0.80', '0.90', '1.20', '1.00', '1.60', '1.10', '1.10', 1),
(10, 'Serrakin', 'Die Serrakin sind eine sehr friedliche Rasse, welche sich nicht gerne in grosse Auseinandersetzungen einmischt. Sie weiss sich aber bei Angriffen sehr gut zu wehren, da die Verteidigungstechnologie ihr Spezialgebiet ist.', '', 'serrakinischer', '', 'serrakinische', 'Beschützer der Serrakin', '1.00', '1.00', '0.90', '1.15', '1.15', '1.10', '0.90', '1.10', '1.10', '0.80', 1);

--
-- Daten für Tabelle `ships`
--

INSERT INTO `ships` (`ship_id`, `ship_name`, `ship_type_id`, `ship_shortcomment`, `ship_longcomment`, `ship_costs_metal`, `ship_costs_crystal`, `ship_costs_fuel`, `ship_costs_plastic`, `ship_costs_food`, `ship_costs_power`, `ship_power_use`, `ship_fuel_use`, `ship_fuel_use_launch`, `ship_fuel_use_landing`, `ship_prod_power`, `ship_capacity`, `ship_people_capacity`, `ship_pilots`, `ship_speed`, `ship_time2start`, `ship_time2land`, `ship_show`, `ship_buildable`, `ship_order`, `ship_actions`, `ship_bounty_bonus`, `ship_heal`, `ship_structure`, `ship_shield`, `ship_weapon`, `ship_race_id`, `ship_launchable`, `ship_fieldsprovide`, `ship_cat_id`, `ship_fakeable`, `special_ship`, `ship_max_count`, `special_ship_max_level`, `special_ship_need_exp`, `special_ship_exp_factor`, `special_ship_bonus_weapon`, `special_ship_bonus_structure`, `special_ship_bonus_shield`, `special_ship_bonus_heal`, `special_ship_bonus_capacity`, `special_ship_bonus_speed`, `special_ship_bonus_pilots`, `special_ship_bonus_tarn`, `special_ship_bonus_antrax`, `special_ship_bonus_forsteal`, `special_ship_bonus_build_destroy`, `special_ship_bonus_antrax_food`, `special_ship_bonus_deactivade`, `ship_points`, `ship_alliance_shipyard_level`, `ship_alliance_costs`) VALUES
(1, 'UNUKALHAI Transportschiff', 1, 'Dies ist ein grosses Transportschiff, dessen riesige Lagerräume alle Arten von Waren aufnehmen können. ', 'Nachdem die Algol Transportschiffe sich mit einem ungeahnten Erfolg im ganzen Universum verbreitet hatten, wurde das Unukalhai Transportschiff konzipiert, welches eine grössere Lagerkapzität aufweist. Da man die Konvois mit Antares schützte, war auch für die Unukalhais keine grössere Bewaffnung nötig; man konzentrierte sich ausserdem vor allem auch auf die grössere Sicherheit für die Navigationssysteme, weil diese bei den Algols viel wegen kosmischer Strahlung ausgefallen sind.', 6000, 1400, 0, 2100, 0, 0, 0, 45, 70, 10, 0, 65000, 0, 1, 2850, 600, 300, 1, 1, 0, 'transport,position,fetch,attack,flight,support,alliance', '0.50', 0, 400, 100, 30, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '9.500', 0, 0),
(2, 'ANTARES Jäger', 1, 'Kleines Kampfschiff, ideal für die Begleitung kleinerer Konvois. Auch geeignet für Raubzüge und Übergriffe auf schwach befestigte Planeten.', 'Der Antares Jäger wurde als erster kampftauglicher Jäger hergestellt, um die Rohstoffkonvois vor Piraten zu schützen. Sie eignen sich zu Beginn als Begleitschutz, aber ihre Technologie ist nicht sehr weit entwickelt, deshalb sind die Herstellungskosten im Vergleich mit ihrer Leistung relativ hoch. Die Antares wurden nicht für grössere Angriffe auf befestigte Planeten konzipiert, auch deshalb werden sie von den wenigsten Armeen in grösseren Mengen genutzt.', 750, 575, 50, 420, 0, 0, 0, 5, 4, 1, 0, 500, 0, 1, 380, 15, 13, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 330, 60, 170, 0, 1, 0, 1, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.795', 0, 0),
(3, 'ZAVIJAH Spionagesonde', 1, 'Diese Sonde erkundet fremde Planeten und sendet die Daten an dein Kontrollzentrum zurück.', 'Nachdem die Raumpiraten wegen den schnell konstruierten planetaren Verteidigungsanlagen nicht mehr jedes System gefahrlos ausrauben konnten, erfanden sie dieses kleine, nützliche Schiff. Es kann wegen seiner Grösse praktisch unbemerkt in Frage kommende Planeten ausspionieren und detaillierte Informationen über die stationierte Flotte liefern. Dank seiner Geschwindigkeit wird es dabei äusserst selten abgeschossen. Um diese Geschwindigkeit erreichen zu können, müssen sie sehr leicht gebaut sein und können keine Bewaffnung tragen. Ausserdem haben sie einen sehr kleinen Laderaum und können deshalb nur über kürzere Distanzen verwendet werden.', 100, 300, 0, 80, 0, 0, 0, 1, 1, 0, 0, 150, 0, 0, 25000, 2, 1, 1, 1, 9, 'position,spy,flight,support', '0.50', 0, 10, 1, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.480', 0, 0),
(4, 'TAURUS Besiedlungsschiff', 1, 'Das TAURUS Besiedlungsschiff ist ein Schiff, mit dem andere Planeten besiedelt werden können. Es kann Rohstoffe und Passagiere aufnehmen, ist aber auch dementsprechend langsam.', 'Sobald auf dem Heimatplaneten die grundlegende Infrastruktur aufgebaut war, waren die Herrscher mit nur einem Planeten nicht mehr zufrieden. Also baute man die Taurus Besiedlungsschiffe, die andere Planeten für das eigene Imperium annektieren können. Da sie die ganze Lebenserhaltung für die Kolonialisten in einer lebensfeindlichen Umwelt gewährleisten müssen, gestaltet sich ihre Herstellung als langwierig und teuer, und das Schiff kann wegen seiner Masse nur langsam bewegt werden.', 8000, 10500, 1200, 5000, 0, 0, 0, 13, 15, 5, 0, 10000, 0, 5, 750, 600, 660, 1, 1, 8, 'transport,position,attack,colonize,flight,support,alliance', '0.50', 0, 1000, 500, 100, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '24.700', 0, 0),
(6, 'HADAR Schlachtschiff', 1, 'Das HADAR-Schlachtschiff ist ein gut gepanzertes und stark bewaffnetes Kriegsschiff. Mit ihm können auch grössere Verteidigungsstellungen ausgeschaltet, oder die eigenen Planeten vor Angriffen geschützt werden.', 'Nachdem jede noch so kleine Nation eine Verteidigung errichtet hatte, welche mit Antares ohne tragbare Verluste nicht geknackt werden konnte, entschlossen sich die grösseren Nationen, ein neues Kampfschiff zu konstruieren. Man nahm den Rumpf eines Besiedlungsschiffes, baute Waffen und eine Panzerung ein, und das Hadar Schlachtschiff war geboren.', 50000, 31500, 19500, 12500, 0, 0, 0, 45, 90, 80, 0, 8500, 0, 4, 3200, 1260, 220, 1, 1, 3, 'transport,position,attack,flight,support,alliance', '0.50', 0, 28200, 7100, 13000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '113.500', 0, 0),
(7, 'POLLUX Bomber', 1, 'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.', 'Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass deshalb die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.', 9700, 21000, 11500, 8500, 0, 0, 0, 55, 80, 70, 0, 2000, 0, 2, 1200, 600, 120, 1, 1, 4, 'transport,position,attack,flight,support,alliance', '0.50', 0, 2600, 500, 18000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '50.700', 0, 0),
(8, 'SIRIUS Invasionsschiff', 1, 'Mit Hilfe dieses Raumschiffes können Planeten von anderen Spielern übernommen werden.', 'Es gab einmal ein florierendes Wirtschaftsimperium und die Infrastruktur ihrer Kolonien wurde von den anderen Völkern beneidet. Einer dieser bösen Nachbaren hatte die Idee, dass er so einen Planeten wirklich gut gebrauchen könnte. So wurde unter strengster Geheimhaltung dieses Invasionsschiff gebaut, welches die Planeten anderer Spieler übernehmen kann. Das Schiff hat aber nicht die grössten Erfolgschancen und es kann keine Hauptplaneten übernehmen. Trotzdem stellt dieses Schiff eine Bedrohung dar, deshalb sollte man seine Planeten nie unbewacht lassen.', 80000, 35000, 55000, 40500, 0, 0, 0, 15, 800, 500, 0, 20000, 0, 20, 600, 800, 500, 1, 1, 9, 'transport,position,attack,invade,flight,support,alliance', '0.50', 0, 2000, 3000, 180, 0, 1, 0, 1, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '210.500', 0, 0),
(9, 'ALGOL Transportschiff', 1, 'Dies ist ein kleines Transportschiff, dessen Lagerräume alle Arten von Waren aufnehmen können. ', 'Das Algol Transportschiff war das erste wirkliche Raumschiff, welches in Serienproduktion ging. Man wollte damit vor allem Rohstoffe zu anderen Planeten transportieren, damit man die natürlichen Ressourcen der verschiedenen Planeten besser ausnutzen kann. Deshalb hat man bei der Ausrüstung auf eine Bewaffnung weitestgehend verzichtet. Obwohl Algols mittlerweile veraltet sind, hat man dieses beliebte Schiff immer wieder mit neuen Motoren modifiziert, deshalb sieht man auch heute noch viele Transporter ähnlichen Typs.', 700, 180, 0, 500, 0, 0, 0, 25, 9, 4, 0, 15000, 0, 1, 800, 500, 250, 1, 1, 7, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 10, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.380', 0, 0),
(10, 'REGULUS Trümmersammler', 1, 'Mit diesem Schiff können die Trümmer der nach einer Schlacht zerstörten Schiffe eingesammelt und wiederverwendet werden.', 'Nachdem die Piraten durch die Entwicklung der mächtigen Kampfschiffe nicht mehr die unbewaffneten Transportkonvois überfallen konnten, entwickelten sie dieses Schiff, um mit ihm nach den grösseren Schlachten zwischen den kriegslustigen Imperien aufzutauchen, und ihren Lebensunterhalt aus den Überresten der zerstörten Schiffe zu gewinnen. Der Wert dieser Trümmersammler wurde schon bald erkannt, und ab dann führte niemand mehr Krieg, ohne sich nicht die Überreste der Schiffe zurück zu holen, um daraus neue Schiffe zu bauen.', 3000, 2000, 1000, 8000, 0, 0, 5, 33, 20, 20, 0, 15000, 0, 2, 600, 600, 200, 1, 1, 10, 'transport,collectdebris,position,attack,flight,support,alliance', '0.50', 0, 800, 1200, 20, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '14.000', 0, 0),
(11, 'RIGEL Dreadnought', 1, 'Dieses Schiff ist eine riesige fliegende Festung. ', 'Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ', 3350000, 2975000, 1750000, 750000, 0, 0, 0, 280, 2350, 3400, 0, 600000, 0, 560, 4800, 620, 400, 1, 1, 5, 'transport,position,attack,flight,support,alliance', '0.50', 0, 1000000, 1350000, 1750000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '8825.000', 0, 0),
(12, 'ELNATH Gassauger', 1, 'Dieses Schiff kann Wasserstoff aus der Atmosphäre von Gasplaneten einsaugen und daraus Tritium gewinnen.', 'Nachdem die Flotten immer grösser wurden, hatte man nicht mehr genug Tritium auf den Planeten zur Verfügung, um sie zu bewegen. Deshalb kam man auf die Idee, Wasserstoff von den unbewohnbaren Gasplaneten abzusaugen und es in Tritium umzuwandeln. Genau dafür wurde dieses Schiff konstruiert. Es wurde schnell klar, dass dieses Saugen äusserst rentabel ist und deshalb wurde der Gassauger soweit verbessert, dass heute eine grössere Flotte ohne ihn praktisch undenkbar ist.', 20000, 7500, 22200, 15000, 0, 0, 0, 55, 160, 130, 0, 9000, 0, 3, 600, 4300, 860, 1, 1, 12, 'transport,position,collectcrystal,collectfuel,flight,support', '0.50', 0, 650, 800, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '64.700', 0, 0),
(13, 'ANDROMEDA Kampfstern', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien.', 'Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.', 20000000, 10000000, 12000000, 12000000, 0, 0, 0, 800, 8000, 4000, 0, 6000000, 0, 990, 10000, 3501, 2450, 1, 1, 7, 'transport,position,attack,flight,support,alliance', '0.50', 0, 8500000, 9000000, 9500000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '54000.000', 0, 0),
(14, 'STARLIGHT Jäger', 1, 'Weiterentwicklung des ANTARES Jäger.', 'Parallel zu den Antares Jägern wurde der STARLIGHT Jäger entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.', 4900, 3400, 2400, 2100, 0, 0, 0, 2, 5, 6, 0, 800, 0, 1, 975, 22, 20, 1, 1, 1, 'transport,position,attack,flight,support,alliance', '0.50', 0, 2100, 1100, 1900, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '12.800', 0, 0),
(15, 'ONEFIGHT Kampfdrohne', 1, 'Die Kampfdrohne ist sehr nützlich, um zuerst die gegnerische Flotte zu zerstören und danach mit Transportern die Rohstoffe abzusahnen. Wie der Name schon sagt, sind diese Drohnen Einweg-Sonden; sie werden bei einem Angriff immer verbraucht.', 'Es gab zwei Nachbarn, die lange Zeit friedlich miteinander lebten, aus dem einfachen Grund, dass die Flotten beider Kontrahenten etwa gleich gross war; und niemand den anderen ohne Verluste hätte angreifen können. Das änderte sich, als der erste die Kampfdrohnen entwickelte, ein billiges Schiff, welches aber eine äusserst grosse Kampfkraft aufweist, aber sobald es von einer Waffe getroffen wird, explodiert. Als die Flotte des einen zerstört war, hatte man der Invasion nichts mehr entgegenzusetzen, und jetzt leben sie als eine Rasse wieder friedlich miteinander.', 200, 700, 300, 300, 0, 0, 0, 1, 15, 2, 0, 300, 0, 0, 17000, 20, 30, 1, 1, 2, 'position,attack,flight,support,alliance', '0.50', 0, 0, 0, 650, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.500', 0, 0),
(16, 'Handelsschiff', 1, 'Ein Schiff der neutralen Handelsgilde.', 'Ein Schiff der neutralen Handelsgilde. Es wird benutzt um Einkäufe im Markt zu den Käufern zu transportieren.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 100000, 0, 0, 10000, 60, 60, 0, 0, 0, 'market', '0.50', 0, 0, 0, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.000', 0, 0),
(17, 'TERRANIA Zerstörer', 1, 'Kann ein Gebäude bombadieren.', 'Den Terranern war die Infrastruktur ihrer Feinde ein Dorn im Auge, also entwickelten sie diesen Zerstörer, um den Gegner durch die Zerstörung seiner Infrastruktur zur Kapitulation zu zwingen. Der Terrania Zerstörer ist ein gutes Schiff, obwohl der Angriff nicht immer erfolgreich ist, da sich herausstellte, dass das Zielen vom Orbit aus nicht die leichteste Übung ist. Dafür kann ein erfolgreicher Bombenabwurf enormen Schaden hervorrufen.', 85000, 40000, 50000, 40000, 0, 0, 0, 100, 651, 525, 0, 50000, 0, 25, 3000, 1950, 1919, 1, 1, 0, 'transport,position,attack,bombard,flight,support,alliance', '0.50', 0, 20000, 19000, 60000, 1, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(18, 'PROMETHEUS Recycler', 1, 'Grosser Recycler.', 'Dieser Recycler wurde nach Prometheus dem Titanen, welcher gegen Zeus rebellierte, und den Menschen das Feuer brachte, benannt, da mit den Rohstoffen, welche die Terraner mit seiner Hilfe gewinnen, deren Flotten gebaut werden. Früher brachte Prometheus ihnen mit dem Feuer die Möglichkeit, eine Kultur zu entwickeln. Heute bringen viele Tausend Prometheus den Menschen mit ihren Rohstoffen die Grundlage, ihre Kultur weiterzuentwickeln.', 10000, 8000, 4000, 25000, 0, 0, 0, 42, 50, 80, 0, 90000, 0, 3, 1500, 360, 182, 1, 1, 0, 'transport,collectdebris,position,attack,flight,support,alliance', '0.50', 0, 800, 1000, 5, 1, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '47.000', 0, 0),
(19, 'GAIA Transporter', 1, 'Bewohnertransporter der Terraner.', 'Als die Erde wegen Überbevölkerung einen vollständigen Kollaps erlitt, musste sie schleunigst evakuiert werden, und dafür wurde dieser Transporter entwickelt. Die Bewohner wurden zu Zehntausenden bei normalerweise untragbaren Bedingungen in diese Kolosse gesteckt und verfrachtet. Nach dieser Katastrophe etablierte dieser Transporter sich zu einem beliebten Fährschiff, mit welchem die Leute zu den Vergnügungsplaneten flogen, um sich vom täglichen Arbeitsstress zu erholen.', 3500, 1000, 750, 1250, 0, 0, 0, 55, 100, 80, 0, 3000, 10000, 1, 900, 720, 360, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 750, 300, 50, 1, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '6.500', 0, 0),
(20, 'ANDREIA Bomber', 1, 'Dieser Bomber ermöglicht Giftgasangriffe.', 'Die Andorianer sind sehr invasionsfreudig, aber sie wollten die invasierten Planeten nicht der ursprünglichen Bevölkerung überlassen, da sie sich selber genug schnell vermehren können. Deshalb fliegen sie vorher mit den Andreia Bombern über die Planeten, welche dann die Bevölkerung mit Giftgas auslöschen. Brutal, aber effizient.', 85000, 40000, 50000, 40000, 0, 0, 0, 93, 525, 650, 0, 15000, 0, 25, 1000, 1200, 1320, 1, 1, 0, 'transport,position,attack,gasattack,flight,support,alliance', '0.50', 0, 25000, 9000, 10000, 2, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(21, 'ATLAS Transporter', 1, 'Grosser Transporter', 'Auch die Andorianer entwickelten einen grösseren Transporter, da sie nicht wollten, dass andere Rassen mit ihren Transportern ihre Ressourcen herumschippern konnten und sie von diesen abhängig wären. Die Atlas entwickelten sich zu viel genutzten Transportern im Andorianischen Imperium. Sie erwiesen sich als viel nützlicher, als es sich die Regierungsmitglieder jemals erhofft hätten.', 30000, 6000, 1000, 12500, 0, 0, 0, 55, 100, 10, 0, 325000, 0, 1, 3100, 720, 300, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 2, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '49.500', 0, 0),
(22, 'ZELOS Kreuzer', 1, 'Weiterentwicklung des HADAR Schlachtschiffes.', 'Nachdem sich der Bau von Hadar Schlachtschiffen durchgesetzt hatte, wollten die Andorianer dieses noch übertreffen. So wurde der Zelos Kreuzer entwickelt.\r\nDieses Schiff hat ungeheuer starke Schilde und ist sehr gut für die Verteidigung Planeten geeignet.', 121000, 44000, 50000, 45400, 0, 0, 0, 45, 160, 100, 0, 16000, 0, 10, 5500, 350, 320, 1, 1, 2, 'transport,position,attack,flight,support,alliance', '0.50', 0, 15000, 56500, 50000, 2, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '260.400', 0, 0),
(23, 'CENTAURUS Spioschiff', 1, 'Kann von einem anderen Spieler eine Technologie klauen.', 'Die Centauri waren äusserst stolz darauf, dass sie die höchsten Technologien aller Völker besassen. Entsprechend gross war der Neid, als sie von einem andern Volk in einer von ihnen vernachlässigten Technologie übertrumpft wurden. Also erfanden sie dieses Spionageschiff, mit dessen Hilfe sie den anderen Völkern etwaige höher entwickelte Technologien klauen können.', 85000, 40000, 50000, 40000, 0, 0, 0, 7, 125, 250, 0, 7500, 0, 45, 600, 2905, 2359, 1, 1, 0, 'transport,position,attack,spy,spyattack,flight,support,alliance', '0.50', 0, 3250, 2250, 500, 8, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(24, 'PEGASUS Gassauger', 1, 'Grosser Gassauger der Centauri.', 'Um ihre teuren Forschungen zu betreiben, mussten die Centauri einen neuen Gassauger entwerfen, welcher eine grössere Kapazität hat, da die normalen Sauger die Bedürfnisse ihrer Forschungslabore nicht stillen konnten. Der Pegasus hat  eine wesentlich grössere Saugkapazität als herkömmliche Sauger, und durch seine hoch entwickelten Saugarme hat er die grössere Effizienz. Dies ist die Antwort der Centauri auf Tritiumknappheit.', 60000, 28000, 25000, 60000, 0, 0, 0, 38, 5, 8, 0, 40000, 0, 3, 1500, 950, 1450, 1, 1, 0, 'transport,position,attack,collectcrystal,collectfuel,flight,support,alliance', '0.50', 0, 800, 1000, 5, 8, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '173.000', 0, 0),
(25, 'EUROPA Fighter', 1, 'Mittelgrosses, für seine Verhältnisse jedoch sehr starkes Kriegsschiff der Centauri.', 'Die Centauri suchten ihren Vorteil in der Überlegenheit der Technologien, aber als die Rigel die Herrschaft über die Schlachtfelder übernahmen, entwickelten sie ihren eigenen Prototypen, den Europa Fighter. Heutzutage eines der stärksten Raumschiffe der mittleren Kampfklasse. Die Europas sind bei weitem nicht so stark wie Rigel, jedoch haben sie eine sehr kurze Startzeit, was sie sehr gefährlich macht.', 20000, 11000, 18000, 8000, 0, 0, 0, 85, 35, 25, 0, 22000, 0, 5, 6900, 280, 870, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 6250, 12500, 7500, 8, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.000', 0, 0),
(26, 'VORGONIA Bomber', 1, 'Kann einen Antraxangriff ausführen', 'Die Vorgonen verachten alle Rassen, die sich von ihrer eigenen unterscheiden, und entschieden sich deshalb dafür, dieses Übel mit allen Mitteln auszumerzen. Dafür bauten sie die Antraxbomber, um mit dem Kampfstoff Antrax die systematische Vernichtung feindlicher Völker zu beginnen. Zusätzlich wird die Nahrung auf dem Planeten vergiftet und dadurch unbrauchbar. Da viele Völker danach von Hungersnöten heimgesucht wurden, haben viele Bewohner Angst vor diesen Bombern und sind deshalb gegen Kriege mit Vorgonen.', 85000, 40000, 50000, 40000, 0, 0, 0, 80, 625, 550, 0, 13500, 0, 42, 1000, 1358, 1989, 1, 1, 0, 'transport,position,attack,antrax,flight,support,alliance', '0.50', 0, 28000, 8000, 8000, 7, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(27, 'PAN Transporter', 1, 'Bewohnertransporter der Vorgonen.', 'Nachdem die Vorgonen die feindliche Bevölkerung mit ihren Antraxbombern eliminiert hatten, mussten sie ihre eigene Bevölkerung auf diese Planeten transportieren. Dazu entwickelten sie die Pan Transporter.', 3250, 1000, 750, 1250, 0, 0, 0, 55, 100, 50, 0, 2000, 6750, 1, 1000, 720, 360, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 7, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '6.250', 0, 0),
(28, 'IKAROS Jäger', 1, 'Schwebt in der Atmosphäre und hat somit keine Start- und Landezeit.', 'Die Vorgonen raubten alle ihre direkten Nachbarn mit Jägern aus, und bald einmal gab es erste Piloten, die gar nie mehr richtig auf dem Heimatplaneten landeten, sondern im Dauereinsatz waren. Dank ihren unerwarteten Raubzügen konnten sie viele Rohstoffe erbeuten. Diese Elitepiloten waren aber bald nicht mehr zufrieden mit den normalen Schiffen, also entwickelten sie ihre Jäger weiter, bis die Ikaros entstanden, die im Orbit des Planeten stationiert sind, so dass sie sofort und ohne Treibstoffverbrauch starten und landen können.', 4000, 2000, 1000, 2000, 0, 0, 0, 30, 0, 0, 0, 20000, 0, 2, 2400, 0, 0, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 350, 2750, 1250, 7, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '9.000', 0, 0),
(29, 'MARAUDER Transporter', 1, 'Grosser Transporter', 'Auch die Ferengi sahen sich genötigt, grosse Transporter zu entwickeln, wenn auch nicht aus denselben Gründen wie die anderen Rassen. Die Ferengi hatten wegen ihrer Titanproduktion alle ihre Lager längstens überfüllt und keinen Platz mehr, um grössere zu bauen. Also erschufen sie mit den Marauder Transportern eine Art fliegendes Lager, damit sie ihr Titan im Weltraum zwischenlagern konnten, wo es genug Platz dafür hat.', 33000, 6000, 1000, 4000, 0, 0, 0, 60, 100, 50, 0, 325000, 0, 1, 3700, 720, 333, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 6, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '44.000', 0, 0),
(30, 'BELL Zerstörer', 1, 'Dieses Schiff hat einen riesigen Schutzschildgenerator an Bord.', 'Die Ferengi waren an einem Punkt angelagt, wo sie ihr Titan nicht mehr verbrauchen konnten. Es musste etwas erfunden werden, das mit möglichst viel Titan und wenig Zusatzstoffen gebaut werden konnte. Aus diesem Bedürfnis entstand der Bell Zerstörer.\r\nDieses Schiff hat einen riesigen Schutzschildgenerator an Bord, welcher einen Schutzschild erzeugt, der kaum überwunden werden kann. Aufgrund der Masse des Schiffes, dessen Antrieben und dem eingebauten Generator gehört der Bell Zerstörer nicht zu den schnellsten Schiffen der Galaxien, jedoch zu den Stärksten im Kampf.\r\nEin Nachteil des Bell Zerstörers ist sein immenser Tritiumverbrauch, der aus dem grossen Gewicht und der tiefen Fluggeschwindigkeit resultiert.', 60000, 5000, 21250, 5000, 0, 0, 0, 100, 150, 80, 0, 40000, 0, 30, 4125, 1230, 890, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 6000, 35000, 1500, 6, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '91.250', 0, 0),
(31, 'RUTICULUS Sammler', 1, 'Allessammler der Ferengi.', 'Den Ferengi war es zu umständlich, Trümmersammler, Asteroidensammler und Gassauger zu haben, also konzipierten sie kurzerhand ein Schiff, welches alles kann. Der Ruticulus Sammler ist deshalb ein äusserst praktisches Schiff, da jeder sie nach seinem Wunsch und entsprechend der jeweiligen Situation anwenden kann.', 20000, 10000, 15000, 30000, 0, 0, 0, 40, 20, 20, 0, 15000, 0, 1, 600, 640, 1800, 1, 1, 0, 'transport,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,flight,support,alliance', '0.50', 0, 800, 1000, 50, 6, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '75.000', 0, 0),
(32, 'RIGELIA Bomber', 1, 'Kann ein Gebäude für eine bestimmte Zeit mittels EMP-Technologie ausser Kraft setzten.', 'Der Rigelia Bomber kann mit seinen EMP-Angriffen die feindlichen Gebäude für eine kurze Zeit ausser Kraft setzen, was in einem Krieg schwerwiegende Folgen haben kann. Obwohl der Bomber sehr teuer ist, und von seiner Kampfkraft her gesehen kaum genutzt werden sollte, sind viele Generäle der Meinung, dass seine Bomben genug effektiv sind, so dass man diese Möglichkeit in einem Krieg immer einsetzen sollte.', 85000, 40000, 50000, 40000, 0, 0, 0, 55, 325, 250, 0, 15000, 0, 41, 1000, 1520, 2001, 1, 1, 0, 'transport,position,attack,emp,flight,support,alliance', '0.50', 0, 25000, 6500, 12500, 3, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(33, 'EOS Transporter', 1, 'Grosser Transporter', 'Als die Cardassianer und die Minbari die grossen Transporter entwickelt hatten, konnten die Rigelianer dem nicht nachstehen und fertigten sofort ihre eigene Version eines grossen Transporters an. Vom Prinzip her ist es genau dasselbe Schiff wie der Saiph Transporter der Minbari. Die Rigelianer passten einfach das Design und die Steuergeräte ihren Bedürfnisse an.', 25000, 7000, 1000, 3000, 0, 0, 0, 55, 100, 50, 0, 325000, 0, 1, 3800, 252, 435, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 3, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '36.000', 0, 0),
(34, 'HELIOS Drohne', 1, 'Weiterentwicklung der Onefight Kampfdrohne. Rassenschiff der Rigelianer.', 'Die Rigelianer waren von den Onefight Kampfdrohnen begeistert. Sie steckten deshalb ihre ganzen Forschungsmittel in deren Weiterentwicklung. So entstand die Helios Drohne: Diese Drohne ist noch effizienter als die Onefight und kann in genügend grosser Anzahl den Gegner empfindlich treffen. Ausserdem können die Helios im Gegensatz zu den Onefights einen Kampf auch überleben.\r\nDie Helios sind überall wegen ihrer Kampfkraft gefürchtet, und da sie auf dem Standardantrieb der Drohnen aufbauen, haben sie auch eine hohe Geschwindigkeit, weshalb man sich nie vor einem Angriff sicher fühlen kann.', 2500, 6200, 2000, 2300, 0, 0, 0, 1, 5, 5, 0, 500, 0, 0, 15000, 40, 60, 1, 1, 0, 'position,attack,flight,support,alliance', '0.50', 0, 1, 0, 6000, 3, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '13.000', 0, 0),
(36, 'CARDASSIA Mutterschiff', 1, 'Heilt während dem Kampf eine gewisse Anzahl Schild- und Strukturpunkte.', 'Nachdem die Cardassianer mit ihren Nilams die ganze Galaxie in Angst und Schrecken versetzt hatten, schlossen sich alle anderen Rassen zu einem Bund zusammen, um die Cardassianer zu vernichten. Trotzdem hatten sie nicht mit dem neuen Geniestreich der Cardassianer gerechnet: Den Mutterschiffen. Mit diesem hoch entwickelten Raumschiff können die Cardassianer ihre Flotte während dem Kampf reparieren, um so Verluste auszugleichen. Nur dank der Hilfe dieses Schiffes konnten die Cardassianer den immerwährenden Angriffen standhalten.', 70000, 27500, 27500, 35000, 0, 0, 0, 60, 70, 10, 0, 1500, 0, 5, 1800, 726, 333, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 10000, 7000, 3000, 125, 9, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '160.000', 0, 0),
(37, 'DEMETER Transporter', 1, 'Grosser Transporter', 'Die Cardassianer waren allgemein wegen ihren vielen Rohstoffen beneidet, vor allem wegen ihrer Nahrung, die sie wie keine anderen herstellen können. Um sich vor Übergriffen zu schützen und um ihre Gegner im Unklaren über ihre wahren Rohstoffmengen zu lassen, entwickelten sie diese Transporter, welche mit den Rohstoffen irgendwo in der Ewigkeit des Alls herumfliegen, damit sie nicht gefunden werden. Die Cardassianer sind die einzigen, deren Organisation solch perfekte Nachschublinien zustande bringt.', 23000, 8300, 1200, 1500, 0, 0, 0, 10, 100, 10, 0, 350000, 0, 1, 3500, 585, 395, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 9, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '34.000', 0, 0),
(38, 'NILAM Fighter', 1, 'Ein starkes Kampfschiff aus der Mittelschweren Klasse, entwickelt von den Cardassianern.', 'Den Cardassianern waren Starlights von Anfang an zu langsam und Drohnen zu schwach. So erfanden sie die Nilam, welche sie zu gefürchteten Jägern entwickelten, da sie spezielle Antriebe haben, die ausserordentlich schnell sind. Die Cardassianer benutzen die Jäger vor allem, um ihre Militärdiktatur aufrechtzuerhalten. Sie wollen schnell reagieren und überall bereitstehen können. Dafür eignen sich die Nilams am besten. Sie kommen aus dem Nichts und verschwinden sofort wieder, nachdem sie die Schlacht gewonnen haben.', 7150, 4000, 2000, 3000, 0, 0, 0, 40, 100, 100, 0, 5000, 0, 4, 6250, 456, 325, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 2900, 2000, 2500, 9, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '16.150', 0, 0),
(40, 'SERA Kreuzer', 1, 'Dieses Schiff kann dem Angegriffen eine Flotte vortäuschen, die gar nicht vorhanden ist.', 'Den räuberischen Orionern gefiel es nicht, dass sich die Feinde auf ihre Angriffe vorbereiten konnten. Die Lösung fanden sie in der Konstruktion vom Sera Kreuzer. Dieser hat die Fähigkeit, Hologramme anderer Schiffe zu erstellen und dem Gegner damit eine grosse angreifende Flotte vortäuschen. Ziel ist es, schwächere Flotten zu vertreiben, so dass die Rohstoffe ungeschützt rumliegen.', 15500, 10500, 6000, 5500, 0, 0, 0, 30, 65, 45, 0, 6000, 0, 2, 8500, 640, 156, 1, 1, 0, 'transport,position,attack,fakeattack,flight,support,alliance', '0.50', 0, 500, 5500, 1500, 4, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '37.500', 0, 0),
(41, 'HYPOS Drohne', 1, 'Kann ein Trümmerfeld beim Gegner erstellen, ohne dass dieser etwas merkt.', 'Die Orioner wollten nicht mehr angreifen müssen, um ein Trümmerfeld zu erstellen. Deshalb schicken sie diese Drohnen vor grossen Schlachten los, um beim Gegner ein klitzekleines Trümmerfeld zu erstellen. Damit konnte den Navigationscomputern der Sammler ein gültiges Ziel zugewiesen werden. Zu diesem Zweck muss sich die Drohne beim Gegner in die Luft sprengen, was selten für mehr als eine Sternschnuppe wahrgenommen wird.', 500, 300, 50, 200, 0, 0, 0, 6, 10, 10, 0, 2000, 0, 0, 17000, 1, 1, 1, 1, 0, 'position,attack,createdebris,flight,support,alliance', '0.50', 0, 10, 0, 1, 4, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.050', 0, 0),
(42, 'MINBARI Jäger', 1, 'Wenn er alleine in oder in einem Flottenverband aus lauter Minbari Jägern fliegt, ist er für die gegnerische Flottenkontrolle nicht sichtbar.', 'Die Minbari sahen es gar nicht gerne, als man ihre Flotten schon im Anflug entdeckte und eine entsprechende Verteidigung bereitstellte. Deshalb liessen sie die besten Köpfe der Galaxie zusammenkommen, um dieses Schiff zu entwickeln, welches durch seine perfekte Tarnung erst im allerletzten Moment entdeckt werden kann. Und dann ist es bereits zu spät...', 20500, 13500, 13500, 10000, 0, 0, 0, 20, 120, 130, 0, 15000, 0, 10, 6500, 1189, 125, 1, 1, 0, 'transport,position,attack,stealthattack,flight,support,alliance', '0.50', 0, 13000, 4500, 5000, 5, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.500', 0, 0),
(43, 'SAIPH Transporter', 1, 'Grosser Transporter der Minbari.', 'Die Minbari entwickelten diese grossen Transporter, um ihren steigenden Rohstofftransport-Bedürfnissen nachzukommen. Die Rohstoffmengen stiegen immer weiter an, und irgendwann war auch die Kapazität der Unukalhai ausgeschöpft. Nun musste eine neue Lösung gefunden werden, und die Ingenieure der Minbari entwickelten diesen grossen Transporter.', 26000, 6000, 3000, 9000, 0, 0, 0, 35, 100, 50, 0, 350000, 0, 1, 5200, 721, 326, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 5, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '44.000', 0, 0),
(44, 'WEZEA Fighter', 1, 'Kampfschiff der Minbari, welches auch Gassaugen kann.', 'Die Minbari liebten es seit eh und je, über die Gasplaneten zu fliegen, da sie von den unbeschreiblich schönen Polarlichtern fasziniert sind, welche man dort beobachten kann. Deshalb gingen sie so weit, sogar ihre Jäger so zu konstruieren, dass sie zu Gasplaneten fliegen und auch Gas saugen können.\r\nEs ist ihnen sogar gelungen, den neuartigen Solarantrieb zu integrieren. Damit verbraucht der WEZEA Fighter nur Tritium für den Start und die Landung.', 14000, 7000, 9000, 8000, 0, 0, 0, 0, 700, 400, 0, 12500, 0, 3, 5100, 1750, 540, 1, 1, 0, 'transport,position,attack,collectfuel,flight,support,alliance', '0.50', 0, 7250, 3500, 5800, 5, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '38.000', 0, 0),
(45, 'ORION Fighter', 1, 'Kampfschiff der Mittelschweren Klasse, entworfen von den Orionern. Kann bis zu 75% der Rohstoffe von einem fremden Planeten mitnehmen. ', 'Den Orionern war die Menge, welche sie normalerweise mit ihren Schiffen von gegnerischen Planeten erbeuten konnten, viel zu wenig. Der Orion Fighter ist ihre Antwort auf dieses Problem. Ein starkes Raumschiff, welches so konzipiert ist, dass es 50% mehr Rohstoffe als Beute mitnehmen kann als alle anderen Schiffe. Zusätzlich hat der Orion schlagkräftige Waffen, was den Fighter zum optimalen Schiff für Piraterie macht.', 35000, 10500, 12500, 5500, 0, 0, 0, 65, 100, 90, 0, 17500, 0, 4, 8000, 451, 352, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.75', 0, 7500, 7000, 14000, 4, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '63.500', 0, 0),
(46, 'FORNAX Asteroidensammler', 1, 'Kann Asteroidenfelder anfliegen und dort Rohstoffe sammeln.', 'Da die Gassauger grossen Erfolg hatten, dachte man, dass man das auch mit Asteroidenfeldern versuchen könne, so dass man auch die anderen Rohstoffe aus dem Weltraum gewinnen konnte. Leider war die praktische Umsetzung schwieriger, da eine sichere  Navigation innerhalb der Asteroidenfelder sich als praktisch unmöglich erwies. Deshalb ist dieses Konzept fehlgeschlagen, da die Sammler schneller von Asteroiden getroffen werden, als dass sie genug Rohstoffe holen können, um ihre Herstellungskosten zurückzugewinnen.', 15000, 5000, 25000, 9000, 0, 0, 0, 65, 100, 120, 0, 45000, 0, 8, 600, 4350, 1050, 1, 1, 11, 'transport,position,attack,collectmetal,flight,support,alliance', '0.50', 0, 250, 1000, 50, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '54.000', 0, 0),
(47, 'TITAN Transporter', 1, 'Dies ist ein relativ billiger und sehr schneller, grosser Transporter.', 'Dies ist ein relativ billiger und sehr schneller, grosser Transporter, allerdings zeigt sich der Preis in seiner Qualität. Er ist sehr schwach. Dieser Transporter setzt auf den Solarantrieb, wodurch er durch ein Sonnensegel unglaublich schnell ohne Treibstoffverbrauch fliegen kann.', 35000, 7000, 5000, 10000, 0, 0, 0, 0, 1000, 600, 0, 150000, 0, 45, 4100, 550, 460, 1, 1, 5, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 50, 20, 1, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.000', 0, 0),
(50, 'ASTERIO Sammler', 1, 'Das effizienteste Asteroidensammlerschiff in ganz Andromeda.', 'Auch wenn die Fornax Asteroidensammler mehr oder weniger erfolglos waren, hiess das noch lange nicht, dass das Konzept unbrauchbar war. Es wurde weiterentwickelt und so entstand der Asterio Sammler, welcher zwar eine kleinere Ladefläche als der Fornax aufweist, dafür aber wesentlich schneller unterwegs\r\nist. Bis jetzt ist es das effizienteste Asteroidensammelschiff in ganz Andromeda.\r\nDieses Schiff ist auch dazu geeignet, um Trümmerfelder anzusteuern und diese zu recyclen.', 3200, 1200, 2500, 2000, 0, 0, 0, 4, 1, 1, 0, 11000, 0, 1, 4500, 3230, 560, 1, 1, 3, 'transport,collectdebris,position,attack,collectmetal,flight,support,alliance', '0.50', 0, 50, 2, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '8.900', 0, 0),
(51, 'HAARP Spionagesonde', 1, 'Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde.', 'Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde. Sie ist enorm schnell und gut geeignet zum Ausräumen verteidigungsloser Planeten sowie zum Ausspionieren weit entfernter Galaxien. ', 1000, 1000, 1000, 500, 0, 0, 0, 1, 1, 1, 0, 800, 0, 0, 60000, 5, 4, 1, 1, 4, 'position,attack,spy,flight,support,alliance', '0.50', 0, 0, 1, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '3.500', 0, 0),
(52, 'AURORA Sonde', 1, 'Diese Sonde wurde entwickelt, da viele schnelle Schiffe nicht genug Treibstoff für lange Strecken mitnehmen konnten.', 'Diese Sonde wurde entwickelt, da viele schnelle Schiffe nicht genug Treibstoff für lange Strecken mitnehmen konnten. Deshalb hat diese sehr schwache und teure Sonde einen riesigen Laderaum, in dem sie den Treibstoff für die mitfliegenden Schiffe bereit halten kann. \r\n\r\nSie wird bei einem Kampf sehr schnell zerstört, da sie praktisch nur aus dünnbeschichteten Tanks & den notwendigen Antrieb besteht.', 20000, 18000, 10000, 9000, 0, 0, 0, 25, 10, 5, 0, 35000, 0, 0, 15000, 20, 15, 1, 1, 2, 'transport,position,attack,flight,support,alliance', '0.50', 0, 1, 1, 1, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.000', 0, 0),
(53, 'IMPERIALER Kreuzer', 1, 'Dies ist eines der grössten Schiffe in Andromeda.', 'Dies ist eines der grössten Schiffe in Andromeda. Es ist enorm stark gepanzert, hat allerdings einen relativ schwachen Schild. Seine Waffen sind aber nicht zu verachten. Es ist das grösste Schiff mit einem Sonnensegel zur Antriebsunterstützung.', 750000, 600000, 415000, 365000, 0, 0, 0, 45, 790, 560, 0, 230000, 0, 35, 5800, 860, 420, 1, 1, 6, 'transport,position,attack,flight,support,alliance', '0.50', 0, 505000, 85000, 335000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2130.000', 0, 0),
(54, 'Alien-Jäger', 1, 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 99999999, 99999999, 99999999, 99999999, 99999999, 0, 0, 1, 0, 0, 0, 1000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', '0.50', 0, 500, 700, 50, 0, 1, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '499999.995', 0, 0),
(55, 'Alien-Kampschiff', 1, 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 99999999, 99999999, 99999999, 99999999, 99999999, 0, 0, 1, 0, 0, 0, 5000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', '0.50', 0, 5000, 7000, 500, 0, 1, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '499999.995', 0, 0),
(56, 'Alien-Mutterschiff', 1, 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 99999999, 99999999, 99999999, 99999999, 99999999, 0, 0, 1, 0, 0, 0, 10000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', '0.50', 0, 50000, 70000, 5000, 0, 1, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '499999.995', 0, 0),
(57, 'ANDROMEDA Mysticum', 1, 'Ein einmaliges Schiff mit speziellen Fähigkeiten.', 'Ein einmaliges Schiff mit speziellen Fähigkeiten.', 58000, 67000, 43600, 37500, 0, 0, 0, 75, 400, 400, 0, 50000, 0, 10, 5000, 950, 1350, 1, 1, 0, 'position,flight', '0.50', 0, 50000, 50000, 0, 0, 1, 0, 3, 0, 1, 1, 0, 350, '2.00', '0.03', '0.03', '0.03', '0.00', '0.03', '0.00', '0.07', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '206.100', 0, 0),
(59, 'MINBARI Mysticum', 1, 'Das Spezialschiff für die Minbari.', 'Das Spezialschiff für die Minbari.', 700000, 550000, 390000, 480000, 120000, 0, 0, 63, 850, 360, 0, 65000, 0, 33, 4100, 1300, 710, 1, 1, 0, 'position,flight', '0.50', 0, 70000, 70000, 0, 5, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.01', '0.02', '0.00', '0.00', '0.00', '0.05', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '2240.000', 0, 0),
(60, 'ANDORIA Mysticum', 1, 'Das Spezialschiff für die Andorianer.', 'Das Spezialschiff für die Andorianer.', 670000, 500000, 350000, 480000, 0, 0, 0, 60, 600, 520, 0, 68000, 0, 30, 5300, 600, 1020, 1, 1, 0, 'position,flight', '0.50', 0, 110000, 86000, 0, 2, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.02', '0.02', '0.00', '0.00', '0.10', '0.00', '0.00', '0.14', '0.00', '0.00', '0.00', '0.00', '2000.000', 0, 0),
(61, 'CARDASSIA Mysticum', 1, 'Das Spezialschiff für die Cardassianer.', 'Das Spezialschiff für die Cardassianer.', 750000, 530000, 320000, 450000, 250000, 0, 0, 75, 450, 300, 0, 65000, 0, 55, 5300, 840, 1150, 1, 1, 0, 'position,flight', '0.50', 0, 1500000, 50000, 0, 9, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.01', '0.01', '0.07', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2300.000', 0, 0),
(62, 'CENTAURI Mysticum', 1, 'Das Spezialschiff für die Centauri.', 'Das Spezialschiff für die Centauri.', 850000, 630000, 360000, 450000, 60000, 0, 0, 65, 300, 360, 0, 120000, 0, 45, 3500, 1080, 780, 1, 1, 0, 'position,flight', '0.50', 0, 65000, 25000, 0, 8, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.01', '0.01', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.03', '0.00', '0.00', '0.00', '2350.000', 0, 0),
(63, 'FERENGI Mysticum', 1, 'Das Spezialschiff für die Ferengi.', 'Das Spezialschiff für die Ferengi.', 930000, 400000, 360000, 520000, 0, 0, 0, 78, 600, 450, 0, 120000, 0, 50, 5000, 870, 980, 1, 1, 0, 'position,flight', '0.50', 0, 200000, 0, 0, 6, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.03', '0.03', '0.00', '0.05', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2210.000', 0, 0),
(64, 'ORION Mysticum', 1, 'Das Spezialschiff für den Orioner.', 'Das Spezialschiff für den Orioner.', 500000, 450000, 680000, 460000, 50000, 0, 0, 80, 500, 400, 0, 175000, 0, 60, 6000, 850, 1320, 1, 1, 0, 'position,flight', '0.50', 0, 80000, 110000, 0, 4, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.01', '0.01', '0.00', '0.00', '0.20', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2140.000', 0, 0),
(65, 'RIGELIA Mysticum', 1, 'Das Spezialschiff für die Rigelianer.', 'Das Spezialschiff für die Rigelianer.', 450000, 760000, 390000, 330000, 100000, 0, 0, 65, 720, 250, 0, 95000, 0, 52, 4500, 750, 1120, 1, 1, 0, 'position,flight', '0.50', 0, 75000, 120000, 0, 3, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.01', '0.01', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.12', '2030.000', 0, 0),
(66, 'TERRANIA Mysticum', 1, 'Das Spezialschiff für die Terraner.', 'Das Spezialschiff für die Terraner.', 650000, 420000, 350000, 530000, 100000, 0, 0, 75, 650, 760, 0, 120000, 0, 56, 3800, 1050, 860, 1, 1, 0, 'position,flight', '0.50', 0, 115000, 85000, 0, 1, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.02', '0.01', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.10', '0.00', '0.00', '2050.000', 0, 0),
(67, 'VORGONIA Mysticum', 1, 'Das Spezialschiff für die Voronen.', 'Das Spezialschiff für die Voronen.', 550000, 550000, 550000, 550000, 200000, 0, 0, 80, 850, 490, 0, 130000, 0, 60, 5200, 1230, 750, 1, 1, 0, 'position,flight', '0.50', 0, 100000, 100000, 0, 7, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.02', '0.02', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.10', '0.00', '2400.000', 0, 0);
INSERT INTO `ships` (`ship_id`, `ship_name`, `ship_type_id`, `ship_shortcomment`, `ship_longcomment`, `ship_costs_metal`, `ship_costs_crystal`, `ship_costs_fuel`, `ship_costs_plastic`, `ship_costs_food`, `ship_costs_power`, `ship_power_use`, `ship_fuel_use`, `ship_fuel_use_launch`, `ship_fuel_use_landing`, `ship_prod_power`, `ship_capacity`, `ship_people_capacity`, `ship_pilots`, `ship_speed`, `ship_time2start`, `ship_time2land`, `ship_show`, `ship_buildable`, `ship_order`, `ship_actions`, `ship_bounty_bonus`, `ship_heal`, `ship_structure`, `ship_shield`, `ship_weapon`, `ship_race_id`, `ship_launchable`, `ship_fieldsprovide`, `ship_cat_id`, `ship_fakeable`, `special_ship`, `ship_max_count`, `special_ship_max_level`, `special_ship_need_exp`, `special_ship_exp_factor`, `special_ship_bonus_weapon`, `special_ship_bonus_structure`, `special_ship_bonus_shield`, `special_ship_bonus_heal`, `special_ship_bonus_capacity`, `special_ship_bonus_speed`, `special_ship_bonus_pilots`, `special_ship_bonus_tarn`, `special_ship_bonus_antrax`, `special_ship_bonus_forsteal`, `special_ship_bonus_build_destroy`, `special_ship_bonus_antrax_food`, `special_ship_bonus_deactivade`, `ship_points`, `ship_alliance_shipyard_level`, `ship_alliance_costs`) VALUES
(68, 'ENERGIJA Solarsatellit', 1, 'Ein Satellit, der im Orbit schwebt und durch Solarpanels Energie gewinnt, welche dann auf dem Planeten verwendet werden kann.', 'Da einige (neu entwickelte) Gebäude enorme Energiemengen verschlingen, wurde der Solarsatellit entwickelt. Diese Sonde wird im Orbit stationiert und erzeugt Energie mit Hilfe der Sonne. Die Energieausbeute pro Solarsatellit ist jedoch abhängig von der jeweiligen Planetentemperatur und der jeweiligen Entfernung zur Sonne.\r\nDie Sonden werden bei einem feindlichen Angriff abgeschossen.', 300, 1500, 100, 100, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, 0, 0, 0, 1, 1, 13, 'flight', '0.50', 0, 100, 50, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2.000', 0, 0),
(69, 'TEREBELLUM Analysator', 1, 'Diese kleine Sonde wurde dafür geschaffen, um Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.', 'Diese kleine Sonde wurde dafür geschaffen, um Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.', 2000, 4500, 3000, 3000, 0, 0, 0, 2, 50, 2, 0, 500, 0, 0, 70000, 10, 1, 1, 1, 1, 'position,analyze,flight,support', '0.50', 0, 100, 200, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '12.500', 0, 0),
(70, 'LORIAL Transportschiff', 1, 'Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.', 'Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.', 17000, 11000, 15000, 7000, 0, 0, 0, 10, 50, 10, 0, 475000, 0, 1, 800, 600, 500, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 200, 500, 50, 10, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '50.000', 0, 0),
(71, 'AURIGA Explorer', 1, 'Dient zur Erkundung der unbekannten Weiten der Galaxie.', 'Dient zur Erkundung der unbekannten Weiten der Galaxie.', 1000, 800, 0, 0, 0, 0, 0, 1, 5, 0, 0, 100, 0, 0, 1500, 10, 0, 1, 1, 6, 'position,explore,flight,support', '0.50', 0, 50, 20, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.800', 0, 0),
(72, 'SERRAKIN Mysticum', 1, 'Das Spezialschiff für die Serrakin', 'Das Spezialschiff für die Serrakin', 800000, 580000, 350000, 450000, 150000, 0, 0, 60, 850, 500, 0, 85000, 0, 45, 5300, 800, 1000, 1, 1, 0, 'position', '0.50', 0, 85000, 95000, 0, 10, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.00', '0.03', '0.00', '0.05', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2330.000', 0, 0),
(73, 'SUPRANALIS Jäger', 1, 'Weiterentwicklung des ANTARES Jäger.', 'Parallel zu den Antares Jägern wurde der STARLIGHT Jäger entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.', 24500, 17000, 10500, 12000, 0, 0, 0, 2, 5, 6, 0, 800, 0, 1, 9750, 22, 20, 0, 0, 2, 'transport,position,attack,flight,support,alliance', '0.50', 0, 21000, 11000, 19000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '64.000', 1, 3000),
(74, 'SUPRANALIS Bomber', 1, 'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.', 'Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass deshalb die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.', 48500, 105000, 42500, 57500, 0, 0, 0, 550, 800, 700, 0, 2000, 0, 2, 2400, 300, 60, 0, 0, 5, 'transport,position,attack,flight,support,alliance', '0.50', 0, 26000, 5000, 180000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '253.500', 3, 10000),
(76, 'SUPRANALIS Dreadnought', 1, 'Dieses Schiff ist eine riesige fliegende Festung. ', 'Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ', 16750000, 14875000, 3750000, 8750000, 0, 0, 0, 2800, 23500, 34000, 0, 600000, 0, 560, 9600, 310, 200, 0, 0, 9, 'transport,position,attack,flight,support,alliance', '0.50', 0, 10000000, 13500000, 17500000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '44125.000', 5, 17000),
(77, 'SUPRANALIS Kampfstern', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien.', 'Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde dieser Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist nur, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.', 100000000, 50000000, 60000000, 60000000, 0, 0, 0, 8000, 80000, 40000, 0, 6000000, 0, 990, 20000, 1750, 1250, 0, 0, 13, 'transport,position,attack,flight,support,alliance', '0.50', 0, 85000000, 90000000, 95000000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '270000.000', 7, 27000),
(78, 'SUPRANALIS Ultra', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien (nun aber wirklich ^^)', 'Der Andromeda Kampfstern galt lange als DAS Kampfschiff schlechthin und nicht wenige behaupten, dass es nicht möglich sei, seine Grösse und Stärke zu übetreffen, doch genau dieses Ziel hatten diverse Imperatoren einer mächtigen Allianz Namens \\"Supranalis Ultra\\".\r\nNach vielen Jahren, unzähligen Arbeitsstunden und diversen Todesopfern war der Prototyp dieses Superschiffs fertig.\r\nEtwas noch nie Dagewesenes wurde erschaffen um die Kontrolle eines ganzen Universums an sich zu reissen...', 1000000000, 500000000, 600000000, 600000000, 0, 0, 0, 10000, 100000, 100000, 0, 50000000, 0, 100000, 20000, 5000, 3000, 0, 0, 14, 'transport,position,attack,flight,support,alliance', '0.50', 0, 10000000, 10000000, 10000000, 0, 1, 0, 6, 0, 0, 1, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2700000.000', 10, 150000),
(79, 'SCORPIUS ZIBAL Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 3900, 3100, 2100, 1500, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '10.600', 0, 0),
(80, 'SCORPIUS SPICA Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 800, 475, 0, 425, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.700', 0, 0),
(81, 'SCORPIUS POLARIS Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 1000, 700, 300, 500, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2.500', 0, 0),
(82, 'SCORPIUS PHOENIX Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 6500, 3500, 3000, 1900, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '14.900', 0, 0),
(83, 'SERPENS Kommandoschiff', 1, 'Fürht Kommandoaktionen aus.', 'Fürht Kommandoaktionen aus.', 3000, 5000, 5000, 2000, 0, 0, 0, 10, 30, 5, 0, 500, 0, 10, 5000, 60, 20, 0, 0, 8, 'position,attack,flight,hijack', '0.50', 0, 2000, 10, 10, 0, 1, 0, 1, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '15.000', 0, 0),
(85, 'Weltenvernichter V2', 1, '', '', 1000000, 1000000, 1000000, 1000000, 1000000, 0, 0, 0, 1, 1, 0, 1000000000, 1000000000, 1, 1000000000, 1, 1, 1, 1, 11, 'transport,position,attack,stealthattack,emp', '0.50', 80000000, 10000000, 10000000, 10000000, 0, 1, 0, 1, 0, 0, 0, 0, 0, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '5000.000', 0, 0),
(90, 'Weltenverteidiger', 1, 'bla', 'bla', 1000000, 1000000, 1000000, 1000000, 0, 0, 0, 0, 1, 1, 0, 1000000000, 1000000000, 1, 1000000, 1, 1, 1, 0, 12, 'transport,fetch,position,attack,invade,spyattack,stealthattack,fakeattack,bombard,emp,antrax,gasattack,collectcrystal,flight,support,alliance', '0.50', 4294967295, 1000000000, 1000000000, 1000000000, 5, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '4000.000', 0, 0);

--
-- Daten für Tabelle `ship_cat`
--

INSERT INTO `ship_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_color`) VALUES
(1, 'Kriegsschiff', 2, '#0080FF'),
(2, 'Ziviles Schiff', 1, '#00FF00'),
(3, 'Episches Schiff', 4, '#B048F8'),
(4, 'Rassenspezifisches Schiff', 3, '#f00'),
(5, 'NPC-Schiff', 6, '#F07902'),
(6, 'Allianzschiff', 5, '#ffffff');

--
-- Daten für Tabelle `ship_requirements`
--

INSERT INTO `ship_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 1, 9, 0, 2),
(2, 1, 11, 0, 1),
(3, 2, 9, 0, 2),
(4, 3, 9, 0, 1),
(5, 4, 9, 0, 5),
(6, 4, 11, 0, 2),
(7, 6, 9, 0, 7),
(8, 6, 11, 0, 7),
(9, 7, 9, 0, 8),
(10, 7, 11, 0, 5),
(11, 8, 9, 0, 10),
(12, 8, 11, 0, 8),
(13, 8, 8, 0, 7),
(14, 1, 0, 4, 4),
(15, 2, 0, 4, 1),
(16, 3, 0, 7, 1),
(17, 3, 0, 4, 1),
(18, 4, 0, 5, 3),
(19, 7, 0, 5, 5),
(20, 6, 0, 6, 4),
(22, 9, 0, 4, 2),
(23, 10, 9, 0, 5),
(24, 10, 0, 5, 5),
(25, 11, 0, 6, 11),
(26, 11, 9, 0, 9),
(27, 11, 11, 0, 10),
(28, 10, 0, 10, 5),
(32, 12, 9, 0, 8),
(33, 12, 0, 3, 6),
(34, 12, 0, 10, 5),
(35, 13, 9, 0, 12),
(36, 13, 11, 0, 10),
(38, 13, 0, 6, 13),
(42, 14, 9, 0, 4),
(43, 14, 11, 0, 4),
(44, 14, 0, 14, 3),
(47, 15, 9, 0, 4),
(46, 15, 0, 5, 7),
(49, 15, 0, 8, 12),
(50, 15, 0, 3, 9),
(51, 8, 0, 5, 8),
(52, 12, 0, 4, 7),
(53, 42, 9, 0, 10),
(54, 42, 0, 16, 7),
(55, 42, 0, 11, 10),
(194, 43, 9, 0, 3),
(57, 43, 0, 20, 5),
(58, 42, 0, 6, 6),
(60, 43, 0, 16, 2),
(61, 44, 9, 0, 8),
(62, 44, 0, 16, 4),
(64, 40, 9, 0, 5),
(65, 40, 0, 16, 4),
(66, 40, 0, 11, 10),
(67, 40, 0, 5, 6),
(68, 41, 9, 0, 3),
(69, 41, 0, 16, 2),
(70, 41, 0, 20, 5),
(71, 45, 9, 0, 7),
(72, 45, 0, 20, 7),
(73, 45, 0, 16, 6),
(74, 36, 9, 0, 7),
(75, 36, 0, 16, 6),
(76, 36, 0, 19, 4),
(77, 36, 0, 20, 6),
(78, 37, 9, 0, 3),
(79, 37, 0, 16, 2),
(80, 37, 0, 20, 5),
(81, 38, 9, 0, 5),
(82, 38, 0, 16, 4),
(83, 38, 0, 6, 5),
(84, 33, 9, 0, 3),
(85, 33, 0, 16, 2),
(86, 33, 0, 20, 5),
(87, 34, 9, 0, 5),
(88, 34, 0, 16, 4),
(89, 34, 0, 5, 10),
(90, 34, 0, 8, 13),
(91, 32, 9, 0, 7),
(92, 32, 0, 16, 6),
(93, 32, 0, 20, 6),
(94, 32, 0, 17, 3),
(95, 30, 9, 0, 5),
(96, 30, 0, 16, 6),
(97, 30, 0, 10, 12),
(98, 30, 0, 5, 6),
(99, 29, 9, 0, 3),
(100, 29, 0, 16, 2),
(101, 29, 0, 20, 5),
(102, 31, 9, 0, 5),
(103, 31, 0, 16, 4),
(104, 31, 0, 10, 8),
(105, 31, 0, 20, 6),
(106, 28, 9, 0, 5),
(107, 28, 0, 16, 4),
(108, 28, 0, 20, 6),
(109, 28, 11, 0, 6),
(110, 27, 9, 0, 3),
(111, 27, 0, 16, 2),
(112, 27, 0, 20, 5),
(113, 26, 9, 0, 7),
(114, 26, 0, 16, 6),
(115, 26, 0, 6, 6),
(116, 26, 0, 18, 4),
(117, 23, 9, 0, 7),
(118, 23, 0, 16, 6),
(119, 23, 0, 6, 5),
(120, 23, 0, 7, 15),
(121, 25, 9, 0, 3),
(122, 25, 11, 0, 7),
(123, 25, 0, 20, 5),
(124, 25, 0, 16, 2),
(125, 24, 9, 0, 5),
(126, 24, 0, 16, 2),
(127, 24, 0, 5, 7),
(128, 24, 0, 10, 8),
(129, 20, 9, 0, 7),
(130, 20, 0, 16, 6),
(131, 20, 0, 6, 6),
(132, 20, 0, 18, 4),
(133, 21, 9, 0, 3),
(134, 21, 0, 16, 2),
(135, 21, 0, 20, 5),
(136, 22, 9, 0, 7),
(137, 22, 0, 6, 7),
(138, 22, 0, 10, 7),
(139, 22, 0, 16, 6),
(140, 19, 9, 0, 3),
(141, 19, 0, 16, 2),
(142, 19, 0, 20, 5),
(143, 18, 9, 0, 5),
(144, 18, 0, 16, 4),
(145, 18, 0, 10, 7),
(146, 18, 0, 5, 7),
(147, 17, 9, 0, 7),
(148, 17, 0, 16, 6),
(149, 17, 0, 6, 6),
(150, 17, 0, 15, 3),
(151, 46, 9, 0, 6),
(152, 46, 0, 12, 3),
(153, 46, 0, 4, 6),
(157, 47, 9, 0, 11),
(158, 47, 11, 0, 7),
(160, 47, 0, 21, 9),
(162, 47, 0, 3, 6),
(163, 50, 9, 0, 6),
(164, 50, 0, 14, 5),
(165, 50, 0, 12, 6),
(166, 50, 0, 3, 5),
(167, 9, 11, 0, 1),
(169, 51, 9, 0, 1),
(170, 51, 0, 4, 3),
(171, 51, 0, 14, 9),
(172, 51, 0, 7, 8),
(173, 51, 0, 11, 5),
(174, 52, 9, 0, 6),
(175, 52, 0, 6, 8),
(176, 52, 0, 3, 4),
(178, 22, 11, 0, 5),
(179, 44, 0, 21, 6),
(180, 37, 0, 21, 3),
(181, 23, 0, 21, 5),
(182, 53, 11, 0, 8),
(184, 53, 0, 14, 10),
(185, 53, 0, 21, 8),
(186, 53, 9, 0, 10),
(187, 53, 0, 9, 4),
(188, 13, 0, 10, 7),
(189, 13, 0, 9, 6),
(190, 13, 0, 8, 7),
(191, 6, 0, 9, 6),
(193, 2, 0, 5, 1),
(195, 57, 9, 0, 5),
(196, 57, 0, 4, 3),
(197, 58, 8, 0, 4),
(199, 60, 0, 5, 5),
(201, 60, 0, 7, 7),
(202, 60, 9, 0, 9),
(203, 61, 0, 19, 5),
(204, 60, 0, 4, 4),
(205, 61, 0, 6, 7),
(206, 61, 9, 0, 9),
(207, 61, 0, 16, 5),
(208, 61, 0, 20, 4),
(209, 62, 0, 21, 6),
(210, 62, 0, 6, 8),
(211, 62, 0, 16, 5),
(212, 62, 9, 0, 9),
(213, 62, 0, 7, 15),
(214, 63, 9, 0, 9),
(215, 63, 0, 5, 10),
(216, 63, 0, 16, 5),
(217, 63, 0, 6, 9),
(218, 63, 0, 10, 10),
(220, 57, 11, 0, 4),
(221, 59, 0, 11, 14),
(222, 59, 9, 0, 9),
(223, 59, 0, 6, 8),
(224, 59, 0, 16, 8),
(225, 64, 0, 20, 8),
(226, 64, 0, 6, 7),
(227, 64, 9, 0, 9),
(228, 64, 0, 16, 5),
(229, 65, 0, 17, 4),
(230, 65, 0, 6, 7),
(231, 65, 0, 20, 6),
(232, 65, 0, 16, 5),
(233, 65, 9, 0, 9),
(234, 66, 0, 15, 4),
(235, 66, 0, 6, 8),
(236, 66, 0, 20, 5),
(237, 66, 0, 16, 5),
(238, 66, 9, 0, 9),
(239, 67, 0, 18, 5),
(240, 67, 0, 6, 8),
(241, 67, 0, 20, 6),
(242, 67, 9, 0, 9),
(243, 67, 0, 16, 5),
(244, 68, 0, 3, 2),
(245, 68, 0, 5, 2),
(246, 69, 9, 0, 8),
(247, 69, 11, 0, 6),
(248, 69, 0, 5, 5),
(249, 69, 0, 9, 4),
(250, 69, 0, 25, 2),
(251, 70, 0, 20, 5),
(252, 70, 9, 0, 3),
(253, 70, 0, 16, 2),
(278, 72, 0, 6, 8),
(275, 71, 9, 0, 1),
(277, 72, 0, 19, 6),
(271, 72, 0, 10, 10),
(279, 72, 0, 16, 5),
(268, 72, 9, 0, 9),
(274, 71, 0, 4, 1),
(280, 68, 9, 0, 1),
(281, 9, 9, 0, 1),
(282, 79, 0, 5, 4),
(286, 82, 0, 5, 5),
(287, 81, 0, 5, 2),
(288, 80, 0, 5, 1),
(289, 83, 24, 0, 3),
(290, 83, 0, 11, 15),
(291, 83, 0, 20, 7),
(292, 83, 0, 25, 3),
(303, 2, 0, 15, 1);

--
-- Daten für Tabelle `sol_types`
--

INSERT INTO `sol_types` (`sol_type_id`, `sol_type_name`, `sol_type_f_metal`, `sol_type_f_crystal`, `sol_type_f_plastic`, `sol_type_f_fuel`, `sol_type_f_food`, `sol_type_f_power`, `sol_type_f_population`, `sol_type_f_buildtime`, `sol_type_comment`, `sol_type_f_researchtime`, `sol_type_consider`) VALUES
(1, 'Gelber Stern', '1.30', '1.30', '1.10', '1.00', '0.90', '1.10', '1.30', '1.10', 'Die gelben Sterne gehören zu der Kategorie "mittelgrosse Sterne". Das Alter solcher Gelben Sterne kann extrem variieren; sie können zwischen einigen Jahrtausenden bis hin zu Jahrmillionen alt sein.<br>Generell gilt jedoch, dass auf Gelben Sternen gemässigte und gute Lebensbedingungen herrschen. Ausserdem ist die Geodiversität relativ gross, was den Abbau von Metallen genauso fördert wie die Entwicklung von Chemikalien. Dank dem mineralhaltigen Boden ist sogar ein gewisser Kristallabbau möglich.<br>Einzig der Nahrung scheint der mineralienhaltige Boden nicht ganz so gut zu bekommen...', '1.10', 1),
(2, 'Blauer Stern', '1.00', '1.30', '1.00', '1.00', '1.00', '0.90', '0.80', '1.00', 'Diese Art von Sternen erscheint dem Beobachter meist blau; das liegt daran, dass im Innern des Sterns eine gewaltige Hitze herrscht, vergleichbar mit der blauen Färbung einer Flamme beim Schweissen.<br>Durch die gigantischen Hitzewellen sind die Lebensbedingungen im Umfeld Blauer Sterne für die verschiedenen Völker nicht optimal. Einige jedoch haben sich inzwischen dem heissen Klima anpassen können und nutzen genau dieses zur Verschmelzung von Kristallinem Material, um qualitativ hochstehende Kristallite herzustellen.<br>Bisher wollte es jedoch noch keinem Volk so richtig gelingen, aus dem heissen Klima einen weiteren Nutzen in Sachen Industrie zu ziehen. Im Gegenteil, meist ist die Stromproduktion und das Wachstum der Bevölkerung tiefer als in anderen Sternsystemen.', '1.10', 1),
(3, 'Roter Stern', '0.90', '1.20', '1.00', '1.20', '1.10', '0.80', '1.30', '1.00', 'Rote Sterne sind eher klein und schon recht alt. Dadurch ist ihre Energieaustrahlung nicht mehr ganz so gross, was wiederum eine gute Klimabedingung für die meisten Völker ist. Deshalb verwundert es nicht, dass man in vielen Roten Sternen alle möglichen Völker antrifft, welche dort seit ewigen Zeiten eine neue Heimat gefunden haben.<br>Ebenfalls positiv wirkt sich die gemässigte Energieabgabe der Roten Sterne auf verschiedenste Produktionen aus, was dann wiederum den dort wohnhaften Völkern zugute kommt.', '1.00', 1),
(4, 'Weisser Stern', '0.90', '1.10', '1.00', '1.60', '1.00', '1.60', '0.95', '1.00', 'Weisse Sterne sind stark energiehaltige Sterne, deren Energieausstösse für das extrem helle Licht verantwortlich sind.<br>Dadurch lässt sich in der Nähe von Weissen Sternen mit relativ wenig Aufwand Tritium und Strom herstellen. Ebenfalls positiv wirkt sich die Energiestrahlung auf die Kristallisation aus, jedoch nicht auf die Menschen. Jene ertragen die gewaltigen Energiemengen nicht zu lange, weshalb der Bevölkerungswachstum in Weissen Sternen meist kleiner als in anderen Sternen ist.', '1.00', 1),
(5, 'Violetter Stern', '1.00', '0.90', '1.00', '0.90', '1.00', '1.00', '1.05', '0.90', 'Violette Sterne sind sehr junge Sterne, die sich meistens innerhalb von Gaswolken befinden. Die für den Betrachter violette Färbung der Sonne entsteht durch die vielen verschiedenen Nebel, welche das Sonnenlicht jeweils verschieden brechen.<br>Weil die Sterne noch ziemlich jung sind, ist noch nicht viel über sie bekannt; die Beobachtungen der verschiedenen Völker haben erst begonnen.', '0.90', 1),
(6, 'Schwarzer Stern', '0.90', '1.00', '1.20', '1.10', '1.00', '0.80', '0.60', '0.85', 'Praktisch keiner weiss etwas über schwarze Sterne, da sie erst vor kurzem durch eine neuartige Objektivtechnologie sichtbar gemacht werden konnten.<br>Erst einzelne überragende Forscher haben angefangen, sich an diese Mysterien im All heranzuwagen.<br>Ungenannte Quellen munkeln jedoch, dass die schwarze Färbung durch aktive schwarze Löcher auftritt, was die Völker natürlich davor abschreckt, mehr über die Schwarzen Sterne rauszufinden.', '1.00', 1),
(7, 'Grüner Stern', '1.40', '1.10', '1.00', '1.00', '1.20', '0.90', '0.90', '1.10', 'Grüne Sterne wirken auf den ersten Blick giftig - und so ganz unrecht ist das auch nicht. Durch Gase aus dem Inneren der Sterne werden immer wieder Epidemien ausgelöst, die Teile der Bewohner von Grünen Sternen dahinraffen.<br>Entgegen den unwirtlichen Lebensbedingungen wirken sich die Gase und die Geostruktur positiv auf die Steingefüge der Sterne aus.<br>Es verwundert daher nicht, dass in Grünen Sternen oftmals Raffinerien, Erzwerke und Metallverarbeitungsanlagen anzutreffen sind.', '1.00', 1);

--
-- Daten für Tabelle `specialists`
--

INSERT INTO `specialists` (`specialist_id`, `specialist_name`, `specialist_desc`, `specialist_enabled`, `specialist_points_req`, `specialist_costs_metal`, `specialist_costs_crystal`, `specialist_costs_plastic`, `specialist_costs_fuel`, `specialist_costs_food`, `specialist_days`, `specialist_prod_metal`, `specialist_prod_crystal`, `specialist_prod_plastic`, `specialist_prod_fuel`, `specialist_prod_food`, `specialist_power`, `specialist_population`, `specialist_time_tech`, `specialist_time_buildings`, `specialist_time_defense`, `specialist_time_ships`, `specialist_costs_buildings`, `specialist_costs_defense`, `specialist_costs_ships`, `specialist_costs_tech`, `specialist_fleet_speed`, `specialist_fleet_max`, `specialist_def_repair`, `specialist_spy_level`, `specialist_tarn_level`, `specialist_trade_time`, `specialist_trade_bonus`) VALUES
(1, 'Admiral', 'Der Flottenadmiral ist ein kriegserfahrener Veteran und meisterhafter Stratege. Auch im heissesten Gefecht behält er im Gefechtsleitstand den Überblick und hält Kontakt mit den ihm unterstellten Admirälen. Ein weiser Herrscher kann sich auf seine Unterstützung im Kampf absolut verlassen und somit mehr Raumflotten gleichzeitig und schneller ins Gefecht führen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.10', 3, '1.00', 0, 0, '1.00', '1.00'),
(2, 'Ingenieur', 'Der Ingenieur ist ein Spezialist für besonders durchdachte und stabile Verteidigungssysteme. Durch seine Mithilfe können Verteidigungsanlagen schneller und günstiger produziert werden. Nach einem Kampf kann er auch schwer beschädigte Anlagen wieder reparieren.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.80', '1.00', '1.00', '0.90', '1.00', '1.00', '1.00', 0, '1.40', 0, 0, '1.00', '1.00'),
(3, 'Geologe', 'Der Geologe ist ein anerkannter Experte in Astromineralogie und -kristallographie. Mithilfe seines Teams aus Metallurgen und Chemieingenieuren unterstützt er interplanetarische Regierungen bei der Erschließung neuer Rohstoffquellen und der Optimierung ihrer Raffination.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.10', '1.10', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(4, 'Professor', 'Die Gilde der Technokraten sind geniale Wissenschaftler, und man findet sie immer dort, wo die Grenzen des technisch Machbaren gesprengt werden. Durch seine reine Anwesenheit inspiriert er die Forscher des Imperiums.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.80', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.90', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(5, 'Biologe', 'Der Biologe steigert durch seine gentechnischen Experimente den Ertrag deiner Gewächshäuser und sorgt für ein rascheres Bevölkerungswachstum.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.30', '1.00', '1.30', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(6, 'Spion', 'Der Spion ist ein Meister der Tarnung und Informationsbeschaffung. Durch seine Tricks ist es möglich, mehr Informationen über den Gegner herauszufinden und die eigenen Schiffe besser zu tarnen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.90', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 3, 2, '1.00', '1.00'),
(7, 'Meisterhändler', 'Durch das Verhandlungsgeschick des Meisterhändlers fallen im Markt keine zusätzlichen Kosten an, er hat weniger Handelsbeschränkungen und seine Handelsschiffe fliegen schneller als alle anderen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '6.00', '0.00'),
(8, 'Energieminister', 'Der Energieminister kennt sich auf dem Gebiet der Energieförderung bestens aus. Durch seine vorausschauende Planung ist es möglich, die Produktion der Kraftwerke drastisch zu steigern. Dadurch kann auch die stromintensive Synthetisierung von Tritium merklich gesteigert werden.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.10', '1.00', '1.40', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(0, 'Nulldummy', 'Nicht löschen', 0, 0, 0, 0, 0, 0, 0, 14, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00');

--
-- Daten für Tabelle `technologies`
--

INSERT INTO `technologies` (`tech_id`, `tech_name`, `tech_type_id`, `tech_shortcomment`, `tech_longcomment`, `tech_costs_metal`, `tech_costs_crystal`, `tech_costs_fuel`, `tech_costs_plastic`, `tech_costs_food`, `tech_costs_power`, `tech_build_costs_factor`, `tech_last_level`, `tech_show`, `tech_order`, `tech_stealable`) VALUES
(7, 'Spionagetechnik', 4, 'Je höher die Spionagetechnik ist, desto mehr können Spionagesonden über gegnerische Planeten herausfinden.', 'Spionage ist die Auskundschaftung und Erlangung von fremden, wohlgehüteten Geheimnissen oder Wissen von fremden Planeten. Die erlangten Informationen werden dann in den eigenen wirtschaftlichen, politischen oder militärischen Machtbereich eingeführt, ohne dass eine eigenständige Erforschung erfolgen müsste. Annähernd sämtliche Imperien bedienen sich der Spionage oder \\"nachrichtendienstlicher Mittel\\", um andere Völker (unabhängig der feindseligen oder freundlichen Einstellung zum eigenen Volk) auszuspionieren.\r\nEine weitere nützliche Eigenschaft der Spionagetechnik ist das Enttarnen von feindlichen Angriffen, welche mit höherer Spionagetechnik schneller vonstatten geht.', 750, 370, 150, 520, 0, 0, '1.50', 50, 1, 1, 1),
(8, 'Waffentechnik', 2, 'Jede Ausbaustufe erhöht die Stärke der Waffen bei Raumschiffen und Verteidigungsanlagen.', 'Durch die Erforschung der Waffentechnik können neue und stärkere Waffen für Raumschiffe und Verteidigungsanlagen gebaut werden.\r\nPro Ausbaustufe erhöht sich die Angriffskraft deiner Schiffe und Verteidigungsanlagen um 10%.', 250, 800, 550, 200, 0, 0, '1.80', 50, 1, 1, 1),
(4, 'Wasserstoffantrieb', 1, 'Einfacher Antrieb für Raumschiffe', 'Ein Wasserstoffantrieb nutzt Wasserstoff als Treibstoff. Dieser wird durch Elektrolyse von Wasser, Reformation von Methanol oder durch Dampfreformation von Erdgas gewonnen.', 500, 300, 250, 50, 0, 0, '1.50', 50, 1, 0, 1),
(5, 'Ionenantrieb', 1, 'Hoch entwickelter Antrieb für Spezialschiffe. Er ist weniger schnell als der Wasserstoffantrieb, dafür kostensparend.', 'Ein Ionenantrieb ist ein Antrieb für Raumfahrzeuge, bei dem die Abstossung von einem Ionenstrahl zur Fortbewegung genutzt wird. Es werden auch je nach Energiequelle die Begriffe \\"solar-elektrischer Antrieb\\" bzw. \\"Solar Electric Propulsion\\" (SEP) und \\"nuklear-elektrischer Antrieb\\" bzw. \\"Nuclear Electric Propulsion\\" (NEP) verwendet.\r\nDer Ionenstrahl besteht aus einem elektrisch geladenen Gas (z.B. Xenon). Erzeugt wird der Ionenstrahl durch ionisierte Gasteilchen, die in einem elektrischen Feld oder mittels einer Kombination eines elektrischen Feldes und eines Magnetfeldes unter Ausnutzung des Hall-Effektes beschleunigt und dann in Form eines Strahls ausgestossen werden. Die Energie zur Erzeugung der Felder wird üblicherweise mit Hilfe von Solarzellen gewonnen. Als Treibstoff des Ionenantriebs dient sowohl das Gas als auch die zusätzlich benötigte elektrische Energie.\r\nDer Vorteil des Ionenantriebs gegenüber dem chemischen Antrieb liegt darin, dass er weniger Treibstoff verbraucht, weil die Geschwindigkeit der austretenden Teilchen wesentlich grösser ist.', 1000, 1500, 800, 300, 0, 0, '1.50', 50, 1, 1, 1),
(6, 'Hyperraumantrieb', 1, 'Sehr schneller Antrieb für grosse Schiffe, der den Hyperraum als Transportmedium benutzt.', 'Der Hyperraumantrieb schafft eine technisch hervorgerufene Abkürzung zwischen weit entfernten Punkten in der Raumzeit. Die Idee ist dabei folgende: Um den Weg vom Nordpol zum Südpol abzukürzen, reise man quer durch die Erde, anstatt entlang der Oberfläche. Der Weg durch die Erde (in die dritte Dimension) ist kürzer als der Weg auf der (zweidimensionalen) Erdoberfläche. Genauso könnte man sich vorstellen, dass unsere Raumzeit auch in einen höherdimensionalen Hyperraum eingebettet ist (wie die Erdoberfläche in den Raum), und man daher durch den Hyperraum abkürzen könnte. Auch hier würde man (im Hyperraum) nicht schneller als Lichtgeschwindigkeit fliegen müssen, um schneller als das Licht im Normalraum am Ziel anzukommen.\r\nDiese Antriebstechnologie wird heute für fast jedes grosse und träge Schiff eingesetzt.', 4000, 6000, 1500, 5500, 0, 0, '1.80', 50, 1, 3, 1),
(3, 'Energietechnik', 4, 'Diese Technologie dient zur Erforschung neuer Arten der Energiegewinnung.', 'Durch die Unterstützung der Energietechnik können neue Arten der Energiegewinnung erforscht werden.', 300, 250, 30, 50, 0, 0, '1.50', 50, 1, 0, 1),
(9, 'Panzerung', 2, 'Jede Ausbaustufe erhöht die Stärke der Panzerung bei Raumschiffen und Verteidigungsanlagen.', 'Jedes Schiff und jede Verteidigungsanlage besitzen eine Panzerung zum Schutz vor feindlichen Angriffen. Pro Ausbaustufe erhöht diese Technologie die Panzerung, auch genannt Struktur, um 10%.', 1000, 150, 320, 270, 0, 0, '1.80', 50, 1, 2, 1),
(10, 'Schutzschilder', 2, 'Jede Ausbaustufe erhöht die Stärke der Schutzschilder bei Raumschiffen und Verteidigungsanlagen.', 'Ein Schutzschild schützt deine Raumschiffe und Verteidigungsanlagen vor feindlichem Beschuss.\r\nPro Ausbaustufe erhöht sich die Effizienz von den Schutzschildern um 10%.', 290, 330, 250, 950, 0, 0, '1.80', 50, 1, 3, 1),
(11, 'Tarntechnik', 2, 'Durch eine hohe Tarntechnik können deine Flotten eine gewisse Zeit vor dem Gegner verborgen bleiben.', 'Die Kriegsära hat begonnen; die Völker erforschen Technologien, mit welchen sie dem Gegner in einem allfälligen Kampf überlegen sind. Die Tarntechnik ist eigentlich schon eine uralte Waffe, welche den Überraschungseffekt ausnutzt, um so eine bessere Ausgangsposition zu haben; doch erst jetzt ist es wirklich möglich, seine Schiffe von der gegnerischen Flottenkontrolle zu verstecken.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben die Schiffe für den Gegner unentdeckt.', 1500, 750, 250, 800, 0, 0, '1.60', 50, 1, 4, 1),
(12, 'Recyclingtechnologie', 4, 'Ermöglicht eine effiziente Wiederverwertung von alten Verteidigungsanlagen und Schiffen.', 'Lange Zeit hatte man eine Technik gesucht, welche verbaute Rohstoffe wieder verwerten kann. Nach jahrelanger Forschung wurde ein Verfahren entwickelt, das Schiffe und Verteidigungsanlagen recyceln kann. Jedoch ist diese Technologie in der Anfangsphase noch sehr ineffizient.\r\nDies kann aber mit der Weiterentwicklung ein wenig eingedämpft werden. Man weiss jedoch, dass die Materialien nie zu 100% recycelt werden können.', 12000, 20000, 2000, 8000, 0, 0, '1.90', 50, 1, 2, 1),
(13, 'Rettungskapseln', 2, 'Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird. ', 'Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird.\r\nEinige Schiffe können nur gebaut werden, wenn gute Rettungskapseln an Bord sind.\r\nUm Grosse Schiffe zu bauen, muss man die Rettungskapseln entwickelt haben.', 12000, 2000, 3000, 8000, 2000, 0, '1.90', 50, 0, 5, 1),
(14, 'Kraftstoffantrieb', 1, 'Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. ', 'Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. Dieser Antrieb ermöglicht es grösseren Schiffen, sich schneller fortzubewegen.', 25500, 7752, 19347, 10474, 0, 0, '1.30', 50, 1, 2, 1),
(15, 'Bombentechnik', 3, 'Mit Hilfe dieser Technik wird die Effektivität von Bombenangriffen gesteigert.', 'Längst hat man rausgefunden, dass das alleinige Zerstören von gegnerischen Flotten nicht mehr unbedingt den gewünschten Effekt hat.\r\nForscher haben aus diesem Grund eine neuartige Waffe entwickelt, mit der es möglich ist, fremde Gebäude zu bombardieren und so den Gegner wieder ins industrielle Mittelalter zu befördern.\r\nDiese Methode der Kriegsführung ist aber noch sehr jung, und deshalb ist die Chance auf eine erfolgreiche Bombardierung noch nicht allzu hoch.\r\nDurch die Erforschung der Bombentechnik wird diese aber deutlich gesteigert.', 13000, 26000, 8000, 13000, 0, 0, '1.75', 50, 1, 0, 1),
(16, 'Rassentechnik', 4, 'Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen.', 'Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen. Je höher sie erforscht ist, desto bessere und stärkere Rassenobjekte können gebaut werden.', 1000, 1000, 1000, 1000, 1000, 0, '1.50', 50, 1, 3, 1),
(17, 'EMP-Technik', 3, 'EMP-Bomben löst einen Elektromagnetischen Impuls aus, welcher elektrische Einrichtungen ausser Betrieb setzen kann.', 'Je länger je mehr schützen die Völker ihre Schiffe, indem sie sie ständig auf Erkundungsflüge schicken und so für den Gegner unerreichbar machen.\r\nEin Forschungsteam der Rigelianer hat es sich zur Aufgabe gemacht, diese Strategie zu vernichten.\r\nNach langen Forschungen haben sie ein Schiff entwickelt, mit dem es möglich ist, ganze Einrichtungen unbrauchbar zu machen.\r\nEin elektromagnetischer Impuls setzt alle elektronischen Geräte ausser Gefecht. Mit Hilfe dieser brillianten Waffe kann man nun dem Gegner beispielsweise die Flottenkontrolle lahm legen und den Schiffen den Abflug vom Planeten verweigern.\r\nJedoch ist auch diese Technologie noch nicht ganz ausgereift; so muss man sich beispielsweise mit einer kurzfristigen Deaktivierung zufrieden geben. Durch die Weiterentwicklung der EMP Technologie erhöht sich jedoch die Effizienz des Angriffes.', 15000, 15000, 10000, 15000, 0, 0, '1.70', 50, 1, 1, 1),
(18, 'Gifttechnik', 3, 'Diese Technologie wird für B- und C- Waffen gebraucht.', 'Die Gifttechnologie ist eine Massenvernichtungswaffe für Bewohner. Durch Zerstörung der Nervenbahnen und allmähliches Verringern der Wahrnehmungsfähigkeit lässt das Gift die Bewohner erkranken und kurze Zeit später an den Folgen sterben. Eine grausame, aber sehr effektive Waffe.\r\nDie Weiterentwicklung ermöglicht einen noch präziseren Einsatz der Gifte.', 10000, 10000, 5000, 20000, 0, 0, '1.50', 50, 1, 2, 1),
(19, 'Regenatechnik', 3, 'Neuartige Materialien ermöglichen gewissen Schiffen, sich während dem Kampf teilweise zu reparieren.', 'Das Heilen von Schiffen war schon immer sehr schwierig und wird sich wohl erst in Zukunft bei einer neuen Generation von Schiffen durchsetzen.\r\nBisher ist es nur einer einzigen Rasse gelungen, ein Schiff herzustellen, welches die eigene Flotte im Kampf heilen kann.\r\nEiner anderen Rasse ist es inzwischen gelungen, diesselbe Technologie für ihre Verteidigungsanlagen anzuwenden.\r\nDurch die Erhöhung der Technologie kann deren Effizienz gesteigert werden.', 30000, 17500, 12500, 17500, 0, 0, '1.90', 50, 1, 3, 1),
(20, 'Warpantrieb', 1, 'Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.', 'Jede Rasse hat nach einer gewissen Zeit angefangen, ihre eigenen Schiffe zu bauen. Eine uns unbekannte Rasse hat den Warpantrieb entwickelt. Die uns bekannten Rassen konnten ihn jedoch nur bedingt anwenden. So sind ihre Schiffe nicht ganz so schnell wie sie eigentlich sein könnten. Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.', 6000, 4500, 2000, 5500, 0, 0, '1.70', 50, 1, 5, 1),
(21, 'Solarantrieb', 1, 'Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. ', 'Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. Schiffe mit einem Solarantrieb können während dem Flug ihr Triebwerk ausschalten und ein riesiges Sonnensegel ausfahren, wodurch sie vom Sonnenwind mit unglaublicher Geschwindigkeit durchs All getragen werden.\r\nDie Erforschung ist nicht sehr billig, jedoch birgt es einen unschlagbaren Vorteil. Die Schiffe verbrauchen viel weniger Treibstoff für den Flug. Es soll sogar Schiffe geben, die allein mit den Solarzellen die benötigte Energie zum Flug aufbringen und so ohne Tritiumverbrauch fliegen können.', 2100, 1300, 1100, 300, 0, 0, '1.80', 50, 1, 4, 1),
(22, 'Wurmlochforschung', 3, 'Ermöglicht einer Flotte das Reisen durch Wurmlöcher. Dadurch wird die Flugzeit einer Flotte enorm verkürzt.', 'Wurmlöcher sind topologische Konstrukte, die weit voneinander entfernt liegende Bereiche des Universums durch eine \\''Abkürzung\\'' verbinden. Ein Ende eines Wurmlochs erscheint dem Beobachter als Kugel, die ihm die Umgebung des anderen Endes zeigt. Obwohl ein durch ein Wurmloch Reisender nie die Lichtgeschwindigkeit überschreiten würde, hätte in Bezug auf die betreffenden Start- und Zielbereiche eine Reise mit Überlichtgeschwindigkeit stattgefunden. Durch die Erforschung der Wurmlöcher gelang es Wissenschaftlern, Technologien für das Reisen durch Wurmlöcher zu entwickeln und somit die Flugzeit enorm zu verkürzen. Ob die zwei Wurmlochenden eines Lochs immer miteinander verknüpft bleiben oder ob  die Verknüpfungen von Zeit zu Zeit ändern, ist Gegenstand aktueller Untersuchungen.\r\nBisher ist es den Forschern jedoch noch nicht gelungen, ein solches Wurmloch länger als ein paar Tage offen zu halten.', 100000, 120000, 175000, 290000, 250000, 0, '1.60', 1, 1, 5, 1),
(23, 'Gentechnik', 3, 'Durch die Manipulierung der Gene ist es möglich, die Leistung der Arbeiter zu steigern und so die Bauzeit zu verringern.', 'Den Forschern ist ein absoluter Durchbruch im Bereich Genforschung gelungen. Bisher waren alle genmanipulierten Arbeiterversuche fehlgeschlagen und die meisten Versuchsobjekte überlebten dieses Experiment nicht. Doch nun gelang mit Hilfe von Hochpräzisionsmaschinen eine genetische Veränderung, sodass die Arbeiter zu höheren Leistungen fähig sind.\r\nDies hat zur Folge, dass die Bauzeit von jeglichen Produkten nochmals gesenkt werden kann.\r\nDiese revolutionäre Errungenschaft hat aber ihren Preis, denn der Eingriff ist extrem zeit- und kostenaufwändig. Viele Wissenschaftler sind sich aber dennoch einig, dass es sich allemal lohnt, diese Technologie zu verbessern und zu perfektionieren.', 100000000, 60000000, 38000000, 40000000, 20000000, 0, '1.40', 8, 1, 6, 0),
(24, 'Raketentechnik', 3, 'Das Wissen um diese Technologie in Verbindung mit dem Raketensilo ermöglichen es, Raketen zu konstruieren.', 'Damit Raketen eingesetzt werden können, muss zuerst die Raketetechnik erforscht sein. Je höher die Raketentechnik erforscht ist, desto bessere und effektivere Raketen können gebaut werden.', 30000, 60000, 400000, 20000, 0, 0, '1.20', 10, 1, 6, 1),
(25, 'Computertechnik', 4, 'Mit Computern können Forscher komplexe Gleichungssysteme lösen, um genauere Flugbahnen zu berechnen.', 'Mit Hilfe der Computerwissenschaft können Forscher komplexe Gleichungssysteme lösen, um damit zum Beispiel genaue Flugbahnen zu berechnen. Dies kann zu einem Vorteil in der gegnerischen Flottenüberwachung führen oder eine bessere Steuerbarkeit von Raketen ermöglichen.', 500, 5000, 0, 3000, 0, 0, '1.30', 15, 1, 4, 1);

--
-- Daten für Tabelle `tech_requirements`
--

INSERT INTO `tech_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 3, 8, 0, 3),
(2, 4, 8, 0, 4),
(3, 4, 9, 0, 2),
(4, 5, 8, 0, 5),
(5, 5, 9, 0, 4),
(6, 6, 8, 0, 8),
(7, 6, 11, 0, 6),
(8, 6, 9, 0, 6),
(9, 7, 8, 0, 4),
(10, 8, 8, 0, 3),
(11, 9, 8, 0, 4),
(12, 10, 8, 0, 4),
(17, 12, 8, 0, 7),
(18, 12, 0, 3, 5),
(19, 13, 11, 0, 4),
(20, 13, 0, 5, 2),
(21, 14, 0, 4, 6),
(22, 14, 8, 0, 6),
(23, 11, 8, 0, 5),
(24, 11, 0, 7, 6),
(25, 15, 8, 0, 8),
(26, 17, 8, 0, 8),
(27, 20, 9, 0, 5),
(28, 20, 8, 0, 4),
(29, 18, 8, 0, 8),
(30, 19, 8, 0, 8),
(31, 16, 8, 0, 5),
(32, 21, 8, 0, 6),
(33, 21, 9, 0, 5),
(34, 21, 0, 3, 6),
(35, 22, 8, 0, 10),
(36, 22, 0, 6, 9),
(37, 22, 0, 3, 10),
(38, 22, 0, 10, 11),
(39, 23, 8, 0, 12),
(40, 23, 7, 0, 15),
(41, 24, 8, 0, 10),
(42, 24, 0, 3, 9),
(44, 24, 0, 4, 10),
(45, 24, 0, 14, 10),
(46, 25, 0, 3, 5),
(47, 25, 13, 0, 6);

--
-- Daten für Tabelle `tech_types`
--

INSERT INTO `tech_types` (`type_id`, `type_name`, `type_order`, `type_color`) VALUES
(1, 'Antriebstechniken', 1, '#ffffff'),
(2, 'Kriegstechnologien', 2, '#ffffff'),
(4, 'Allgemeine Technologien', 0, '#ffffff'),
(3, 'Hi - Technologien', 3, '#ffffff');

--
-- Daten für Tabelle `ticket_cat`
--

INSERT INTO `ticket_cat` (`id`, `name`, `sort`) VALUES
(1, 'Beleidigung in Nachricht', 0),
(2, 'Rathaus-Missbrauch', 1),
(3, 'Missachtung der Angriffsregeln', 2),
(4, 'Pushing-Verdach', 3),
(5, 'Cheat-Verdach', 4),
(6, 'Bugusing-Verdacht', 5),
(7, 'Anstössiges Bild', 6),
(8, 'Sonstiger Regelverstoss', 7),
(9, 'Änderung meiner fixen E-Mail-Adresse', 9),
(10, 'Änderung meines Namens (Accountübergabe)', 10),
(11, 'Probleme mit einer Flotte (Ungültige Koordinaten, hängenbleibende Flotte)', 11),
(12, 'Problem mit der Allianz (Ränge, Forum, Bündnisse, Auslösung etc)', 12),
(15, 'Probleme mit den Account-Einstellungen (Design, Urlaubsmodus etc)', 13),
(14, 'Anderes Problem', 20),
(16, 'Verdacht auf Accounthacking', 14),
(17, 'Probleme mit meinem Passwort', 15);

--
-- Daten für Tabelle `tips`
--

INSERT INTO `tips` (`tip_id`, `tip_text`, `tip_active`) VALUES
(1, 'Gib niemals dein Passwort an andere Leute, auch nicht an Moderatoren und Admins. Logge dich nur über www.etoa.ch ein und niemals über eine andere Seite. Akzeptiere keine Dateien von fremden Spielern und sorge dafür, dass dein Passwort sicher ist und niemand Zugriff auf deinen Account bekommt.', 1),
(2, 'Gründet Allianzen oder schliesst euch einer bestehen Allianz an, um gemeinsam gegen Feinde zu kämpfen und spezielle Allianzgebäude und -schiffe bauen zu können.', 1);
