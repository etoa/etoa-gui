<?PHP

         	// Datenänderung übernehmen
            if ($_POST['data_submit']!="" && checker_verify())
            {
              dbquery("
              UPDATE
                  ".$db_table['users']."
              SET
                  user_msgsignature='".addslashes($_POST['user_msgsignature'])."',
                  user_msg_preview='".$_POST['user_msg_preview']."',
                  user_msgcreation_preview='".$_POST['user_msgcreation_preview']."',
                  user_msg_copy=".$_POST['user_msg_copy'].",
                  user_msg_blink=".$_POST['user_msg_blink']."
             	WHERE
                  user_id='".$cu->id()."';");
                      
              $s['user']['msgsignature']=$_POST['user_msgsignature'];
              $s['user']['msg_preview']=$_POST['user_msg_preview'];
              $s['user']['msgcreation_preview']=$_POST['user_msgcreation_preview'];
              $s['user']['msg_copy']=$_POST['user_msg_copy'];
              $s['user']['msg_blink']=$_POST['user_msg_blink'];
              
              success_msg("Nachrichten-Einstellungen wurden ge&auml;ndert!");
                  
              $res = dbquery("SELECT * FROM ".$db_table['users']." WHERE user_id='".$cu->id()."';");
              $arr = mysql_fetch_array($res);
            }

            echo "<form action=\"?page=$page&mode=messages\" method=\"post\" enctype=\"multipart/form-data\">";
            $cstr = checker_init();
            infobox_start("Nachrichtenoptionen",1);

            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Nachrichten-Signatur:</th>
            	<td class=\"tbldata\"><textarea name=\"user_msgsignature\" cols=\"50\" rows=\"2\" width=\"65%\">".stripslashes($arr['user_msgsignature'])."</textarea></td>
            </tr>";
		        //Nachrichtenvorschau (Neue/Archiv) (An/Aus)
		    		echo "<tr>
	    				 		<th class=\"tbldata\" width=\"36%\">Nachrichtenvorschau (Neue/Archiv):</th>
    						<td class=\"tbldata\" width=\"16%\">
                    <input type=\"radio\" name=\"user_msg_preview\" value=\"1\" ";
                    if ($arr['user_msg_preview']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert
                    <input type=\"radio\" name=\"user_msg_preview\" value=\"0\" ";
                    if ($arr['user_msg_preview']==0) echo " checked=\"checked\"";
                    echo "/> Deaktiviert
           			</td>
           	 </tr>";
           	     
		          //Nachrichtenvorschau (Erstellen) (An/Aus)
		          echo "<tr>
		              		<th class=\"tbldata\" width=\"36%\">Nachrichtenvorschau (Erstellen):</th>
              		<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"1\" ";
                      if ($arr['user_msgcreation_preview']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                      <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"0\" ";
                      if ($arr['user_msgcreation_preview']==0) echo " checked=\"checked\"";
                      echo "/> Deaktiviert
                  </td>
               </tr>";

		          // Blinkendes Nachrichtensymbol (An/Aus)
		          echo "<tr>
		              		<th class=\"tbldata\" width=\"36%\">Blinkendes Nachrichtensymbol:</th>
              		<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_msg_blink\" value=\"1\" ";
                      if ($arr['user_msg_blink']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                      <input type=\"radio\" name=\"user_msg_blink\" value=\"0\" ";
                      if ($arr['user_msg_blink']==0) echo " checked=\"checked\"";
                      echo "/> Deaktiviert
                  </td>
               </tr>";
               
		          // Text kopieren (An/Aus)
		          echo "<tr>
		              		<th class=\"tbldata\" width=\"36%\">Text bei Antwort/Weiterleiten kopieren:</th>
              		<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_msg_copy\" value=\"1\" ";
                      if ($arr['user_msg_copy']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                      <input type=\"radio\" name=\"user_msg_copy\" value=\"0\" ";
                      if ($arr['user_msg_copy']==0) echo " checked=\"checked\"";
                      echo "/> Deaktiviert
                  </td>
               </tr>";               

            infobox_end(1);
            echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
            echo "</form><br/><br/>";
 
?>
