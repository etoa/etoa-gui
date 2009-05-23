<?php
	echo "<h1>Berichte</h1>";

	echo "<ul class=\"horizMenu\">
	<li>Typ: <a href=\"\">Alle</a></li>";
	foreach (Report::$types as $k=>$v)
	{
		echo "<li><a href=\"$k\">$v</a></li>";
	}
	echo "</ul>";
	


?>
