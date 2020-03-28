<?PHP
  echo "<h1>Universum</h1>";
  
  //
  // Universum erstellen
  //
  if (isset($_POST['submit_create_universe']))
  {
    echo "<h2>Urknall - Schritt 2/3</h2>";
    $cfg->set("num_of_sectors","",$_POST['num_of_sectors_p1'],$_POST['num_of_sectors_p2']);
    $cfg->set("num_of_cells","",$_POST['num_of_cells_p1'],$_POST['num_of_cells_p2']);
    $cfg->set("space_percent_solsys",intval($_POST['space_percent_solsys']));
    $cfg->set("space_percent_asteroids",intval($_POST['space_percent_asteroids']));
    $cfg->set("space_percent_nebulas",intval($_POST['space_percent_nebulas']));
    $cfg->set("space_percent_wormholes",intval($_POST['space_percent_wormholes']));
    $cfg->set("persistent_wormholes_ratio",max(0, min(100, intval($_POST['persistent_wormholes_ratio']))));
    $cfg->set("num_planets","",$_POST['num_planets_p1'],$_POST['num_planets_p2']);
    $cfg->set("solsys_percent_planet",intval($_POST['solsys_percent_planet']));
    $cfg->set("solsys_percent_asteroids",intval($_POST['solsys_percent_asteroids']));
    $cfg->set("planet_fields","",$_POST['planet_fields_p1'],$_POST['planet_fields_p2']);

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    tableStart("Systfemanordnung",400);
    $xdim = ($cfg->num_of_sectors->p1*$cfg->num_of_cells->p1);
    $ydim = ($cfg->num_of_sectors->p2*$cfg->num_of_cells->p2);
    
    echo "<tr>
      <th>Dimension:</th>
      <td>".$xdim."x".$ydim." Zellen</td>
    </tr>";
    echo "<tr>
      <th>Karte:</th>
      <td>";
      echo "<input type=\"radio\" name=\"map_image\" value=\"\" checked=\"checked\" /> <img style=\"width:".$xdim."px;height:".$ydim."px;\" src=\"../images/galaxylayout_random.png\" /> Zufällig";
      $dir = "../images/galaxylayouts";
      $d = opendir($dir);
      while ($f = readdir($d))
      {
        if (is_file($dir.DIRECTORY_SEPARATOR.$f) && substr($f,strrpos($f,".png"))==".png" && $ims = getimagesize($dir.DIRECTORY_SEPARATOR.$f))
        {
          if ($ims[0]==$xdim && $ims[1]==$ydim)
          {
            echo "<div><input type=\"radio\" name=\"map_image\" value=\"$f\" /> <img src=\"".$dir."/".$f."\" alt=\"".$dir."/".$f."\" /> ".basename($f,".png")."	</div>";							
          }
        }
      }			
      echo "</td>
    </tr>";
    echo "<tr>
      <th>Genauigkeit:</th>
      <td><input type=\"text\" name=\"map_precision\" value=\"95\" size=\"2\" maxlength=\"3\"/>%</td>
    </tr>";
    tableEnd();
    
    echo button("Zurück","?page=$page&amp;sub=$sub")." &nbsp; <input onclick=\"return confirm('Universum wirklich erstellen?')\" type=\"submit\" name=\"submit_create_universe2\" value=\"Weiter\" >";
    echo "</form>";
  }
  
  // Erweitern
  elseif(isset($_POST['submit_expansion_universe']))
  {
    echo "<h2>Universum erweitern</h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "<b>Universum (".$conf['num_of_sectors']['p1']."x".$conf['num_of_sectors']['p2'].") erweitern</b><br><br>";
    echo "Erweitere das Universum. Es werden dabei die bereits gespeicherten Daten &uuml;bernommen bez&uuml;glich der der Aufteilung von Planeten, Sonnensystemen, Gasplaneten, Wurml&ouml;chern etc. &Auml;ndere allenfals die Daten unter dem Link \"Universum\".<br><br>";

    echo "Gr&ouml;sse nach dem Ausbau: ";
    //erstellt 2 auswahllisten für die ausbaugrösse
        echo "<select name=\"expansion_sector_x\">";
        for ($x=($conf['num_of_sectors']['p1']+1);10>=$x;$x++)
        {
                echo "<option value=\"$x\">$x</option>";
        }
        echo "</select>";
        echo " x ";
        echo "<select name=\"expansion_sector_y\">";
        for ($x=($conf['num_of_sectors']['p2']+1);10>=$x;$x++)
        {
                echo "<option value=\"$x\">$x</option>";
        }
        echo "</select>";
        echo "<br>";

    echo "<input onclick=\"return confirm('Universum wirklich erweitern?')\" type=\"submit\" name=\"submit_expansion_universe2\" value=\"Erweitern\" >";
    echo "</form>";
  }
  
  // Reset
  elseif (isset($_POST['submit_reset']))
  {
    echo "<h2>Runde zur&uuml;cksetzen</h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "Runde wirklich zur&uuml;cksetzen?<br/><br/>";
    echo "<input onclick=\"return confirm('Reset wirklich durchf&uuml;hren?')\" type=\"submit\" name=\"submit_reset2\" value=\"Ja, die gesamte Runde zur&uuml;cksetzen\" >";
    echo "</form>";
  }
  
  elseif (isset($_POST['submit_galaxy_reset']))
  {
    Universe::reset(false);
    echo "Das Universum wurde zurückgesetzt!<br/><br/>".button("Weiter","?page=$page&amp;sub=$sub");
  }
  
  elseif(isset($_POST['submit_reset2']))
  {
    Universe::reset();
    echo "Die Runde wurde zurückgesetzt!<br/><br/>".button("Weiter","?page=$page&amp;sub=$sub");
  }

  elseif (isset($_POST['submit_addstars']))
  {
    $n = (int)$_POST['number_of_stars'];
    if ($n < 0)
    {
      $n = 0;
    }
    echo Universe::addStarSystems($n);
    echo " Sternensysteme wurden hinzugefügt!<br/><br/>".button("Weiter","?page=$page&amp;sub=$sub");
  }

  // Uni-Optionen
  else
  {
    if(isset($_POST['submit_create_universe2']))
    {
      echo "<h2>Urknall - Schritt 3/3</h2>";
      Universe::create($_POST['map_image'],$_POST['map_precision']);
      echo "<br/><br/>
      <img src=\"../misc/map.image.php?req_admin\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/><br/><br/>
      <input type=\"button\" value=\"Weiter\" onclick=\"document.location='?page=$page&sub=uni'\" />";
    }
    else
    {

      // Check if universe exists
      $res = dbquery("SELECT COUNT(id) FROM cells;");
      $arr = mysql_fetch_row($res);
      if ($arr[0]==0)
      {
        echo "<h2>Urknall - Schritt 1/3</h2>";
        echo "Das Universum existiert noch nicht, bitte prüfe die Einstellungen und klicke auf 'Weiter':<br/><br/>";

        ?>
        <script type="text/javascript">
          function alignSystemPercentage()
          {
            sum = parseInt(document.getElementById('space_percent_solsys').value)+
            parseInt(document.getElementById('space_percent_asteroids').value)+
            parseInt(document.getElementById('space_percent_nebulas').value)+
            parseInt(document.getElementById('space_percent_wormholes').value);
            res = 100 - sum
            document.getElementById('space_percent_empty').value = res.toString();
            if (res < 0 || res > 100)
              document.getElementById('space_percent_empty').style.color = "red";
            else
              document.getElementById('space_percent_empty').style.color = "";
          }
          function alignObjectsInSystemPercentage()
          {
            sum = parseInt(document.getElementById('solsys_percent_planet').value)+
            parseInt(document.getElementById('solsys_percent_asteroids').value);
            res = 100 - sum
            document.getElementById('solsys_percent_empty').value = res.toString();
            if (res < 0 || res > 100)
              document.getElementById('solsys_percent_empty').style.color = "red";
            else
              document.getElementById('solsys_percent_empty').style.color = "";
          }				
        </script>						
        <?PHP
        
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
        echo "Alle Einstellungen werden von der <a href=\"?page=config&sub=editor&category=3\">Konfiguration</a> &uuml;bernommen!<br/><br/>";
        
        tableStart("Galaxie",420);			
        echo "<tr>
          <th>Sektoren:</th>
          <td>
            <input type=\"text\" name=\"num_of_sectors_p1\" value=\"".$cfg->num_of_sectors->p1."\" size=\"2\" maxlength=\"2\" />x
            <input type=\"text\" name=\"num_of_sectors_p2\" value=\"".$cfg->num_of_sectors->p2."\" size=\"2\" maxlength=\"2\" />
          </td></tr>";
        echo "<tr>
          <th>Anzahl Zellen pro Sektor:</th>
          <td>
            <input type=\"text\" name=\"num_of_cells_p1\" value=\"".$cfg->num_of_cells->p1."\" size=\"2\" maxlength=\"2\" />x
            <input type=\"text\" name=\"num_of_cells_p2\" value=\"".$cfg->num_of_cells->p2."\" size=\"2\" maxlength=\"2\" />
          </td></tr>";
        echo "</table>";
        
        tableStart("Verteilung der Systeme",420);				
        echo "<tr>
          <th>Sternensysteme:</th>
          <td><input type=\"text\" name=\"space_percent_solsys\" id=\"space_percent_solsys\" value=\"".$cfg->space_percent_solsys."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
        </tr>";
        echo "<tr>
          <th>Asteroidenfelder:</th>
          <td><input type=\"text\" name=\"space_percent_asteroids\" id=\"space_percent_asteroids\" value=\"".$cfg->space_percent_asteroids."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
        </tr>";
        echo "<tr>
          <th>Nebelwolken:</th>
          <td><input type=\"text\" name=\"space_percent_nebulas\" id=\"space_percent_nebulas\" value=\"".$cfg->space_percent_nebulas."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
        </tr>";
        echo "<tr>
          <th>Wurmlöcher:</th>
          <td><input type=\"text\" name=\"space_percent_wormholes\" id=\"space_percent_wormholes\" value=\"".$cfg->space_percent_wormholes."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%
		  davon <input type=\"text\" name=\"persistent_wormholes_ratio\" id=\"persistent_wormholes_ratio\" value=\"".$cfg->persistent_wormholes_ratio."\" size=\"2\" maxlength=\"2\" />% persistent
		  </td>
        </tr>";
        echo "<tr>
          <th>Leerer Raum:</th>
          <td><input type=\"text\" id=\"space_percent_empty\" value=\"\" size=\"2\" maxlength=\"2\" readonly=\"readonly\"/>%</td>
        </tr>";
        echo "</table>";			
        
        tableStart("Sternensystem",420);				
        echo "<tr>
          <th>Objekte pro Sternensystem:</th>
          <td><input type=\"text\" name=\"num_planets_p1\" value=\"".$cfg->num_planets->p1."\" size=\"2\" maxlength=\"2\" /> min, 
              <input type=\"text\" name=\"num_planets_p2\" value=\"".$cfg->num_planets->p2."\" size=\"2\" maxlength=\"2\" /> max
          </td></tr>";
        echo "<tr>
          <th>Planeten:</th>
          <td><input type=\"text\" name=\"solsys_percent_planet\" id=\"solsys_percent_planet\" value=\"".$cfg->solsys_percent_planet."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignObjectsInSystemPercentage()\" />%</td>
        </tr>";
        echo "<tr>
          <th>Asteroidenfelder:</th>
          <td><input type=\"text\" name=\"solsys_percent_asteroids\" id=\"solsys_percent_asteroids\" value=\"".$cfg->solsys_percent_asteroids."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignObjectsInSystemPercentage()\" />%</td>
        </tr>";
        echo "<tr>
          <th>Leerer Raum:</th>
          <td><input type=\"text\" id=\"solsys_percent_empty\" value=\"\" size=\"2\" maxlength=\"2\" readonly=\"readonly\" />%</td>
        </tr>";				
          
        echo "</table>";
  
        tableStart("Planeten",420);				
        echo "<tr>
          <th>Felder pro Planet:</th>
          <td>
            <input type=\"text\" name=\"planet_fields_p1\" value=\"".$cfg->planet_fields->p1."\" size=\"2\" maxlength=\"2\" /> min, 
            <input type=\"text\" name=\"planet_fields_p2\" value=\"".$cfg->planet_fields->p2."\" size=\"2\" maxlength=\"2\" /> max
          </td>
        </tr>";
        echo "</table>";
  
        echo "<script type=\"text/javascript\">
          alignSystemPercentage();
          alignObjectsInSystemPercentage();
        </script>";	        
        
        echo "<br/><input type=\"submit\" name=\"submit_create_universe\" value=\"Weiter\" >";
        echo "</form><br/>";
      }
      else
      {
        echo "<h2>&Uuml;bersicht</h2>";

        tableStart("Informationen", GALAXY_MAP_WIDTH);
        $res = dbquery("SELECT MAX(sx),MAX(sy) FROM cells;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Sektoren</th><td>".$arr[0]." x ".$arr[1]."</td></tr>";
        $res = dbquery("SELECT MAX(cx),MAX(cy) FROM cells;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Zellen pro Sektor</th><td>".$arr[0]." x ".$arr[1]."</td></tr>";

        $res = dbquery("SELECT COUNT(*) FROM stars;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Sterne</th><td>".nf($arr[0])."</td></tr>";
        $res = dbquery("SELECT COUNT(*) FROM planets;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Planeten</th><td>".nf($arr[0])."</td></tr>";
        $res = dbquery("SELECT COUNT(*) FROM asteroids;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Asteroidenfelder</th><td>".nf($arr[0])."</td></tr>";
        $res = dbquery("SELECT COUNT(*) FROM nebulas;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Nebel</th><td>".nf($arr[0])."</td></tr>";
        $res = dbquery("SELECT COUNT(*) FROM wormholes;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Wurmlöcher</th><td>".nf($arr[0])."</td></tr>";
        $res = dbquery("SELECT COUNT(*) FROM space;");
        $arr = mysql_fetch_row($res);
        echo "<tr><th>Leerer Raum</th><td>".nf($arr[0])."</td></tr>";
        
        tableEnd();
        
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";

        echo "<h3>Sternensysteme hinzufügen</h3>";
        echo "Hiermit können <input style=\"width:3em\" type=\"number\" name=\"number_of_stars\" value=\"0\" > Sternensysteme hinzugfügt werden.<br/><br/>";
        echo "<input type=\"submit\" name=\"submit_addstars\" value=\"Ja, Sternensysteme hinzufügen\" ><br><br>";

        $res = dbquery("SELECT COUNT(id) FROM planets WHERE planet_user_id>0;");
        $arr = mysql_fetch_row($res);					
        if ($arr[0]==0)
        {
          echo "<h3>Universum löschen</h3>";
          echo "Es sind noch keine Planeten im Besitz von Spielern. Das Universum kann ohne Probleme gelöscht werden.<br/><br/>
          <input type=\"submit\" name=\"submit_galaxy_reset\" value=\"Universum zurücksetzen\" ><br/>";
        }
        else
        {
          echo "<h3>Universum löschen</h3>";
          echo "Es sind bereits Planeten im Besitz von Spielern. Du kannst das Universum zurücksetzen, jedoch werden 
          sämtliche Gebäude, Schiffe, Forschungen etc von den Spielern gelöscht.<br/><br/>
          <input type=\"submit\" name=\"submit_galaxy_reset\" value=\"Universum zurücksetzen\" onclick=\"return confirm('Universum wirklich zurücksetzen? ALLE Einheiten der Spieler werden gelöscht, jedoch keine Spieleraccounts!')\"><br/>";
        }

        // Reset
        echo "<h3>Runde komplett zur&uuml;cksetzen</h3>";
        echo "Hiermit kann die gesamte Runde zurückgesetzt werden (User, Allianzen, Planeten).<br/><br/>";
        echo "<input type=\"submit\" name=\"submit_reset\" value=\"Ja, die gesamte Runde zur&uuml;cksetzen\" ><br><br>";

        echo "</form>";
      }
    }
  }
?>