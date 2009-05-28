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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	// $Author$
	// $Date$
	// $Rev$
	//

	define("REPORT_LIMIT",20);

	echo "<h1>Berichte</h1>";

/*
	echo "<ul class=\"horizMenu\">
	<li>Typ: <a href=\"?page=$page\">Neuste</a>";
	foreach (Report::$types as $k=>$v)
	{
		echo "<li><a href=\"?page=$page&amp;type=$k\">$v</a></li>";
	}
	echo "</ul>"; */

	// Show navigation
	$tabitems = array("all"=>"Neuste Berichte");
	foreach (Report::$types as $k=>$v)
	{
		$tabitems[$k] = $v;
	}
	show_tab_menu("type",$tabitems);

	// Detect report type
	$type = isset($_GET['type']) ? $_GET['type'] : 'all';

	// Limit for pagination
	$limit =  (isset($_GET['limit'])) ? intval($_GET['limit']) : 0;
	$limit-= $limit%REPORT_LIMIT;
	$limitstr = $limit.",".REPORT_LIMIT;

	// Load all reports
	if ($type == "all")
	{
		$reports = Report::find(array("user_id"=>$cu->id),"timestamp DESC",$limitstr);
		$totalReports = Report::find(array("user_id"=>$cu->id),"timestamp DESC","",1);
	}
	else
	{
		$reports = Report::find(array("type"=>$type,"user_id"=>$cu->id),"timestamp DESC",$limitstr);
		$totalReports = Report::find(array("type"=>$type,"user_id"=>$cu->id),"timestamp DESC","",1);
	}

	// Check if reports available
	if (count($reports)>0)
	{
		// Table title
		if ($type == "all")
			tableStart("Neueste Berichte");
		else
			tableStart(Report::$types[$type]."berichte");

		// Pagination navigation
		echo "<tr><th colspan=\"4\">";
		echo "<div style=\"float:right;\">";
		if ($limit>0)
		{
			echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=0'\" /> ";
			echo "<input type=\"button\" value=\"&lt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=".($limit-REPORT_LIMIT)."'\" /> ";
		}
		echo " ".$limit."-".($limit+REPORT_LIMIT)." ";
		if ($limit+REPORT_LIMIT<$totalReports)
		{
			echo "<input type=\"button\" value=\"&gt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=".($limit+REPORT_LIMIT)."'\" /> ";
			echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=".($totalReports-($totalReports%REPORT_LIMIT))."'\" /> ";
			echo "</div></th></tr>";
		}

		// Table header
		echo "<tr>
		<th colspan=\"2\">Nachricht:</th>";
		if ($type == "all")
			echo "<th style=\"width:100px;\">Kategorie:</th>";
		echo "<th style=\"width:150px\">Datum:</th>
		</tr>";

		// Iterate through each report
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
