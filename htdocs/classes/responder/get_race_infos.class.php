<?PHP
class GetRaceInfosJsonResponder extends JsonResponder 
{
  function getRequiredParams() {
    return array('id');
  }

  function getResponse($params) {
    
    $data = array();
        
	$ttm = new TutorialManager();

	defineImagePaths();
	
	$val = $params['id'];
	
	if ($val > 0)
	{
		$res=dbQuerySave("
			SELECT 
				* 
			FROM 
				races 
			WHERE 
				race_id=?
		;", array($val));
		
		if ($arr=mysql_fetch_array($res)) {
		
			ob_start();
			
			echo text2html($arr['race_comment'])."<br/><br/>";
			tableStart('',300);
			echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
			if ($arr['race_f_metal']!=1)
			{
				echo "<tr><th>".RES_ICON_METAL."Produktion von ".RES_METAL.":</td><td>".get_percent_string($arr['race_f_metal'],1)."</td></tr>";
			}
			if ($arr['race_f_crystal']!=1)
			{
				echo "<tr><th>".RES_ICON_CRYSTAL."Produktion von ".RES_CRYSTAL.":</td><td>".get_percent_string($arr['race_f_crystal'],1)."</td></tr>";
			}
			if ($arr['race_f_plastic']!=1)
			{
				echo "<tr><th>".RES_ICON_PLASTIC."Produktion von ".RES_PLASTIC.":</td><td>".get_percent_string($arr['race_f_plastic'],1)."</td></tr>";
			}
			if ($arr['race_f_fuel']!=1)
			{
				echo "<tr><th>".RES_ICON_FUEL."Produktion von ".RES_FUEL.":</td><td>".get_percent_string($arr['race_f_fuel'],1)."</td></tr>";
			}
			if ($arr['race_f_food']!=1)
			{
				echo "<tr><th>".RES_ICON_FOOD."Produktion von ".RES_FOOD.":</td><td>".get_percent_string($arr['race_f_food'],1)."</td></tr>";
			}
			if ($arr['race_f_power']!=1)
			{
				echo "<tr><th>".RES_ICON_POWER."Produktion von Energie:</td><td>".get_percent_string($arr['race_f_power'],1)."</td></tr>";
			}
			if ($arr['race_f_population']!=1)
			{
				echo "<tr><th>".RES_ICON_PEOPLE."Bevölkerungswachstum:</td><td>".get_percent_string($arr['race_f_population'],1)."</td></tr>";
			}
			if ($arr['race_f_researchtime']!=1)
			{
				echo "<tr><th>".RES_ICON_TIME."Forschungszeit:</td><td>".get_percent_string($arr['race_f_researchtime'],1,1)."</td></tr>";
			}
			if ($arr['race_f_buildtime']!=1)
			{
				echo "<tr><th>".RES_ICON_TIME."Bauzeit:</td><td>".get_percent_string($arr['race_f_buildtime'],1,1)."</td></tr>";
			}
			if ($arr['race_f_fleettime']!=1)
			{
				echo "<tr><th>".RES_ICON_TIME."Fluggeschwindigkeit:</td><td>".get_percent_string($arr['race_f_fleettime'],1)."</td></tr>";
			}
			tableEnd();
			tableStart('',500);
			
			echo  "<tr><th colspan=\"3\">Spezielle Schiffe:</th></tr>";
			$res=dbquery("
			SELECT 
				ship_id
			FROM 
				ships 
			WHERE 
			ship_race_id='".$val."' 
			AND ship_buildable=1 
			AND special_ship=0;");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_array($res))
				{
					$ship = new Ship($arr['ship_id']);
					echo "<tr><td style=\"background:black;\"><img src=\"".$ship->imgPath()."\" style=\"width:40px;height:40px;border:none;\" alt=\"ship".$ship->id."\" /></td>
					<th style=\"width:180px;\">".text2html($ship->name)."</th>
					<td>".text2html($ship->shortComment)."</td></tr>";
				}
			}
			else
				echo "<tr><td colspan=\"3\">Keine Rassenschiffe vorhanden</td></tr>";
			
			tableEnd();
			tableStart('',500);
			echo  "<tr><th colspan=\"3\">Spezielle Verteidigung:</th></tr>";
			$res=dbquery("
			SELECT 
				def_id,
				def_name,
				def_shortcomment 
			FROM 
				defense 
			WHERE 
			def_race_id='".$val."' 
			AND def_buildable=1;");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_array($res))
				{
			  $s_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id']."_small.".IMAGE_EXT;
					echo "<tr><td style=\"background:black;\"><img src=\"".$s_img."\" style=\"width:40px;height:40px;border:none;\" alt=\"def".$arr['def_id']."\" /></td>
					<th style=\"width:180px;\">".text2html($arr['def_name'])."</th>
					<td>".text2html($arr['def_shortcomment'])."</td></tr>";
				}
			}
			else
				echo "<tr><td colspan=\"3\">Keine Rassenverteidigung vorhanden</td></tr>";
			
				
			tableEnd();
			
			$data['content'] = ob_get_clean();
		}
	}

    return $data;
  }
}
?>