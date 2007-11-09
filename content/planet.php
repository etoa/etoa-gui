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
	// 	File: planet.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about a specified planet
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// DATEN LADEN

	$sol_type = get_sol_types_array();
	$planet_type = get_planet_types_array();

	if (intval($_GET['planet_info_id'])>0)
	{
		$id = intval($_GET['planet_info_id']);
	}	
	elseif(intval($_GET['id'])>0)
	{
		$id = intval($_GET['id']);
	}	
	elseif(intval($_POST['id'])>0)
	{
		$id = intval($_POST['id']);
	}	
	elseif(isset($_POST['search_submit']))
	{
		echo "<h1>Planeten-Datenbank</h1>
		Ungültige Kennung!<br/><br/>";
	}

	if ($id>0)
	{
		$res = dbquery("
		SELECT     
			p.planet_id,
			p.planet_user_id,
			p.planet_name,
			p.planet_image,
			p.planet_solsys_pos,
			p.planet_fields,
            p.planet_fields_used,
            p.planet_temp_from,
            p.planet_temp_to,
            p.planet_desc,
            sp.cell_id,
            sp.cell_sx,
            sp.cell_sy,
            sp.cell_cx,
            sp.cell_cy,
            pt.type_name as ptype,
            s.type_name as stype
		FROM 
            ".$db_table['planets']." AS p
    INNER JOIN
    (
    	".$db_table['space_cells']." AS sp
    	
    		INNER JOIN
      		".$db_table['sol_types']." AS s
    		ON sp.cell_solsys_solsys_sol_type=s.type_id   			
    )
    ON p.planet_solsys_id=sp.cell_id
    
    INNER JOIN 
   		".$db_table['planet_types']." AS pt
   	ON p.planet_type_id=pt.type_id
		WHERE
			p.planet_id='".intval($id)."'
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			echo "<h1>&Uuml;bersicht &uuml;ber den Planeten ".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos'];
			if ($arr['planet_name']!="") echo " (".$arr['planet_name'].")";
			echo "</h1>";
	
			$p_img = IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['planet_image'].".gif";
	
			infobox_start("Planetendaten",1);
			echo "<tr>
				<td width=\"320\" class=\"tbldata\" style=\"background:#000;vertical-align:middle\" rowspan=\"7\">
					<img src=\"$p_img\" width=\"310\" height=\"310\"/>
				</td>";
			echo "<td width=\"100\" class=\"tbltitle\">Besitzer:</td>
			<td class=\"tbldata\">";
			if ($arr['planet_user_id']>0)
			{
				$user = get_user_nick($arr['planet_user_id']);
				if ($user!="") echo $user." [<a href=\"?page=userinfo&id=".$arr['planet_user_id']."\" title=\"Info\">Info</a>]";
			}
			else
				echo "<i>Unbewohnter Planet</i>";
			echo "</td>
			<td rowspan=\"7\" class=\"tbldata\">
				<table style=\"width:100%;height:100%;\">
				<tr>
					<th class=\"tbltitle\" colspan=\"2\">Pos</th>
					<th class=\"tbltitle\">Kennung</th>
				</tr>";
				// Neighbour-List
				$sres = dbquery("
				SELECT
					planet_id,
					planet_solsys_pos,
					planet_name,
					planet_image					
				FROM
					planets
				WHERE
					planet_solsys_id=".$arr['cell_id']."
				;");
				while ($sarr=mysql_fetch_row($sres))
				{
					if ($sarr[0]==$arr['planet_id'])
					{
						$style=" style=\"color:yellow;font-weight:bold;\"";
					}
					else
					{
						$style="";
					}
					$p_img = IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$sarr[3]."_small.gif";
					echo "<tr>
						<th class=\"tbltitle\">
							<img src=\"$p_img\" style=\"width:20px;height:20px;border:none;\" alt=\"".$arr['type_name']."\" />
						</th>
						<th class=\"tbltitle\" $style>
							".$sarr[1]."</th>
						<td class=\"tbldata\" $style>
							<a href=\"?page=planet&amp;id=".$sarr[0]."\" $style>
								".Planet::getIdentifier($sarr[0])."
							</a></td>
					</tr>";					
				}
				echo "</table>
			</td>
			</tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Sonnentyp:</td>
				<td class=\"tbldata\">".$arr['stype']."</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Planettyp:</td>
				<td class=\"tbldata\">".$arr['ptype']."</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Felder:</td>
				<td class=\"tbldata\">".$arr['planet_fields']." total</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Gr&ouml;sse:</td>
				<td class=\"tbldata\">".nf($conf['field_squarekm']['v']*$arr['planet_fields'])." km&sup2;</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Temperatur:</td>
				<td class=\"tbldata\">".$arr['planet_temp_from']."&deg;C bis ".$arr['planet_temp_to']."&deg;C <br/>";
				$spw = Planet::getsolarPowerBonus($arr['planet_temp_from'],$arr['planet_temp_to']);
				echo "Solarbonus <img src=\"images/infohelp.png\" style=\"width:10px;\" ".tm("Temperaturbonus","Die Planetentemperatur verstärkt oder schwächt die Produktion von Energie durch Solarsatelliten. Je näher ein Planet bei der Sonne ist, desto besser ist die Produktion.")."/>:";
				if ($spw>=0)
				{
					echo "<span style=\"color:#0f0\">+".$spw."</span>";
				}
				else
				{
					echo "<span style=\"color:#f00\">".$spw."</span>";
				}
			echo "</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Beschreibung:</td>
				<td class=\"tbldata\">".($arr['planet_desc']!="" ? $arr['planet_desc'] : '-')."</td></tr>";
			infobox_end(1);
			
			// Previous and next planet
			$idprev = $arr['planet_id']-1;
			$idnext = $arr['planet_id']+1;
			$pmres = dbquery("
			SELECT 
				MAX(planet_id) 
			FROM 
				planets");	
			$pmarr=mysql_fetch_row($pmres);		
			if ($idprev>0)
			{	
				$str_prev =	"<td class=\"tbldata\"><input type=\"button\" value=\"&lt;\" onclick=\"document.location='?page=planet&amp;id=".$idprev."'\" /></td>";
			}
			if ($idnext <= $pmarr[0])
			{
				$str_next = "<td class=\"tbldata\"><input type=\"button\" value=\"&gt;\" onclick=\"document.location='?page=planet&amp;id=".$idnext."'\" /></td>";
			}
		}
		else
		{
			echo "<h1>Planeten-Datenbank</h1>
			Der Planet mit der Kennung <b>".Planet::getIdentifier($id)."</b> existiert nicht!<br/><br/>";
		}
	}
	else
	{
		echo "<h1>Planeten-Datenbank</h1>";
	}

	echo "<form action=\"?page=$page\" method=\"post\" name=\"planetsearch\">";
	infobox_start("Planetensuche",1,0);
	if ($arr['planet_id']>0)
		$idn = Planet::getIdentifier($arr['planet_id'],0);
	echo "<tr>
		$str_prev
		<th class=\"tbltitle\">Kennung:</th>
		<td class=\"tbldata\">
			P<input type=\"text\" name=\"id\" size=\"5\" maxlength=\"7\" value=\"".$idn."\" /> &nbsp; 
			<input type=\"submit\" name=\"search_submit\" value=\"Planet anzeigen\" />
		</td>
		$str_next
	</tr>";
	infobox_end(1);
	echo "<input type=\"button\" value=\"Raumkarte\" onclick=\"document.location='?page=space'\" /> &nbsp; ";
	if ($arr['cell_id']>0)
	{
		echo "<input type=\"button\" value=\"Sonnensystem\" onclick=\"document.location='?page=solsys&id=".$arr['cell_id']."'\" />";
	}
	echo "</form>
	<script type=\"\">document.forms['planetsearch'].elements[0].focus();</script>";
	


?>

