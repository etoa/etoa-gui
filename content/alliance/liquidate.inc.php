<?PHP
if (Alliance::checkActionRights('liquidate'))
{
		echo "<h2>Allianz aufl&ouml;sen</h2>";
		
		// Prüft, ob noch Mitglieder vorhanden sind (keine Bewerbungen!)
		$res = dbquery("
		SELECT 
			user_id 
		FROM 
			users
		WHERE 
			user_alliance_id='".$cu->alliance_id."' 
			AND user_id!='".$cu->id()."';");
		
		if (mysql_num_rows($res)>0)
		{
			echo "Allianz kann nicht aufgel&ouml;st werden, da sie noch Mitglieder hat. L&ouml;sche zuerst die Mitglieder!
			<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
		}
		else
		{
			echo "<form action=\"?page=$page\" method=\"post\">";
			echo "<input name=\"id_control\" type=\"hidden\" value=\"".$cu->alliance_id."\" />";
			checker_init();
			echo "Willst du die Allianz wirklich aufl&ouml;sen?<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Nein\" />&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"liquidatesubmit\" value=\"Ja\" />";
			echo "</form>";
		}
}
?>