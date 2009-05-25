<?php
	echo "<h1>Berichte</h1>";

	echo "<ul class=\"horizMenu\">
	<li>Typ: ";
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
		<th>Datum:</th>
		<th>Raumobjekt:</th>
		<th>Aktivität:</th>
		<th>Waren:</th>
		</tr>";

		foreach ($reports as $rid => $r)
		{
			$ent = Entity::createFactoryById($r->entity1Id);
			echo "<tr>";
			echo "<td>".df($r->timestamp)."</td>";
			echo "<td>".$ent."</td>";
			echo "<td>".$r->subject."<br/>Handel #".$r->recordId."<br/>";
			if ($r->subType == "resadd")
			{
				echo "Gebühr: ".round(($r->factor-1)*100,2)."%";
			}
			if ($r->subType == "rescancel")
			{
				echo "Zurückerstattet: ".round($r->factor*100)."%";
			}
			if ($r->content !="")
				echo "<br/><br/>".$r->content;
			echo "</td>";
			echo "<td style=\"padding:0px;\">";
			echo "<table class=\"tb\" style=\"margin:0px;\">";

			if ($r->subType == "resadd")
			{
				echo "<tr><th>Rohstoff:</th><th>Angebot:</th><th>Preis:</th></tr>";
				foreach ($resNames as $k=>$v)
				{
					echo "<tr>
					<td>".$v."</td>
					<td>".nf($r->resSell[$k])."</td>
					<td>".nf($r->resBuy[$k])."</td>
					</tr>";
				}
			}
			if ($r->subType == "rescancel")
			{
				echo "<tr><th>Rohstoff:</th><th>Angeboten:</th><th>Retour:</th></tr>";
				foreach ($resNames as $k=>$v)
				{
					echo "<tr>
					<td>".$v."</td>
					<td>".nf($r->resSell[$k])."</td>
					<td>".nf($r->resSell[$k]*$r->factor)."</td>
					</tr>";
				}
			}

			echo "</table>";
			echo "</td>";
			echo "</tr>";
		}
		tableEnd();
	}
?>
