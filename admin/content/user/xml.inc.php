<?PHP
	echo "<h1>XML-Import/Export</h1>";
	
	echo "<h2>Export</h2>";

	if (isset($_POST['exportcache']))
	{
		$xmlfile = writeUserToXml($_POST['export_user_id'],"../");
		success_msg("Die Userdaten wurden nach [b]".$xmlfile."[/b] exportiert.");
	}
	if (isset($_POST['exportdl']))
	{
		echo "<script type=\"text/javascript\">window.open('misc/user_xml.php?id=". $_POST['export_user_id']."');</script>";
	}

	echo "Bei jeder Löschung eines Spielers werden automatisch seine Daten
	in ein XML-File geschrieben und dieses in einem Ordner abgelegt. Wenn du manuell von
	einem User ein Backup erstellen willst, kannst du das hier tun:<br/><br/>";
	echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	echo "Spieler wählen: <select name=\"export_user_id\">";
	$res = dbquery("
	SELECT
		user_id,
		user_nick
	FROM
		users
	ORDER BY
		user_nick;		
	");
	if (mysql_num_rows($res)>0)
	{
		while($arr=mysql_fetch_array($res))
		{
			echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']."</option>";
		}		
	}
	echo "</select> 
	<input type=\"submit\" name=\"exportcache\" value=\"Exportieren\" /> 
	<input type=\"submit\" name=\"exportdl\" value=\"Herunterladen\" /></form>";
	
	echo "<h2>Import</h2>";

?>