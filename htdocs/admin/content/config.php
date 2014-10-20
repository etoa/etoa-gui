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
	// 	Dateiname: config.php
	// 	Topic: Generelle Konfigurationseinstellungen
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	$tpl->assign("title", "Konfiguration");

	$conf_type['text']="Textfeld";
	$conf_type['textarea']="Textblock";
	$conf_type['timedate']="Zeit/Datum-Feld";
	$conf_type['onoff']="Ein/Aus-Schalter";
	
	//
	// Restore
	//
	if ($sub=="restoredefaults")
	{
		echo "<h2>Konfiguration auf Standardwerte zurücksetzen</h2>";
		if (isset($_POST['restoresubmit']))
		{
			if ($cnt = $cfg->restoreDefaults())
			{
				ok_msg("$cnt Einstellungen wurden wiederhergestellt!");
				BackendMessage::reloadConfig();
			}
		}

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "<p>Soll die Konfigurationstabelle wirklich auf ihre Standardwerte zurückgesetzt werden?</p>";
		echo "<p><input type=\"submit\" name=\"restoresubmit\" value=\"Ja, Einstellungen zurücksetzen\" /></p>";
		echo "</form>";
	}

	//
	// Check
	//
	elseif ($sub=="check")
	{
		echo "<h2>Integritätsprüfung</h2>";
		$cnt=0;
		if ($xml = simplexml_load_file(RELATIVE_ROOT."config/defaults.xml"))
		{
			foreach ($xml->items->item as $i)
			{
				if (!isset($cfg->{$i['name']}))
				{
					echo $i['name']." existiert in der Standardkonfiguration, aber nicht in der Datenbank! ";
					$cfg->add((string)$i['name'],(string)$i->v,(string)$i->p1,(string)$i->p2);
					echo "<b>Behoben</b><br/>";
				}
				$cnt++;
			}
		}
		echo "<p>$cnt Einträge in der Standardkonfiguration.</p>";

		$cnt=0;
		foreach ($cfg->getArray() as $cn => $ci)
		{
			$cnt++;
			$found = false;
			foreach ($xml->items->item as $i)
			{
				if ($i['name']==$cn)
				{
					$found = true;
					break;
				}
			}
			if (!$found)
			{
				echo $cn." existiert in der Datenbank, aber nicht in der Standardkonfiguration! ";
				$cfg->del($cn);
				echo "<b>Gelöscht</b><br/>";
			}
		}
		echo "<p>$cnt Datensätze in der Datenbank.</p>";
		echo "<p>Prüfung abgeschlossen!</p>";
		
	}
	
	//
	// Config-Editor
	//
	elseif ($sub=="editor")
	{
    $tpl->setView("config/editor");
    $tpl->assign("subtitle", 'Erweiterte Konfiguration');
    
    if (isset($_POST['submit']))
    {
      foreach ($cfg->categories() as $ck => $cv) {      
        foreach ($cfg->itemInCategory($ck) as $i)
        {
          $v = isset($i->v) ? create_sql_value((string)$i->v['type'],(string)$i['name'],"v",$_POST) : "";
          $p1 = isset($i->p1) ? create_sql_value((string)$i->p1['type'],(string)$i['name'],"p1",$_POST) : "";
          $p2 = isset($i->p2) ? create_sql_value((string)$i->p2['type'],(string)$i['name'],"p2",$_POST) : "";
          $cfg->add((string)$i['name'],$v,$p1,$p2);
        }
      }
	  BackendMessage::reloadConfig();
      $tpl->assign('msg', "&Auml;nderungen wurden &uuml;bernommen!");
      $tpl->assign('msg_type', "ok");
 			$tpl->assign('activeTab', $_POST['activeTab']);
    }

    $configData = array();
    foreach ($cfg->categories() as $ck => $cv) {
      $configData[$ck]['name'] = $cv;
      $items = array();
      foreach ($cfg->itemInCategory($ck) as $i)
      {
        if (isset($i->v))
        {
          $items[] = array(
            'label' => $i->v['comment'],
            'name' => $i['name'],
            'type' => 'v',
            'field' => display_field((string)$i->v['type'], (string)$i['name'], "v"),
          );
        }
        if (isset($i->p1))
        {
          $items[] = array(
            'label' => $i->p1['comment'],
            'name' => $i['name'],
            'type' => 'p1',
            'field' => display_field((string)$i->p1['type'], (string)$i['name'], "p1"),
          );				
        }
        if (isset($i->p2))
        {
          $items[] = array(
            'label' => $i->p2['comment'],
            'name' => $i['name'],
            'type' => 'p2',
            'field' => display_field((string)$i->p2['type'], (string)$i['name'], "p2"),
          );				
        }
      }        
      $configData[$ck]['items'] = $items;
    }

    $tpl->assign("configData", $configData);

	}
	
	//
	// Base
	//
	else
	{
		$tpl->setView("config/base");
		$tpl->assign("subtitle", 'Grundkonfiguration');
		
		if (isset($_POST['submit']))
		{
			foreach ($cfg->getBaseItems() as $i)
			{
				$v = isset($i->v) ? create_sql_value((string)$i->v['type'],(string)$i['name'],"v",$_POST) : "";
				$p1 = isset($i->p1) ? create_sql_value((string)$i->p1['type'],(string)$i['name'],"p1",$_POST) : "";
				$p2 = isset($i->p2) ? create_sql_value((string)$i->p2['type'],(string)$i['name'],"p2",$_POST) : "";
				$cfg->add((string)$i['name'],$v,$p1,$p2);
			}
			BackendMessage::reloadConfig();
			$tpl->assign('msg', "&Auml;nderungen wurden &uuml;bernommen!");
			$tpl->assign('msg_type', "ok");
		}			
		$items = array();
		foreach ( $cfg->getBaseItems() as $i)
		{
			if (isset($i->v))
			{
				$items[] = array(
					'label' => $i->v['comment'],
					'name' => $i['name'],
					'field' => display_field((string)$i->v['type'], (string)$i['name'], "v"),
				);
			}
			if (isset($i->p1))
			{
				$items[] = array(
					'label' => $i->p1['comment'],
					'name' => $i['name'],
					'field' => display_field((string)$i->p1['type'], (string)$i['name'], "p1"),
				);				
			}
			if (isset($i->p2))
			{
				$items[] = array(
					'label' => $i->p2['comment'],
					'name' => $i['name'],
					'field' => display_field((string)$i->p2['type'], (string)$i['name'], "p2"),
				);				
			}
		}
		$tpl->assign("items", $items);
	}
?>

