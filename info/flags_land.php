<?PHP	
	
	//Array flags
	
	$fl[1]['n']="Schweiz";
	$fl[1]['f']="[flag ch]";
	
	$fl[2]['n']="Argentinien";  
	$fl[2]['f']="[flag ar]";
	
	$fl[3]['n']="Österreich";
	$fl[3]['f']="[flag at]";
	
	$fl[4]['n']="Australien";
	$fl[4]['f']="[flag au]";
	
	$fl[5]['n']="Beneluxstaaten";
	$fl[5]['f']="[flag benelux]";
	
	$fl[6]['n']="Bulgarien";
	$fl[6]['f']="[flag bg]";
	
	$fl[7]['n']="Brasilien";
	$fl[7]['f']="[flag br]";
	
	$fl[8]['n']="Kanada";
	$fl[8]['f']="[flag ca]";
	
	$fl[9]['n']="China";
	$fl[9]['f']="[flag cn]";
	
	$fl[10]['n']="Tschechien";
	$fl[10]['f']="[flag cz]";
	
	$fl[11]['n']="Deutschland";
	$fl[11]['f']="[flag de]";
	
	$fl[12]['n']="Dänemark";
	$fl[12]['f']="[flag dk]";
	
	$fl[13]['n']="Estland";  
	$fl[13]['f']="[flag ee]"; 
	
	$fl[14]['n']="Europa";  
	$fl[14]['f']="[flag eu]";
	
	$fl[15]['n']="Finnland";  
	$fl[15]['f']="[flag fi]";
	
	$fl[16]['n']="Frankreich";  
	$fl[16]['f']="[flag fr]";
	
	$fl[17]['n']="Grossbritannien";  
	$fl[17]['f']="[flag gb]";
	
	$fl[18]['n']="Griechenland";  
	$fl[18]['f']="[flag gr]";
	
	$fl[19]['n']="Kroatien";  
	$fl[19]['f']="[flag hr]";
	
	$fl[20]['n']="Israel";  
	$fl[20]['f']="[flag il]";
	
	$fl[21]['n']="Indien";  
	$fl[21]['f']="[flag in]";
	
	$fl[22]['n']="Japan";  
	$fl[22]['f']="[flag jp]";
	
	$fl[23]['n']="Südkorea";  
	$fl[23]['f']="[flag kp]";
	
	$fl[24]['n']="Luxemburg";  
	$fl[24]['f']="[flag lu]";
	
	$fl[25]['n']="Lettland";  
	$fl[25]['f']="[flag lv]";
	
	$fl[26]['n']="Niederlande";  
	$fl[26]['f']="[flag nl]";
	
	$fl[27]['n']="Norwegen";  
	$fl[27]['f']="[flag no]";
	
	$fl[28]['n']="Polen";  
	$fl[28]['f']="[flag pl]";
	
	$fl[29]['n']="Russland";  
	$fl[29]['f']="[flag ru]";
	
	$fl[30]['n']="Schweden";  
	$fl[30]['f']="[flag se]";
	
	$fl[31]['n']="Slowakei";  
	$fl[31]['f']="[flag sk]";
	
	$fl[32]['n']="Spanien";  
	$fl[32]['f']="[flag sp]";
	
	$fl[33]['n']="Türkei";  
	$fl[33]['f']="[flag ty]";
	
	$fl[34]['n']="USA";  
	$fl[34]['f']="[flag us]";
	
	$fl[35]['n']="Vatikan";  
	$fl[35]['f']="[flag vn]";
	
	$fl[36]['n']="Welt";  
	$fl[36]['f']="[flag world]";
	
	//Table flags
	infobox_start("Liste mit den vorhandenen Flaggen",1);
		
		echo "<tr><th class=\"tbltitle\">Land</th><th class=\"tbltitle\">BBCode</th><th class=\"tbltitle\">Flagge</th></tr>";
  	
  	foreach($fl as $land)
		{
			echo "<tr><td class=\"tbldata\" style=\"text-align:left\">".$land['n']."</td>";
			echo "<td class=\"tbldata\" style=\"text-align:left\">".$land['f']."</td>";
			echo "<td class=\"tbldata\" style=\"text-align:left\">".text2html($land['f'])."</td></tr>";
		}
	infobox_end(1);
	
?>