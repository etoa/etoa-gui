<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

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
	* Shows information about the planetar population
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

    $sx_num = $config->param1Int('num_of_sectors');
    $sy_num = $config->param2Int('num_of_sectors');
    $cx_num = $config->param1Int('num_of_cells');
    $cy_num = $config->param2Int('num_of_cells');

	echo '<h1>Galaxie</h1>';
	tableStart("Galaxiekarte");
	echo '<tr><td id="galaxy_map_nav">Anzeigen: <select onchange="document.getElementById(\'img\').src=\'misc/map.image.php\'+this.options[this.selectedIndex].value;">
	<option value="?legend&t='.time().'">Normale Galaxieansicht</option>
	<option value="?legend&type=populated&t='.time().'">Bev&ouml;lkerte Systeme</option>
	<option value="?legend&type=own&t='.time().'">Systeme mit eigenen Planeten</option>
	<option value="?legend&type=alliance&t='.time().'">Systeme mit Allianzplaneten</option>
	</select></td></tr>';
	echo '<tr><td id="galaxy_map_container"><img src="misc/map.image.php?legend" alt="Galaxiekarte" id="img" alt="galaxymap" usemap="#Galaxy" style="border:none;"/></td></tr>';
	tableEnd();

	echo '<map name="Galaxy"><br />';
	$sec_x_size=GALAXY_MAP_WIDTH/$sx_num;
	$sec_y_size=GALAXY_MAP_WIDTH/$sy_num;
	$xcnt=1;
	$ycnt=1;
	for ($x=0;$x<GALAXY_MAP_WIDTH;$x+=$sec_x_size)
	{
	 	$ycnt=1;
		for ($y=0;$y<GALAXY_MAP_WIDTH;$y+=$sec_y_size)
		{
	  	echo '<area shape="rect" coords="'.$x.','.(GALAXY_MAP_WIDTH-$y).','.($x+$sec_x_size).','.(GALAXY_MAP_WIDTH-$y-$sec_y_size).'" href="?page=sector&sx='.$xcnt.'&sy='.$ycnt.'" alt="Sektor '.$xcnt.' / '.$ycnt.'" '.tm("Sektor ".$xcnt." / ".$ycnt."","Klicken um Karte anzuzeigen").'><br />';
	  	$ycnt++;
	  }
	  $xcnt++;
	}
	echo '</map>';

?>
