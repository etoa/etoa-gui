<?PHP
	include("../conf.inc.php");
	include("../functions.php");
	include("../def.inc.php");
	dbconnect();
	dbquery("SET NAMES 'utf8';");

	$conf = get_all_config();
	
	$rssValue = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
	//$rssValue .= "<!DOCTYPE rss PUBLIC \"-//Netscape Communications//DTD RSS 0.91//EN\" \"http://my.netscape.com/publish/formats/rss-0.91.dtd\">\r\n";
	$rssValue .= "<rss version=\"2.0\">\r\n";
	
	// Build the channel tag
	$rssValue .= "<channel>\r\n";
	$rssValue .= "<title>EtoA Rathaus ".GAMEROUND_NAME."</title>\r\n";
	$rssValue .= "<link>http://www.etoa.ch</link>\r\n";
	$rssValue .= "<description>Rathaus der EtoA ".GAMEROUND_NAME."</description>\r\n";
	$rssValue .= "<language>de</language>\r\n";
	
	// Build the image tag
	$rssValue .= "<image>\r\n";
	$rssValue .= "<title>EtoA Rathaus</title>\r\n";
	$rssValue .= "<url>http://www.etoa.ch/images/game_logo.gif</url>\r\n";
	$rssValue .= "<link>http://www.etoa.ch</link>\r\n";
	$rssValue .= "</image>\r\n";
	
	$res=dbquery("
	SELECT 
		alliance_news_title,
		alliance_news_text
	FROM
		".$db_table['alliance_news']."
	WHERE
		alliance_news_public=1
	ORDER BY
		alliance_news_date DESC
	
	;");	
	
	// The records were retrieved OK, let's start building the item tags
	while($arr = mysql_fetch_array($res))
	{
		$rssValue .= "<item>\r\n";
		$rssValue .= "<title>".text2html($arr['alliance_news_title'])."</title>\r\n";
		$rssValue .= "<description>".text2html($arr['alliance_news_text'])."</description>\r\n";
		$rssValue .= "<link>http://www.etoa.ch</link>\r\n";
		$rssValue .= "</item>\r\n";
	}

	$rssValue .= "</channel>\r\n";
	$rssValue .= "</rss>";
		
	// Output the generated RSS XML
	
	header("Content-type: text/xml");	
	echo $rssValue;
	
	dbclose();
?>



