<?php
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
	// 	$Id$
	//

	echo "<h1>Berichte</h1>";

/*
	echo "<ul class=\"horizMenu\">
	<li>Typ: <a href=\"?page=$page\">Neuste</a>";
	foreach (Report::$types as $k=>$v)
	{
		echo "<li><a href=\"?page=$page&amp;type=$k\">$v</a></li>";
	}
	echo "</ul>"; */

	$tabitems = array("all"=>"Neuste Berichte");
	foreach (Report::$types as $k=>$v)
	{
		$tabitems[$k] = $v;
	}
	show_tab_menu("type",$tabitems);


	$type = isset($_GET['type']) ? $_GET['type'] : 'all';

	if ($type == "all")
		$reports = Report::find(array("user_id"=>$cu->id),"timestamp DESC");
	else
		$reports = Report::find(array("type"=>$type,"user_id"=>$cu->id),"timestamp DESC");

	if (count($reports)>0)
	{
		if ($type == "all")
			tableStart("Neueste Berichte");
		else
			tableStart(Report::$types[$type]."berichte");
		echo "<tr>
		<th colspan=\"2\">Nachricht:</th>";
		if ($type == "all")
			echo "<th style=\"width:100px;\">Kategorie:</th>";
		echo "<th style=\"width:150px\">Datum:</th>
		</tr>";

		foreach ($reports as $rid => $r)
		{
			if (!$r->read)
			{
				$im_path = "images/pm_new.gif";
			}
			else
			{
				$im_path = "images/pm_normal.gif";
			}
			echo "<tr>
			<td style=\"width:16px\"><img src=\"".$im_path."\" alt=\"Mail\" id=\"repimg".$rid."\" /></td>
			<td><a href=\"javascript:;\" onclick=\"toggleBox('report".$rid."');xajax_reportSetRead(".$rid.")\" >".$r->subject."</a></td>";
			if ($type == "all")
				echo "<td><b>".$r->typeName()."</b></td>";
			echo "<td>".df($r->timestamp)."</td></tr>";
			echo "<tr><td colspan=\"4\" style=\"padding:10px;display:none;\" id=\"report".$rid."\">";
			echo $r;
			echo "</td>";
			echo "</tr>";
		}
		tableEnd();
	}
	else
	{
		err_msg("Keine Berichte vorhanden!");
	}
?>
