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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: population.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about the planetar population
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	define('GALAXY_MAP_DOT_RADIUS',3);
	define('GALAXY_MAP_WIDTH',500);
	define('GALAXY_MAP_LEGEND_HEIGHT',40);

	$sx_num = $cfg->param1('num_of_sectors');
	$sy_num = $cfg->param2('num_of_sectors');
	$cx_num = $cfg->param1('num_of_cells');
	$cy_num = $cfg->param2('num_of_cells');
	
	echo '<h1>Galaxie</h1>';
	iBoxStart("Karte",GALAXY_MAP_WIDTH+20);
	echo 'Anzeigen: <select onchange="document.getElementById(\'img\').src=\'misc/map.image.php\'+this.options[this.selectedIndex].value;">
	<option value="?t='.time().'">Normale Galaxieansicht</option>
	<option value="?type=populated&t='.time().'">Bev&ouml;lkerte Systeme</option>
	<option value="?type=own&t='.time().'">Systeme mit eigenen Planeten</option>
	<option value="?type=alliance&t='.time().'">Systeme mit Allianzplaneten</option>
	</select> &nbsp; <br/><br/>';
	echo '<img src="misc/map.image.php" alt="Galaxiekarte" id="img" alt="galaxymap" usemap="#Galaxy" style="border:none;"/>';
	iBoxEnd();
	
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