<h1>Flotten&uuml;bersicht</h1>
<?PHP
	define(HELP_URL,"?page=help&site=shipyard");

	//Button "Zurück zum Raumschiffshafen"
	echo "<input type=\"button\" onclick=\"document.location='?page=haven'\" value=\"Raumschiffshafen des aktuellen Planeten anzeigen\" /><br/><br/>";

	//Listet alle Schiffe auf die der User besizt
	
	$ship_names = array();		//Speichert alle Schiffsnamen
	$shiplist_data = array();	//Speichert die gefundenen Shiplist Einträge
	$queue_data = array();		//Speichert die gefundenen Queue Einträge
	$planet_data = array();		//Speichert alle Planetnamen des Users

		//Speichert Planetnamen in ein Array
		foreach ($planets->own as $p)
		{
			$planet_data[$p->id]['name']=$p->name;
		}
		
		//Lädt Schiffsdaten
    $res = dbquery("
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
    if(mysql_num_rows($res)>0)
    {
    	while ($arr=mysql_fetch_array($res))
    	{
    		$ship_names[$arr['ship_id']]['name']=$arr['ship_name'];
    		$ship_names[$arr['ship_id']]['special']=$arr['special_ship'];
    	}    	
    }		
		
		//Lädt Daten in der Schiffsliste
    $res = dbquery("
    SELECT
    	shiplist_ship_id,
      shiplist_planet_id,
      shiplist_count
    FROM
    	".$db_table['shiplist']."
    WHERE
    	shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
    if(mysql_num_rows($res)>0)
    {
    	while ($arr=mysql_fetch_array($res))
    	{
    		$shiplist_data[$arr['shiplist_ship_id']]['count']=$arr['shiplist_count'];
    	}    	
    }				

		//Lädt Daten in der Bauliste
    $res = dbquery("
    SELECT
    	queue_planet_id,
    	queue_ship_id,
    	queue_cnt
    FROM
    	".$db_table['ship_queue']."
    WHERE
    	queue_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
    if(mysql_num_rows($res)>0)
    {
    	while ($arr=mysql_fetch_array($res))
    	{
    		$queue_data[$arr['queue_ship_id']]['count']=$arr['queue_cnt'];
    	}    	
    }	
    		
    		
    //Tabelle erzeugen wenn daten vorhande sind
    if(isset($shiplist_data) || isset($queue_data))
    {  	
	   	//Zeigt alle gefundenen Schiffseinträge an
	   	foreach ($ship_names as $id => $data)
	   	{
	   		//Sucht die Shiplist oder Queue ID in dem Schiffsarray
	   		if (array_key_exists($id, $shiplist_data) || array_key_exists($id, $queue_data))
	   		{
	   			echo "".$ship_names[$id]['name']." [".$id."]: ".$shiplist_data[$id]['count']." / ".$queue_data[$id]['count']."";
	   			
	   			echo "<br>";	
	   		}
	   	}
	 	}
	 	else
	 	{
	 		echo "Es sind noch keine Schiffe vorhanden!<br>";
	 	}
   	
   	
   	/*
    $res = dbquery("
    SELECT
    	s.ship_id,
      s.ship_name,
      s.special_ship,
    	SUM(sl.shiplist_count) AS count
    FROM
    	".$db_table['ships']." AS s
    	INNER JOIN
	    	".$db_table['shiplist']." AS sl
	    ON s.ship_id=sl.shiplist_ship_id
	  	sl.shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
    GROUP BY
    	sl.shiplist_ship_id
    ORDER BY
    	s.ship_name;");
    if(mysql_num_rows($res)>0)
    {
    	$show=true;
    	while ($arr=mysql_Fetch_array($res))
    	{
    		$item_list[$arr['ship_id']]['name']=$arr['ship_name'];
    		$item_list[$arr['ship_id']]['special']=$arr['special_ship'];
    		$item_list[$arr['ship_id']]['count']+=$arr['count'];
	    	$items[$arr['ship_id']]=true;
    	}    	
    }
    
    $res = dbquery("
    SELECT
    	s.ship_id,
      s.ship_name,
      s.special_ship,
    	SUM(q.queue_cnt) AS count
    FROM
    	".$db_table['ships']." AS s
    	INNER JOIN
	    	".$db_table['ship_queue']." AS q
	    ON s.ship_id=q.queue_ship_id
	    AND q.queue_user_id='".$_SESSION[ROUNDID]['user']['id']."'
	  GROUP BY 
	  	q.queue_ship_id
    ORDER BY
    	s.ship_name;");
    if(mysql_num_rows($res)>0)
    {
    	$show=true;
    	while ($arr=mysql_Fetch_array($res))
    	{
    		$item_list[$arr['ship_id']]['name']=$arr['ship_name'];
    		$item_list[$arr['ship_id']]['special']=$arr['special_ship'];
    		$item_list[$arr['ship_id']]['build_count']+=$arr['count'];
    		$item_list[$arr['ship_id']]['planets'][$arr['planet_id']]['name']+=$arr['planet_name'];
    		$item_list[$arr['ship_id']]['planets'][$arr['planet_id']]['build_count']=$arr['count'];
    		
	    	$items[$arr['ship_id']]=true;
    	}    	
    }

    if ($show)
    {
    	infobox_start("Flotten&uuml;bersicht",1,0);
    	echo "<tr>
    			<td class=\"tbltitle\" colspan=\"2\">Schiff</td>
    			<td class=\"tbltitle\">Anzahl (Total)</td>
    			<td class=\"tbltitle\">In Bau (Total)</td>
    	     </tr>";
        foreach ($items as $id => $tmp)
        {
            $tm_info="";
            $tm_count="";
            $tm_build_count="";
            /*
            while ($parr = mysql_fetch_array($pres))
            {
                $tm_info.="".$parr['planet_name'].": ".nf($parr['shiplist_count'])." (".nf($parr['shiplist_build_count']).")<br>";

                if($parr['shiplist_count']>0)
                	$tm_count.="".$parr['planet_name'].": ".nf($parr['shiplist_count'])."<br>";

                if($parr['shiplist_build_count']>0)
                	$tm_build_count.="".$parr['planet_name'].": ".nf($parr['shiplist_build_count'])."<br>";
            }
						
            //Prüft, das es keine leeren Kästechen gibt
            if($tm_count=="")
            	$show_tm_count="";
            else
            	$show_tm_count=tm("Anzahl",$tm_count);

            if($tm_build_count=="")
            	$show_tm_build_count="";
            else
            	$show_tm_build_count=tm("Anzahl",$tm_build_count);

            $s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$id."_small.".IMAGE_EXT;

            if($arr['special_ship']==1)
            	$link="?page=ship_upgrade";
            else
            	$link="".HELP_URL."&amp;id=".$id."";

            echo "<tr>
                    <td class=\"tbldata\">
                        <a href=\"".$link."\" title=\"Info zu diesem Schiff anzeigen\"><img src=\"$s_img\" width=\"40\" height=\"40\" border=\"0\" /></a>
                    </td>
                    <td class=\"tbltitle\" ".tm("Anzahl",$tm_info)." >
                        ".$item_list[$id]['name']."
                    </td>
                    <td class=\"tbldata\" ".$show_tm_count.">
                    	".nf($item_list[$id]['count'])."
                    </td>
                    <td class=\"tbldata\" ".$show_tm_build_count.">
                    	".nf($item_list[$id]['build_count'])."
                    </td>";
        }

       	infobox_end(1);
    }
    else
    {
    	echo "Es sind noch keine Schiffe vorhanden!<br>";
    }*/

?>