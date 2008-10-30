<?PHP
         	// Datenänderung übernehmen
            if (isset($_POST['data_submit']) && checker_verify())
            {
              dbquery("
              UPDATE
                  user_properties
              SET
                  spyship_count='".max(1,intval($_POST['spyship_count']))."',
                  spyship_id='".$_POST['spyship_id']."',
                  fleet_rtn_msg='".$_POST['fleet_rtn_msg']."'
             	WHERE
                  id='".$cu->id()."';");
                      
              success_msg("Benutzer-Daten wurden ge&auml;ndert!");
                  
            }
			
			$res = dbquery("SELECT * FROM user_properties WHERE id='".$cu->id()."';");
			$arr = mysql_fetch_array($res);

            echo "<form action=\"?page=$page&mode=game\" method=\"post\" enctype=\"multipart/form-data\">";
            $cstr = checker_init();
            tableStart("Spieloptionen");

            echo "<tr>
            	<th style=\"width:250px;\"><b>Anzahl Spionagesonden für Direktscan:</b></th>
              <td>
              	<input type=\"text\" name=\"spyship_count\" maxlength=\"5\" size=\"5\" value=\"".$arr['spyship_count']."\">
              </td>
            </tr>";
            
            echo "<tr><th>Typ des Spionageschiffs für Direktscan:</th>
            <td>";
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
            	echo '<select name="spyship_id"><option value="0">(keines)</option>';
            	while ($sarr=mysql_fetch_array($sres))
            	{
            		echo '<option value="'.$sarr['ship_id'].'"';
            		if ($arr['spyship_id']==$sarr['ship_id'])
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
            			<th width=\"36%\">Nachricht bei Transport-/Spionagerückkehr:</th>
            			<td width=\"16%\">
                      <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"1\" ";
                      if ($arr['fleet_rtn_msg']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert &nbsp;
                  
                      <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"0\" ";
                      if ($arr['fleet_rtn_msg']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>"; 

            tableEnd();
            echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
            echo "</form><br/><br/>";
?>