<?php
	echo "<h1>Berichte</h1>";

	echo "<ul class=\"horizMenu\">
	<li>Typ: <a href=\"?page=$page\">Neuste</a>";
	foreach (Report::$types as $k=>$v)
	{
		echo "<li><a href=\"?page=$page&amp;type=$k\">$v</a></li>";
	}
	echo "</ul>";

	$type = isset($_GET['type']) ? $_GET['type'] : 'all';

	if ($type=="market")
	{
		$reports = Report::find(array("type"=>$type,"user_id"=>$cu->id),"timestamp DESC");

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
		$reports = Report::find(array("user_id"=>$cu->id),"timestamp DESC","10");

		tableStart("Neueste Berichte");
		echo "<tr>
		<th style=\"width:100px;\">Kategorie:</th>
		<th>Betreff:</th>
		<th style=\"width:150px\">Datum:</th>
		</tr>";

		foreach ($reports as $rid => $r)
		{
			echo "<tr>
			<td><b>".$r->typeName()."</b></td>
			<td>".$r->subject."</td>
			<td>".df($r->timestamp)."</td></tr>";

		}
		tableEnd();
	}
?>
