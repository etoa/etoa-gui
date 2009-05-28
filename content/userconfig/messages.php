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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	// $Author$
	// $Date$
	// $Rev$
	//

 	// Datenänderung übernehmen
    if (isset($_POST['data_submit']) && checker_verify())
    {
      $cu->properties->msgSignature = addslashes($_POST['msgsignature']);
      $cu->properties->msgPreview = $_POST['msg_preview'];
      $cu->properties->msgCreationPreview = $_POST['msgcreation_preview'];
      $cu->properties->msgBlink = $_POST['msg_blink'];
      $cu->properties->msgCopy = $_POST['msg_copy'];
			$cu->properties->fleetRtnMsg = $_POST['fleet_rtn_msg'];

      success_msg("Nachrichten-Einstellungen wurden ge&auml;ndert!"); 
    }
    
    echo "<form action=\"?page=$page&mode=messages\" method=\"post\" enctype=\"multipart/form-data\">";
    $cstr = checker_init();
    tableStart("Nachrichtenoptionen");

    echo "<tr>
    	<th width=\"35%\">Nachrichten-Signatur:</th>
    	<td>
    		<textarea name=\"msgsignature\" cols=\"50\" rows=\"4\" width=\"65%\">".stripslashes($cu->properties->msgSignature)."</textarea></td>
    </tr>";
    //Nachrichtenvorschau (Neue/Archiv) (An/Aus)
		echo "<tr>
			 		<th width=\"36%\">Nachrichtenvorschau (Neue/Archiv):</th>
				<td width=\"16%\">
            <input type=\"radio\" name=\"msg_preview\" value=\"1\" ";
            if ($cu->properties->msgPreview==1) echo " checked=\"checked\"";
            echo "/> Aktiviert
            <input type=\"radio\" name=\"msg_preview\" value=\"0\" ";
            if ($cu->properties->msgPreview==0) echo " checked=\"checked\"";
            echo "/> Deaktiviert
   			</td>
   	 </tr>";
   	     
      //Nachrichtenvorschau (Erstellen) (An/Aus)
      echo "<tr>
          		<th width=\"36%\">Nachrichtenvorschau (Erstellen):</th>
      		<td width=\"16%\">
              <input type=\"radio\" name=\"msgcreation_preview\" value=\"1\" ";
              if ($cu->properties->msgCreationPreview==1) 
              	echo " checked=\"checked\"";
              echo "/> Aktiviert
              <input type=\"radio\" name=\"msgcreation_preview\" value=\"0\" ";
              if ($cu->properties->msgCreationPreview==0) 
              	echo " checked=\"checked\"";
              echo "/> Deaktiviert
          </td>
       </tr>";

      // Blinkendes Nachrichtensymbol (An/Aus)
      echo "<tr>
          		<th width=\"36%\">Blinkendes Nachrichtensymbol:</th>
      		<td width=\"16%\">
              <input type=\"radio\" name=\"msg_blink\" value=\"1\" ";
              if ($cu->properties->msgBlink==1) 
              	echo " checked=\"checked\"";
              echo "/> Aktiviert
              <input type=\"radio\" name=\"msg_blink\" value=\"0\" ";
              if ($cu->properties->msgBlink==0) 
              	echo " checked=\"checked\"";
              echo "/> Deaktiviert
          </td>
       </tr>";
       
      // Text kopieren (An/Aus)
      echo "<tr>
          		<th width=\"36%\">Text bei Antwort/Weiterleiten kopieren:</th>
      		<td width=\"16%\">
              <input type=\"radio\" name=\"msg_copy\" value=\"1\" ";
              if ($cu->properties->msgCopy==1) 
              	echo " checked=\"checked\"";
              echo "/> Aktiviert
              <input type=\"radio\" name=\"msg_copy\" value=\"0\" ";
              if ($cu->properties->msgCopy==0) 
              	echo " checked=\"checked\"";
              echo "/> Deaktiviert
          </td>
       </tr>";               

		// Rückflug-Benachrichtingung für Flotten
    echo "<tr>
    			<th width=\"36%\">Nachricht bei Transport-/Spionagerückkehr:</th>
    			<td width=\"16%\">
              <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"1\" ";
              if ($cu->properties->fleetRtnMsg==1) echo " checked=\"checked\"";
              echo "/> Aktiviert &nbsp;
          
              <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"0\" ";
              if ($cu->properties->fleetRtnMsg==0) echo " checked=\"checked\"";
    					echo "/> Deaktiviert
    		</td>
  		</tr>"; 

    tableEnd();
    echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
    echo "</form><br/><br/>";
 
?>
