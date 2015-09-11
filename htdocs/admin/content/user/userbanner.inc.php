<?PHP
	echo "<h1>Spieler-Banner</h1>";
	
	echo "<p>Banner werden jeweils beim Aktualisieren der Punkte neu generiert.</p>";
	
	$res=dbquery("
	SELECT
		user_nick,
		user_id
	FROM
		users
	");
	while ($arr = mysql_fetch_assoc($res))
	{
		$name = Ranking::getUserBannerPath($arr['user_id']);
		if (file_exists($name))
		{
			echo '<img src="'.$name.'" alt="Banner" style="width:'.USERBANNER_WIDTH.'px;heigth:'.USERBANNER_HEIGTH.'px;" /> ';
		}
		else
		{
			echo '<div  style="display:inline-block; width:'.USERBANNER_WIDTH.'px;heigth:'.USERBANNER_HEIGTH.'px;">Banner für <b>'.$arr['user_nick'].'</b> existiert nicht!</div> ';
		}
	}
?>
