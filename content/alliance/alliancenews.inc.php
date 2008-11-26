<?PHP

if (Alliance::checkActionRights('alliancenews'))
{

	echo "<h2>Allianznews</h2>";
	if ((isset($_POST['newssubmit']) || isset($_POST['newssubmitsend'])) && checker_verify())
	{
		if (check_illegal_signs($_POST['news_title'])!="")
		{
			echo "<div style=\"color:red;\"><b>Fehler:</b> Ungültige Zeichen (".check_illegal_signs($_POST['news_title']).") im Newstitel!!</div><br/>";
			$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
			$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
			$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
		}
		elseif (isset($_POST['newssubmitsend']) && isset($_POST['news_text']) && $_POST['news_text']!="" && $_SESSION['alliance']['news']['preview'])
		{
			$_SESSION['alliance']['news']=Null;
			
			dbquery("
			INSERT INTO 
			alliance_news
			(alliance_news_alliance_id,
			alliance_news_user_id,
			alliance_news_title,
			alliance_news_text,
			alliance_news_date,
			alliance_news_alliance_to_id)
			VALUES
			(".$cu->allianceId.",
			".$cu->id.",
			'".addslashes($_POST['news_title'])."',
			'".addslashes($_POST['news_text'])."',
			".time().",
			".$_POST['alliance_id'].")");
			
			echo "<div style=\"color:#0f0;\">News wurde gesendet!</div><br/>";
						
			// Gebe nur Punkte falls Nachricht öffentlich oder an andere Allianz
			if ($cu->allianceId!=$_POST['alliance_id'])
			{
				$cu->rating->addDiplomacyRating(DIPLOMACY_POINTS_PER_NEWS,"Rathausnews verfasst (ID:".mysql_insert_id().", ".addslashes($_POST['news_text']).")");
			}
			
			// Update rss file
			Townhall::genRss();			
		}
		elseif (isset($_POST['news_title']) && isset($_POST['news_text']) && $_POST['news_title']!="" && $_POST['news_text']!="")
		{
			$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
			$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
			$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
			iBoxStart("Vorschau - ".$_POST['news_title']);
			echo text2html($_POST['news_text']);
			iBoxEnd();
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
	if (isset($_GET['message_subject']) && $_GET['message_subject']!="")
	{
		$_SESSION['alliance']['news']['news_title']=$_GET['message_subject'];
	}
	
	tableStart("Neue Allianzenews");
	if(isset($_SESSION['alliance']['news']['alliance_id']) && $_SESSION['alliance']['news']['alliance_id']!=0)
	{
		$aid = $_SESSION['alliance']['news']['alliance_id'];
	}
	else
	{
		$aid = $cu->allianceId;
	} 
	
	if(isset($_SESSION['alliance']['news']['news_title']))
	{
		$news_title = $_SESSION['alliance']['news']['news_title'];
	}
	else
	{
		$news_title = "";
	}
	
	if(isset($_SESSION['alliance']['news']['news_text']))
	{
		$news_text = $_SESSION['alliance']['news']['news_text'];
	}
	else
	{
		$news_text = "";
	}
	
	echo "<tr><th class=\"tbldata\" colspan=\"3\">Sende diese Nachricht nur ab, wenn du dir bezüglich der Ratshausreglen sicher bist! Eine Missachtung kann zur Sperrung des Accounts führen!</th></tr>";
	echo "<tr>
		<td class=\"tbltitle\" width=\"170\">Betreff:</td>
		<td class=\"tbldata\" colspan=\"2\"><input type=\"text\" name=\"news_title\" value=\"".$news_title."\" size=\"62\" maxlength=\"255\"></td></tr>";
	echo "<tr>
		<td class=\"tbltitle\" width=\"170\">Text:</td>
		<td class=\"tbldata\" colspan=2><textarea name=\"news_text\" rows=\"18\" cols=\"60\">".$news_text."</textarea></td></tr>";
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
			alliances");
		while ($alliances=mysql_fetch_assoc($alliance))
		{
			echo "<option value=\"".$alliances['alliance_id']."\"";
			if ($alliances['alliance_id']==$aid) echo " selected=\"selected\"";
			echo ">[".$alliances['alliance_tag']."]  ".$alliances['alliance_name']."</option>";
		}
		echo "</select></td>
	</tr>";
	tableEnd();
	if (isset($_SESSION['alliance']['news']['preview']) && $_SESSION['alliance']['news']['preview'])
	{
		echo "<input type=\"submit\" name=\"newssubmitsend\" value=\"Senden\"> &nbsp; ";
	}
	echo "<input type=\"submit\" name=\"newssubmit\" value=\"Vorschau\">";
	echo " &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
	echo "</form>";
	
}
?>