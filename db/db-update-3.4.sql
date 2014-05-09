--
-- Backend message queue
--

CREATE TABLE `backend_message_queue` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `cmd` varchar(255) NOT NULL,
 `arg` varchar(255) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `cmd` (`cmd`,`arg`)
) ENGINE=MEMORY AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Alliance building user countdown
--

CREATE TABLE `alliance_building_cooldown` (
 `cooldown_user_id` int(10) unsigned NOT NULL,
 `cooldown_alliance_building_id` int(10) unsigned NOT NULL,
 `cooldown_end` int(10) unsigned NOT NULL,
 UNIQUE KEY `cooldown_user_id` (`cooldown_user_id`,`cooldown_alliance_building_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Boost system
--

ALTER TABLE `users` 
ADD COLUMN `boost_bonus_production` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `discoverymask_last_updated`,
ADD COLUMN `boost_bonus_building` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `boost_bonus_production`;

--
-- Mysticum Readiness
--

ALTER TABLE `ships` ADD `special_ship_bonus_readiness` DECIMAL( 4, 2 ) NOT NULL AFTER `special_ship_bonus_deactivade`;
ALTER TABLE `shiplist` ADD `shiplist_special_ship_bonus_readiness` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `fleet_ships` ADD `fs_special_ship_bonus_readiness` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0';

--
-- Text editor
--

CREATE TABLE `texts` (
 `text_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `text_content` text COLLATE utf8_unicode_ci NOT NULL,
 `text_updated` int(10) unsigned NOT NULL,
 `text_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
 PRIMARY KEY (`text_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tutorials
--

CREATE TABLE `tutorial` (
 `tutorial_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `tutorial_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`tutorial_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `tutorial` (`tutorial_id`, `tutorial_title`) VALUES
(1, 'Rassenauswahl'),
(2, 'Bauweise');

CREATE TABLE `tutorial_texts` (
 `text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `text_tutorial_id` int(11) NOT NULL,
 `text_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `text_content` text COLLATE utf8_unicode_ci NOT NULL,
 `text_step` tinyint(2) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `tutorial_texts` (`text_id`, `text_tutorial_id`, `text_title`, `text_content`, `text_step`) VALUES
(1, 1, 'Willkommen', 'Willkommen, werter neuer Imperator in den Galaxien Andromedas!\r\n\r\nDer Grundgedanke des Spieles (wie bei fast allen Aufbau-BG´s) liegt darin, durch Rohstoffe Gebäude und Schiffe zu bauen. Bei EtoA jedoch sind die Möglichkeiten ungleich größer und vielfältiger. Dieses Tutorial soll euch ein wenig Entscheidungshilfe geben, erfolgreich den Wirren des Universums zu trotzen.', 0),
(2, 1, 'Die Rohstoffe (1/2)', 'Es gibt in EtoA 5 verschiedene Arten von Rohstoffen:\r\n\r\n[list][*][b]Titan[/b], Grundstoff zum Bau von fast Allem. Wird mit fortschreitendem Spiel zur Massenware. zum Massenprodukt[*][b]Silizium[/b], für Forschung und Schiffe. Zu Beginn sehr rar, wird aber im weiteren Verlauf auch zum Massenprodukt.[*][b]PVC[/b], Man meint man hat genug, wenn man es dann braucht (vor allem für den Schiffsbau) hat man immer zu wenig.[*][b]Tritium[/b], als Treibstoff überlebensnotwendig. Aber auch zur Forschung wichtig. Ist selten reichlich vorhanden, auch im späteren Spiel keine Massenware. Gut zum Handeln.[*][b]Nahrung[/b], wichig zum schnelleren Bauen von Minen, Schiffen, Forschung und natürlich zum Verschicken von Schiffen. Denn ohne Nahrung wird kein Pilot ein Schiff besteigen.[/list]\r\nDiese Rohstoffe können grundsätzlich auf jedem Planeten produziert werden. Durch geschickte Wahl des Sternensystems und des Planeten kann die Produktion einiger Rohstoffe stark erhöht werden, allerdings meist zu Lasten eines anderen Rohstoffes. Genaueres liefert die Hilfe.', 1),
(3, 1, 'Die Rohstoffe (2/2)', 'Mit den passenden Schiffen kann man Rohstoffe auch im Weltall sammeln, grundsätzlich kann jede Rasse sammeln gehen. Einige Rassen haben jedoch Spezialschiffe, welche dafür besser geeignet sind. Auch hier hilft die Hilfe.\r\nSammeln kann man\r\n[list][*][b]Asteroiden[/b] (Titan, Silizium, PVC)[*][b]Sternennebel[/b] (Silizium)[*][b]Gasplaneten[/b] (Tritium)[*][b]Trümmerfelder[/b] durch Kämpfe (Titan, Silizium, PVC)[/list]', 2),
(4, 1, 'Die grundsätzlichen Spielweisen', 'Das Ganze ist natürlich zeitaufwendig und reicht auf keinen Fall aus um einen Account ausbauen zu können. Dies bringt uns zum nächsten Teil des Tutorials:\r\n\r\nEs gibt drei grundsätzliche Spielweisen: der Miner, der Fleeter und der Händler.\r\n\r\nNatürlich kann (und wird wohl) man einen Mix spielen, je nachdem wieviel Zeit man hat oder wo die eigenen Ziele liegen.', 3),
(18, 1, 'Sterne und Planeten (2/2)', 'Grundsätzlich zu empfehlen ist ein gelber Stern, da er folgende Boni mitbringt:\r\n\r\n+35% Titan\r\n+30% Silizium\r\n+10% PVC\r\n\r\nDazu nehmen wir evtl. einen Eisplaneten:\r\n\r\n+10% Titan\r\n+30% Silizium\r\n+25% PVC\r\n+30% Tritium\r\n\r\nDie Kombination Gelb/Eis ergibt somit:\r\n\r\n+45% Titan\r\n+60% Silizium\r\n+35% PVC\r\n+30% Tritium\r\n\r\nDamit läßt sich zu Beginn ganz gut leben. Nicht verschweigen darf man jedoch den Malus von -25% auf Nahrung und die um 10% erhöhte Bauzeit. Zu Beginn sind diese Werte vernachlässigbar, im Laufe des Spieles kann das ganz anders aussehen.\r\nFerner kann man durch die Wahl der Rasse die Werte ebenfalls noch verändern. Nimmt man zb den Cardassianer mit +60% Nahrung bekommt man einen Bonus von +35% Nahrung bei obiger Kombo. Allerdings auch 10% weniger an Titan/Silizium. Und man hat die Mali/Boni der Rasse dann bei jedem Planeten.\r\n', 9),
(5, 1, 'Die Wahl der Rasse', 'Sofern ihr euch jetzt in einem der Profile wiedergefunden habt solltet ihr euch jetzt der Rassenwahl zuwenden.\r\n\r\nEs gibt in EtoA zehn verschiedene Rassen, alle haben einen Bonus oder Malus auf die Produktion bestimmter Rohstoffe. Eine Tabelle findest du in der Hilfe unter Rassen. Alternativ gibt es hier: LINK EINFÜGEN einen Rechner der dich dabei unterstützen kann. Ferner hat jede Rasse spezielle Schiffe, welche nur von dieser Rasse gebaut werden kann. Auch hier bitte die Hilfe aufrufen.\r\n\r\nEine kleine Entscheidungshilfe mit Beispiel:\r\n\r\nLiegt einem eher die Spielweise des Händlers sollte man eine Rasse wählen die einen Bonus auf einen eher seltenen Rohstoff hat, oder die Schiffe besitzt mit denen man effektiver im Weltall sammeln gehen kann. (Bsp. Vorgone)\r\nIst man eher der Fleeter sollte man eine Rasse wählen die schnelle/günstige oder Schiffe mit Spezialfunktionen bauen kann. (Bsp. Minbari)\r\nAls Miner ist evtl. die Rasse Serrakin interessant da sie effektive Verteidigungsanlagen bauen kann. Oder man sucht eine Rasse mit hohem Silizium/Titan Bonus.\r\nNatürlich gibt es für jeden Zweck auch andere Rassen, hier sollte jeder schauen welche Rasse ihm am besten liegt um seine Spielweise am ehesten zu unterstützen. Denn durch geschicktes Kombinieren der Rasse und der Planeten kann man sogar gute Rohstoff-Boni + besondere Schiffe bekommen. Zwar wird man nie ein Top-Fleeter mit Top-Boni bekommen, aber man kann auf jeden Fall näher herankommen.', 7),
(6, 1, 'Sterne und Planeten (1/2)', 'Das bringt uns zum nächsten Punkt für einen erfolgreichen Einstieg: Die Sterne und Planeten\r\n\r\nIn EtoA gibt es 7 Arten von Sternen und 6 Arten von Planeten. Ein Sternensystem kann verschiedene Planeten beinhalten. Dabei wird jeder Planet den Einflüssen des Sternes ausgesetzt, dh. Die Boni/Mali des Sternes werden in die Berechnung der Boni/Mali der Planeten im Sternensystem mit einbezogen.\r\nJedes Imperium kann aus max. 15 Planeten bestehen. Jeder Planet gehört dir alleine. Welche Kombination du dir im weiteren Verlauf aussuchst und besiedelst hängt von deiner Spielweise ab. So kann man zb. die Kolonien nahe beieinander legen, oder man nimmt nur eine Kombo und muss dann vll. weiter fliegen weil es diese nicht überall gibt.\r\n\r\nViel entscheidender ist die Wahl der Startkombination, auch wenn sie vielleicht nicht die Optimale für die weitere Spielweise ist. Ausgleichen kann man sie ja durch die Kolonien.\r\nGerade als Neuling solltest du eine Kombination auswählen die es dir ermöglicht zügig deinen Planeten ausbauen zu können. Boni auf Titan und Silizium sind zu Beginn sehr wichtig. Aber auch Tritium sollte nicht vergessen werden, denn ohne Treibstoff fliegt auch kein Besiedelungsschiff.\r\nEher nebensächlich sind zu Beginn Bau- bzw. Forschungs-Zeit Boni. Eine Tabelle findest du in der Hilfe. ', 8),
(7, 1, 'Deine Entscheidung!', 'Hier nochmals eine Zusammenfassung der wichtigen Fragen:\r\n[list][*]Wieviel Zeit will/kann ich aufbringen[*]Welche Spielweise liegt mir am ehesten[*]Welche Rasse wähle ich dafür (Schiffe, Ressourcen)[*]Welche Stern/Planeten-Kombination unterstützt meine Spielweise und kann evtl. Nachteile meiner Rasse ausgleichen.[/list]\r\nNeulingen ist angeraten, den Startplaneten so zu wählen das kein Malus bei Titan oder Silizium vorhanden ist. Bei der Wahl der Kolonien kann das wieder anders aussehen.\r\n\r\nViel Erfolg, mein Imperator, möge dein Reich groß und mächtig werden und lange bestehen !\r\n\r\nDein EtoA-Team.', 10),
(8, 2, 'Auf gehts!', 'Du hast deine Kombo gefunden? Dann geht es hier weiter mit einer kleinen Anleitung zu einem erfolgreichen Start.\r\n\r\nWICHTIG: Solltest du aus Versehen die falsche Kombination ausgewählt haben und hast du noch nichts darauf gebaut melde es einem Administrator. Er kann dir vll aus dieser misslichen Lage heraus helfen. Das ist auf jeden Fall besser als mit einer Kombination weiter zu spielen die schlechte Startmöglichkeiten hat.\r\n', 0),
(9, 2, 'Ressourcen', 'Grundstoffe sind [b]Titan[/b](Tit) und [b]Silizium[/b](Sil). Weiterführend kommt [b]PVC[/b] dazu, am Ende benötigt man [b]Tritium[/b](Trit). [b]Nahrung[/b](Nah) dient der Bauzeitverkürzung und wird zum Fliegen benötigt. Nebenbei benötigt jede Mine auch [b]Energie[/b]. Man muss also auch diese Sparte mit ausbauen.\r\n\r\nJede Ressource baut auf die andere auf. Ohne Tit/Sili kein pvc. Ohne pvc kein Trit. Ohne Trit keine Besiedlungsschiffe.\r\nDaher ist es gerade zu Beginn sehr wichtig mit den Ressourcen sparsam umzugehen und sie sinnvoll zu verbauen.', 1),
(10, 2, 'Die Baureihenfolge', 'Hier scheiden sich die Geister. Jeder Spieler wird eine andere Vorgehensweise haben die ihn am ehesten zu seinem Ziel führt, welches er sich zum Ziel gesetzt hat. (Profil) Auch die Boni sind ein bedeutender Faktor. Daher folgt hier eine allgemeingültige Vorgehensweise.\r\n\r\nZu Beginn habt ihr einen Grundstock an allen Ressourcen die es euch ermöglichen soweit bauen zu können, dass ihr Tit/Sil/PVC selbstständig produzieren könnt.\r\n[list][*]Zuerst Titan- und Silizium-Minen ausbauen. Wobei hier zuerst Titan, danach Sili. Sie sollten immer 2 Stufen Unterschied haben. Bsp: Titan 5, Sili 3[*]Bei 5/3 solltet ihr an die Grenze eurer Energie gestoßen sein. Daher muß jetzt ein Windkraftwerk gebaut werden. Baut dieses aber immer erst wenn die Energie nahe bei 0 ist. Das gilt auch für die weiteren Stufen.[*]Nach dem Kraftwerk sollte die PVC-Produktion um 1-2 Stufen erhöht werden, je nachdem wieviel tit/sil über ist. Ohne PVC gibt es keine Kraftwerke. Ohne Kraftwerke keine Energie. Ohne Energie wird die Produktion sämtlicher Ressourcen gedrosselt.[*]Sobald ihr Wind auf Stufe 5 habt könnt ihr mit dem Bau von Tritium-Anlagen beginnen. Dies sollte auch direkt geschehen denn ohne Trit keine Forschung, ohne Forschung keine Antriebe, ohne Antriebe kein Besiedlungsschiff. Oder aber es dauert ewig, weil ihr zu spät mit dem Ausbau von Trit begonnen habt.[*]Bewährt hat sich auch, früh die Nahrung auf Stufe 2 oder 3 zu bringen, wenn man Ress über hat. Man braucht sie zwar nicht zu Beginn, aber später kann man vll schneller forschen um sein Besiedlungsschiff zu bauen.[*]Sollte eure Bevölkerung keinen Platz mehr zum Wachsen haben, baut das Wohnmodul aus. Hier reichen aber 2 Stufen locker aus.[*]Sinnlos ist das Bauen von Speichern. Zu Beginn werdet ihr immer zuwenig als zuviel Ressourcen haben.[/list]\r\nNun sollte eure Rohstoffproduktion auf soliden Füßen stehen.', 2),
(11, 2, 'Verteidigung', 'Leider gibt es auch in EtoA Spieler, die nach euren Ressourcen trachten. Daher ist es wichtig, nach ca. 48-60h eine Verteidigungsanlage auf eurem Planeten aufgestellt zu haben. Dazu benötigt ihr eine [b]Waffenfabrik[/b] und eine [b]Spica Flakkanone[/b]. Beides muss in der Ressourcenplanung und somit beim Ausbau bedacht werden.', 3),
(12, 2, 'Schiffe', 'Sobald die Voraussetzungen für den Bau von Schiffen da sind, baut euch einen [b]AURIGA Explorer[/b], mit dem ihr die Sternenkarte aufdecken könnt. So findet ihr die passenden Kombos für euer TAURUS Besiedlungsschiff, das Ziel eurer Bemühungen.\r\nAllerdings braucht es dafür eine Schiffswerft, eine Flottenkontrolle und einen Ionenantrieb welcher in einem Forschungslabor erforscht werden muss.\r\n\r\nUm dies alles sinnvoll zu erreichen und auch um zügig [b]TAURUS[/b] bauen zu können hat sich folgender Ausbau bewährt:\r\n\r\nTitan 11(+1)\r\nSilizium 11(+1)\r\nPVC 10(+1)\r\nTritium 8(+1)\r\n\r\nJe nach Rasse/Stern/Planeten-Kombo kann das natürlich anders aussehen. So wird einem Rigelianer Sil 9 reichen, braucht aber tit 12 oder 13.', 4),
(13, 2, 'Abschluss', 'Die richtige Kombo ist gefunden? Das 1. TAURUS ist unterwegs? Hoffentlich habt ihr nicht vergessen Tit/Sili zum Bau von Minen sowie soviel PVC mitzuschicken damit ihr Wind 3 bauen könnt. Denn ohne Wind 3 keine PVC-Fabrik, ohne PVC kein Kraftwerke, ohne Kraftwerk keine Ressourcen.....ihr kennt das ja.\r\n\r\nPVC ist dabei? Dann alles Gute und viel Glück beim Vergrößern deines Einflussbereiches, werter Imperator.\r\n\r\nDein EtoA- Team', 5),
(15, 1, 'Der Miner', 'Der [b]Miner[/b] spielt sein Spiel gemütlich und lebt fast ausschließlich von seiner eigenen Ressourcen-Produktion. Nebenbei wird er auch im Weltall Ressourcen sammeln gehen. Dabei werden die Ressourcen meist in größere Minen investiert. Nur ein kleiner Teil ihrer Ressourcen wandert in Schiffe oder Verteidigung. Vor dem Miner braucht man sich grundsätzlich nicht zu fürchten, jedoch sollte man nicht denken es sind leichte Opfer, denn sie haben oft Freunde unter den 24/7 Spielern (ständig online); welche ihnen gerne helfen. Oder sie haben sich einer Allianz angeschlossen, die sie beschützen kann.\r\n\r\n[i]Zeitaufwand: Gering/Mittel[/i]', 4),
(16, 1, 'Der Fleeter', 'Der [b]Fleeter[/b] gehört zu den aggressiven Spielern in EtoA. Er deckt seinen zusätzlichen Bedarf an Ressourcen durch Raiden (Stehlen von Ressourcen) oder aber durch Kämpfe mit anderen Spielern. Dafür wandert ein Großteil seiner Produktion in Schiffe. Der Vorteil ist dabei die abschreckende Wirkung einer großen Flotte. (Der Nachteil ist, wenn die Flotte mal zerstört werden sollte ist es für ihn wesentlich schwieriger, wieder an neue Flotte zu kommen, da seine Eigen-Produktion an Ressourcen nicht ausreicht). Das Einzige was gegen den Fleeter hilft ist Ressourcen und Schiffe in Sicherheit zu bringen (Saven) oder ihm mittels einer Verteidigung welche ihm ordentliche Verluste beibringt, die Lust zu nehmen. Sollten man öfters von dem gleichen Fleeter geraidet werden so kann es durchaus von Nutzen sein, eine freundliche Anfrage zu schicken. Meist wird darauf positiv reagiert.\r\n\r\n[i]Zeitaufwand: Hoch[/i]', 5),
(17, 1, 'Der Händler', 'Der [b]Händler[/b] hat sich auf das Handeln von Waren und Schiffen spezialisiert. Er baut seine Markplätze hoch aus um immer genug große oder kleine Angebote in den Marktplatz stellen zu können. Er ist meistens ein ruhiger Geselle der sein Ressourcen-Extra mit dem Verkauf von Waren realisiert. Hier ist die Wahl der Rasse ein entscheidender Faktor. Nahrung oder Tritium oder PVC sind gern genommene Rohstoffe, auch einige Schiffe lassen sich gut verkaufen, sei es sie sind schnell oder haben eine Spezialfunktion. Mehr dazu findet ihr in der Hilfe.\r\n\r\n[i]Zeitaufwand Mittel[/i]', 6);

CREATE TABLE `tutorial_user_progress` (
 `tup_user_id` int(10) unsigned NOT NULL,
 `tup_tutorial_id` int(10) unsigned NOT NULL,
 `tup_text_step` tinyint(1) unsigned NOT NULL DEFAULT '0',
 `tup_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
 UNIQUE KEY `tup_user_id` (`tup_user_id`,`tup_tutorial_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- User surveillance
--
ALTER TABLE `user_surveillance` ADD `request_raw` text NOT NULL AFTER `request`;
