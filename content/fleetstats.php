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
	// 	File: fleetstats.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about all ships
	*
	* @package etoa_gameserver
	* @author Lamborghini <lamborghini@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo '<h1>Schiffsübersicht</h1>';
	
	define(HELP_URL,"?page=help&site=shipyard");

	//Button "Zurück zum Raumschiffshafen"
	echo "<input type=\"button\" onclick=\"document.location='?page=fleets'\" value=\"Flotten anzeigen\" /> &nbsp; ";
	echo "<input type=\"button\" onclick=\"document.location='?page=haven'\" value=\"Raumschiffshafen des aktuellen Planeten anzeigen\" /><br/><br/>";
	

	$planet_data = array();		//Speichert alle Planetnamen des Users

	//Speichert Planetnamen in ein Array
	foreach ($planets->own as $p)
	{
		$planet_data[$p->id]['name']=$p->name;
	}
	

	//Prüft ob Schiffe vorhanden sind
  $res = dbquery("
  SELECT
  	shiplist_id
  FROM
  	".$db_table['shiplist']."
  WHERE
  	shiplist_user_id='".$s['user']['id']."'
  LIMIT 1;");
  if(mysql_num_rows($res)>0)
  {
		infobox_start("Schiffe",1,0);
		echo "<tr>
						<td class=\"tbltitle\" colspan=\"2\">Schiff</td>
						<td class=\"tbltitle\" width=\"100\">Im Orbit</td>
						<td class=\"tbltitle\" width=\"100\">Im Bau</td>
						<td class=\"tbltitle\" width=\"100\">Im All</td>
					</tr>";
		
		//Listet alle Schiffe auf, die allgemein gebaut werden können (auch die, die der User nach dem Technikbaum noch nicht bauen könnte oder nicht seiner Rasse entsprechen)
	  $sres = dbquery("
	  SELECT
	  	ship_id,
	    ship_name,
	    special_ship
	  FROM
	  	".$db_table['ships']."
	  WHERE
	  	ship_buildable='1'
	  	AND ship_show='1'
	  ORDER BY
	  	special_ship DESC,
	  	ship_name;");
	  if(mysql_num_rows($sres)>0)
	  {
	  	while ($sarr=mysql_fetch_array($sres))
	  	{
	  		
	  		//
	  		//Gesamtanzahl Schiffe (Im ganzen Account)
	  		//
	  		
	  		//Zählt Gesamtanzahl gebauter Schiffe pro Typ zusammen
			  $res = dbquery("
			  SELECT
			  	SUM(shiplist_count) AS cnt
			  FROM
			  	".$db_table['shiplist']."
			  WHERE
			  	shiplist_user_id='".$s['user']['id']."'
			  	AND shiplist_ship_id='".$sarr['ship_id']."'
			  GROUP BY
			  	shiplist_ship_id
			  LIMIT 1;");
			  if(mysql_num_rows($res)>0)
			  {
			  	$arr=mysql_fetch_array($res);
			  	
	  			$shiplist_cnt=$arr['cnt'];
			  }
			  else
			  {
			  	$shiplist_cnt=0;
			  }
			  
	  		//Zählt Gesamtanzahl bauender Schiffe pro Typ zusammen
			  $res = dbquery("
			  SELECT
			  	SUM(queue_cnt) AS cnt
			  FROM
			  	".$db_table['ship_queue']."
			  WHERE
			  	queue_user_id='".$s['user']['id']."'
			  	AND queue_ship_id='".$sarr['ship_id']."'
			  GROUP BY
			  	queue_ship_id
			  LIMIT 1;");
			  if(mysql_num_rows($res)>0)
			  {
			  	$arr=mysql_fetch_array($res);
			  	
	  			$ship_queue_cnt=$arr['cnt'];
			  }		  
			  else
			  {
			  	$ship_queue_cnt=0;
			  }					    	
			  	 
		  	//Zählt Gesamtanzahl fliegender Schiffe pro Typ zusammen
				$res = dbquery("
				SELECT
				  SUM(fs.fs_ship_cnt) AS cnt
				FROM
		      ".$db_table['fleet_ships']." AS fs
		      INNER JOIN
		      ".$db_table['fleet']." AS f
		      ON fs.fs_fleet_id=f.fleet_id
		      AND f.fleet_user_id='".$s['user']['id']."'
		      AND fs.fs_ship_id='".$sarr['ship_id']."'
				GROUP BY
					fs.fs_ship_id
				LIMIT 1;");
			  if(mysql_num_rows($res)>0)
			  {
			  	$arr=mysql_fetch_array($res);
			  	
	  			$fleet_ships_cnt=$arr['cnt'];
			  }		  
			  else
			  {
			  	$fleet_ships_cnt=0;
			  }				  
			  
			  
			  
			  //
			  //Zählt Anzahl Schiffe auf jedem Planet
			  //
			  	
				$shiplist="";
				$ship_queue="";
				
	  		//Sucht Einträge in der shiplist (bereits gebaute Schiffe)
			  $res = dbquery("
			  SELECT
			  	shiplist_count,
			  	shiplist_planet_id
			  FROM
			  	".$db_table['shiplist']."
			  WHERE
			  	shiplist_user_id='".$s['user']['id']."'
			  	AND shiplist_ship_id='".$sarr['ship_id']."';");
			  if(mysql_num_rows($res)>0)
			  {
			  	while ($arr=mysql_fetch_array($res))
			  	{
			  		$shiplist.="".$planet_data[$arr['shiplist_planet_id']]['name'].": ".nf($arr['shiplist_count'])."<br>";
			  	}    	
			  }	
			  
		  	//Sucht Einträge in der ship_queue (bauende Schiffe)
			  $res = dbquery("
			  SELECT
			  	SUM(queue_cnt) AS cnt,
			  	queue_planet_id
			  FROM
			  	".$db_table['ship_queue']."
			  WHERE
			  	queue_user_id='".$s['user']['id']."'
			  	AND queue_ship_id='".$sarr['ship_id']."'
			  GROUP BY
			  	queue_planet_id;");
			  if(mysql_num_rows($res)>0)
			  {
			  	while ($arr=mysql_fetch_array($res))
			  	{
			  		$ship_queue.="".$planet_data[$arr['queue_planet_id']]['name'].": ".nf($arr['cnt'])."<br>";
			  	}    	
			  }		
			  			  
			  //Zeigt Informationen an wenn vorhanden
			  if($shiplist_cnt!=0 || $ship_queue_cnt!=0 || $fleet_ships_cnt!=0)
			  {
			  	$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_small.".IMAGE_EXT;
			  	echo "<tr>
			  					<td class=\"tbldata\">";
			  					
			  					if($sarr['special_ship']==1)
			  					{
			  						echo "<a href=\"?page=ship_upgrade&amp;id=".$sarr['ship_id']."\" title=\"Zum Upgrademenu\"><img src=\"".$s_img."\" style=\"width:40px;height:40px;\"/></a>";
			  					}
			  					else
			  					{
			  						echo "<a href=\"".HELP_URL."\" title=\"Info zu diesem Schiff anzeigen\"><img src=\"".$s_img."\" style=\"width:40px;height:40px;\"/></a>";
			  					}
			  		echo "</td>
			  					<td class=\"tbltitle\">
			  						".$sarr['ship_name']."
			  					</td>";
			  					
			  				//Spalte gebauter Schiffe
		  					if($shiplist!="")
		  					{
		  						echo "
			  					<td class=\"tbldata\" ".tm("Anzahl",$shiplist).">
			  						".nf($shiplist_cnt)."
			  					</td>";
			  				}
			  				else
			  				{
			  					echo "
			  					<td class=\"tbldata\">
			  						&nbsp;
			  					</td>";		  					
			  				}
			  				
			  				//Spalte bauender Schiffe
		  					if($ship_queue!="")
		  					{
		  						echo "
			  					<td class=\"tbldata\" ".tm("Anzahl",$ship_queue).">
			  						".nf($ship_queue_cnt)."
			  					</td>";
			  				}
			  				else
			  				{
			  					echo "
			  					<td class=\"tbldata\">
			  						&nbsp;
			  					</td>";		  					
			  				}
			  				
			  				//Spalte fliegender Schiffe
		  					if($fleet_ships_cnt!="")
		  					{
		  						echo "
			  					<td class=\"tbldata\">
			  						".nf($fleet_ships_cnt)."
			  					</td>";
			  				}
			  				else
			  				{
			  					echo "
			  					<td class=\"tbldata\">
			  						&nbsp;
			  					</td>";		  					
			  				}			  				
			  	echo "</tr>";
			  }	  
	  	}    	
	  }		
		
		infobox_end(1);
		
		//Arrays löschen (Speicher freigeben)
		mysql_free_result($res);
		mysql_free_result($sres);
    unset($arr);
    unset($sarr);
    unset($shiplist);
    unset($ship_queue);
    unset($planet_data);
	}
	else
	{
		echo "Es sind noch keine Schiffe vorhanden!<br>";
	}

	
?>