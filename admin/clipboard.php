<?PHP
	require("inc/includer.inc.php");
	
	adminHtmlHeader($s['theme']);

		if ($s['user_id']>0)
		{
			echo "<h1>Zwischenablage</h1>";
			
			if (isset($_GET['add_user']) && $_GET['add_user']>0)
			{
				$s['cp_users'][$_GET['add_user']]=$_GET['add_user'];
			}
			if (isset($_GET['rem_user']) && $_GET['rem_user']>0)
			{
				$s['cp_users'][$_GET['rem_user']]=null;
			}			
			
			echo "<h2>Benutzer [<a href=\"index.php?page=home&amp;sub=stats\" target=\"main\">alle</a>]</h2>";
			if (isset($s['cp_users']) && count($s['cp_users'])>0)
			{
				foreach ($s['cp_users'] as $uid)
				{
					if ($uid>0)
					{
						$res = dbquery("SELECT user_nick FROM users WHERE user_id=".$uid.";");
						if (mysql_num_rows($res)>0)
						{
							$arr = mysql_fetch_row($res);
							echo "<a href=\"index.php?page=user&amp;sub=edit&amp;user_id=".$uid."\" target=\"main\">
							<a href=\"index.php?page=user&amp;sub=edit&amp;user_id=".$uid."\" target=\"main\">
							".$arr[0]."</a>&nbsp;
							<a href=\"?rem_user=".$uid."\" target=\"_self\">
							<img src=\"../images/delete.gif\" style=\"border:none;height:10px;\"></a>

							<br/>";
						}
					}
				}
			}
			else
			{			
				echo "<i>Nichts vorhanden!</i><br/><br/>";
			}
			
			echo "<br/><br/>
			[<a href=\"?\" target=\"_self\">Aktualisieren</a>]
			[<a href=\"index.php?cbclose=1\" target=\"_top\">Schliessen</a>]";
		}
		else
		{
			echo "Nicht eingeloggt!";
		}

	adminHtmlFooter();
	require("inc/footer.inc.php");
?>
