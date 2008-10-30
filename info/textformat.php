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
	Help::navi(array("Texformatierung","textformat"));
	
	// Array BBCode
	
	$bb[0]['m']="Text <b>fett</b> schreiben";
	$bb[0]['b']="[b]EtoA[/b]";
	
	$bb[1]['m']="Text <b>unterstreichen</b>";
	$bb[1]['b']="[u]EtoA[/u]";
	
	$bb[2]['m']="Text <b>kursiv</b> schreiben";
	$bb[2]['b']="[i]EtoA[/i]";
	
	$bb[3]['m']="<b>Verschiedenfarbig</b> schreiben";
	$bb[3]['b']="[color=red]EtoA[/color]";
	
	$bb[4]['m']="<b>Grösse</b> ändern";
	$bb[4]['b']="[size=15]EtoA[/size]";
	
	$bb[5]['m']="<b>Schriftart</b> ändern";
	$bb[5]['b']="[font=times]EtoA[/font]";
	
	$bb[6]['m']="<b>Textausrichtung</b> ändern: zentriert";
	$bb[6]['b']="[center]EtoA[/center]";
	
	$bb[7]['m']="<b>Textausrichtung</b> ändern: rechtsbündig";
	$bb[7]['b']="[right]EtoA[/right]";
	
	$bb[8]['m']="<b>E-Mail</b> Link erstellen <b>(Adresse sichtbar)</b>";
	$bb[8]['b']="[email]mail@etoa.ch[/email]";	
	
	$bb[9]['m']="<b>E-Mail</b> Link erstellen <b>(Adresse unsichtbar)</b>";
	$bb[9]['b']="[email=mail@etoa.ch]EtoA[/email]";
	
	$bb[10]['m']="<b>Link</b> zu einer Homepage erstellen <b>(Adresse sichtbar)</b>";
	$bb[10]['b']="[url]http://www.etoa.ch[/url]";
	
	$bb[11]['m']="<b>Link</b> zu einer Homepage erstellen <b>(Adresse unsichtbar)</b>";
	$bb[11]['b']="[url=http://www.etoa.ch]EtoA[/url]";                   
	
	$bb[12]['m']="<b>Bild</b> einfügen auf einer Homepage <b>(Bild sichtbar)</b>";
	$bb[12]['b']="[img]http://etoa.ch/images/logo_mini.gif[/img]";
	
	$bb[13]['m']="<b>Anklickbares</b> Bild einfügen";
	$bb[13]['b']="[url=http://etoa.ch/images/logo_mini.gif][img]http://etoa.ch/images/logo_mini.gif[/img][/url]";	
	
	$bb[14]['m']="<b>Link</b> zu einem Bild im Internet einfügen <b>(Bild nicht sichtbar)</b>";
	$bb[14]['b']="[url=http://etoa.ch/images/logo_mini.gif]EtoA Logo[/url]";  
	
	$bb[15]['m']="Text <b>zitieren (ohne Autor)</b>";
	$bb[15]['b']="[quote]EtoA[/quote]";
	
	$bb[16]['m']="Text <b>zitieren (mit Autor)</b>";
	$bb[16]['b']="[quote=Hans Muster]EtoA[/quote]";
	
	$bb[17]['m']="Blockcode: <b>Zentriert</b> den Text und verwendet die Schriftart <b>Courier New</b> (praktisch für Programmcode)";           
	$bb[17]['b']="[bc]EtoA ist ein Onlinebrowsergame[/bc]";
	
	$bb[18]['m']="Liste erstellen: Aufzählung <b>ohne</b> Nummerierung; beliebig viele Elemente möglich";
	$bb[18]['b']="[list][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]";

	$bb[19]['m']="Liste erstellen: Aufzählung <b>mit nummerischer</b> Nummerierung; beliebig viele Elemente möglich";
	$bb[19]['b']="[nlist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/nlist]";                        

	$bb[20]['m']="Liste erstellen: Aufzählung <b>mit nummerischer</b> Nummerierung; beliebig viele Elemente möglich";
	$bb[20]['b']="[list=1][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]";                        

	$bb[21]['m']="Liste erstellen: Aufzählung <b>mit alphabetischer</b> Nummerierung; beliebig viele Elemente möglich";
	$bb[21]['b']="[alist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/alist]";  
	
	$bb[22]['m']="Liste erstellen: Aufzählung <b>mit alphabetischer</b> Nummerierung; beliebig viele Elemente möglich";
	$bb[22]['b']="[list=a][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]";
	
	$bb[23]['m']="Liste erstellen: Aufzählung <b>mit römischer</b> Nummerierung; beliebig viele Elemente möglich";
	$bb[23]['b']="[rlist][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/rlist]";  
	
	$bb[24]['m']="Liste erstellen: Aufzählung <b>mit römischer</b> Nummerierung; beliebig viele Elemente möglich";
	$bb[24]['b']="[list=I][*]Andorianer[*]Minbari[*]Vorgonen[*]etc.[/list]";
	
	$bb[25]['m']="<b>Flagge</b> eines Landes einfügen";
	$bb[25]['b']="[flag ch]";
	
	$bb[26]['m']="<b>Flagge</b> eines schweizer Kantons einfügen";
	$bb[26]['b']="[flag ch-be]";	
	
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