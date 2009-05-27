<?php
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

	if ($type!="all")
	{
		$reports = Report::find(array("type"=>$type,"user_id"=>$cu->id),"timestamp DESC");

		if (count($reports)>0)
		{
			tableStart("Marktberichte");
			echo "<tr>
			<th colspan=\"2\">Nachricht:</th>
			<th>Datum:</th>
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
				echo "<tr>";
				echo "<td style=\"width:16px\"><img src=\"".$im_path."\" alt=\"Mail\" id=\"repimg".$rid."\" /></td>";
				echo "<td>";
				echo "<a href=\"javascript:;\" onclick=\"toggleBox('report".$rid."');xajax_reportSetRead(".$rid.")\" >".$r->subject."</a>";
				echo "</td>
				<td>".df($r->timestamp)."</td>
				</tr>";
				echo "<tr><td colspan=\"3\" style=\"padding:10px;display:none;\" id=\"report".$rid."\">";
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
	}
	else
	{
		$reports = Report::find(array("user_id"=>$cu->id),"timestamp DESC");

		tableStart("Neueste Berichte");
		echo "<tr>
		<th colspan=\"2\">Nachricht:</th>
		<th style=\"width:100px;\">Kategorie:</th>
		<th style=\"width:150px\">Datum:</th>
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
			<td><a href=\"javascript:;\" onclick=\"toggleBox('report".$rid."');xajax_reportSetRead(".$rid.")\" >".$r->subject."</a></td>
			<td><b>".$r->typeName()."</b></td>
			<td>".df($r->timestamp)."</td></tr>";
			echo "<tr><td colspan=\"4\" style=\"padding:10px;display:none;\" id=\"report".$rid."\">";
			echo $r;
			echo "</td>";
			echo "</tr>";
		}
		tableEnd();
	}
?>
