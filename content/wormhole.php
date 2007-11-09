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
	// 	File: wormhole.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.03.2006
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Information about a wormhole
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


$res = dbquery("SELECT * FROM ".$db_table['space_cells']." WHERE cell_wormhole_id='".intval($_GET['id'])."' OR cell_id='".intval($_GET['id'])."';");
$arr1 = mysql_fetch_array($res);
$arr2 = mysql_fetch_array($res);
$coords1= $arr1['cell_sx']."/".$arr1['cell_sy']." : ".$arr1['cell_cx']."/".$arr1['cell_cy'];
$coords2= $arr2['cell_sx']."/".$arr2['cell_sy']." : ".$arr2['cell_cx']."/".$arr2['cell_cy'];
$sx1=$arr1['cell_sx'];
$sx2=$arr2['cell_sx'];
$sy1=$arr1['cell_sy'];
$sy2=$arr2['cell_sy'];


echo "<p>Dieses Wurmloch stellt eine Verbindung zwischen <a href=\"?page=space&sx=$sx1&sy=$sy1\">$coords1</a> und <a href=\"?page=space&sx=$sx2&sy=$sy2\">$coords2</a> her!</p>";

?>