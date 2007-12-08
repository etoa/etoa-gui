<?PHP

	showTitle('Feeds');
	
	echo "<div style=\"margin:0px auto;width:600px;\">";
	echo 'Was ist ein RSS-Feed? Klicke <a href="http://de.wikipedia.org/wiki/Rss">hier</a> für mehr Infos!<br/><br/>';
	infobox_start("Vorhandene Feeds",1);
	Rss::showOverview();
	infobox_end(1);
	echo '</div>';

?>