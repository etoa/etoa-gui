<?PHP
	echo "<h2>Allianznews</h2>";
	if (($_POST['newssubmit']!="" || $_POST['newssubmitsend']!="") && checker_verify())
	{
		if (check_illegal_signs($_POST['news_title'])!="")
		{
			echo "<div style=\"color:red;\"><b>Fehler:</b> Ungültige Zeichen (".check_illegal_signs($_POST['news_title']).") im Newstitel!!</div><br/>";
			$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
			$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
			$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
		}
		elseif ($_POST['newssubmitsend']!="" && $_POST['news_text']!="" && $_SESSION['alliance']['news']['preview'])
		{
			$_SESSION['alliance']['news']=Null;
			dbquery("INSERT INTO ".$db_table['alliance_news']."
			(alliance_news_alliance_id,
			alliance_news_user_id,
			alliance_news_title,
			alliance_news_text,
			alliance_news_date,
			alliance_news_alliance_to_id)
			VALUES
			(".$s['user']['alliance_id'].",
			".$s['user']['id'].",
			'".addslashes($_POST['news_title'])."',
			'".addslashes($_POST['news_text'])."',
			".time().",
			".$_POST['alliance_id'].")");
			echo "<div style=\"color:#0f0;\">News wurde gesendet!</div><br/>";
			$_SESSION['alliance']['news']=null;
		}
		elseif ($_POST['news_title']!="" && $_POST['news_text']!="")
		{
			$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
			$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
			$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
			infobox_start("Vorschau - ".$_POST['news_title']);
			echo text2html($_POST['news_text']);
			infobox_end();
			$_SESSION['alliance']['news']['preview']=true;
		}
		else
		{
			$_SESSION['alliance']=array();
			$_SESSION['alliance']['news']=array();
			$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
			$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
			$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
			echo "<div style=\"color:red;\"><b>Fehler:</b> Nicht alle Felder ausgefüllt!</div><br/>";
		}
	}

	echo "<form action=\"?page=$page&action=".$_GET['action']."\" method=\"post\">";
	checker_init();
	if ($_GET['message_subject']!="") $_SESSION['alliance']['news']['news_title']=$_GET['message_subject'];
	infobox_start("Neue Allianzenews",1);
	$aid=$_SESSION['alliance']['news']['alliance_id'];
	if ($aid==0) $aid=$s['user']['alliance_id'];
	echo "<tr><th class=\"tbldata\" colspan=\"3\">Sende diese Nachricht nur ab, wenn du dir bezüglich der Ratshausreglen sicher bist! Eine Missachtung kann zur Sperrung des Accounts führen!</th></tr>";
	echo "<tr>
		<td class=\"tbltitle\" width=\"170\">Betreff:</td>
		<td class=\"tbldata\" colspan=\"2\"><input type=\"text\" name=\"news_title\" value=\"".$_SESSION['alliance']['news']['news_title']."\" size=\"62\" maxlength=\"255\"></td></tr>";
	echo "<tr>
		<td class=\"tbltitle\" width=\"170\">Text:</td>
		<td class=\"tbldata\" colspan=2><textarea name=\"news_text\" rows=\"18\" cols=\"60\">".$_SESSION['alliance']['news']['news_text']."</textarea></td></tr>";
	echo "<tr>
		<td class=\"tbltitle\" width=\"170\">Ziel:</td>
		<td class=\"tbldata\" colspan=2>
			<select name=\"alliance_id\">
				<option value=\"0\" style=\"font-weight:bold;color:#0f0;\">Öffentliches Rathaus</option>";
		$alliance=dbquery("
		SELECT
                    alliance_id,
                    alliance_tag,
                    alliance_name
		FROM
			".$db_table['alliances']."");
		while ($alliances=mysql_fetch_array($alliance))
		{
			echo "<option value=\"".$alliances['alliance_id']."\"";
			if ($alliances['alliance_id']==$_SESSION['alliance']['news']['alliance_id']) echo " selected=\"selected\"";
			echo ">[".$alliances['alliance_tag']."]  ".$alliances['alliance_name']."</option>";
		}
		echo "</select></td>
	</tr>";
	infobox_end(1);
	if ($_SESSION['alliance']['news']['preview'])
		echo "<input type=\"submit\" name=\"newssubmitsend\" value=\"Senden\"> &nbsp; ";
	echo "<input type=\"submit\" name=\"newssubmit\" value=\"Vorschau\">";
	echo " &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
	echo "</form>";
?>