<?PHP
	
	//Array flags
	
	$kt[1]['n']="Argau";
	$kt[1]['f']="[flag ch-ag]";
	
	$kt[2]['n']="Appenzell Innerrhode";
	$kt[2]['f']="[flag ch-ai]";
	
	$kt[3]['n']="Appenzell Ausserrhoden";
	$kt[3]['f']="[flag ch-ar]";
	
	$kt[4]['n']="Bern";
	$kt[4]['f']="[flag ch-be]";
	
	$kt[5]['n']="Basel Land";
	$kt[5]['f']="[flag ch-bl]";
	
	$kt[6]['n']="Basel Stadt";
	$kt[6]['f']="[flag ch-bs]";
	
	$kt[7]['n']="Graubünden";
	$kt[7]['f']="[flag ch-gr]";
	
	$kt[8]['n']="Jura";
	$kt[8]['f']="[flag ch-ju]";
	
	$kt[9]['n']="Luzern";
	$kt[9]['f']="[flag ch-lu]";
	
	$kt[10]['n']="Nidwalden";
	$kt[10]['f']="[flag ch-nw]";
	
	$kt[11]['n']="Obwalden";
	$kt[11]['f']="[flag ch-ow]";
	
	$kt[12]['n']="Schaffhausen";
	$kt[12]['f']="[flag ch-sh]";
	
	$kt[13]['n']="Schwyz";
	$kt[13]['f']="[flag ch-sz]";
	
	$kt[14]['n']="Solothurn";
	$kt[14]['f']="[flag ch-so]";
	
	$kt[15]['n']="Thurgau";
	$kt[15]['f']="[flag ch-tg]";
	
	$kt[16]['n']="Tessin";
	$kt[16]['f']="[flag ch-ti]";
	
	$kt[17]['n']="Uri";
	$kt[17]['f']="[flag ch-ur]";
	
	$kt[18]['n']="Waadt";
	$kt[18]['f']="[flag ch-vd]";
	
	$kt[19]['n']="Wallis";
	$kt[19]['f']="[flag ch-vs]";
	
	$kt[20]['n']="Zug";
	$kt[20]['f']="[flag ch-zg]";
	
	$kt[21]['n']="Zürich";
	$kt[21]['f']="[flag ch-zh]";
	
	$kt[22]['n']="Genf";
	$kt[22]['f']="[flag ch-ge]";
	
	//Table Flag
	infobox_start("Liste mit den vorhandenen Flaggen",1);
		
		echo "<tr><th class=\"tbltitle\">Kanton</th><th class=\"tbltitle\">BBCode</th><th class=\"tbltitle\">Flagge</th></tr>";
  	
  	foreach($kt as $city)
		{
			echo "<tr><td class=\"tbldata\" style=\"text-align:left\">".$city['n']."</td>";
			echo "<td class=\"tbldata\" style=\"text-align:left\">".$city['f']."</td>";
			echo "<td class=\"tbldata\" style=\"text-align:left\">".text2html($city['f'])."</td></tr>";
		}
	infobox_end(1);

?>