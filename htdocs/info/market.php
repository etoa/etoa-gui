<?php

	echo "<h2>Markt</h2>";
	HelpUtil::breadCrumbs(array("Markt","market"));

	iBoxStart("Markt");
	echo "<div align=\"justify\">";

	echo "Der Marktplatz, das Handelszentrum von Andromeda.<br>
		Baue den Marktplatz aus, um Zugang zum \"<a href=\"?page=market\">Markt</a>\" zu erhalten und somit in das Reich der H&auml;ndler einzutauchen.
		Erwerbe Waren von anderen Spielern oder stelle deine eigenen Angebote ein. Je h&ouml;her du den Markt ausgebaut hast, desto mehr Angebote kannst du einstellen und desto mehr Handelsm&ouml;glichkeiten hast du: <br>Stufe ".MIN_MARKET_LEVEL_RESS." -> Rohstoffhandel<br>Stufe ".MIN_MARKET_LEVEL_SHIP." -> Schiffshandel<br>Stufe ".MIN_MARKET_LEVEL_AUCTION." -> Auktionen<br>
		Wird ein Handel erfolgreich abgeschlossen, so werden die Waren in den nächsten paar Minuten an die betroffenen Spieler versendet.";

	echo "</div>";
	iBoxEnd();


	iBoxStart("Die verschiedenen Funktionen");
	echo "<div align=\"justify\">";

		echo "
		<b>Angebote aufgeben</b><br>
		Hier kannst du eigene Angebote in den Markt stellen.<br><br>";

		echo "
		<ul>
        <i>Rohstoffhandel:</i><br>
        Der Markt bietet die M&ouml;glichkeit Rohstoffe zu kaufen oder verkaufen. Wenn du Rohstoffe verkaufen willst, gib die gew&uuml;nschte Anzahl in das entsprechende Eingabefeld ein und definiere einen Preis daf&uuml;r.
        Es ist aber darauf zu achten, dass der Markt einen Mindest- sowie einen Maximalbetrag festlegt der nicht unter- bzw. &uuml;berschritten werden kann.
        Dieser Wert wird aus der Summe der zu verkaufenden Rohstoffe, welche mit den aktuellen Rohstoffkursen verrechnet wurden, gebildet.<br>
        Der Mindestpreis betr&auml;gt beim Rohstoffhandel ".(RESS_PRICE_FACTOR_MIN*100)."% und der Maximalpreis ".(RESS_PRICE_FACTOR_MAX*100)."% des Gesamtwertes. Der Markt rechnet dir immer diese Mindest- und Höchstpreise aus.<br>
        Beim Rohstoffhandel fallen dem Verkäufer die Marktgebühren (".((MARKET_SELL_TAX-1)*100)."%) zur Last, welche sich auf die maximal zu verkaufenden Rohstoffmengen auswirken. Es ist also nicht möglich 100% der aktuellen Rohstoffe auf dem Planeten zum Verkauf anzubieten!<br>
        Wird das Angebot nicht innerhalb von ".$conf['market_response_time']['v']." Tagen verkauft, so wird es automatisch gelöscht. Du erhälst dannach aber nur noch einen gewissen Prozentsatz zurück. Dieser Wert ist der gleiche, wie wenn du das Angebot manuell zurückziehst!
		</ul><br>";
		echo "
		<ul>
        <i>Schiffshandel:</i><br>
        Der Markt bietet auch die M&ouml;glichkeit Schiffe zu kaufen oder verkaufen. Wenn du ein Schiff verkaufen willst, w&auml;hle das gew&uuml;nschte Schiff aus dem Optionsfeld aus und bestimme die Anzahl die du verkaufen willst. Nun kannst du den Preis der Schiffe festlegen. Die Zahlen die bereits in den Feldern stehen, sollen nur als Hilfe dienen, um dich &uuml;ber den Wert der Schiffe zu informieren. Diese Zahlen k&ouml;nnen beliebig ge&auml;ndert werden!
        Es ist aber darauf zu achten, dass auch bei dieser Art von Handel der Markt einen Mindest- sowie einen Maximalbetrag festlegt, der nicht unter- bzw. &uuml;berschritten werden kann.
        Dieser Wert wird aus der Summe der Baurohstoffe errechnet, welche alle Schiffe zusammen gekostet haben (exklusiv Nahrung).
        Der Mindestpreis betr&auml;gt beim Schiffshandel ".(SHIP_PRICE_FACTOR_MIN*100)."% und der Maximalpreis ".(SHIP_PRICE_FACTOR_MAX*100)."% des Gesamtwertes.<br>
        Der Schiffshandel ist von den Marktgebühren ausgeschlossen, sowohl Käufer wie auch Verkäufer zahlen keine Zuschläge!<br>
        Wird das Angebot nicht innerhalb von ".$conf['market_response_time']['v']." Tagen verkauft, so wird es automatisch gelöscht. Du erhälst dannach aber nur noch einen gewissen Prozentsatz zurück. Dieser Wert ist der gleiche, wie wenn du das Angebot manuell zurückziehst!
		</ul><br>";
		echo "
		<ul>
        <i>Auktionen:</i><br>
        Diese neuartige Form von Handel erlaubt dir, Rohstoffe zu versteigern.
        Das Ziel einer Auktion ist es, seine Waren zum best m&ouml;glichen Preis zu verkaufen. Im Auktionsformular kannst du, wie beim Rohstoffhandel, die Mengen Rohstoffe angeben die du versteigern willst.<br>
        Unter dem Abschnitt \"Preis\" kannst du angeben, welche Rohstoffe du f&uuml;r dein Angebot haben willst. Mach einen Haken bei den Rohstoffarten mit denen bezahlt werden soll.<br>
        Unter dem Abschnitt \"Dauer\" wird die Dauer der Auktion festgelegt. Anders als beim normalen Verkauf, sind die Auktionen zeitlich begrenzt!<br>
        Die Mindestdauer bei einer Auktion betr&auml;gt ".AUCTION_MIN_DURATION." Tage. Diese Zeit kann nach Wunsch verl&auml;ngert werden.<br>
        Ist alles ausgef&uuml;llt was n&ouml;tig ist, kannst du die Auktion starten und ab diesem Zeitpunkt ist die Auktion im Handel.
        Die anderen Spieler haben nun die M&ouml;glichkeit bei der Auktion zu bieten. Der Gewinner ist, wer nach Ablauf der Zeit das h&ouml;chste Gebot aufweisst, oder derjenige, der als erstes bereit ist, den Maximalbetrag zu zahlen!<br>
        Wie auch beim Schiffs- und Rohstoffhandel gibt es bei den Auktionen einen Mindest- und Maximalbetrag, der f&uuml;r die angebotene Ware zu zahlen ist.
        Das Mindestgebot liegt hier bei ".(AUCTION_PRICE_FACTOR_MIN*100)."% und das H&ouml;chstgebot bei ".(AUCTION_PRICE_FACTOR_MAX*100)."%.<br>
        Wenn ein Spieler diese ".(AUCTION_PRICE_FACTOR_MAX*100)."% bietet, ist er der sofortige Gewinner der Auktion und somit ist die Auktion beendet. Ansonsten ist der H&ouml;chstbietende nach Ablauf der Zeit der Gewinner.<br>
        Hat in der ganzen Zeit kein Spieler bei deiner Auktion geboten so ist die Auktion ebenfalls beendet und du bekommst deine Waren mit dem Restwert zur&uuml;ckerstattet.<br>
        Ist ein Sieger bekannt und die Auktion wird als \"Beendet\" angesehen, so sind die Waren an die betroffenen Spieler gesendet. Das Angebot bleibt aber aus Sicherheitsgr&uuml;nden noch f&uuml;r ".AUCTION_DELAY_TIME." Stunden sichtbar!
        Nach den abgelaufenen ".AUCTION_DELAY_TIME." Stunden wird das Angebot endg&uuml;ltig gel&ouml;scht.
    </ul><br><br>";

		echo "
		<b>Eigene Angebote</b><br>
        Hier stehen alle deine Angebote, die du momentan am Laufen hast und hier kannst du sie auch aufheben, falls sich kein K&auml;ufer blicken l&auml;sst, oder du dich vertippt hast.
        Aber Achtung! Du erh&auml;lst nur noch einen gewissen Prozentsatz vom Angebot zur&uuml;ck. Je h&ouml;her du den Markt ausgebaut hast, desto mehr bekommst du von deinen Waren zur&uuml;ck erstattet.<br>
        Eine Auktion kann nur zur&uuml;ck genommen werden, wenn noch niemand geboten hat.<br>
        Die eingestellten Angebote werden ausserdem nach ".$conf['market_response_time']['v']." Tagen automatisch gelöscht, wenn sie vorher nicht gekauft wurden (Auktionen ausgeschlossen)!<br><br><br>";

		echo "
		<b>Angebotssuche</b><br><br>
				<ul>
					<i>Rohstoffe</i><br>
	        Auflistung aller Rohstoffangebote, die momentan im Handel sind.
	        Die eigenen Angebote werden nicht angezeigt!
	        Nach einem Kauf, werden die Waren \"sofort\" gesendet und das Angebot wird gel&ouml;scht.<br>
	        Mit Hilfe vom Filter, lassen sich die angezeigten Angebote sortieren bzw. filtern.
   			</ul><br>";

		echo "
				<ul>
					<i>Schiffe</i><br>
	        Auflistung aller Schiffsangebote, die momentan im Handel sind.
	        Die eigenen Angebote werden nicht angezeigt!
	        Nach einem Kauf, werden die Waren \"sofort\" gesendet und das Angebot wird gel&ouml;scht.<br>
	        Mit Hilfe vom Filter, lassen sich die angezeigten Angebote sortieren bzw. filtern.
        </ul><br>";

		echo "
				<ul>
					<i>Auktionen</i><br>
	        Auflistung aller Auktionen, die momentan im Handel sind.
	        Die eigenen Auktionen werden nicht angezeigt!<br>
	        Mit Hilfe vom Filter, lassen sich die angezeigten Auktionen sortieren bzw. filtern.
	        Um bei einer Auktion mitzubieten, musst du in den Rohstofffeldern einen Betrag angeben, welcher gr&ouml;sser als das momentane H&ouml;chstgebot oder mindestens so gross wie das Mindestgebot ist.<br>
	        Wird eine Auktion angezeigt f&uuml;r die man gar nicht mehr bieten kann, so ist diese bereits verkauft und wird nur noch aus Sicherheitsgr&uuml;nden angezeigt!
	      </ul>";
	echo "</div>";
	iBoxEnd();

	iBoxStart("Rohstoffkurse");
	echo "<div align=\"justify\">";

	echo "Wie einigemale erwähnt, rechnet der Markt der Wert eines Angebots mit speziellen Rohstoffkursen.<br>
	Diese werden durch das Prinzip von \"Angebot und Nachfrage\" erstellt und sind folglich variable Werte die immer auf dem aktuellen Stand sind!<br>
	Am Anfang haben alle Rohstoffarten den selben Kurs (1). Wird jetzt vermehrt von einer Art angeboten (grosses Angebot), so sink der Wert dieses Rohstoffes und die Folge daraus ist eine Verteuerung vom Rest. Wird ein Rohstoff viel verlangt (grosse Nachfrage) so steigt der Kurs von diesem und die anderen Rohstoffe sinken im Preis.<br>
	Eine Tabelle zu den aktuellen Kursen findest du <a href=\"?$link&amp;site=rates\">hier</a>.";

	echo "</div>";
	iBoxEnd();


?>
