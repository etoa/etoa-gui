<?PHP
	/**
	* Fleet-Action: Attack
	*/

	// Calc battle
	require_once("inc/battle.inc.php");
	$binfo = battle($arr['fleet_id'],$arr['fleet_planet_to']);

	
	// Send messages
	$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);
	send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Kampfbericht (".$binfo[2].")",$binfo[1]);
	send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Kampfbericht (".$binfo[3].")",$binfo[1]);

	// Add log
	add_log(1,$binfo[1],$arr['fleet_landtime']);

	// Flotte zurückschicken
	if ($binfo[4])
	{
		$action="ar";
		fleet_return($arr,$action);
	}
?>