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
			'm' => "<b>Verschiedenfarbig</b> schreiben",
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
			'm' => "<b>Flagge</b> eines Landes einfügen",
			'b' => "[flag ch]",
		],
		[
			'm' => "<b>Flagge</b> eines schweizer Kantons einfügen",
			'b' => "[flag ch-be]"
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
	
	
	//Table flags
	iBoxStart("Liste mit den vorhandenen Flaggen");
		{
			echo "Klicke auf den jeweiligen Link, wenn du eine Flagge für ein Land oder eine Flagge für einen schweizer Kanton einfügen möchtest. Als BBCode musst du dann jeweils nur den aufgeführten Tag in deinen text schreiben.<br/>
						Bemerkung: Beide Listen sind nicht vollständig, d.h. es kann sein, dass dein Kanton oder dein Lieblingsland gerade fehlt!<br/><br/>";
			echo "<b><a href=\"javascript:;\" onclick=\"window.open('show.php?info=flags_land','infobox','width=800,height=600,scrollbars=yes')\">Land</a></b><br/><br/>";
			echo "<b><a href=\"javascript:;\" onclick=\"window.open('show.php?info=flag_kanton','infobox','width=800,height=600,scrollbars=yes')\">Kanton</a></b><br/>";
		}
	iBoxEnd();
	
	//Table colour
	iBoxStart("Erlaubte Schriftfarben");
		{
			echo "Klicke auf den Link, um eine Tabelle mit allen erlaubten Farben für den BBCode zu öffnen. Im BBCode kannst du entweder den Farbnamen eingeben oder den Hexadecimalcode.<br/><br/>";
			echo "<b><a href=\"javascript:;\" onclick=\"window.open('show.php?info=colorlist','infobox','width=800,height=600,scrollbars=yes')\">Farbliste</a></b><br/>";
		}		
	iBoxEnd();
	
	iBoxStart("Fehlerquellen");
		{
			echo "Es kann vorkommen, dass du mehrere BB-Codes kombinieren möchtest (zum Beispiel eine grössere Schrift und andere Farbe).<br/>
						Dabei ist es wichtig, dass du die Reihenfolge der Tags beachtest; es gillt, immer von innen nach aussen arbeiten.<br/><br/>
						Beispiel: Text rot schreiben und Schriftgrösse 15;<br/><br/>
						[color=red][size=15]EtoA[/size][/color] ist richtig und ergibt ".text2html('[color=red][size=15]EtoA[/size][/color]').", aber [color=red][size=15]EtoA[/color][/size] ist falsch und gibt kein gültiges Resultat!<br/><br/>
						Theoretisch kannst du so viele verschiedene Tags hinterinander hängen. Wichtig ist einfach, dass die Reihenfole immer beachtet wird. Um nicht unschöne Fehler in einer Nachricht oder in einem Forenpost zu haben, ist es immer gut, wenn ingame die Nachrichtenvorschau eingeschaltet ist und wenn man vor dem absenden des Posts noch schnell auf Vorschau klickt im Forum. So sparrt man Zeit und verhindert unschöne Fehler.";
		}
	iBoxEnd();
?>