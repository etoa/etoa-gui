<?php

	echo "<h1>Minimap</h1>";
	
	//
	// Submit-Befehle ausführen
	//

	//
	// Neue Minimap erstellen
	//
	if(isset($_POST['create_minimap']))
	{
		// Definiert den X und Y Wert als Zahl (INT)
		$fields_x = ceil(intval($_POST['fields_x']));
		$fields_y = ceil(intval($_POST['fields_y']));
		
		// Prüft, ob X und Y Wert angegeben wurde
		if($fields_x > 0 && $fields_y > 0)
		{
			// Erstellt für jedes Feld einen Datensatz
			for ($x=1;$x<=$fields_x;$x++)
			{
				for ($y=1;$y<=$fields_y;$y++)
				{
						// Speichert Felder
						dbquery("
						INSERT INTO 
						minimap 
						(
							field_x,
							field_y,
							field_typ_id
						) 
						VALUES 
						( 
							".$x.",
							".$y.",
							1
						);");
				}
			}
		
			echo "<b>Es wurde erfolgreich eine ".$fields_x." x ".$fields_y." (".($fields_x*$fields_y).") Felder grosse Map erstellt!</b><br><br><br>";
		}
		else
		{
			echo "<b>Fehler:</b> Die Eingegebenen Werte sind ungültig.!<br><br><br>";
		}
	}
	
	
	
	//
	// Neue Minimap erstellen
	//
	if(isset($_POST['delete_minimap']))
	{
		dbquery("DELETE FROM minimap;");
		echo "<b>Minimap erfolgreich gelöscht!</b><br><br><br>";		
	}
	

	
	//
	// Mapänderungen speichern
	//
	if(isset($_POST['save_settings']))
	{
		// Lädt die ID aller Felder und Speichert nur diese, welche einen Wert ungleich 0 haben und somit geändert wurden
		$res = dbquery("
		SELECT 
			field_id
		FROM 
			minimap;");		
		$cnt = 0;				
		while($arr=mysql_fetch_array($res))
		{
			// Überprüft ob das Feld geändert wurde
			if($_POST["map_field_".$arr['field_id']."_typ_id"] > 0)
			{
				// Stellt Update-SQL des Feldes her
				dbquery("
				UPDATE 
					minimap 
				SET
					field_typ_id=".$_POST["map_field_".$arr['field_id']."_typ_id"]."
				WHERE 
					field_id=".$arr['field_id'].";");
				$cnt++;
			}
		}		
		
		echo "<b>".$cnt." Felder erfolgreich geändert!</b><br><br>";
	}
	
	
	
	//
	// Content
	//
	
	// Liest Kartengrösse aus DB raus und überprüft gleichzeitig, ob schon eine Map erstellt ist
	$res = dbquery("
	SELECT 
		MAX(m2.field_x) AS fields_x,
		MAX(m1.field_y) AS fields_y
	FROM 
		minimap AS m1,
		minimap AS m2
	GROUP BY
		m1.field_x,
		m2.field_y
	;");
	if (mysql_num_rows($res)>0)
	{
		$arr = mysql_fetch_array($res);
		
		//
		// Map löschen
		//
		echo "<h2>Map löschen</h2>";
		echo "<form action=\"?page=".$page."\" method=\"post\">
						<input type=\"submit\" class=\"button\" name=\"delete_minimap\" value=\"Löschen\">";
		echo "</form><br><br>";
		
		
		
		//
		// Map bearbeiten
		//
		echo "<h2>Map (".$arr['fields_x']."x".$arr['fields_y'].") bearbeiten</h2>";
		
		// Lädt Variabeln
		DEFINE("IMAGE_PATH","../images/minimap/");
		$fields_x = $arr['fields_x'];
		$fields_y = $arr['fields_y'];
		
		// Map Hintergrund
		$map_background = "".IMAGE_PATH."/galaxy/empty_space1.gif";
		$figure = "".IMAGE_PATH."/Greenman.gif";
		$yellow_star = "".IMAGE_PATH."/galaxy/sol1.gif";
		$wormhole = "".IMAGE_PATH."/galaxy/wormhole.gif";
		$nebula = "".IMAGE_PATH."/galaxy/nebula2.gif";
		
				
				
		// Setzt das Zentrum der Karte auf den eingegebenen X-Wert
		if(isset($_POST['position_x']))
		{
			$position_x = $_POST['position_x'];
		}
		// Wenn nichts angegeben wurde: X=1
		else
		{
			$position_x = 1;
		}
		
		// Setzt das Zentrum der Karte auf den eingegebenen Y-Wert
		if(isset($_POST['position_y']))
		{
			$position_y = $_POST['position_y'];
		}
		// Wenn nichts angegeben wurde: Y=1
		else
		{
			$position_y = 1;
		}
		
		// Setzt die Ansichtsgrösse auf den eingegebenen X-Wert
		if(isset($_POST['show_x']))
		{
			$show_x = $_POST['show_x'];
		}
		// Wenn nichts angegeben wurde: X=10
		else
		{
			$show_x = 10;
		}
		
		// Setzt die Ansichtsgrösse auf den eingegebenen Y-Wert
		if(isset($_POST['show_y']))
		{
			$show_y = $_POST['show_y'];
		}
		// Wenn nichts angegeben wurde: Y=10
		else
		{
			$show_y = 10;
		}		
				
		
		
		
		//
		// Anzeigeoptionen
		//
		
		// Ermöglicht dem User das Einstellen der Angezeigten Felder
		iBoxStart("Anzeigeoptionen");
		echo "<form action=\"?page=".$page."\" method=\"post\">
						<b>Angezeigte Felder</b> [<span style=\"font-style:italic\" ".mTT("Angezeigte Felder","- Es dürfen nur ganze, gerade Zahlen verwendet werden, ansonsten werden die Bilder nicht geladen<br><br>- Bei zu hohen Zahlen werden die Ladezeiten extrem!<br><br>- Sollte der Angezeige Ausschnitt mehr als die doppelte Grösse der Map betragen, werden die Bilder an den Randstellen auch nicht mehr dargestellt!").">INFO</span>]<br>
						<input type=\"text\" id=\"show_x\" name=\"show_x\" value=\"".$show_x."\" maxlength=\"3\" size=\"2\" title=\"X-Richtung\"> x <input type=\"text\" id=\"show_y\" name=\"show_y\" value=\"".$show_y."\" maxlength=\"3\" size=\"2\" title=\"Y-Richtung\"> (Max. ".$arr['fields_x']."x".$arr['fields_y'].")<br><br>
						<input type=\"submit\" class=\"button\" name=\"change_settings\" value=\"Speichern\"><br><br>";
		echo "</form>";
		iBoxEnd();
		
		
		
		//
		// Map darstellen
		//
		
		?>
		<script type="text/javascript">
		
		//
		// Javascript-Funktionen
		//
		
		// Lädt zuerst die Mapfelder mit den dazugehörigen Eigenschaften
		<?php
			$res = dbquery("
			SELECT 
				*
			FROM 
					minimap AS map
				INNER JOIN
					minimap_field_types AS types
				ON map.field_typ_id=types.field_typ_id;");
					
			echo "var map = new Array();";
			
			while($arr=mysql_fetch_array($res))
			{
				echo "map['".$arr['field_x']."_".$arr['field_y']."_id'] = ".$arr['field_id'].";\n";
				echo "map['".$arr['field_x']."_".$arr['field_y']."_typ_id'] = ".$arr['field_typ_id'].";\n";
				echo "map['".$arr['field_x']."_".$arr['field_y']."_event_id'] = ".$arr['field_event_id'].";\n";
				echo "map['".$arr['field_x']."_".$arr['field_y']."_name'] = '".$arr['field_name']."';\n";
				echo "map['".$arr['field_x']."_".$arr['field_y']."_image'] = '".$arr['field_image']."';\n";
				echo "map['".$arr['field_x']."_".$arr['field_y']."_blocked'] = ".$arr['field_blocked'].";\n";
				echo "map['".$arr['field_x']."_".$arr['field_y']."_changed'] = 0;\n";
			}
		?>
		
		
		// Script zum Auslesen von JS-Arrays 
		//..................................//
		function padding(laenge) 
		{
		  result = '';
		  for (i = 0; i < laenge; i++)
		    result = result + '___';
		  return result;
		}

		function print_r(das_array, ebene) 
		{
		  var result = '';  
		  for (var wert in das_array)
		    if (typeof das_array[wert] == "object")
		      result = result + ' ' + padding(ebene) + wert + "\n" + print_r(das_array[wert], ebene + 1);
		    else
		      result = result + ' ' + padding(ebene) + wert + ' = ' + das_array[wert] + "\n";
		
		  return result;
		}
		//.................................//
		
		
		
		//
		// Map Generator: Ermöglicht das Scrollen der Map und das Ändern der Felder
		//
		
		function move(route)
		{					
			//alert(print_r(map, 0)); // Liest ein JS-Array aus. Funktioniert wie gleichnamiger PHP-Befehl
			
			
			//
			// Definitionen
			//
			
			// Wandelt PHP-Variabeln in JS-Variabeln um
			image_path = "<?PHP echo IMAGE_PATH; ?>";
			figure = "<?PHP echo $figure; ?>";
			fields_x = <?PHP echo $fields_x; ?>;		// Gesamte Mapgrösse X-Wert
			fields_y = <?PHP echo $fields_y; ?>;		// Gesamte Mapgrösse Y-Wert
			var show_x = parseInt(document.getElementById('show_x').value);				// Angezeigte Zellen X-Wert
			var show_y = parseInt(document.getElementById('show_y').value);				// Angezeigte Zellen Y-Wert

			// Ruft aktuelle Position ab
			var position_x = parseInt(document.getElementById('position_x').value);
			var position_y = parseInt(document.getElementById('position_y').value);
			var new_position_x = position_x;				// Standart: Keine Positionsänderung
			var new_position_y = position_y;				// Standart: Keine Positionsänderung
					
			
					
			//
			// Script
			//
			
			// Setzt neue Koordinaten...
			
			// ... in Richtung: Auf
			if (route == "up")
			{
				new_position_x = position_x;			// X-Koordinate bleibt gleich
				new_position_y = position_y - 1;	// Y-Koordinate nimmt um 1 Feld ab
				
				// Ermöglicht das Überspringen des Y-Endes
				// Wenn User Y-Anfang überschreitet wird User auf Y-Ende gesetzt
				if(new_position_y <= 0)
				{
					var new_position_y = fields_y + new_position_y;
				}
			}
			
			// ... in Richtung: Ab
			else if (route == "down")
			{
				new_position_x = position_x;			// X-Koordinate bleibt gleich
				new_position_y = position_y +1;		// Y-Koordinate nimmt um 1 Feld zu
				
				// Ermöglicht das Überspringen des Y-Endes
				// Wenn User Y-Ende überschreitet wird User auf Y-Anfang gesetzt
				if(new_position_y > fields_y)
				{
					var new_position_y = new_position_y - fields_y;
				}
			}
			
			// ... in Richtung: Links
			else if (route == "left")
			{
				new_position_x = position_x - 1;	// X-Koordinate nimmt um 1 Feld ab
				new_position_y = position_y;			// Y-Koordinate bleibt gleich
				
				// Ermöglicht das Überspringen des X-Endes
				// Wenn User X-Anfang überschreitet wird User auf X-Ende gesetzt
				if(new_position_x <= 0)
				{
					var new_position_x = fields_x + new_position_x;
				}
			}
			
			// ... in Richtung: Rechts
			else if (route == "right")
			{
				new_position_x = position_x + 1;	// X-Koordinate nimmt um 1 Feld zu
				new_position_y = position_y;			// Y-Koordinate bleibt gleich
				
				// Ermöglicht das Überspringen des X-Endes
				// Wenn User X-Ende überschreitet wird User auf X-Anfang gesetzt
				if(new_position_x > fields_x)
				{
					var new_position_x = new_position_x - fields_x;
				}
			}
			
			
			// Definiert Angezeigte Felder 
			
			// Kleinste angezeigte X-Koordinate
			show_x_min = new_position_x - show_x/2;
			
			// Grösste angezeigte X-Koordinate
			show_x_max = new_position_x + show_x/2;
			
			// Kleinste angezeigte Y-Koordinate
			show_y_min = new_position_y - show_y/2;
			
			// Grösste angezeigte X-Koordinate
			show_y_max = new_position_y + show_y/2;
					
			
			// Ändert neue Koordinaten
			document.getElementById('position_x').value = new_position_x;
			document.getElementById('position_y').value = new_position_y;
			
			
			
			//
			// Aktualisieren der Map
			//
						
			cell_cnt = 1;
			for (x=show_x_min;x<=show_x_max;x++)
			{
				// Ermöglicht das Überspringen des X-Endes
				if(x <= 0)
				{
					var coords_x = fields_x + x;
				}
				else if(x > fields_x)
				{
					var coords_x = x - fields_x;
				}
				else
				{
					var coords_x = x;
				}
				document.getElementById('cell_'+cell_cnt+'').innerHTML=coords_x;
				document.getElementById('cell_'+cell_cnt+'').title="X-Koordinate: "+coords_x+"";
				cell_cnt++;
			}
			
			for (y=show_y_min;y<=show_y_max;y++)
			{
				var first = true;
				
				// Ermöglicht das Überspringen des Y-Endes
				if(y <= 0)
				{
					var coords_y = fields_y + y;
				}
				else if(y > fields_y)
				{
					var coords_y = y - fields_y;
				}
				else
				{
					var coords_y = y;
				}
				
				document.getElementById('cell_'+cell_cnt+'').innerHTML=coords_y;
				document.getElementById('cell_'+cell_cnt+'').title="Y-Koordinate: "+coords_y+"";
				for (x=show_x_min;x<=show_x_max;x++)
				{			
					// Ermöglicht das Überspringen des X-Endes
					if(x <= 0)
					{
						var coords_x = fields_x + x;
					}
					else if(x > fields_x)
					{
						var coords_x = x - fields_x;
					}
					else
					{
						var coords_x = x;
					}
					
					
					if (first)
					{
						cell_cnt++;
					}
					var first = false;
					
					// Verlinkt jedes dargestellte Feld mit der "change_field()" Funktion
					var content = "<a href='javascript:;' onclick='change_field("+cell_cnt+","+coords_x+","+coords_y+","+map[""+coords_x+"_"+coords_y+"_id"]+");'><img src='"+image_path+""+map[""+coords_x+"_"+coords_y+"_image"]+"' title='"+map[""+coords_x+"_"+coords_y+"_name"]+" ("+coords_x+"/"+coords_y+")' border='0' width='42px' height='42px' /></a>";
					
					document.getElementById('cell_'+cell_cnt+'').innerHTML=content;
					cell_cnt++;
				}
			}
		}
		
		
		
		//
		// Feld ändern
		//
		
		function change_field (cell_cnt, x, y, field_id)
		{
			image_path = "<?PHP echo IMAGE_PATH; ?>";
			var image = document.getElementById('new_background_image').value;
			var typ_id = document.getElementById('new_background_id').value;
			var name = document.getElementById('new_background_name').value;
			var content = "<a href='javascript:;' onclick='change_field("+cell_cnt+","+x+","+y+");'><img src='"+image_path+""+image+"' title='"+name+" ("+x+"/"+y+")' border='0' width='42px' height='42px' /></a>";
			
			// Ändert Daten im Array, sodass die geänderten Felder auch beim Scrollen beibehalten werden
			map[""+x+"_"+y+"_typ_id"] = typ_id;
			map[""+x+"_"+y+"_name"] = name;
			map[""+x+"_"+y+"_image"] = image;
			map[""+x+"_"+y+"_changed"] = 1;
			
			// Ändert Bild direkt auf der Map
			document.getElementById('cell_'+cell_cnt+'').innerHTML=content;			
			
			// Ändert die Hiddenfelder, welche zum Speichern ausgelesen werden
			document.getElementById('map_field_'+field_id+'_typ_id').value=typ_id;
			
			// Gibt Button zum Speichern der Änderungen frei
			document.getElementById('save_settings').disabled=false;
			document.getElementById('save_settings').style.color='#0f0';
		}
		
		
		
		//
		// Wechselt zwischen den verschidenen Tabs
		//
		
		function showTab(id)
		{
			document.getElementById('paint').style.display='none';
			document.getElementById('event').style.display='none';
			
			document.getElementById(id).style.display='';
		
		}	
		
		
		
		//
		// Zeigt das momentan gewählte Feld an
		//
		
		function change_selected_image(image, id, name)
		{
			image_path = "<?PHP echo IMAGE_PATH; ?>"; 
			var content = "<img src='"+image_path+""+image+"' title='"+name+"' border='0' width='42px' height='42px' />";
			document.getElementById('actuel_image').innerHTML=content;
			
			document.getElementById('new_background_id').value=id;
			document.getElementById('new_background_name').value=name;
			document.getElementById('new_background_image').value=image;
		}	
		</script>
		
	<?PHP
		/*
		if(isset($_POST['position_x']))
		{
			$position_x = $_POST['position_x'];
		}
		else
		{
			$position_x = 1;
		}
		
		if(isset($_POST['position_y']))
		{
			$position_y = $_POST['position_y'];
		}
		else
		{
			$position_y = 1;
		}
		
		if(isset($_POST['show_x']))
		{
			$show_x = $_POST['show_x'];
		}
		else
		{
			$show_x = 10;
		}
		
		if(isset($_POST['show_y']))
		{
			$show_y = $_POST['show_y'];
		}
		else
		{
			$show_y = 10;
		}
		*/
		
		// Cell ID Counter
		$cell_cnt = 1;
		
		// Kleinste angezeigte X-Koordinate
		$show_x_min = $position_x - $show_x/2;
		
		// Grösste angezeigte X-Koordinate
		$show_x_max = $position_x + $show_x/2;		
		
		// Kleinste angezeigte Y-Koordinate
		$show_y_min = $position_y - $show_y/2;
		
		// Grösste angezeigte X-Koordinate
		$show_y_max = $position_y + $show_y/2;
				
		
		
		
		//
		// Stellt Minimap dar
		//
		
		iBoxStart("Minimap");
		
		echo "<form action=\"?page=".$page."\" method=\"post\">
		<div style=\"text-align:center;\"><b>Zentrum</b><br>";
		
		// Position der Karte kann angegeben werden
		echo "<input type=\"text\" id=\"position_x\" name=\"position_x\" value=\"".$position_x."\" maxlength=\"3\" size=\"2\" title=\"X-Richtung\"> / <input type=\"text\" id=\"position_y\" name=\"position_y\" value=\"".$position_y."\" maxlength=\"3\" size=\"2\" title=\"Y-Richtung\"> (Max. ".$fields_x."x".$fields_y.")<br>";
		
		// Ermöglicht das Scrollen auf der Karte...
		// ... nach Links
		echo "<a href=\"javascript:;\" onclick=\"move('left');\"><img src=\"".IMAGE_PATH."sector_middleleft.gif\" title=\"Left\" border=\"0\" width=\"42px\" height=\"42px\" /></a> ";
		// ... nach Rechts
		echo "<a href=\"javascript:;\" onclick=\"move('right');\"><img src=\"".IMAGE_PATH."sector_middleright.gif\" title=\"Right\" border=\"0\" width=\"42px\" height=\"42px\" /></a> ";
		// ... nach Oben
		echo "<a href=\"javascript:;\" onclick=\"move('up');\"><img src=\"".IMAGE_PATH."sector_topcenter.gif\" title=\"Up\" border=\"0\" width=\"42px\" height=\"42px\" /></a> ";
		// ... nach Unten
		echo "<a href=\"javascript:;\" onclick=\"move('down');\"><img src=\"".IMAGE_PATH."sector_bottomcenter.gif\" title=\"Down\" border=\"0\" width=\"42px\" height=\"42px\" /></a><br><br>
		</div>";
		
		
		
		//
		// Stellt Tabelle mit PHP dar und vergibt jedem Feld eine ID, auf welche die JS-Funktion Bezug nimmt
		//
		
		echo "<table class=\"tblc\">";
		
		// Stellt Leeres Feld oben Links dar und speichert darin die gesamte Map in Formularfelder
		echo "<tr>
						<td>";
						
						// Lädt ID von allen Mapfeldern
						$res = dbquery("
						SELECT 
							field_id
						FROM 
							minimap;");						
						while($arr=mysql_fetch_array($res))
						{
							// Speichert den neuen Feldtyp wenn dieser geändert wird.
							echo "<input type=\"hidden\" id=\"map_field_".$arr['field_id']."_typ_id\" name=\"map_field_".$arr['field_id']."_typ_id\" value=\"0\">";
						}						
										
			echo "</td>";
		
		for ($x=$show_x_min;$x<=$show_x_max;$x++)
		{
			echo "<td id=\"cell_".$cell_cnt."\" name=\"cell_".$cell_cnt."\" title=\"X-Koordinate: ".$x."\" style=\"text-align:center;vertical-align:middle;\"></td>";
			$cell_cnt++;
		}
		echo "</tr>";
		
		for ($y=$show_y_min;$y<=$show_y_max;$y++)
		{
			$first = true;
			echo "<tr>";
			echo "<td id=\"cell_".$cell_cnt."\" name=\"cell_".$cell_cnt."\" title=\"Y-Koordinate: ".$y."\"></td>";
			for ($x=$show_x_min;$x<=$show_x_max;$x++)
			{				
				if($first)
				{
					$cell_cnt++;
				}
				
				$first = false;
				echo "<td id=\"cell_".$cell_cnt."\" name=\"cell_".$cell_cnt."\"></td>";	
				$cell_cnt++;
			}
			
			echo "</tr>";
		}
		
		echo "</table>";
		echo "<br><br><div style=\"text-align:center;\"><input type=\"button\" value=\"Load\" onclick=\"move('wasauchimmer');\" /><br><br><input type=\"submit\" value=\"Änderungen speichern\" id=\"save_settings\" name=\"save_settings\" style=\"color:#f00;\" disabled=\"true\"/></div></form>";
		iBoxEnd();
			
		
		
		//
		// Map bearbeiten: Zeigt die Bearbeitungstool an
		//
		
		iBoxStart("Map bearbeiten");
		echo "<div style=\"text-align:center;\">";
		
		// Mit diesen Buttons kann man zwischen den verschiedenen Bereichen wechseln
		echo "<input type=\"button\" value=\"Zeichnen\" onclick=\"showTab('paint');\" /> <input type=\"button\" value=\"Event\" onclick=\"showTab('event');\" /><br><br>";
		
		
		
		//
		// Bereiche: Mit den folgenden Tools kann die Map editiert werden
		//
		
		//
		// Zeichen
		//
		
		echo "<div id=\"paint\" name=\"paint\" style=\"display:none\">";
		echo "Klicke auf den gewünschten Hintergrund und füge diesen in der Map ein, ebenfalls mit einem Klick auf das gewünschte Feld.<br><br>";
		
		// Zeigt Aktuell gewähltes Bild an und übergibt die ID dessen an die main Map
		echo "<div id=\"actuel_image\" name=\"actuel_image\">Kein Bild gewählt</div>";
		echo "<input type=\"hidden\" id=\"new_background_image\" name=\"new_background_image\" value=\"\">
					<input type=\"hidden\" id=\"new_background_name\" name=\"new_background_name\" value=\"\">
					<input type=\"hidden\" id=\"new_background_id\" name=\"new_background_id\" value=\"\">";
					
		// Lädt alle Felder
		$res = dbquery("
		SELECT 
			*
		FROM 
			minimap_field_types;");
		if(mysql_num_rows($res)>0)
		{
			$rows = 10; // Definiert, wie viele Felder in eine Zeile kommen
			$cnt = 1;		// Zähler
			
			echo "<table class=\"tbl\">";
			echo "<tr>";
			
			// Schleife, die alle Felder ausgibt
			while($arr=mysql_fetch_array($res))
			{
				// Wenn "$rows" Felder dargestellt sind, wird eine neue Zeile angefangen
				if($cnt == $rows)
				{
					echo "</tr><tr>";
					$cnt = 1;
				}
				
				// Zeigt die geladenen Felder dar
				echo "<td>
								<a href=\"javascript:;\" onclick=\"change_selected_image('".$arr['field_image']."',".$arr['field_typ_id'].",'".$arr['field_name']."');\"><img src=\"".IMAGE_PATH."".$arr['field_image']."\" title=\"".$arr['field_name']."\" id=\"".$arr['field_typ_id']."\" border=\"0\" width=\"42px\" height=\"42px\" /></a>
							</td>";
				
				$cnt++;
			}
			echo "</tr>";
			echo "</table>";
		}
		else
		{
			echo "Es sind noch keine Felder definiert!<br>";
		}
		echo "</div>";
		
		
		
		//
		// Events 
		//
		
		echo "<div id=\"event\" name=\"event\" style=\"display:none\">";
		echo "ändere das event";
		echo "</div>";
		echo "</div>";
		iBoxEnd();
	}
	// Es besteht noch keine Map
	else
	{
		echo "Es ist keine Map vorhanden!<br><br><br>";
		
		//
		// Neue Map erstellen
		//
		echo "<h2>Neue Map erstellen</h2>Es wird eine X*Y Grosse Map mit dem Standartfeld (ID=1) erstellt. Da es X*Y INSERT-Befehle geben wird, kann die Generierung etwas dauern!<br><br>";
		
		echo "<form action=\"?page=".$page."\" method=\"post\">
						Felder in X-Richtung: <input type=\"text\" name=\"fields_x\" value=\"100\" maxlength=\"5\" size=\"3\"><br>
						Felder in Y-Richtung: <input type=\"text\" name=\"fields_y\" value=\"100\" maxlength=\"5\" size=\"3\"><br><br>
						<input type=\"submit\" class=\"button\" name=\"create_minimap\" value=\"Erstellen\">";
		echo "</form>";
	}

?>