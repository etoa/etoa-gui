<?PHP
         	// Datenänderung übernehmen
            if (isset($_POST['data_submit']) && checker_verify())
            {
              dbquery("
              UPDATE
                  users
              SET
                  user_spyship_count='".max(1,intval($_POST['user_spyship_count']))."',
                  user_spyship_id='".$_POST['user_spyship_id']."',
                  user_fleet_rtn_msg='".$_POST['user_fleet_rtn_msg']."'
             	WHERE
                  user_id='".$cu->id()."';");
                      
              success_msg("Benutzer-Daten wurden ge&auml;ndert!");
                  
              $res = dbquery("SELECT * FROM ".$db_table['users']." WHERE user_id='".$cu->id()."';");
              $arr = mysql_fetch_array($res);
            }

            echo "<form action=\"?page=$page&mode=game\" method=\"post\" enctype=\"multipart/form-data\">";
            $cstr = checker_init();
            infobox_start("Spieloptionen",1);

            echo "<tr>
            	<td class=\"tbldata\" style=\"width:250px;\"><b>Anzahl Spionagesonden für Direktscan:</b></td>
              <td class=\"tbldata\">
              	<input type=\"text\" name=\"user_spyship_count\" maxlength=\"5\" size=\"5\" value=\"".$arr['user_spyship_count']."\">
              </td>
            </tr>";
            
            echo "<tr><td class=\"tbldata\" style=\"\">
            <b>Typ des Spionageschiffs für Direktscan:</b></td>
            <td class=\"tbldata\">";
						$sres = dbquery("
						SELECT 
			        ship_id, 
			        ship_name
						FROM 
							ships 
						WHERE 
							ship_buildable='1'
							AND (
							ship_actions LIKE '%,spy'
							OR ship_actions LIKE 'spy,%'
							OR ship_actions LIKE '%,spy,%'
							OR ship_actions LIKE 'spy'
							)
						ORDER BY 
							ship_name ASC");
            if (mysql_num_rows($sres)>0)
            {
            	echo '<select name="user_spyship_id"><option value="0">(keines)</option>';
            	while ($sarr=mysql_fetch_array($sres))
            	{
            		echo '<option value="'.$sarr['ship_id'].'"';
            		if ($arr['user_spyship_id']==$sarr['ship_id'])
            		 echo ' selected="selected"';
            		echo '>'.$sarr['ship_name'].'</option>';
            	}
            }
            else
            {
            	echo "Momentan steht kein Schiff zur Auswahl!";
            }
            echo "</td></tr>";
	
						// Rückflug-Benachrichtingung für Flotten
	          echo "<tr>
            			<th class=\"tbldata\" width=\"36%\">Nachricht bei Transport-/Spionagerückkehr:</th>
            			<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_fleet_rtn_msg\" value=\"1\" ";
                      if ($arr['user_fleet_rtn_msg']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert &nbsp;
                  
                      <input type=\"radio\" name=\"user_fleet_rtn_msg\" value=\"0\" ";
                      if ($arr['user_fleet_rtn_msg']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>"; 

            infobox_end(1);
            echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
            echo "</form><br/><br/>";
?>