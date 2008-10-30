<?PHP
		$themes = get_imagepacks();
		$designs = get_designs();

		//change Theme skript
		echo "<script type=\"text/javascript\">";
		echo "function changeTheme()\n{";
		echo "var id=document.getElementById('image_select').options[document.getElementById('image_select').selectedIndex].value;\n";
		echo "switch (id)\n{";
		foreach ($themes as $k => $v)
		{
			echo "case '$k': document.getElementById('image_url').value='".$k."';\n document.getElementById('image_ext').value='".$v['extensions'][0]."'\n;break;";
		}
		echo "default: document.getElementById('image_url').value='';document.getElementById('image_ext').value='';";
		echo "}\n";
		echo "}\n</script>";
		


				//
        // Daten werden gespeichert
        //
        
        if (isset($_POST['data_submit_design']) && $_POST['data_submit_design']!="")
        {
          //Prüft eingaben auf unerlaube Zeichen
          $check_image = check_illegal_signs($_POST['image_url']);
          $sqla = "";
          if ($check_image=="")
          {
              if($_POST['image_ext']!="" && $_POST['image_url']!="")
              {
                  //Wandelt alle \ (backslash) in / um (Da windows den pfad mit \ angibt!)
                  $grafikpack = str_replace("\\", "/", $_POST['image_url']);
                  $sqla = " image_url='".$grafikpack."',";
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
          
          if ($_POST['image_select']!='')
          {
          	$sqla = " image_url='".$_POST['image_select']."',";
          }
          
          // Ändert die Bildendung, aber nur, wenn ein neues Packet gewählt wurde
          if(isset($_POST['image_ext']))
          {
          	$sqla .= " image_ext='".$_POST['image_ext']."',";
          }
          
          if (dbquery("
          UPDATE
              user_properties
          SET
          		".$sqla."
              css_style='".$_POST['css_style']."',
              game_width='".$_POST['game_width']."',
              planet_circle_width='".$_POST['planet_circle_width']."',
              item_show='".$_POST['item_show']."',
              image_filter='".$_POST['image_filter']."',
              helpbox='".$_POST['helpbox']."',                          
              notebox='".$_POST['notebox']."',
              havenships_buttons='".$_POST['havenships_buttons']."',
              show_adds=".$_POST['show_adds']."                          
          WHERE
              id='".$cu->id()."';")
          )
          {
            success_msg("Design-Daten wurden geändert!");
            $cu->addToUserLog("settings","{nick} ändert das Design.",0);
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
        tableStart("Designoptionen");
        
        //Design wählen
        echo "<tr>
            <th width=\"36%\">Design w&auml;hlen:</th>
            <td width=\"64%\" colspan=\"4\">
                    <select name=\"css_style\" id=\"designSelector\" onchange=\"xajax_designInfo(this.options[this.selectedIndex].value);\">";
                    foreach ($designs as $k => $v)
                    {
                        echo "<option value=\"$k\"";
                        if (CSS_STYLE==DESIGN_DIRECTORY."/".$k) echo " selected=\"selected\"";
                        echo ">".$v['name']."</option>";
                    }
                    echo "</select>
                    <div id=\"designInfo\"></div>";
                    echo "<script type=\"text/javascript;\">xajax_designInfo(document.getElementById('designSelector').options[document.getElementById('designSelector').selectedIndex].value);</script>";
        echo "</tr>";

        // Bildpacket wählen
        echo "<tr>
                <th width=\"36%\">Bildpaket w&auml;hlen:</th>
                <td width=\"64%\" colspan=\"4\">
                    <select id=\"image_select\" name=\"image_select\" onchange=\"xajax_imagePackInfo(this.options[this.selectedIndex].value);\">";
                    echo "<option value=\"\">(Selbstdefiniert oder Standard)</option>";
                    foreach ($themes as $k => $v)
                    {
                        echo "<option value=\"$k\"";
                        if (IMAGE_PATH==$k) echo " selected=\"selected\"";
                        echo ">".$v['name']."</option>";
                    }
                    echo "</select> <span id=\"imagePackExtension\"></span><br/>
                    <div id=\"imagePackInfo\"></div>";
                    echo "<script type=\"text/javascript;\">xajax_imagePackInfo(document.getElementById('image_select').options[document.getElementById('image_select').selectedIndex].value,'".IMAGE_EXT."','".IMAGE_PATH."');</script>";
             echo "</td>";
        echo "</tr>";

        //Spielgrösse
        echo "<tr>
                <th width=\"36%\">Spielgr&ouml;sse: (nur alte Designs)</th>
                <td width=\"64%\" colspan=\"4\">
                    <select name=\"game_width\">";
                    for ($x=70;$x<=100;$x+=10)
                    {
                        echo "<option value=\"$x\"";
                        if ($cu->getp("game_width")==$x) echo " selected=\"selected\"";
                        echo ">".$x."%</option>";
                    }
                    echo "</select> <span ".tm("Info","Das Spiel wurde optimiert f&uuml;r eine Aufl&ouml;sung von 1280*1024 Pixeln! Wenn du diese besitzt empfiehlt es sich bei den Classic Designs (Blue und Dark) eine Spielgr&ouml;sse von 80% zu w&auml;hlen. Bei einer kleineren Aufl&ouml;sung empfiehlt es sich eine Spielgr&ouml;sse von 100% einzustellen!",1)."><u>Info</u></span>
                </td>
             </tr>";

        //Planetkreisgrösse
        echo "<tr>
                <th width=\"36%\">Planetkreisgr&ouml;sse:</th>
                <td width=\"64%\" colspan=\"4\">
                  <select name=\"planet_circle_width\">";
                  for ($x=450;$x<=700;$x+=50)
                  {
                      echo "<option value=\"$x\"";
                      if ($cu->getp("planet_circle_width")==$x) echo " selected=\"selected\"";
                      echo ">".$x."</option>";
                  }
                echo "</select> <span ".tm("Info","Mit dieser Option l&auml;sst sich die gr&ouml;sse des Planetkreises in der &Uuml;bersicht einstellen.<br>Je nach Aufl&ouml;sung die du verwendest ist es beispielsweise nicht m&ouml;glich eine Gr&ouml;sse von 700 Pixeln zu haben. Finde selber heraus welche Gr&ouml;sse am besten Aussieht.",1)."><u>Info</u></span>
                </td>
            </tr>";
	
				//Schiff/Def Ansicht (Einfach/Voll)
        echo "<tr>
            		<th width=\"36%\">Schiff/Def Ansicht:</th>";
          echo "<td width=\"16%\">
          				<input type=\"radio\" name=\"item_show\" value=\"full\"";
          				if($cu->getp("item_show")=='full') echo " checked=\"checked\"";
          				echo " /> Volle Ansicht 
          			</td>
          			<td width=\"48%\" colspan=\"3\">
           				<input type=\"radio\" name=\"item_show\" value=\"small\"";
          				if($cu->getp("item_show")=='small') echo " checked=\"checked\"";
          				echo " /> Einfache Ansicht
           			</td>";
        echo "</tr>";


				//Bildfilter (An/Aus)
        echo "<tr>
            		<th width=\"36%\">Bildfilter:</th>";
          echo "<td width=\"16%\">
          				<input type=\"radio\" name=\"image_filter\" value=\"1\"";
          				if($cu->getp("image_filter")==1) echo " checked=\"checked\"";
          				echo "/> An  
          			</td>
          			<td width=\"48%\" colspan=\"3\">
          				<input type=\"radio\" name=\"image_filter\" value=\"0\"";
          				if($cu->getp("image_filter")==0) echo " checked=\"checked\"";
          				echo "/> Aus
          			</td>";
       	echo "</tr>";
            	
					//Hilfefenster (Aktiviert/Deaktiviert)
          echo "<tr>
            			<th width=\"36%\">Separates Hilfefenster:</th>
            			<td width=\"16%\">
                      <input type=\"radio\" name=\"helpbox\" value=\"1\" ";
                      if ($cu->getp("helpbox")==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"helpbox\" value=\"0\" ";
                      if ($cu->getp("helpbox")==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";            
            
					//Notizbox (Aktiviert/Deaktiviert)
          echo "<tr>
            			<th width=\"36%\">Separater Notizbox:</th>
            			<td width=\"16%\">
                      <input type=\"radio\" name=\"notebox\" value=\"1\" ";
                      if ($cu->getp("notebox")==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"notebox\" value=\"0\" ";
                      if ($cu->getp("notebox")==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";   
          		
					// Hafen Buttons
          echo "<tr>
            			<th width=\"36%\">Vertausche Buttons in Hafen-Schiffauswahl:</th>
            			<td width=\"16%\">
                      <input type=\"radio\" name=\"havenships_buttons\" value=\"1\" ";
                      if ($cu->getp("havenships_buttons")==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"havenships_buttons\" value=\"0\" ";
                      if ($cu->getp("havenships_buttons")==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";     
          		
					// Werbebanner
          echo "<tr>
            			<th width=\"36%\">Werbung anzeigen:</th>
            			<td width=\"16%\">
                      <input type=\"radio\" name=\"show_adds\" value=\"1\" ";
                      if ($cu->getp("show_adds")==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"show_adds\" value=\"0\" ";
                      if ($cu->getp("show_adds")==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";           		         
            tableEnd();

            echo "<input type=\"submit\" name=\"data_submit_design\" value=\"&Uuml;bernehmen\"></form><br/><br/>";


            tableStart("Bildpakete herunterladen");
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
		                <td>".$v['name']."</td>
		                <td><a href=\"".$path."\">".$file."</a></td>
		                <td><a href=\"mailto:".$v['email']."\">".$v['author']."</a></td>
		                <td>".byte_format($fs)."</td>
		                <td>".df($t)."</td>
		                </tr>";
		            	}
								}
							}
            }
            if ($cnt==0)
            {
            	echo '<tr><td colspan="5" class="tbldata"><i>Keine Downloads vorhanden!</i></tr>';
            }
            tableEnd();


?>