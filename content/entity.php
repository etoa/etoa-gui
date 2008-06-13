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
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// DATEN LADEN

	if(isset($_GET['id']) && intval($_GET['id'])>0)
	{
		$id = intval($_GET['id']);
	}	
	elseif(isset($_POST['id']) && intval($_POST['id'])>0)
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
		$ent = Entity::createFactoryById($id);
		if ($ent->isValid())
		{
		
		echo "<h1>&Uuml;bersicht &uuml;ber ".$ent." (".$ent->entityCodeString().")</h1>";
		if ($ent->entityCode()=='p')
		{
			infobox_start("Planetendaten",1);
			echo "<tr>
				<td width=\"320\" class=\"tbldata\" style=\"background:#000;vertical-align:middle\" rowspan=\"7\">
					<img src=\"".$ent->imagePath("b")."\" alt=\"planet\" width=\"310\" height=\"310\"/>
				</td>";
			echo "<td width=\"100\" class=\"tbltitle\">Besitzer:</td>
			<td class=\"tbldata\">";
			if ($ent->ownerId()>0)
				echo "<a href=\"?page=userinfo&amp;id=".$ent->ownerId()."\">".$ent->owner()."</a>";
			else
				echo $ent->owner();
			echo "</td>
			</tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Sonnentyp:</td>
				<td class=\"tbldata\">".$arr['stype']."</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Planettyp:</td>
				<td class=\"tbldata\">".$arr['ptype']."</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Felder:</td>
				<td class=\"tbldata\">".$ent->fields." total</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Gr&ouml;sse:</td>
				<td class=\"tbldata\">".nf($conf['field_squarekm']['v']*$ent->fields)." km&sup2;</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Temperatur:</td>
				<td class=\"tbldata\">".$ent->temp_from."&deg;C bis ".$ent->temp_to."&deg;C <br/>";
				echo "<img src=\"images/heat_small.png\" alt=\"Heat\" style=\"width:16px;float:left;\" /> <a href=\"?page=help&amp;site=tempbonus\">Wärmebonus</a>: ";
				$spw = $ent->solarPowerBonus();
				if ($spw>=0)
				{
					echo "<span style=\"color:#0f0\">+".$spw."</span>";
				}
				else
				{
					echo "<span style=\"color:#f00\">".$spw."</span>";
				}
				echo " Energie pro Solarsatellit<br style=\"clear:both;\"/>
				<img src=\"images/ice_small.png\" alt=\"Cold\" style=\"width:16px;float:left;\" /> <a href=\"?page=help&amp;site=tempbonus\"> Kältebonus</a>: ";
				$spw = $ent->fuelProductionBonus();
				if ($spw>=0)
				{
					echo "<span style=\"color:#0f0\">+".$spw."%</span>";
				}
				else
				{
					echo "<span style=\"color:#f00\">".$spw."%</span>";
				}				
			echo " ".RES_FUEL."-Produktion</td></tr>";
			echo "<tr>
				<td width=\"100\" class=\"tbltitle\">Beschreibung:</td>
				<td class=\"tbldata\">".($ent->desc!="" ? $ent->desc : '-')."</td></tr>";
			infobox_end(1);
		}
		else
		{
			infobox_start("Objektdaten");			
			echo "Über dieses Objekt sind keine weiteren Daten verfügbar!";
			infobox_end();
		}
		
			// Previous and next entity
			$idprev = $id-1;
			$idnext = $id+1;
			$pmres = dbquery("
			SELECT 
				MAX(id) 
			FROM 
				entities");	
			$pmarr=mysql_fetch_row($pmres);		
			if ($idprev>0)
			{	
				$str_prev =	"<td class=\"tbldata\"><input type=\"button\" value=\"&lt;\" onclick=\"document.location='?page=$page&amp;id=".$idprev."'\" /></td>";
			}
			if ($idnext <= $pmarr[0])
			{
				$str_next = "<td class=\"tbldata\"><input type=\"button\" value=\"&gt;\" onclick=\"document.location='?page=$page&amp;id=".$idnext."'\" /></td>";
			} 
		}
		else
		{
			echo "<h1>Raumobjekt-Datenbank</h1>
			Das Objekt mit der Kennung <b>".$id."</b> existiert nicht!<br/><br/>";
		}
	}
	else
	{
		echo "<h1>Raumobjekt-Datenbank</h1>
		Das Objekt mit der Kennung <b>".$id."</b> existiert nicht!<br/><br/>";
	}
	
	
	echo "<form action=\"?page=$page\" method=\"post\" name=\"planetsearch\">";
	infobox_start("Objektsuche",1,0);
	echo "<tr>";
		if (isset($str_prev)) echo $str_prev;
		echo "<th class=\"tbltitle\">Kennung:</th>
		<td class=\"tbldata\">
			<input type=\"text\" name=\"id\" size=\"5\" maxlength=\"7\" value=\"".$id."\" /> &nbsp; 
			<input type=\"submit\" name=\"search_submit\" value=\"Objekt anzeigen\" />
		</td>";
		if (isset($str_next)) echo $str_next;
	echo "</tr>";
	infobox_end(1);
	echo "<input type=\"button\" value=\"Zur Raumkarte\" onclick=\"document.location='?page=space'\" /> &nbsp; ";
	if ($ent)
		echo "<input type=\"button\" value=\"Zur Systemkarte\" onclick=\"document.location='?page=cell&amp;id=".$ent->cellId()."'\" />";			
	echo "</form>
	<script type=\"\">document.forms['planetsearch'].elements[0].focus();</script>";
	


?>

