<?PHP
         	// Datenänderung übernehmen
            if (isset($_POST['data_submit']) && checker_verify())
            {
              dbquery("
              UPDATE
                  user_properties
              SET
                  msgsignature='".addslashes($_POST['msgsignature'])."',
                  msg_preview='".$_POST['msg_preview']."',
                  msgcreation_preview='".$_POST['msgcreation_preview']."',
                  msg_copy=".$_POST['msg_copy'].",
                  msg_blink=".$_POST['msg_blink']."
             	WHERE
                  id='".$cu->id()."';");
                      
              success_msg("Nachrichten-Einstellungen wurden ge&auml;ndert!");
                  
              $res = dbquery("SELECT * FROM user_properties WHERE id='".$cu->id()."';");
              $arr = mysql_fetch_array($res);
            }

            echo "<form action=\"?page=$page&mode=messages\" method=\"post\" enctype=\"multipart/form-data\">";
            $cstr = checker_init();
            infobox_start("Nachrichtenoptionen",1);

            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Nachrichten-Signatur:</th>
            	<td class=\"tbldata\"><textarea name=\"msgsignature\" cols=\"50\" rows=\"2\" width=\"65%\">".stripslashes($arr['msgsignature'])."</textarea></td>
            </tr>";
		        //Nachrichtenvorschau (Neue/Archiv) (An/Aus)
		    		echo "<tr>
	    				 		<th class=\"tbldata\" width=\"36%\">Nachrichtenvorschau (Neue/Archiv):</th>
    						<td class=\"tbldata\" width=\"16%\">
                    <input type=\"radio\" name=\"msg_preview\" value=\"1\" ";
                    if ($arr['msg_preview']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert
                    <input type=\"radio\" name=\"msg_preview\" value=\"0\" ";
                    if ($arr['msg_preview']==0) echo " checked=\"checked\"";
                    echo "/> Deaktiviert
           			</td>
           	 </tr>";
           	     
		          //Nachrichtenvorschau (Erstellen) (An/Aus)
		          echo "<tr>
		              		<th class=\"tbldata\" width=\"36%\">Nachrichtenvorschau (Erstellen):</th>
              		<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"msgcreation_preview\" value=\"1\" ";
                      if ($arr['msgcreation_preview']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                      <input type=\"radio\" name=\"msgcreation_preview\" value=\"0\" ";
                      if ($arr['msgcreation_preview']==0) echo " checked=\"checked\"";
                      echo "/> Deaktiviert
                  </td>
               </tr>";

		          // Blinkendes Nachrichtensymbol (An/Aus)
		          echo "<tr>
		              		<th class=\"tbldata\" width=\"36%\">Blinkendes Nachrichtensymbol:</th>
              		<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"msg_blink\" value=\"1\" ";
                      if ($arr['msg_blink']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                      <input type=\"radio\" name=\"msg_blink\" value=\"0\" ";
                      if ($arr['msg_blink']==0) echo " checked=\"checked\"";
                      echo "/> Deaktiviert
                  </td>
               </tr>";
               
		          // Text kopieren (An/Aus)
		          echo "<tr>
		              		<th class=\"tbldata\" width=\"36%\">Text bei Antwort/Weiterleiten kopieren:</th>
              		<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"msg_copy\" value=\"1\" ";
                      if ($arr['msg_copy']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                      <input type=\"radio\" name=\"msg_copy\" value=\"0\" ";
                      if ($arr['msg_copy']==0) echo " checked=\"checked\"";
                      echo "/> Deaktiviert
                  </td>
               </tr>";               

            infobox_end(1);
            echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
            echo "</form><br/><br/>";
 
?>
