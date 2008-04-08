<?PHP
		$themes = get_imagepacks();
		$designs = get_designs();

		//change Theme skript
		echo "<script type=\"text/javascript\">";
		echo "function changeTheme()\n{";
		echo "var id=document.getElementById('user_image_select').options[document.getElementById('user_image_select').selectedIndex].value;\n";
		echo "switch (id)\n{";
		foreach ($themes as $k => $v)
		{
			echo "case '$k': document.getElementById('user_image_url').value='".$k."';\n document.getElementById('user_image_ext').value='".$v['extensions'][0]."'\n;break;";
		}
		echo "default: document.getElementById('user_image_url').value='';document.getElementById('user_image_ext').value='';";
		echo "}\n";
		echo "}\n</script>";
		


				//
        // Daten werden gespeichert
        //
        
        if (isset($_POST['data_submit_design']) && $_POST['data_submit_design']!="")
        {
          //Prüft eingaben auf unerlaube Zeichen
          $check_image = check_illegal_signs($_POST['user_image_url']);
          $sqla = "";
          if ($check_image=="")
          {
              if($_POST['user_image_ext']!="" && $_POST['user_image_url']!="")
              {
                  //Wandelt alle \ (backslash) in / um (Da windows den pfad mit \ angibt!)
                  $grafikpack = str_replace("\\", "/", $_POST['user_image_url']);
                  $sqla = " user_image_url='".$grafikpack."',";
              }
              else
              {
                echo "Du hast keinen Bildpfad angeben!<br>";
              }
          }
          else
          {
            if($check_image!="")
              $signs=$check_image;
            echo "Du hast ein unerlaubtes Zeichen ( ".$signs." ) im Grafikpfad!";
          }
          
          if ($_POST['user_image_select']!='')
          {
          	$sqla = " user_image_url='".$_POST['user_image_select']."',";
          }
          
          // Ändert die Bildendung, aber nur, wenn ein neues Packet gewählt wurde
          if(isset($_POST['user_image_ext']))
          {
          	$sqla .= " user_image_ext='".$_POST['user_image_ext']."',";
          }
          
          if (dbquery("
          UPDATE
              users
          SET
          		".$sqla."
              user_css_style='".$_POST['user_css_style']."',
              user_game_width='".$_POST['user_game_width']."',
              user_planet_circle_width='".$_POST['user_planet_circle_width']."',
              user_item_show='".$_POST['user_item_show']."',
              user_image_filter='".$_POST['user_image_filter']."',
              user_helpbox='".$_POST['user_helpbox']."',                          
              user_notebox='".$_POST['user_notebox']."',
              user_havenships_buttons='".$_POST['user_havenships_buttons']."',
              user_show_adds=".$_POST['user_show_adds']."                          
          WHERE
              user_id='".$cu->id()."';")
          )
          {
            success_msg("Design-Daten wurden geändert!");
            if (mysql_affected_rows()>0)
            {
            	echo "<script type=\"text/javascript\">document.location='?page=$page&mode=design&changes=1';</script>";
            }
          }
        }
				if (isset($_GET['changes']) && $_GET['changes']==1)
				{
         	success_msg("Design-Daten wurden geändert!");
        }


				//
				//Formular
				//
				
        echo "<form action=\"?page=$page&mode=design\" method=\"post\">";
        $cstr = checker_init();
        infobox_start("Designoptionen",1);
        
        //Design wählen
        echo "<tr>
            <th class=\"tbldata\" width=\"36%\">Design w&auml;hlen:</th>
            <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <select name=\"user_css_style\" id=\"designSelector\" onchange=\"xajax_designInfo(this.options[this.selectedIndex].value);\">";
                    foreach ($designs as $k => $v)
                    {
                        echo "<option value=\"$k\"";
                        if (CSS_STYLE==$k) echo " selected=\"selected\"";
                        echo ">".$v['name']."</option>";
                    }
                    echo "</select>
                    <div id=\"designInfo\"></div>";
                    echo "<script type=\"text/javascript;\">xajax_designInfo(document.getElementById('designSelector').options[document.getElementById('designSelector').selectedIndex].value);</script>";
        echo "</tr>";

        // Bildpacket wählen
        echo "<tr>
                <th class=\"tbldata\" width=\"36%\">Bildpaket w&auml;hlen:</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <select id=\"user_image_select\" name=\"user_image_select\" onchange=\"xajax_imagePackInfo(this.options[this.selectedIndex].value);\">";
                    echo "<option value=\"\">(Selbstdefiniert oder Standard)</option>";
                    foreach ($themes as $k => $v)
                    {
                        echo "<option value=\"$k\"";
                        if (IMAGE_PATH==$k) echo " selected=\"selected\"";
                        echo ">".$v['name']."</option>";
                    }
                    echo "</select> <span id=\"imagePackExtension\"></span><br/>
                    <div id=\"imagePackInfo\"></div>";
                    echo "<script type=\"text/javascript;\">xajax_imagePackInfo(document.getElementById('user_image_select').options[document.getElementById('user_image_select').selectedIndex].value,'".IMAGE_EXT."','".IMAGE_PATH."');</script>";
             echo "</td>";
        echo "</tr>";

        //Spielgrösse
        echo "<tr>
                <th class=\"tbldata\" width=\"36%\">Spielgr&ouml;sse: (nur alte Designs)</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <select name=\"user_game_width\">";
                    for ($x=70;$x<=100;$x+=10)
                    {
                        echo "<option value=\"$x\"";
                        if ($cu->game_width==$x) echo " selected=\"selected\"";
                        echo ">".$x."%</option>";
                    }
                    echo "</select> <span ".tm("Info","Das Spiel wurde optimiert f&uuml;r eine Aufl&ouml;sung von 1280*1024 Pixeln! Wenn du diese besitzt empfiehlt es sich bei den Classic Designs (Blue und Dark) eine Spielgr&ouml;sse von 80% zu w&auml;hlen. Bei einer kleineren Aufl&ouml;sung empfiehlt es sich eine Spielgr&ouml;sse von 100% einzustellen!",1)."><u>Info</u></span>
                </td>
             </tr>";

        //Planetkreisgrösse
        echo "<tr>
                <th class=\"tbldata\" width=\"36%\">Planetkreisgr&ouml;sse:</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                  <select name=\"user_planet_circle_width\">";
                  for ($x=450;$x<=700;$x+=50)
                  {
                      echo "<option value=\"$x\"";
                      if ($cu->planet_circle_width==$x) echo " selected=\"selected\"";
                      echo ">".$x."</option>";
                  }
                echo "</select> <span ".tm("Info","Mit dieser Option l&auml;sst sich die gr&ouml;sse des Planetkreises in der &Uuml;bersicht einstellen.<br>Je nach Aufl&ouml;sung die du verwendest ist es beispielsweise nicht m&ouml;glich eine Gr&ouml;sse von 700 Pixeln zu haben. Finde selber heraus welche Gr&ouml;sse am besten Aussieht.",1)."><u>Info</u></span>
                </td>
            </tr>";
	
				//Schiff/Def Ansicht (Einfach/Voll)
        echo "<tr>
            		<th class=\"tbldata\" width=\"36%\">Schiff/Def Ansicht:</th>";
          echo "<td class=\"tbldata\" width=\"16%\">
          				<input type=\"radio\" name=\"user_item_show\" value=\"full\"";
          				if($arr['user_item_show']=='full') echo " checked=\"checked\"";
          				echo " /> Volle Ansicht 
          			</td>
          			<td class=\"tbldata\" width=\"48%\" colspan=\"3\">
           				<input type=\"radio\" name=\"user_item_show\" value=\"small\"";
          				if($arr['user_item_show']=='small') echo " checked=\"checked\"";
          				echo " /> Einfache Ansicht
           			</td>";
        echo "</tr>";


				//Bildfilter (An/Aus)
        echo "<tr>
            		<th class=\"tbldata\" width=\"36%\">Bildfilter:</th>";
          echo "<td class=\"tbldata\" width=\"16%\">
          				<input type=\"radio\" name=\"user_image_filter\" value=\"1\"";
          				if($arr['user_image_filter']==1) echo " checked=\"checked\"";
          				echo "/> An  
          			</td>
          			<td class=\"tbldata\" width=\"48%\" colspan=\"3\">
          				<input type=\"radio\" name=\"user_image_filter\" value=\"0\"";
          				if($arr['user_image_filter']==0) echo " checked=\"checked\"";
          				echo "/> Aus
          			</td>";
       	echo "</tr>";
            	
					//Hilfefenster (Aktiviert/Deaktiviert)
          echo "<tr>
            			<th class=\"tbldata\" width=\"36%\">Separates Hilfefenster:</th>
            			<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_helpbox\" value=\"1\" ";
                      if ($arr['user_helpbox']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td class=\"tbldata\" width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"user_helpbox\" value=\"0\" ";
                      if ($arr['user_helpbox']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";            
            
					//Notizbox (Aktiviert/Deaktiviert)
          echo "<tr>
            			<th class=\"tbldata\" width=\"36%\">Separater Notizbox:</th>
            			<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_notebox\" value=\"1\" ";
                      if ($arr['user_notebox']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td class=\"tbldata\" width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"user_notebox\" value=\"0\" ";
                      if ($arr['user_notebox']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";   
          		
					// Hafen Buttons
          echo "<tr>
            			<th class=\"tbldata\" width=\"36%\">Vertausche Buttons in Hafen-Schiffauswahl:</th>
            			<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_havenships_buttons\" value=\"1\" ";
                      if ($arr['user_havenships_buttons']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td class=\"tbldata\" width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"user_havenships_buttons\" value=\"0\" ";
                      if ($arr['user_havenships_buttons']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";     
          		
					// Werbebanner
          echo "<tr>
            			<th class=\"tbldata\" width=\"36%\">Werbung anzeigen:</th>
            			<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_show_adds\" value=\"1\" ";
                      if ($arr['user_show_adds']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td class=\"tbldata\" width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"user_show_adds\" value=\"0\" ";
                      if ($arr['user_show_adds']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";           		         
            infobox_end(1);

            echo "<input type=\"submit\" name=\"data_submit_design\" value=\"&Uuml;bernehmen\"></form><br/><br/>";


            infobox_start("Bildpakete herunterladen",1);
            $cnt=0;
            echo '<tr>
            <th class="tbltitle">Bildpaket</th>
            <th class="tbltitle">Datei</th>
            <th class="tbltitle">Autor</th>
            <th class="tbltitle">Grösse</th>
            <th class="tbltitle">Letzte Änderung</th></tr>';
            foreach ($themes as $k => $v)
            {
            	if (count($v['files'])>0)
            	{
	            	foreach ($v['files'] as $file)
	            	{
		           		$path = IMAGEPACK_DOWNLOAD_DIRECTORY."/".$file;
	            		if (is_file($path))
	            		{
	            			$cnt++;
		            		$fs = filesize($path);
		            		$t = filemtime($path);
		                echo "<tr>
		                <td class=\"tbldata\">".$v['name']."</td>
		                <td class=\"tbldata\"><a href=\"".$path."\">".$file."</a></td>
		                <td class=\"tbldata\"><a href=\"mailto:".$v['email']."\">".$v['author']."</a></td>
		                <td class=\"tbldata\">".byte_format($fs)."</td>
		                <td class=\"tbldata\">".df($t)."</td>
		                </tr>";
		            	}
								}
							}
            }
            if ($cnt==0)
            {
            	echo '<tr><td colspan="5" class="tbldata"><i>Keine Downloads vorhanden!</i></tr>';
            }
            infobox_end(1);


?>