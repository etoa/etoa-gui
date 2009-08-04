<?PHP

$xajax->register(XAJAX_FUNCTION,"logSelectorCat");
$xajax->register(XAJAX_FUNCTION,"checkLogFormular");
$xajax->register(XAJAX_FUNCTION,"logChangeButton");
$xajax->register(XAJAX_FUNCTION,"showBattle");

$xajax->register(XAJAX_FUNCTION,"applyLogFilter");
$xajax->register(XAJAX_FUNCTION,"applyGameLogFilter");
$xajax->register(XAJAX_FUNCTION,"applyFleetLogFilter");
$xajax->register(XAJAX_FUNCTION,"applyAttackAbuseLogFilter");

function applyLogFilter($args,$limit=0)
{
	$objResponse = new xajaxResponse();
	require_once("inc/admin_functions.inc.php");
	ob_start();
	showLogs($args,$limit);
	$objResponse->assign("log_contents","innerHTML",ob_get_clean());

	return $objResponse;
}

function applyGameLogFilter($args,$limit=0)
{
	$objResponse = new xajaxResponse();
	require_once("inc/admin_functions.inc.php");
	ob_start();
	showGameLogs($args,$limit);
	$objResponse->assign("log_contents","innerHTML",ob_get_clean());

	return $objResponse;
}

function applyFleetLogFilter($args,$limit=0)
{
	$objResponse = new xajaxResponse();
	require_once("inc/admin_functions.inc.php");
	ob_start();
	showFleetLogs($args,$limit);
	$objResponse->assign("log_contents","innerHTML",ob_get_clean());

	return $objResponse;
}

function applyAttackAbuseLogFilter($args,$limit=0)
{
	$objResponse = new xajaxResponse();
	require_once("inc/admin_functions.inc.php");
	ob_start();
	showAttackAbuseLogs($args,$limit);
	$objResponse->assign("log_contents","innerHTML",ob_get_clean());

	return $objResponse;
}

function logSelectorCat($cat)
{
	
	ob_start();
	$objResponse = new xajaxResponse();
	
	
	// Definiert die "Anzahl Datensätze"
	$limit_options = "<option value=\"10\">10</option>";
	for ($x=100;$x<=2000;$x+=100)
	{
		$limit_options .= "<option value=\"".$x."\">".$x."</option>";
	}
	
	// Definiert die Auswahlbereiche für normale Zahlenfelder
	$int_options = "<option value=\"\">Alle</option>";
	$int_options .= "<option value=\"=0\">0</option>";
	for ($x=0;$x<=8;$x++)
	{
		$min = pow(10,$x);
		$max = pow(10,$x+1);
		$int_options .= "<option value=\"BETWEEN ".$min." AND ".$max."\">".nf($min)." - ".nf($max)."</option>";
	}
	$int_options .= "<option value=\">".$max."\">> ".nf($max)."</option>";
	
	// Definiert die Auswahlbereiche für bonus Zahlenfelder
	$int_bonus_options = "<option value=\"\">Alle</option>";
	for ($x=0;$x<=400;$x+=20)
	{
		$min = $x;
		$max = $x+20;
		$int_bonus_options .= "<option value=\"BETWEEN ".$min." AND ".$max."\">".$min."% - ".$max."%</option>";
	}
	$int_bonus_options .= "<option value=\">".$max."\">> ".$max."%</option>";	
	

	// Definiert die Zeitbox
	function show_logs_timebox($element_name,$def_val,$seconds=0)
	{
		// Liefert Tag 1-31
		$return .= "<select name=\"".$element_name."_d\" id=\"".$element_name."_d\">";
		for ($x=1;$x<32;$x++)
		{
			$return .= "<option value=\"".$x."\"";
			if (date("d",$def_val)==$x)
			{ 
				$return .= " selected=\"selected\"";
			}
			$return .= ">".$x."</option>";
		}
		$return .= "</select>.";

		// Liefert Monat 1-12
		$return .= "<select name=\"".$element_name."_m\" id=\"".$element_name."_m\">";
		for ($x=1;$x<13;$x++)
		{
			$return .= "<option value=\"".$x."\"";
			if (date("m",$def_val)==$x)
			{
				$return .= " selected=\"selected\"";
			}
			$return .= ">".$x."</option>";
		}
		$return .= "</select>.";

		// Liefert Jahr +-1 vom jetzigen Jahr
		$return .= "<select name=\"".$element_name."_y\" id=\"".$element_name."_y\">";
		for ($x=date("Y")-1;$x<date("Y")+2;$x++)
		{
			$return .= "<option value=\"".$x."\"";
			if (date("Y",$def_val)==$x)
			{
				$return .= " selected=\"selected\"";
			}
			$return .= ">".$x."</option>";
		}
		$return .= "</select> &nbsp;&nbsp;";

		// Liefert Stunden von 00-24
		$return .= "<select name=\"".$element_name."_h\" id=\"".$element_name."_h\">";
		for ($x=0;$x<25;$x++)
		{
			$return .= "<option value=\"".$x."\"";
			if (date("H",$def_val)==$x)
			{
				$return .= " selected=\"selected\"";
			}
			$return .= ">".$x."</option>";
		}
		$return .= "</select>:";

		// Liefert Minuten 1-60
		$return .= "<select name=\"".$element_name."_i\" id=\"".$element_name."_i\">";
		for ($x=0;$x<60;$x++)
		{
			$return .= "<option value=\"".$x."\"";
			if (date("i",$def_val)==$x)
			{
				$return .= " selected=\"selected\"";
			}
			$return .= ">".$x."</option>";
		}
		$return .= "</select>";

		return $return;
		
	}  


	
	// SQL- String
	$out = "<input type=\"hidden\" value=\"\" id=\"sql_query\" name=\"sql_query\"/>";

	
	if($cat['log_cat']=="logs")
	{
		$out .= "allgemeine tab laden...";
		
  	// Such-Formular
  	$out .= "<table class=\"tbl\">";
  	
  	$out .= "<tr>
	  					<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle\" colspan=\"2\">Suche</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Anzahl Datens&auml;tze</td>
	  					<td class=\"tbldata\">
	  						<select name=\"limit\">
	  							".$limit_options."
	  						</select>
							</td>
	  				</tr> 				
	  				";
  				
  	$out .= "</table><br><br>";
  	
  	// Check- und Anzeigefelder
  	$out .= "<table class=\"tbl\">";
	  $out .= "<tr>
	  					<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">Ergebnis</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"button\" name=\"check_formular\" id=\"check_formular\" value=\"Eingaben Prüfen\" onclick=\"xajax_checkLogFormular(xajax.getFormValues('log_selector'));\"/>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" id=\"check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<div style=\"color:red;font-weight:bold;\">Eingaben zuerst Prüfen lassen!</div>
	  					</td>
	  				</tr> 
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"submit\" name=\"logs_submit\" id=\"logs_submit\" value=\"Ergebnisse anzeigen\" disabled=\"disabled\"/>
	  					</td>
	  				</tr>";	
  	$out .= "</table>";	
  }
  elseif($cat['log_cat']=="logs_fleet")
  {
		// Such Formular
		$out .= "<table class=\"tbl\">";
  		
  		$out .= "<tr>
	  				<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle\" colspan=\"2\">Suche</td>
	  			</tr>
				<tr>
	  				<td class=\"tbltitle\" style=\"vertical-align:middle;width:30%\">Feld</td>
	  				<td class=\"tbltitle\" style=\"vertical-align:middle;width:70%\">Kriterium</td>
	  			</tr>
	  			<tr>
	  				<td class=\"tbltitle\">Flotten User</td>
	  				<td class=\"tbldata\">
						<input type=\"text\" name=\"user_nick_fleet\" id=\"user_nick_fleet\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value,'user_nick_a','citybox1');\" onchange=\"xajax_logChangeButton();\"><br/>
		          	  <div class=\"citybox\" id=\"citybox1\">&nbsp;</div>
	  				</td>
	  			</tr>
	  			<tr>
	  				<td class=\"tbltitle\">Entity User</td>
	  				<td class=\"tbldata\">
						<input type=\"text\" name=\"user_nick_entity\" id=\"user_nick_entity\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value,'user_nick_a','citybox1');\" onchange=\"xajax_logChangeButton();\"><br/>
		          	  <div class=\"citybox\" id=\"citybox1\">&nbsp;</div>
	  				</td>
	  			</tr>	
	  			<tr>
	  				<td class=\"tbltitle\">Logs nach</td>
	  				<td class=\"tbldata\">";
	  					$out .= show_logs_timebox("time_min",time());
	 					$out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"add_logs_game_time_min\" value=\"1\" onclick=\"xajax_logChangeButton();\"> Aktivieren 
	 				</td>
	 			</tr>
	 			<tr>
	  				<td class=\"tbltitle\">Logs vor</td>
	  				<td class=\"tbldata\">";
	  					$out .= show_logs_timebox("time_max",time());
	 					$out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"add_logs_game_time_max\" value=\"1\" onclick=\"xajax_logChangeButton();\"> Aktivieren 
	 				</td>
	 			</tr>
				<tr>
					<td class=\"tbltitle\">Aktion:</td><td class=\"tbldata\"><select name=\"action\">
						<option value=\"\"></option>";
						$fas = FleetAction::getAll();
						foreach ($fas as $fa)
						{
							$out .= "<option value=\"".$fa->code()."\" style=\"color:".FleetAction::$attitudeColor[$fa->attitude()]."\"";
							$out .= ">".$fa->name()."</option>";
						}
						$out .= "</select> &nbsp; <select name=\"status\">
							<option value=\"\"></option>";
						foreach (FleetAction::$statusCode as $k => $v)
						{
							$out .= "<option value=\"".$k."\" ";
							$out .= ">".$v."</option>";
						}
						$out .= "</select></td></tr>
	  			<tr>
	  				<td class=\"tbltitle\">Anzahl Datens&auml;tze</td>
	  				<td class=\"tbldata\">
	  					<select name=\"limit\">
	  						".$limit_options."
	  					</select>
					</td>
	  			</tr> 				
	  				";
  				
  	$out .= "</table><br><br>";
  	
  	// Check- und Anzeigefelder
  	$out .= "<table class=\"tbl\">";
	  $out .= "<tr>
	  					<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">Ergebnis</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"button\" name=\"check_formular\" id=\"check_formular\" value=\"Eingaben Prüfen\" onclick=\"xajax_checkLogFormular(xajax.getFormValues('log_selector'));\"/>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" id=\"check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<div style=\"color:red;font-weight:bold;\">Eingaben zuerst Prüfen lassen!</div>
	  					</td>
	  				</tr> 
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"submit\" name=\"logs_submit\" id=\"logs_submit\" value=\"Ergebnisse anzeigen\" disabled=\"disabled\"/>
	  					</td>
	  				</tr>";	
  	$out .= "</table>";  	

  }
  elseif($cat['log_cat']=="logs_battle")
  {  	
  	// Such-Formular
  	$out .= "<table class=\"tbl\">";
  	
  	$out .= "<tr>
	  					<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle\" colspan=\"2\">Suche</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\" style=\"vertical-align:middle;width:30%\">Feld</td>
	  					<td class=\"tbltitle\" style=\"vertical-align:middle;width:70%\">Kriterium</td>
	  				</tr>
						<tr>
	  					<td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;vertical-align:middle\">Allgemeines</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Krieg</td>
	  					<td class=\"tbldata\">
	  						<input type=\"radio\" name=\"alliances_have_war\" value=\"1\" onclick=\"xajax_logChangeButton();\"> Ja <input type=\"radio\" name=\"alliances_have_war\" value=\"0\" checked=\"checked\" onclick=\"xajax_logChangeButton();\"> Egal
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Angriff nach</td>
	  					<td class=\"tbldata\">";
	  						$out .= show_logs_timebox("battle_time_min",time());
	 							$out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"add_battle_time_min\" value=\"1\" onclick=\"xajax_logChangeButton();\"> Aktivieren 
	 						</td>
	 					</tr>
	 					<tr>
	  					<td class=\"tbltitle\">Angriff vor</td>
	  					<td class=\"tbldata\">";
	  						$out .= show_logs_timebox("battle_time_max",time());
	 							$out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"add_battle_time_max\" value=\"1\" onclick=\"xajax_logChangeButton();\"> Aktivieren 
	 						</td>
	 					</tr>
	  				<tr>
	  					<td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;vertical-align:middle\">Angreifer</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">User</td>
	  					<td class=\"tbldata\">
								<input type=\"text\" name=\"user_nick_a\" id=\"user_nick_a\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value,'user_nick_a','citybox1');\" onchange=\"xajax_logChangeButton();\"><br/>
		            <div class=\"citybox\" id=\"citybox1\">&nbsp;</div>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Allianz</td>
	  					<td class=\"tbldata\">
	  						<input type=\"text\" name=\"alliance_name_a\" id=\"alliance_name_a\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchAlliance(this.value,'alliance_name_a','citybox3');\" onchange=\"xajax_logChangeButton();\"><br/>
		            <div class=\"citybox\" id=\"citybox3\">&nbsp;</div>
	  					</td>
	  				</tr> 
	  				<tr>
	  					<td class=\"tbltitle\">Anzahl Schiffe</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_ship_cnt_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Waffen</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_weapon_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Schild</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_shield_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Struktur</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_structure_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Waffenbonus</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_weapon_bonus_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_bonus_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Schildbonus</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_shield_bonus_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_bonus_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Strukturbonus</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_structure_bonus_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_bonus_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Gewonnene EXP</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_win_exp_a\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;vertical-align:middle\">Verteidiger</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">User</td>
	  					<td class=\"tbldata\">
	  						<input type=\"text\" name=\"user_nick_d\" id=\"user_nick_d\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value,'user_nick_d','citybox2');\" onchange=\"xajax_logChangeButton();\"><br/>
		            <div class=\"citybox\" id=\"citybox2\">&nbsp;</div>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Allianz</td>
	  					<td class=\"tbldata\">
	  						<input type=\"text\" name=\"alliance_name_d\" id=\"alliance_name_d\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchAlliance(this.value,'alliance_name_d','citybox4');\" onchange=\"xajax_logChangeButton();\"><br/>
		            <div class=\"citybox\" id=\"citybox4\">&nbsp;</div>
	  					</td>
	  				</tr> 
	  				<tr>
	  					<td class=\"tbltitle\">Planet ID</td>
	  					<td class=\"tbldata\">
	  						<input type=\"text\" name=\"planet_id\" id=\"planet_id\" maxlength=\"5\" size=\"5\" value=\"\" onkeyup=\"xajax_logChangeButton();\">
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Anzahl Schiffe</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_ship_cnt_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Anzahl Verteidigungsanlagen</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_def_cnt\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Waffen</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_weapon_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Schild</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_shield_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Struktur</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_structure_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Waffenbonus</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_weapon_bonus_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_bonus_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Schildbonus</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_shield_bonus_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_bonus_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Strukturbonus</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_structure_bonus_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_bonus_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Gewonnene EXP</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_win_exp_d\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;vertical-align:middle\">Beute</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_METAL."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_win_metal\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_CRYSTAL."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_win_crystal\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_PLASTIC."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_win_plastic\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_FUEL."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_win_fuel\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_FOOD."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"user_win_food\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;vertical-align:middle\">Trümmerfeld</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_METAL."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"tf_metal\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_CRYSTAL."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"tf_crystal\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">".RES_PLASTIC."</td>
	  					<td class=\"tbldata\">
	  						<select name=\"tf_plastic\" onchange=\"xajax_logChangeButton();\">
	  							".$int_options."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;vertical-align:middle\">&nbsp;</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Anzahl Datens&auml;tze</td>
	  					<td class=\"tbldata\">
	  						<select name=\"limit\" onchange=\"xajax_logChangeButton();\">
	  							".$limit_options."
	  						</select>
							</td>
	  				</tr> 				
	  				";
  				
  	$out .= "</table><br><br>";
  	
  	// Check- und Anzeigefelder
  	$out .= "<table class=\"tbl\">";
	  $out .= "<tr>
	  					<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">Ergebnis</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"button\" name=\"check_formular\" id=\"check_formular\" value=\"Eingaben Prüfen\" onclick=\"xajax_checkLogFormular(xajax.getFormValues('log_selector'));\"/>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" id=\"check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<div style=\"color:red;font-weight:bold;\">Eingaben zuerst Prüfen lassen!</div>
	  					</td>
	  				</tr> 
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"submit\" name=\"logs_submit\" id=\"logs_submit\" value=\"Ergebnisse anzeigen\" disabled=\"disabled\"/>
	  					</td>
	  				</tr>";	
  	$out .= "</table>";
  	
  }
  elseif($cat['log_cat']=="logs_game")
  {
  	
  	$game_cat = "<option value=\"0\" selected=\"selected\">Alle</option>";
  	
		// logs-game-cat laden
		$res=dbquery("
		SELECT 
			*
		FROM
			logs_game_cat;");
		while ($arr=mysql_fetch_array($res))
		{
			$game_cat .= "<option value=\"".$arr['logs_game_cat_id']."\">".$arr['logs_game_cat_name']."</option>";
		}
  	
  	
  	// Such Formular
  	$out .= "<table class=\"tbl\">";
  	
  	$out .= "<tr>
	  					<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle\" colspan=\"2\">Suche</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\" style=\"vertical-align:middle;width:30%\">Feld</td>
	  					<td class=\"tbltitle\" style=\"vertical-align:middle;width:70%\">Kriterium</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Kategorie</td>
	  					<td class=\"tbldata\">
	  						<select name=\"logs_game_cat\" id=\"logs_game_cat\">
	  						".$game_cat."
	  						</select>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbltitle\">User</td>
	  					<td class=\"tbldata\">
								<input type=\"text\" name=\"user_nick_a\" id=\"user_nick_a\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value,'user_nick_a','citybox1');\" onchange=\"xajax_logChangeButton();\"><br/>
		            <div class=\"citybox\" id=\"citybox1\">&nbsp;</div>
	  					</td>
	  				</tr>	  				
	  				<tr>
	  					<td class=\"tbltitle\">Logs nach</td>
	  					<td class=\"tbldata\">";
	  						$out .= show_logs_timebox("logs_game_time_min",time());
	 							$out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"add_logs_game_time_min\" value=\"1\" onclick=\"xajax_logChangeButton();\"> Aktivieren 
	 						</td>
	 					</tr>
	 					<tr>
	  					<td class=\"tbltitle\">Logs vor</td>
	  					<td class=\"tbldata\">";
	  						$out .= show_logs_timebox("logs_game_time_max",time());
	 							$out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"add_logs_game_time_max\" value=\"1\" onclick=\"xajax_logChangeButton();\"> Aktivieren 
	 						</td>
	 					</tr>
	  				<tr>
	  					<td class=\"tbltitle\">Anzahl Datens&auml;tze</td>
	  					<td class=\"tbldata\">
	  						<select name=\"limit\">
	  							".$limit_options."
	  						</select>
							</td>
	  				</tr> 				
	  				";
  				
  	$out .= "</table><br><br>";
  	
  	// Check- und Anzeigefelder
  	$out .= "<table class=\"tbl\">";
	  $out .= "<tr>
	  					<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">Ergebnis</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"button\" name=\"check_formular\" id=\"check_formular\" value=\"Eingaben Prüfen\" onclick=\"xajax_checkLogFormular(xajax.getFormValues('log_selector'));\"/>
	  					</td>
	  				</tr>
	  				<tr>
	  					<td class=\"tbldata\" id=\"check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<div style=\"color:red;font-weight:bold;\">Eingaben zuerst Prüfen lassen!</div>
	  					</td>
	  				</tr> 
	  				<tr>
	  					<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	  						<input type=\"submit\" name=\"logs_submit\" id=\"logs_submit\" value=\"Ergebnisse anzeigen\" disabled=\"disabled\"/>
	  					</td>
	  				</tr>";	
  	$out .= "</table>";  	
  }
	else
	{
		$out .= "Kat w&auml;hlen...";
	}
  $objResponse->assign("catSelector","innerHTML", $out);	
	

	$objResponse->assign("logsinfo","innerHTML",ob_get_contents());
	ob_end_clean();
	
	return $objResponse;	
	
}


function checkLogFormular($val)
{
	
	ob_start();
	$objResponse = new xajaxResponse();
	
	//Flotten Query erstellen
	if($val['log_cat']=="logs_fleet")
	{
		$sql_select = "id";
		$sql_table = 'logs_fleet';
		$sql_where_start = "id!=0";
		$sql_add = "";
		$sql_order = "ORDER BY landtime DESC";
		$sql_limit = $val['limit'];
		print_r($val);
	// Kampfzeit min.
		if($val['add_fleet_time_min']==1)
		{
			$sql_add .= " AND landtime >= '".mktime($val['time_min_h'],$val['time_min_i'],0,$val['time_min_m'],$val['time_min_d'],$val['time_min_y'])."'";
		}
		
		// Kampfzeit max.
		if($val['add_fleet_time_max']==1)
		{
			$sql_add .= " AND landtime <= '".mktime($val['time_max_h'],$val['time_max_i'],0,$val['time_max_m'],$val['time_max_d'],$val['time_max_y'])."'";
		}
		
		
		// Angreiffer
		if($val['user_nick_entity']!="")
		{
			$sql_add .= " AND fleet_user_id='".get_user_id($val['user_nick_fleet'])."'";
		}
		
		// Verteidiger
		if($val['user_nick_entity']!="")
		{
			$sql_add .= " AND entity_user_id='".get_user_id($val['user_nick_entity'])."'";
		}
		
		// Aktion
		if($val['action']!="")
		{
			$sql_add .= " AND action='".$val['action']."'";
		}
		
		// Status
		if($val['status']!="")
		{
			$sql_add .= " AND status='".$val['status']."'";
		}
		echo $sql_select.$sql_add;
	}
	
	// Kampfberichte Query erstellen
	if($val['log_cat']=="logs_battle")
	{
		$sql_select = "logs_battle_id";
		$sql_table = 'logs_battle';
		$sql_where_start = "logs_battle_id!=0";
		$sql_add = "";
		$sql_order = "ORDER BY logs_battle_fleet_landtime DESC";
		$sql_limit = $val['limit'];
		
		// Kampfzeit min.
		if($val['add_battle_time_min']==1)
		{
			$sql_add .= " AND logs_battle_fleet_landtime >= '".mktime($val['battle_time_min_h'],$val['battle_time_min_i'],0,$val['battle_time_min_m'],$val['battle_time_min_d'],$val['battle_time_min_y'])."'";
		}
		
		// Kampfzeit max.
		if($val['add_battle_time_max']==1)
		{
			$sql_add .= " AND logs_battle_fleet_landtime <= '".mktime($val['battle_time_max_h'],$val['battle_time_max_i'],0,$val['battle_time_max_m'],$val['battle_time_max_d'],$val['battle_time_max_y'])."'";
		}
		
		
		// Angreiffer
		if($val['user_nick_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_id='".get_user_id($val['user_nick_a'])."'";
		}
		
		// Verteidiger
		if($val['user_nick_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_id='".get_user_id($val['user_nick_d'])."'";
		}
		
		// Allianz Angreiffer
		if($val['alliance_name_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_alliance_name='".$val['alliance_name_a']."'";
		}
		
		// Allianz Verteidiger
		if($val['alliance_name_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_alliance_name='".$val['alliance_name_d']."'";
		}
		
		// Krieg
		if($val['alliances_have_war']==1)
		{
			$sql_add .= " AND logs_battle_alliances_have_war='1'";
		}	
		
		// Planet
		if($val['planet_id']!="")
		{
			$sql_add .= " AND logs_battle_planet_id='".$val['planet_id']."'";
		}
		
		// Anzahl Schiffe Angreiffer
		if($val['user_ship_cnt_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_ships_cnt ".$val['user_ship_cnt_a']."";
		}	
		
		// Anzahl Schiffe Verteidiger
		if($val['user_ship_cnt_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_ships_cnt ".$val['user_ship_cnt_d']."";
		}		
		
		// Anzahl Verteidigungsanlagen
		if($val['user_def_cnt']!="")
		{
			$sql_add .= " AND logs_battle_user2_defs_cnt ".$val['user_def_cnt']."";
		}	
		
		// Waffen Angreiffer
		if($val['user_weapon_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_weapon ".$val['user_weapon_a']."";
		}	
		
		// Schild Angreiffer
		if($val['user_shield_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_shield ".$val['user_shield_a']."";
		}	
		
		// Struktur Angreiffer
		if($val['user_structure_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_structure ".$val['user_structure_a']."";
		}	
		
		// Waffen Bonus Angreiffer
		if($val['user_weapon_bonus_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_weapon_bonus ".$val['user_weapon_bonus_a']."";
		}	
		
		// Schild Bonus Angreiffer
		if($val['user_shield_bonus_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_shield_bonus ".$val['user_shield_bonus_a']."";
		}	
		
		// Struktur Bonus Angreiffer
		if($val['user_structure_bonus_a']!="")
		{
			$sql_add .= " AND logs_battle_user1_structure_bonus ".$val['user_structure_bonus_a']."";
		}	
		
		
		// Waffen Verteidiger
		if($val['user_weapon_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_weapon ".$val['user_weapon_d']."";
		}	
		
		// Schild Verteidiger
		if($val['user_shield_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_shield ".$val['user_shield_d']."";
		}	
		
		// Struktur Verteidiger
		if($val['user_structure_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_structure ".$val['user_structure_d']."";
		}	
		
		// Waffen Bonus Verteidiger
		if($val['user_weapon_bonus_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_weapon_bonus ".$val['user_weapon_bonus_d']."";
		}	
		
		// Schild Bonus Verteidiger
		if($val['user_shield_bonus_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_shield_bonus ".$val['user_shield_bonus_d']."";
		}	
		
		// Struktur Bonus Verteidiger
		if($val['user_structure_bonus_d']!="")
		{
			$sql_add .= " AND logs_battle_user2_structure_bonus ".$val['user_structure_bonus_d']."";
		}	
		echo $sql_add;

	}
	
	// Allgemeine Logs Query erstellen
	elseif($val['log_cat']=="logs")
	{
		$sql_select = "log_id";
		$sql_table = 'logs';
		$sql_where_start = "log_id!=0";	
		$sql_add = "";
		$sql_order = "";
		$sql_limit = $val['limit'];	
		
	}
	
	// GameLogs Query erstellen
	elseif($val['log_cat']=="logs_game")
	{
		$sql_select = "logs_game_id";
		$sql_table = "logs_game 
									INNER JOIN 
									logs_game_cat
									ON logs_game_cat=logs_game_cat_id";
		$sql_where_start = "logs_game_id!='0'";	
		$sql_add = "";	
		$sql_order = "ORDER BY logs_game_timestamp DESC";
		$sql_limit = $val['limit'];
		
		// Kategorie
		if($val['logs_game_cat']!=0)
		{
			$sql_add .= " AND logs_game_cat='".$val['logs_game_cat']."'";
		}
		
		// User
		if($val['user_nick_a']!="")
		{
			$sql_add .= " AND logs_game_user_id='".get_user_id($val['user_nick_a'])."'";
		}
		
		// Min. Zeit
		if($val['add_logs_game_time_min']==1)
		{
			$sql_add .= " AND logs_game_timestamp >= '".mktime($val['logs_game_time_min_h'],$val['logs_game_time_min_i'],0,$val['logs_game_time_min_m'],$val['logs_game_time_min_d'],$val['logs_game_time_min_y'])."'";
		}
		
		// Max. Zeit
		if($val['add_logs_game_time_max']==1)
		{
			$sql_add .= " AND logs_game_timestamp <= '".mktime($val['logs_game_time_max_h'],$val['logs_game_time_max_i'],0,$val['logs_game_time_max_m'],$val['logs_game_time_max_d'],$val['logs_game_time_max_y'])."'";
		}
		
		
		
	}
	
	
	
	//
	// SQL-Abfrage
	//
	
	$res = dbquery("
	SELECT
		".$sql_select."
	FROM
		".$sql_table."
	WHERE
		".$sql_where_start."
    ".$sql_add."
  LIMIT 
  	".$sql_limit.";");
	$cnt = mysql_num_rows($res);
	
	// Query generierung für Anzeige (mit ORDER BY)
	$sql_query = "
	SELECT
		*
	FROM
		".$sql_table."
	WHERE
		".$sql_where_start."
    ".$sql_add."
  ".$sql_order."
  LIMIT 
  	".$sql_limit.";";
	
  //
  // End Prüfung
  //
  
	// Keine Angebote gefunden
	if($cnt <= 0)
	{
		$out_check_message = "<div style=\"color:red;font-weight:bold;\">Keine Einträge gefunden</div>";
		
		$objResponse->assign("logs_submit","disabled",true);
		$objResponse->assign("logs_submit","style.color",'#f00'); 			
	}  	
	// Angebot ist OK
	else
	{		
		$out_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!<br>".$cnt." Einträge gefunden!</div>";
		
		$objResponse->assign("logs_submit","disabled",false);
		$objResponse->assign("logs_submit","style.color",'#0f0');			
	}
	
	
	// XAJAX ändert Daten
	$objResponse->assign("check_message","innerHTML", $out_check_message);
	$objResponse->assign("sql_query","value", $sql_query); 	

	
	
	
	$objResponse->assign("logsinfo","innerHTML",ob_get_contents());
	ob_end_clean();
	
	return $objResponse;	
	
}



function logChangeButton()
{
	ob_start();
	$objResponse = new xajaxResponse();
	

	$out_check_message = "<div style=\"color:red;font-weight:bold;\">Neue Eingaben zuerst Prüfen lassen!</div>";
		
	// XAJAX ändert Daten
	$objResponse->assign("logs_submit","disabled",true);
	$objResponse->assign("logs_submit","style.color",'#f00'); 
	$objResponse->assign("check_message","innerHTML", $out_check_message);


	$objResponse->assign("logsinfo","innerHTML",ob_get_contents());
	ob_end_clean();
	
	return $objResponse;	
	
}



function showBattle($battle,$id)
{	
	ob_start();
	$objResponse = new xajaxResponse();
		
	if($battle!="")
	{
		$objResponse->assign("show_battle_".$id."","innerHTML", $battle);
	}	
	else
	{
		$objResponse->assign("show_battle_".$id."","innerHTML", "");
	}

	$objResponse->assign("logsinfo","innerHTML",ob_get_contents());
	ob_end_clean();
	
	return $objResponse;	
	
}


?>