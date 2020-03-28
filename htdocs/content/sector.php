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

	/**
	* Space sector map
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	$_SESSION['currentEntity']=serialize($cp);

	// Wenn Planet aktiv, Koordinaten aus der DB lesen
	if (isset($_GET['sector']))
	{
		list($sx,$sy) = explode(",",$_GET['sector']);
	}
  // Coordinates from POST request
	elseif (isset($_POST['sx']) && intval($_POST['sx'])>0 && isset($_POST['sy']) && intval($_POST['sy'])>0)
	{
		$sx	= $_POST['sx'];
		$sy	= $_POST['sy'];
	}
  // Coordinates from GET request
	elseif (isset($_GET['sx']) && intval($_GET['sx'])>0 && isset($_GET['sy']) && intval($_GET['sy'])>0)
	{
		$sx	= $_GET['sx'];
		$sy	= $_GET['sy'];
	}
  // Current Planet
	elseif ($cp)
	{
		$sx = $cp->sx;
		$sy = $cp->sy;
	}
	// Default coordinates (galactic center)
	else
	{
		$sx = $cfg->param1('map_init_sector');
		$sy = $cfg->param2('map_init_sector');
	}

	$sx = intval($sx);
	$sy = intval($sy);

	echo "<h1>Sektor ".$sx."/".$sy."</h1>";

	$sector_pic = "images/map";

  // Load galaxy dimensions
	$sx_num = $cfg->param1('num_of_sectors');
	$sy_num = $cfg->param2('num_of_sectors');

  // Validate coordinates
	if ($sx>$sx_num) {
    $sx = $sx_num;
  }
	if ($sy>$sy_num) {
    $sy = $sy_num;
  }
	if ($sx<1) {
    $sx = 1;
  }
	if ($sy<1) {
    $sy = 1;
  }

  // Determine coordinates of neighbouring sectors
	$sx_tl = $sx-1;
	$sx_tc = $sx;
	$sx_tr = $sx+1;
	$sx_ml = $sx-1;
	$sx_mr = $sx+1;
	$sx_bl = $sx-1;
	$sx_bc = $sx;
	$sx_br = $sx+1;

	$sy_tl = $sy+1;
	$sy_tc = $sy+1;
	$sy_tr = $sy+1;
	$sy_ml = $sy;
	$sy_mr = $sy;
	$sy_bl = $sy-1;
	$sy_bc = $sy-1;
	$sy_br = $sy-1;

	echo "<form action=\"?page=$page\" method=\"post\">";

  tableStart("Sektorkarte");

  // Navigation
	echo "<tr><td id=\"sector_map_nav\">
	<a href=\"?page=galaxy\">Galaxie</a> &raquo; &nbsp;";
	echo "<select name=\"sector\" onchange=\"document.location='?page=$page&sector='+this.value\">";
	for ($x=1;$x<=$sx_num;$x++)
	{
		for ($y=1;$y<=$sy_num;$y++)
		{
			echo "<option value=\"$x,$y\"";
			if ($x==$sx && $y==$sy)
				echo " selected=\"selected\"";
			echo ">Sektor $x/$y &nbsp;</option>";
		}
	}
	echo "</select></td></tr>";


  echo "<tr><td id=\"sector_map_container\">";
  echo "<table id=\"sector_map_table\">";


  // Top row: Buttons to upper sectors
	echo "<tr>";
  echo "<td class=\"sector_map_neighbour_nav\" style=\"width:45px;height:45px;\">";
	if ($sx_tl && $sy_tl!=0 && $sx_tl!=$sx_num+1 && $sy_tl!=$sy_num+1)
	{
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_tl/$sy_tl");
    $tt->addText("Sektor $sx_tl/$sy_tl anzeigen");
		echo "<a href=\"?page=$page&amp;sx=$sx_tl&amp;sy=$sy_tl\" ".$tt.">";
		echo "<img src=\"$sector_pic/sector_topleft.gif\" alt=\"Sektor $sx_tl/$sy_tl\" onmouseover=\"$(this).attr('src','$sector_pic/sector_topleft_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_topleft.gif');\"/></a>";
	}
	else
	{
		echo "&nbsp;";
	}
  echo "</td>";

  echo "<td class=\"sector_map_neighbour_nav\" style=\"height:45px;\">";
	if ($sx_tc && $sy_tc!=0 && $sx_tc!=$sx_num+1 && $sy_tc!=$sy_num+1)
	{
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_tc/$sy_tc");
    $tt->addText("Sektor $sx_tc/$sy_tc anzeigen");
		echo "<a href=\"?page=$page&amp;sx=$sx_tc&amp;sy=$sy_tc\" ".$tt.">";
    echo "<img src=\"$sector_pic/sector_topcenter.gif\" alt=\"Sektor $sx_tc/$sy_tc\" onmouseover=\"$(this).attr('src','$sector_pic/sector_topcenter_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_topcenter.gif');\"/></a>";
	}
	else
	{
    echo "&nbsp;";
	}
  echo "</td>";

  echo "<td class=\"sector_map_neighbour_nav\" style=\"width:45px;height:45px;\">";
	if ($sx_tr && $sy_tr!=0 && $sx_tr!=$sx_num+1 && $sy_tr!=$sy_num+1)
	{
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_tr/$sy_tr");
    $tt->addText("Sektor $sx_tr/$sy_tr anzeigen");
		echo "<a href=\"?page=$page&amp;sx=$sx_tr&amp;sy=$sy_tr\" ".$tt.">";
    echo "<img src=\"$sector_pic/sector_topright.gif\" alt=\"Sektor $sx_tr/$sy_tr\" onmouseover=\"$(this).attr('src','$sector_pic/sector_topright_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_topright.gif');\" /></a>";
	}
	else
	{
		echo "&nbsp;";
	}
  echo "</td>";
	echo "</tr>";

  // Middle row: Map and buttons to left and right sectors
	echo "<tr>";
  echo "<td class=\"sector_map_neighbour_nav\" style=\"width:45px;\">";
	if ($sx_ml && $sy_ml!=0 && $sx_ml!=$sx_num+1 && $sy_ml!=$sy_num+1)
  {
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_ml/$sy_ml");
    $tt->addText("Sektor $sx_ml/$sy_ml anzeigen");
		echo "<a href=\"?page=$page&amp;sx=$sx_ml&amp;sy=$sy_ml\" ".$tt.">";
    echo "<img src=\"$sector_pic/sector_middleleft.gif\" alt=\"Sektor $sx_ml/$sy_ml\" onmouseover=\"$(this).attr('src','$sector_pic/sector_middleleft_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_middleleft.gif');\" /></a>";
	}
	else
	{
		echo "&nbsp;";
	}
  echo "</td>";

  // Map
  echo "<td class=\"sector_map_cell\">";

	// Load user's systems
  $res = dbquery("
  SELECT 
  	cells.id as id
  FROM 
  	planets
  INNER JOIN
  (
  	entities
  	INNER JOIN
  		cells 
  		ON cells.id=entities.cell_id
  )
 	ON entities.id=planets.id
  	AND planet_user_id='".$cu->id."';");
  $user_solsys_ids = array();
  while ($arr = mysql_fetch_row($res))
  {
  	$user_solsys_ids[]=$arr[0];
  }

  $sectorMap = new SectorMapRenderer($cfg->param1('num_of_cells'), $cfg->param2('num_of_cells'));
  $sectorMap->enableRuler(true);
  $sectorMap->enableTooltips(true);
  $sectorMap->setUserCellIDs($user_solsys_ids);
  if (isset($cp)) {
      $sectorMap->setSelectedCell($cp->getCell());
  }
  $sectorMap->setImpersonatedUser($cu);
  $sectorMap->setCellUrl("?page=cell&amp;id=");
  $sectorMap->setUndiscoveredCellJavaScript("xajax_launchExplorerProbe('##ID##')");
  echo $sectorMap->render($sx, $sy);

  echo "</td>";


  echo "<td class=\"sector_map_neighbour_nav\" style=\"width:45px;\">";
	if ($sx_mr && $sy_mr!=0 && $sx_mr!=$sx_num+1 && $sy_mr!=$sy_num+1)
  {
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_mr/$sy_mr");
    $tt->addText("Sektor $sx_mr/$sy_mr anzeigen");
		echo "<a href=\"?page=$page&amp;sx=$sx_mr&amp;sy=$sy_mr\" ".$tt.">";
    echo "<img src=\"$sector_pic/sector_middleright.gif\" alt=\"Sektor $sx_mr/$sy_mr\" onmouseover=\"$(this).attr('src','$sector_pic/sector_middleright_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_middleright.gif');\" /></a>";
	}
  else
  {
		echo "&nbsp;";
	}
  echo "</td>";
  echo "</tr>";


  // Bottom row: Buttons to lower sectors
  echo "<tr>";
  echo "<td class=\"sector_map_neighbour_nav\" style=\"width:45px;height:45px;\">";
  if ($sx_bl && $sy_bl!=0 && $sx_bl!=$sx_num+1 && $sy_bl!=$sy_num+1)
  {
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_bl/$sy_bl");
    $tt->addText("Sektor $sx_bl/$sy_bl anzeigen");
    echo "<a href=\"?page=$page&amp;sx=$sx_bl&amp;sy=$sy_bl\">";
    echo "<img src=\"$sector_pic/sector_bottomleft.gif\" alt=\"Sektor $sx_bl/$sy_bl\" onmouseover=\"$(this).attr('src','$sector_pic/sector_bottomleft_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_bottomleft.gif');\" /></a>";
  }
  else
  {
    echo "&nbsp;";
  }
  echo "</td>";

  echo "<td class=\"sector_map_neighbour_nav\" style=\"height:45px;\">";
  if ($sx_bc && $sy_bc!=0 && $sx_bc!=$sx_num+1 && $sy_bc!=$sy_num+1)
  {
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_bc/$sy_bc");
    $tt->addText("Sektor $sx_bc/$sy_bc anzeigen");
    echo "<a href=\"?page=$page&amp;sx=$sx_bc&amp;sy=$sy_bc\">";
    echo "<img src=\"$sector_pic/sector_bottomcenter.gif\" alt=\"Sektor $sx_bc/$sy_bc\" onmouseover=\"$(this).attr('src','$sector_pic/sector_bottomcenter_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_bottomcenter.gif');\" /></a>";
  }
  else
  {
    echo "&nbsp;";
  }
  echo "</td>";

  echo "<td class=\"sector_map_neighbour_nav\" style=\"width:45px;height:45px;\">";
  if ($sx_br && $sy_br!=0 && $sx_br!=$sx_num+1 && $sy_br!=$sy_num+1)
  {
    $tt = new Tooltip();
    $tt->addTitle("Sektor $sx_br/$sy_br");
    $tt->addText("Sektor $sx_br/$sy_br anzeigen");
    echo "<a href=\"?page=$page&amp;sx=$sx_br&amp;sy=$sy_br\">";
    echo "<img src=\"$sector_pic/sector_bottomright.gif\" alt=\"Sektor $sx_br/$sy_br\" onmouseover=\"$(this).attr('src','$sector_pic/sector_bottomright_On.gif');\" onmouseout=\"$(this).attr('src','$sector_pic/sector_bottomright.gif');\" /></a>";
  }
  else
  {
    echo "&nbsp;";
  }
  echo "</td>";
  echo "</tr>";

  echo "</table></td></tr>";

  echo "<tr><td>Die Galaxie besteht aus $sx_num x $sy_num Sektoren.</td></tr>";

  tableEnd();

	echo "<div id=\"spy_info_box\" style=\"display:none;\">";
	iBoxStart("Flotten");
	echo "<div id=\"spy_info\"></div>";
	iBoxEnd();
	echo "</div>";

  echo "</form>";

?>
