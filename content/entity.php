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
		echo "<h1>Planeten-Datenbank</h1>";
		error_msg("Ungültige Kennung!");
	}

	if ($id>0)
	{
		
		
		
		if ($ent = Entity::createFactoryById($id))
		{
		$cell = new Cell($ent->cellId());
		
		if ($cu->discovered($cell->absX(),$cell->absY())>0)
		{						
		
			if ($ent->isValid())
			{
			
			echo "<h1>&Uuml;bersicht &uuml;ber ".$ent." (".$ent->entityCodeString().")</h1>";
			if ($ent->entityCode()=='p')
			{
				tableStart("Planetendaten");
				echo "<tr>
					<td width=\"320\" style=\"background:#000;;vertical-align:middle\" rowspan=\"".($ent->debrisField ? 8 : 7)."\">
						<img src=\"".$ent->imagePath("b")."\" alt=\"planet\" width=\"310\" height=\"310\"/>
					</td>";
				echo "<th width=\"100\">Besitzer:</th>
				<td>";
				if ($ent->ownerId()>0)
					echo "<a href=\"?page=userinfo&amp;id=".$ent->ownerId()."\">".$ent->owner()."</a>";
				else
					echo $ent->owner();
				echo "</td>
				</tr>";
				echo "<tr>
					<th width=\"100\">Sonnentyp:</th>
					<td>".$ent->starTypeName."</td></tr>";
				echo "<tr>
					<th width=\"100\">Planettyp:</th>
					<td>".$ent->typeName."</td></tr>";
				echo "<tr>
					<th width=\"100\">Felder:</th>
					<td>".$ent->fields." total</td></tr>";
				echo "<tr>
					<th width=\"100\">Gr&ouml;sse:</th>
					<td>".nf($conf['field_squarekm']['v']*$ent->fields)." km&sup2;</td></tr>";
				echo "<tr>
					<th width=\"100\">Temperatur:</th>
					<td>".$ent->temp_from."&deg;C bis ".$ent->temp_to."&deg;C <br/><br/>";
					echo "<img src=\"images/heat_small.png\" alt=\"Heat\" style=\"width:16px;float:left;\" />
					Wärmebonus: ".helpLink("tempbonus")."<br/> ";
					$spw = $ent->solarPowerBonus();
					if ($spw>=0)
					{
						echo "<span style=\"color:#0f0\">+".$spw."</span>";
					}
					else
					{
						echo "<span style=\"color:#f00\">".$spw."</span>";
					}
					echo " Energie pro Solarsatellit <br style=\"clear:both;\"/><br/>
					<img src=\"images/ice_small.png\" alt=\"Cold\" style=\"width:16px;float:left;\" /> 
					Kältebonus: ".helpLink("tempbonus")."<br/> ";
					$spw = $ent->fuelProductionBonus();
					if ($spw>=0)
					{
						echo "<span style=\"color:#0f0\">+".$spw."%</span>";
					}
					else
					{
						echo "<span style=\"color:#f00\">".$spw."%</span>";
					}				
				echo " ".RES_FUEL."-Produktion </td></tr>";
				echo "<tr>
					<th width=\"100\">Beschreibung:</th>
					<td>".($ent->desc!="" ? $ent->desc : '-')."</td></tr>";
				if ($ent->debrisField)
				{
					echo '<tr>
					<th class="tbltitle">Trümmerfeld:</th><td>
					'.RES_ICON_METAL."".nf($ent->debrisMetal).'<br style="clear:both;" /> 
					'.RES_ICON_CRYSTAL."".nf($ent->debrisCrystal).'<br style="clear:both;" /> 
					'.RES_ICON_PLASTIC."".nf($ent->debrisPlastic).'<br style="clear:both;" /> 
					</td></tr>';
				}				
					
				tableEnd();
			}
			elseif ($ent->entityCode()=='s')
			{
				tableStart("Sterndaten");
				echo "<tr>
					<td width=\"220\" style=\"background:#000;vertical-align:middle\" rowspan=\"2\">
						<img src=\"".$ent->imagePath("b")."\" alt=\"star\" width=\"220\" height=\"220\"/>
					</td>";
				echo "<th style=\"height:20px;\">Typ:</th>
				<td>".$ent->type()." ".helpLink("stars")."</td>
				</tr>";

				$data = $ent->typeData();
				
				echo "<tr><th>Beschreibung:</th><td>".$data['comment']."</td></tr>";

					
				tableEnd();
			}			
			else
			{
				iBoxStart("Objektdaten");			
				echo "Über dieses Objekt sind keine weiteren Daten verfügbar!";
				iBoxEnd();
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
					$str_prev =	"<td><input type=\"button\" value=\"&lt;\" onclick=\"document.location='?page=$page&amp;id=".$idprev."'\" /></td>";
				}
				if ($idnext <= $pmarr[0])
				{
					$str_next = "<td><input type=\"button\" value=\"&gt;\" onclick=\"document.location='?page=$page&amp;id=".$idnext."'\" /></td>";
				} 
			}
			else
			{
				echo "<h1>Raumobjekt-Datenbank</h1>";
				error_msg("Das Objekt mit der Kennung [b]".$id."[/b] existiert nicht!");
			}
		}
		else
		{
			echo "<h1>Raumobjekt-Datenbank</h1>";
			error_msg("Das Objekt mit der Kennung [b]".$id."[/b] wurde noch nicht entdeckt!");
		}
		}
		else
		{
			echo "<h1>Raumobjekt-Datenbank</h1>";
			error_msg("Das Objekt mit der Kennung [b]".$id."[/b] existiert nicht!");
		}
	}
	else
	{
		echo "<h1>Raumobjekt-Datenbank</h1>";
		error_msg("Das Objekt mit der Kennung [b]".$id."[/b] existiert nicht!");
	}
	
	
	echo "<form action=\"?page=$page\" method=\"post\" name=\"planetsearch\">";
	tableStart("Objektsuche");
	echo "<tr>";
		if (isset($str_prev)) echo $str_prev;
		echo "<th>Kennung:</th>
		<td>
			<input type=\"text\" name=\"id\" size=\"5\" maxlength=\"7\" value=\"".$id."\" /> &nbsp; 
			<input type=\"submit\" name=\"search_submit\" value=\"Objekt anzeigen\" />
		</td>";
		if (isset($str_next)) echo $str_next;
	echo "</tr>";
	tableEnd();
	echo "<input type=\"button\" value=\"Zur Raumkarte\" onclick=\"document.location='?page=sector'\" /> &nbsp; ";
	if ($ent)
		echo "<input type=\"button\" value=\"Zur Systemkarte\" onclick=\"document.location='?page=cell&amp;id=".$ent->cellId()."'\" />";			
	echo "</form>
	<script type=\"\">document.forms['planetsearch'].elements[0].focus();</script>";
	


?>

