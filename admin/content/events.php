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
	// 	Dateiname: events.php	
	// 	Topic: Verwaltung der Events 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 26.04.2006
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 26.04.2006
	// 	Kommentar: 	
	//

	function parse_event_msg($message,$event)
	{
		global $rsc;
		foreach ($event as $t=> $a)
		{
			foreach ($a as $s=>$v)
			{
				$message=str_replace("{".$t.":".$s.":metal}",nf($v['metal'])." ".$rsc['metal'],$message);
				$message=str_replace("{".$t.":".$s.":crystal}",nf($v['crystal'])." ".$rsc['crystal'],$message);
				$message=str_replace("{".$t.":".$s.":plastic}",nf($v['plastic'])." ".$rsc['plastic'],$message);
				$message=str_replace("{".$t.":".$s.":fuel}",nf($v['fuel'])." ".$rsc['fuel'],$message);
				$message=str_replace("{".$t.":".$s.":food}",nf($v['food'])." ".$rsc['food'],$message);
				$message=str_replace("{".$t.":".$s.":people}",nf($v['people'])."",$message);
				$message=str_replace("{".$t.":".$s.":ship}",$v['ship'],$message);
				$message=str_replace("{".$t.":".$s.":shipcnt}",nf($v['shipcnt']),$message);
				$message=str_replace("{".$t.":".$s.":def}",$v['def'],$message);
				$message=str_replace("{".$t.":".$s.":defcnt}",nf($v['defcnt']),$message);
				$message=str_replace("{".$t.":".$s.":building}",$v['building'],$message);
				$message=str_replace("{".$t.":".$s.":buildinglevel}",$v['buildinglevel'],$message);
				$message=str_replace("{".$t.":".$s.":tech}",$v['tech'],$message);
				$message=str_replace("{".$t.":".$s.":techlevel}",$v['techlevel'],$message);
  	
			}
		}
		return $message;
	}

	function shiplist_add($user_id,$planet_id,$ship_id,$cnt)
	{
		global $db_table;
		if ($cnt!=0)
		{
			if (mysql_num_rows(dbquery("SELECT shiplist_id FROM ".$db_table['shiplist']." WHERE shiplist_user_id='".$user_id."' AND shiplist_planet_id='".$planet_id."';"))>0)
				print("UPDATE ".$db_table['shiplist']." SET shiplist_ship_id='".$ship_id."',shiplist_count='".ceil($cnt)."' WHERE shiplist_user_id='".$user_id."' AND shiplist_planet_id='".$planet_id."';");
			else
				print("INSERT INTO ".$db_table['shiplist']." (shiplist_ship_id,shiplist_count,shiplist_user_id,shiplist_planet_id) VALUES ('".$ship_id."','".ceil($cnt)."','".$user_id."','".$planet_id."');");
		}		
	}

	function deflist_add($user_id,$planet_id,$def_id,$cnt)
	{
		global $db_table;
		if ($cnt!=0)
		{
			if (mysql_num_rows(dbquery("SELECT deflist_id FROM ".$db_table['deflist']." WHERE deflist_user_id='".$user_id."' AND deflist_planet_id='".$planet_id."';"))>0)
				print("UPDATE ".$db_table['deflist']." SET deflist_def_id='".$def_id."',deflist_count='".ceil($cnt)."' WHERE deflist_user_id='".$user_id."' AND deflist_planet_id='".$planet_id."';");
			else
				print("INSERT INTO ".$db_table['deflist']." (deflist_def_id,deflist_count,deflist_user_id,deflist_planet_id) VALUES ('".$def_id."','".ceil($cnt)."','".$user_id."','".$planet_id."');");
		}		
	}
	
	
	function planet_add_ress($planet_id,$r)
	{
		global $db_table;
	 	print("UPDATE ".$db_table['planets']." SET planet_res_metal=planet_res_metal+".$r['metal'].", planet_res_crystal=planet_res_crystal+".$r['crystal'].", planet_res_plastic=planet_res_plastic+".$r['plastic'].", planet_res_fuel=planet_res_fuel+".$r['fuel'].", planet_res_food=planet_res_food+".$r['food'].",planet_people=planet_people+".$r['people']." WHERE planet_id=".$planet_id.";");
		
	}
	
	
	$rsc=get_resources_array();
	
	if ($sub=="test")
	{
		echo "<h1>Ereignisse testen</h1>";
		if ($_GET['test_id']>0)
		{
			$res=dbquery("SELECT * FROM ".$db_table['events']." WHERE event_id='".$_GET['test_id']."';");
			$arr=mysql_fetch_array($res);
			echo "<h2>Test des Ereignisses ".$arr['event_title']."</h2>";
			echo "<i>Diese Ereignisse sind rein virtueller Art und werden nicht wirklich durchgef&uuml;hrt!</i><br/><br/>";
			$ures=dbquery("SELECT user_id,user_nick,user_points FROM ".$db_table['users']." ORDER BY RAND() LIMIT 1;");
			if (mysql_num_rows($ures)>0)
			{
				$uarr=mysql_fetch_array($ures);
				$upoints=$uarr['user_points'];
				$user_id=$uarr['user_id'];
				$pres=dbquery("SELECT planet_id,cell_sx,cell_sy,cell_cx,cell_cy,planet_name,planet_solsys_pos FROM ".$db_table['planets'].",".$db_table['space_cells']." WHERE planet_solsys_id=cell_id AND planet_user_id=".$uarr['user_id']." ORDER BY RAND() LIMIT 1;");
				if (mysql_num_rows($pres)>0)
				{				
					$parr=mysql_fetch_array($pres);
					$planet=$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']." (".$parr['planet_name'].")";
					$planet_id=$parr['planet_id'];

					$key1="reward";
					$key2="p";
					for ($x=0;$x<4;$x++)
					{
						$event[$key1][$key2]['rate']=$arr['event_'.$key1.'_'.$key2.'_rate'];
						$event[$key1][$key2]['metal'] = mt_rand($arr['event_'.$key1.'_'.$key2.'_metal_min'],$arr['event_'.$key1.'_'.$key2.'_metal_max'])*(($upoints*$event[$key1][$key2]['rate'])+1);
						$event[$key1][$key2]['crystal'] = mt_rand($arr['event_'.$key1.'_'.$key2.'_crystal_min'],$arr['event_'.$key1.'_'.$key2.'_crystal_max'])*(($upoints*$event[$key1][$key2]['rate'])+1);
						$event[$key1][$key2]['plastic'] = mt_rand($arr['event_'.$key1.'_'.$key2.'_plastic_min'],$arr['event_'.$key1.'_'.$key2.'_plastic_max'])*(($upoints*$event[$key1][$key2]['rate'])+1);
						$event[$key1][$key2]['fuel'] = mt_rand($arr['event_'.$key1.'_'.$key2.'_fuel_min'],$arr['event_'.$key1.'_'.$key2.'_fuel_max'])*(($upoints*$event[$key1][$key2]['rate'])+1);
						$event[$key1][$key2]['food'] = mt_rand($arr['event_'.$key1.'_'.$key2.'_food_min'],$arr['event_'.$key1.'_'.$key2.'_food_max'])*(($upoints*$event[$key1][$key2]['rate'])+1);
						$event[$key1][$key2]['people'] = mt_rand($arr['event_'.$key1.'_'.$key2.'_people_min'],$arr['event_'.$key1.'_'.$key2.'_people_max'])*(($upoints*$event[$key1][$key2]['rate'])+1);
						if ($arr['event_'.$key1.'_'.$key2.'_ship_id']>0)
						{
							$sres=dbquery("SELECT ship_name FROM ".$db_table['ships']." WHERE ship_id=".$arr['event_'.$key1.'_'.$key2.'_ship_id'].";");
							if ($sarr=mysql_fetch_array($sres))
								$event[$key1][$key2]['ship']=$sarr['ship_name'];
							else
								$event[$key1][$key2]['ship']="Unbekannter Schiffstyp";
						}
						$event[$key1][$key2]['shipid']=$arr['event_'.$key1.'_'.$key2.'_ship_id'];        
						$event[$key1][$key2]['shipcnt'] = ceil(mt_rand($arr['event_'.$key1.'_'.$key2.'_ship_min'],$arr['event_'.$key1.'_'.$key2.'_ship_max'])*(($upoints*$event[$key1][$key2]['rate'])+1));
						if ($arr['event_'.$key1.'_'.$key2.'_def_id']>0)
						{
							$sres=dbquery("SELECT def_name FROM ".$db_table['defense']." WHERE def_id=".$arr['event_'.$key1.'_'.$key2.'_def_id'].";");
							if ($sarr=mysql_fetch_array($sres))
								$event[$key1][$key2]['def']=$sarr['def_name'];
							else
								$event[$key1][$key2]['def']="Unbekannte Verteidigungsanlage";
						}
						$event[$key1][$key2]['defid']=$arr['event_'.$key1.'_'.$key2.'_def_id'];                         
						$event[$key1][$key2]['defcnt'] = ceil(mt_rand($arr['event_'.$key1.'_'.$key2.'_def_min'],$arr['event_'.$key1.'_'.$key2.'_def_max'])*(($upoints*$event[$key1][$key2]['rate'])+1));
						if ($arr['event_'.$key1.'_'.$key2.'_building_id']>0)
						{
							$sres=dbquery("SELECT building_name FROM ".$db_table['buildings']." WHERE building_id=".$arr['event_'.$key1.'_'.$key2.'_building_id'].";");
							if ($sarr=mysql_fetch_array($sres))
								$event[$key1][$key2]['building']=$sarr['building_name'];
							else
								$event[$key1][$key2]['building']="Unbekanntes Geb&auml;ude";
						}
						$event[$key1][$key2]['buildingid']=$arr['event_'.$key1.'_'.$key2.'_building_id'];                         
						$event[$key1][$key2]['buildinglevel'] = $arr['event_'.$key1.'_'.$key2.'_building_level'];
						if ($arr['event_'.$key1.'_'.$key2.'_tech_id']>0)
						{
							$sres=dbquery("SELECT tech_name FROM ".$db_table['technologies']." WHERE tech_id=".$arr['event_'.$key1.'_'.$key2.'_tech_id'].";");
							if ($sarr=mysql_fetch_array($sres))
								$event[$key1][$key2]['tech']=$sarr['tech_name'];
							else
								$event[$key1][$key2]['tech']="Unbekannte Technologie";
						}
						$event[$key1][$key2]['techid']=$arr['event_'.$key1.'_'.$key2.'_tech_id'];                         
						$event[$key1][$key2]['techlevel'] = $arr['event_'.$key1.'_'.$key2.'_tech_level'];
						
						if ($key1=="reward" && $key2=="p") { $key1="costs";}
						elseif ($key1=="costs" && $key2=="p") { $key1="reward";$key2="n";}
						elseif ($key1=="reward" && $key2=="n") { $key1="costs";}

					}
					
					$message=parse_event_msg($arr['event_text'],$event);
					$answer_pos=parse_event_msg($arr['event_answer_pos'],$event);
					$answer_neg=parse_event_msg($arr['event_answer_neg'],$event);


					$message=str_replace("{planet}",$planet,$message);
					$answer_pos=str_replace("{planet}",$planet,$answer_pos);
					$answer_neg=str_replace("{planet}",$planet,$answer_neg);			
					
					


					echo "<table>";
					echo "<tr><th class=\"tbltitle\">Nick:</th><td class=\"tbldata\">".$uarr['user_nick']."</td></tr>";
					echo "<tr><th class=\"tbltitle\">Punkte:</th><td class=\"tbldata\">".nf($uarr['user_points'])."</td></tr>";
					echo "<tr><th class=\"tbltitle\">Planet:</th><td class=\"tbldata\">".$planet."</td></tr>";
					echo "<tr><th class=\"tbltitle\">Nachricht-Subjekt:</th><td class=\"tbldata\">".$arr['event_title']." auf $planet</td></tr>";
					echo "<tr><th class=\"tbltitle\">Nachricht:</th><td class=\"tbldata\">".text2html($message)."</td></tr>";
					if ($answer_pos!="")
						echo "<tr><th class=\"tbltitle\">Antwort Positiv:</th><td class=\"tbldata\">".text2html($answer_pos)."</td></tr>";
					if ($answer_neg!="")
					echo "<tr><th class=\"tbltitle\">Antwirt Negativ:</th><td class=\"tbldata\">".text2html($answer_neg)."</td></tr>";
				  echo "</table><br/>";

					$v=$event['reward']['p'];

					$v['metal'] = $event['reward']['p']['metal']-$event['costs']['p']['metal'];
					$v['crystal'] = $event['reward']['p']['crystal']-$event['costs']['p']['crystal'];
					$v['plastic'] = $event['reward']['p']['plastic']-$event['costs']['p']['plastic'];
					$v['fuel'] = $event['reward']['p']['fuel']-$event['costs']['p']['fuel'];
					$v['food'] = $event['reward']['p']['food']-$event['costs']['p']['food'];
					$v['people'] = $event['reward']['p']['people']-$event['costs']['p']['people'];
				
				
					if ($arr['event_ask']==1)
					{
						echo "<h2>Datenbankaktionen positive Antwort</h2>";



						echo "<h2>Datenbankaktionen negative Antwort</h2>";
						
						
						
						
						
					}
					else
					{
						echo "<h2>Datenbankaktionen</h2>";

						planet_add_ress($planet_id,$v);

						shiplist_add($user_id,$planet_id,$event['reward']['p']['shipid'],$event['reward']['p']['shipcnt']);
						shiplist_add($user_id,$planet_id,$event['costs']['p']['shipid'],-$event['costs']['p']['shipcnt']);
						deflist_add($user_id,$planet_id,$event['reward']['p']['defid'],$event['reward']['p']['defcnt']);
						deflist_add($user_id,$planet_id,$event['costs']['p']['defid'],-$event['costs']['p']['defcnt']);

								
								

								
					}
				
				
					echo "<br/><input type=\"button\" value=\"Neu testen\" onclick=\"document.location='?page=$page&sub=$sub&test_id=".$_GET['test_id']."'\" /> &nbsp; <input type=\"button\" value=\"&Uuml;bersicht\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
				}
				else
					cms_err_msg("Keine Planeten vorhanden!");				
			}			
			else
			{
				cms_err_msg("Keine User vorhanden!");
			}			
		}
		else
		{		
			echo "Ereignis w&auml;hlen:<br/><br/>";
			$res=dbquery("SELECT event_id,event_title FROM ".$db_table['events'].";");
			if (mysql_num_rows($res)>0)
			{
				echo "<table><tr><th class=\"tbltitle\">Event</th><td>&nbsp;</td></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".$arr['event_title']."</td><td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;test_id=".$arr['event_id']."\">Testen</a></td></tr>";
				}			
				echo "</table>";
			}
			else
				cms_err_msg("Keine Ereignisse vorhanden");	
		}		
	}	
	elseif ($sub=="exec")
	{
		advanced_form("events_exec");
	}
	else
	{
		advanced_form("events");
	}

?>