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
	//

 	// Datenänderung übernehmen
  if (isset($_POST['data_submit']) && checker_verify())
  {
  	$cu->properties->spyShipId = $_POST['spyship_id'];
  	$cu->properties->spyShipCount = $_POST['spyship_count'];
  	$cu->properties->analyzeShipId = $_POST['analyzeship_id'];
  	$cu->properties->analyzeShipCount = $_POST['analyzeship_count'];
  	$cu->properties->exploreShipId = $_POST['exploreship_id'];
  	$cu->properties->exploreShipCount = $_POST['exploreship_count'];
  	$cu->properties->startUpChat = $_POST['startup_chat'];
	$cu->properties->showCellreports = $_POST['show_cellreports'];
	$cu->properties->enableKeybinds = $_POST['keybinds_enable'];
	
	if ( (	strlen($_POST['chat_color'])==3 &&
			preg_match('/^[a-fA-F0-9]{3}$/', $_POST['chat_color']) ) ||
		(	strlen($_POST['chat_color'])==6 &&
			preg_match('/^[a-fA-F0-9]{6}$/', $_POST['chat_color']) ))
	{
  		$cu->properties->chatColor = $_POST['chat_color'];
  		if($_POST['chat_color'] == '000' || $_POST['chat_color'] == '000000')
  		{
  			success_msg('Chatfarbe schwarz auf schwarz ist eine Weile ja ganz lustig, aber in ein paar Minuten bitte zur&uuml;ck&auml;ndern ;)');
  		} else {
    		success_msg('Benutzer-Daten wurden ge&auml;ndert!');
  		}
	}
	else
	{
  		$cu->properties->chatColor = "FFF";
    	error_msg('Ung&uuml;ltiger RGB-Farbwert, Standardwert #FFF wurde eingef&uuml;gt.');
	}
  }
			

  echo "<form action=\"?page=$page&mode=game\" method=\"post\" enctype=\"multipart/form-data\">";
  $cstr = checker_init();
  tableStart("Spieloptionen");

  echo "<tr>
  	<th><b>Anzahl Spionagesonden für Direktscan:</b></th>
    <td>
    	<input type=\"text\" name=\"spyship_count\" maxlength=\"5\" size=\"5\" value=\"".$cu->properties->spyShipCount."\">
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
  		if ($cu->properties->spyShipId == $sarr['ship_id'])
  		 echo ' selected="selected"';
  		echo '>'.$sarr['ship_name'].'</option>';
  	}
  }
  else
  {
  	echo "Momentan steht kein Schiff zur Auswahl!";
  }
  echo "</td></tr>";
  
  echo "<tr>
  	<th><b>Anzahl Analyzatoren für Quickanalyse:</b></th>
    <td>
    	<input type=\"text\" name=\"analyzeship_count\" maxlength=\"5\" size=\"5\" value=\"".$cu->properties->analyzeShipCount."\">
    </td>
  </tr>";
  
  echo "<tr><th>Typ des Analyzators für Quickanalyse:</th>
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
		ship_actions LIKE '%,analyze'
		OR ship_actions LIKE 'analyze,%'
		OR ship_actions LIKE '%,analyze,%'
		OR ship_actions LIKE 'analyze'
		)
	ORDER BY 
		ship_name ASC");
  if (mysql_num_rows($sres)>0)
  {
  	echo '<select name="analyzeship_id"><option value="0">(keines)</option>';
  	while ($sarr=mysql_fetch_array($sres))
  	{
  		echo '<option value="'.$sarr['ship_id'].'"';
  		if ($cu->properties->analyzeShipId == $sarr['ship_id'])
  		 echo ' selected="selected"';
  		echo '>'.$sarr['ship_name'].'</option>';
  	}
  }
  else
  {
  	echo "Momentan steht kein Schiff zur Auswahl!";
  }
  echo "</td></tr>";
  
  // Default explore ship
  echo "<tr>
  	<th><b>Anzahl Erkundungsschiffe für Direkterkundung:</b></th>
    <td>
    	<input type=\"text\" name=\"exploreship_count\" maxlength=\"5\" size=\"5\" value=\"".$cu->properties->exploreShipCount."\">
    </td>
  </tr>";
  
  echo "<tr><th>Typ des Erkundungsschiffs für Direkterkundung:</th>
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
		ship_actions LIKE '%,explore'
		OR ship_actions LIKE 'explore,%'
		OR ship_actions LIKE '%,explore,%'
		OR ship_actions LIKE 'explore'
		)
	ORDER BY 
		ship_name ASC");
  if (mysql_num_rows($sres)>0)
  {
  	echo '<select name="exploreship_id"><option value="0">(keines)</option>';
  	while ($sarr=mysql_fetch_array($sres))
  	{
  		echo '<option value="'.$sarr['ship_id'].'"';
  		if ($cu->properties->exploreShipId == $sarr['ship_id'])
  		 echo ' selected="selected"';
  		echo '>'.$sarr['ship_name'].'</option>';
  	}
  }
  else
  {
  	echo "Momentan steht kein Schiff zur Auswahl!";
  }
  echo "</td></tr>";  

  //Berichte im Sonnensystem (Aktiviert/Deaktiviert)
  echo "<tr>
    			<th>Berichte im Sonnensystem:</th>
    			<td>
              <input type=\"radio\" name=\"show_cellreports\" value=\"1\" ";
              if ($cu->properties->showCellreports==1) echo " checked=\"checked\"";
              echo "/> Aktiviert &nbsp; 
          
              <input type=\"radio\" name=\"show_cellreports\" value=\"0\" ";
              if ($cu->properties->showCellreports==0) echo " checked=\"checked\"";
    					echo "/> Deaktiviert
    		</td>
  		</tr>";
	//Notizbox (Aktiviert/Deaktiviert)
  echo "<tr>
    			<th>Chat beim Login öffnen:</th>
    			<td>
              <input type=\"radio\" name=\"startup_chat\" value=\"1\" ";
              if ($cu->properties->startUpChat==1) echo " checked=\"checked\"";
              echo "/> Aktiviert &nbsp; 
          
              <input type=\"radio\" name=\"startup_chat\" value=\"0\" ";
              if ($cu->properties->startUpChat==0) echo " checked=\"checked\"";
    					echo "/> Deaktiviert
    		</td>
  		</tr>";  
// Chat font color
echo "<tr>
  			<th>Chat Schriftfarbe:</th>
  			<td>
            #<input type=\"text\"
					id=\"chat_color\"
					name=\"chat_color\"
					size=\"6\"
					maxsize=\"6\"
					value=\"".$cu->properties->chatColor."\"
					onkeyup=\"addFontColor(this.id,'chatPreview')\"
					onchange=\"addFontColor(this.id,'chatPreview')\"/>&nbsp;
			<div id=\"chatPreview\" style=\"color:#".$cu->properties->chatColor.";\">&lt;".$cu." | ".date("H:i",time())."&gt; Chatvorschau </div>
  		</td>
		</tr>";
//Keybinds (Aktiviert/Deaktiviert)
echo "<tr>
    		<th>Keybinds (Navigation mit Tastatur):</th>
    		<td>
              <input type=\"radio\" name=\"keybinds_enable\" value=\"1\" ";
              if ($cu->properties->enableKeybinds==1) echo " checked=\"checked\"";
              echo "/> Aktiviert &nbsp; 
              <input type=\"radio\" name=\"keybinds_enable\" value=\"0\" ";
              if ($cu->properties->enableKeybinds==0) echo " checked=\"checked\"";
			  echo "/> Deaktiviert
    	</td>
  		</tr>";  


  tableEnd();
  echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
  echo "</form><br/><br/>";
?>