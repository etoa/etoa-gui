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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: textformat.php
	// 	Topic: Hilfeseite Textformat
	// 	Autor: Selina Tanner alias Demora
	// 	Erstellt: 31.05.2007
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.05.2007
	// 	Kommentar:
	//

	echo "<h2>Texformatierung</h2>";		
	HelpUtil::breadCrumbs(array("Texformatierung","textformat"));
	
	// Array BBCode
	
	$bb = [
		[
			'm' => "Text <b>fett</b> schreiben",
			'b' => "[b]EtoA[/b]"
		],
		[
			'm' => "Text <b>unterstreichen</b>",
			'b' => "[u]EtoA[/u]"
		],
		[
			'm' => "Text <b>kursiv</b> schreiben",
			'b' => "[i]EtoA[/i]"
		],
		[
			'm' => '<b>Verschiedenfarbig</b> schreiben (<a href="#colors">Farbliste</a>)',
			'b' => "[color=red]EtoA[/color]"
		],
		[
			'm' => "<b>Grösse</b> ändern",
			'b' => "[size=15]EtoA[/size]"
		],
		[
			'm' => "<b>Schriftart</b> ändern",
			'b' => "[font=times]EtoA[/font]"
		],
		[
			'm' => "<b>Textausrichtung</b> ändern: zentriert",
			'b' => "[center]EtoA[/center]"
		],
		[
			'm' => "<b>Textausrichtung</b> ändern: rechtsbündig",
			'b' => "[right]EtoA[/right]"
		],
		[
			'm' => "<b>E-Mail</b> Link erstellen <b>(Adresse sichtbar)</b>",
			'b' => "[email]mail@etoa.ch[/email]"
		],
		[
			'm' => "<b>E-Mail</b> Link erstellen <b>(Adresse unsichtbar)</b>",
			'b' => "[email=mail@etoa.ch]EtoA[/email]"
		],
		[
			'm' => "<b>Link</b> zu einer Homepage erstellen <b>(Adresse sichtbar)</b>",
			'b' => "[url]http://www.etoa.ch[/url]"
		],
		[
			'm' => "<b>Link</b> zu einer Homepage erstellen <b>(Adresse unsichtbar)</b>",
			'b' => "[url=http://www.etoa.ch]EtoA[/url]"
		],
		[
			'm' => "<b>Bild</b> einfügen auf einer Homepage <b>(Bild sichtbar)</b>",
			'b' => "[img]http://etoa.ch/images/logo_mini.gif[/img]"
		],
		[
			'm' => "<b>Anklickbares</b> Bild einfügen",
			'b' => "[url=http://etoa.ch/images/logo_mini.gif][img]http://etoa.ch/images/logo_mini.gif[/img][/url]"
		],
		[
			'm' => "<b>Interner Link</b>",
			'b' => "Erkunde das [page=cell&id=635]System 635[/page] mit dem [page=help&site=shipyard&id=71]AURIGA Explorer[/page] und schreib mir eine [page=messages&mode=new]Nachricht[/page] mit den Resultaten!"
		],
		[
			'm' => "<b>Link</b> zu einem Bild im Internet einfügen <b>(Bild nicht sichtbar)</b>",
			'b' => "[url=http://etoa.ch/images/logo_mini.gif]EtoA Logo[/url]"
		],
		[
			'm' => "Text <b>zitieren (ohne Autor)</b>",
			'b' => "[quote]EtoA[/quote]"
		],
		[
			'm' => "Text <b>zitieren (mit Autor)</b>",
			'b' => "[quote=Hans Muster]EtoA[/quote]"
		],
		[
			'm' => "Blockcode: <b>Zentriert</b> den Text und verwendet die Schriftart <b>Courier New</b> (praktisch für Programmcode)",
			'b' => "[bc]EtoA ist ein Onlinebrowsergame[/bc]"
		],
		[
			'm' => "Liste erstellen: Aufzählung <b>ohne</b> Nummerierung; beliebig viele Elemente möglich",
			'b' => "[list][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
		],
		[
			'm' => "Liste erstellen: Aufzählung <b>mit nummerischer</b> Nummerierung; beliebig viele Elemente möglich",
			'b' => "[nlist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/nlist]"
		],
		[
			'm' => "Liste erstellen: Aufzählung <b>mit nummerischer</b> Nummerierung; beliebig viele Elemente möglich",
			'b' => "[list=1][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
		],
		[
			'm' => "Liste erstellen: Aufzählung <b>mit alphabetischer</b> Nummerierung; beliebig viele Elemente möglich",
			'b' => "[alist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/alist]"
		],
		[
			'm' => "Liste erstellen: Aufzählung <b>mit alphabetischer</b> Nummerierung; beliebig viele Elemente möglich",
			'b' => "[list=a][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
		],
		[
			'm' => "Liste erstellen: Aufzählung <b>mit römischer</b> Nummerierung; beliebig viele Elemente möglich",
			'b' => "[rlist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/rlist]"
		],
		[
			'm' => "Liste erstellen: Aufzählung <b>mit römischer</b> Nummerierung; beliebig viele Elemente möglich",
			'b' => "[list=I][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]"
		],
		[
			'm' => '<b>Flagge</b> eines Landes einfügen oder Kantons einfügen (<a href="#flags">Flaggen</a>)',
			'b' => "[flag ch-be] liegt in der [flag ch]",
		]
	];
	
	$kt = [
		[
			'n' => "Argau",
			'f' => "[flag ch-ag]"
		],
		[
			'n' => "Appenzell Innerrhode",
			'f' => "[flag ch-ai]"
		],
		[
			'n' => "Appenzell Ausserrhoden",
			'f' => "[flag ch-ar]"
		],
		[
			'n' => "Bern",
			'f' => "[flag ch-be]"
		],
		[
			'n' => "Basel Land",
			'f' => "[flag ch-bl]"
		],
		[
			'n' => "Basel Stadt",
			'f' => "[flag ch-bs]",
		],
		[
			'n' => "Graubünden",
			'f' => "[flag ch-gr]"
		],
		[
			'n' => "Jura",
			'f' => "[flag ch-ju]"
		],
		[
			'n' => "Luzern",
			'f' => "[flag ch-lu]"
		],
		[
			'n' => "Nidwalden",
			'f' => "[flag ch-nw]",
		],
		[
			'n' => "Obwalden",
			'f' => "[flag ch-ow]"
		],
		[
			'n' => "Schaffhausen",
			'f' => "[flag ch-sh]"
		],
		[
			'n' => "Schwyz",
			'f' => "[flag ch-sz]"
		],
		[
			'n' => "Solothurn",
			'f' => "[flag ch-so]"
		],
		[
			'n' => "Thurgau",
			'f' => "[flag ch-tg]"
		],
		[
			'n' => "Tessin",
			'f' => "[flag ch-ti]"
		],
		[
			'n' => "Uri",
			'f' => "[flag ch-ur]"
		],
		[
			'n' => "Waadt",
			'f' => "[flag ch-vd]"
		],
		[
			'n' => "Wallis",
			'f' => "[flag ch-vs]"
		],
		[
			'n' => "Zug",
			'f' => "[flag ch-zg]"
		],
		[
			'n' => "Zürich",
			'f' => "[flag ch-zh]"
		],
		[
			'n' => "Genf",
			'f' => "[flag ch-ge]"
		]
	];
	
	$fl = [
		[	
			'n' => "Schweiz",
			'f' => "[flag ch]"
		],
		[
			'n' => "Argentinien",
			'f' => "[flag ar]"
		],
		[
			'n' => "Österreich",
			'f' => "[flag at]"
		],
		[
			'n' => "Australien",
			'f' => "[flag au]"
		],
		[
			'n' => "Beneluxstaaten",
			'f' => "[flag benelux]"
		],
		[
			'n' => "Bulgarien",
			'f' => "[flag bg]"
		],
		[
			'n' => "Brasilien",
			'f' => "[flag br]"
		],
		[
			'n' => "Kanada",
			'f' => "[flag ca]"
		],
		[
			'n' => "China",
			'f' => "[flag cn]"
		],
		[
			'n' => "Tschechien",
			'f' => "[flag cz]"
		],
		[
			'n' => "Deutschland",
			'f' => "[flag de]"
		],
		[
			'n' => "Dänemark",
			'f' => "[flag dk]"
		],
		[
			'n' => "Estland",
			'f' => "[flag ee]"
		],
		[
			'n' => "Europa",
			'f' => "[flag eu]"
		],
		[
			'n' => "Finnland",
			'f' => "[flag fi]"
		],
		[
			'n' => "Frankreich",
			'f' => "[flag fr]"
		],
		[
			'n' => "Grossbritannien",
			'f' => "[flag gb]"
		],
		[
			'n' => "Griechenland",
			'f' => "[flag gr]"
		],
		[
			'n' => "Kroatien",
			'f' => "[flag hr]"
		],
		[
			'n' => "Israel",
			'f' => "[flag il]"
		],
		[
			'n' => "Indien",
			'f' => "[flag in]"
		],
		[
			'n' => "Japan",
			'f' => "[flag jp]"
		],
		[
			'n' => "Südkorea",
			'f' => "[flag kp]"
		],
		[
			'n' => "Luxemburg",
			'f' => "[flag lu]"
		],
		[
			'n' => "Lettland",
			'f' => "[flag lv]"
		],
		[
			'n' => "Niederlande",
			'f' => "[flag nl]"
		],
		[
			'n' => "Norwegen",
			'f' => "[flag no]"
		],
		[
			'n' => "Polen",
			'f' => "[flag pl]"
		],
		[
			'n' => "Russland",
			'f' => "[flag ru]"
		],
		[
			'n' => "Schweden",
			'f' => "[flag se]"
		],
		[
			'n' => "Slowakei",
			'f' => "[flag sk]"
		],
		[
			'n' => "Spanien",
			'f' => "[flag sp]"
		],
		[
			'n' => "Türkei",
			'f' => "[flag ty]"
		],
		[
			'n' => "USA",
			'f' => "[flag us]"
		],
		[
			'n' => "Vatikan",
			'f' => "[flag vn]"
		],
		[
			'n' => "Welt",
			'f' => "[flag world]"
		]
	];

	// Table code
	tableStart("Liste der wichtigsten BB-Codes");
	echo "<tr><th class=\"tbltitle\">Das m&ouml;chtest du machen</th><th class=\"tbltitle\">BBCode an einem kleinen Beispiel</th><th class=\"tbltitle\">Sichtbares Resultat</th></tr>";
	// Several cells of the table
	foreach ($bb as $code)
	{
		echo "<tr><td class=\"tbldata\" style=\"text-align:left\">".$code['m']."</td>";
		echo "<td class=\"tbldata\" style=\"text-align:left\">".$code['b']."</td>";
		echo "<td class=\"tbldata\" style=\"text-align:left\">".text2html($code['b'])."</td></tr>";
	}
	tableEnd();	
	
	// Potential errors
	iBoxStart("Fehlerquellen");
	echo "Es kann vorkommen, dass du mehrere BB-Codes kombinieren möchtest (zum Beispiel eine grössere Schrift und andere Farbe).<br/>
		Dabei ist es wichtig, dass du die Reihenfolge der Tags beachtest; es gillt, immer von innen nach aussen arbeiten.<br/><br/>
		Beispiel: Text rot schreiben und Schriftgrösse 15;<br/><br/>
		[color=red][size=15]EtoA[/size][/color] ist richtig und ergibt ".text2html('[color=red][size=15]EtoA[/size][/color]').", aber [color=red][size=15]EtoA[/color][/size] ist falsch und gibt kein gültiges Resultat!<br/><br/>
		Theoretisch kannst du so viele verschiedene Tags hinterinander hängen. Wichtig ist einfach, dass die Reihenfole immer beachtet wird. Um nicht unschöne Fehler in einer Nachricht oder in einem Forenpost zu haben, ist es immer gut, wenn ingame die Nachrichtenvorschau eingeschaltet ist und wenn man vor dem absenden des Posts noch schnell auf Vorschau klickt im Forum. So sparrt man Zeit und verhindert unschöne Fehler.";
	iBoxEnd();
	
	//Table colour
	echo '<a id="colors"></a>';
	iBoxStart("Schriftfarben");
	echo "Im BBCode kannst du entweder den Farbnamen eingeben oder den Hexadecimalcode.<br/><br/>";
	include("colorlist.php");
	iBoxEnd();
	
	//Table flags
	echo '<a id="flags"></a>';
	iBoxStart("Flaggen");
	echo "Klicke auf den jeweiligen Link, wenn du eine Flagge für ein Land oder eine Flagge für einen schweizer Kanton einfügen möchtest.
		Als BBCode musst du dann jeweils nur den aufgeführten Tag in deinen text schreiben.<br/>
		<br/>Bemerkung: Beide Listen sind nicht vollständig, d.h. es kann sein, dass dein Kanton oder dein Lieblingsland gerade fehlt!<br/>";
	echo '<table style="width:100%;"><tr><td style="width:50%;vertical-align:top;">';
	tableStart();
	echo "<tr><th class=\"tbltitle\">Kanton</th><th class=\"tbltitle\">BBCode</th><th class=\"tbltitle\">Flagge</th></tr>";  	
  	foreach($kt as $city)
	{
		echo "<tr><td class=\"tbldata\" style=\"text-align:left\">".$city['n']."</td>";
		echo "<td class=\"tbldata\" style=\"text-align:left\">".$city['f']."</td>";
		echo "<td class=\"tbldata\" style=\"text-align:left\">".text2html($city['f'])."</td></tr>";
	}
	tableEnd();
	echo '</td><td style="width:50%;vertical-align:top;">';
	tableStart();
	echo "<tr><th class=\"tbltitle\">Land</th><th class=\"tbltitle\">BBCode</th><th class=\"tbltitle\">Flagge</th></tr>";
  	foreach($fl as $land)
	{
		echo "<tr><td class=\"tbldata\" style=\"text-align:left\">".$land['n']."</td>";
		echo "<td class=\"tbldata\" style=\"text-align:left\">".$land['f']."</td>";
		echo "<td class=\"tbldata\" style=\"text-align:left\">".text2html($land['f'])."</td></tr>";
	}		
	tableEnd();
	echo '</td></tr></table>';
	iBoxEnd();
?>