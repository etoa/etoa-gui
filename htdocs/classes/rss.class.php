<?php

class Rss
{
    static function showOverview()
    {
			$d = opendir(RSS_DIR);
			while ($f = readdir($d))
			{
				if (stristr($f,".rss"))
				{
					$xml = simplexml_load_file(RSS_DIR."/".$f);
					echo "<tr><th class=\"tbltitle\">".$xml->channel->title."</th>" .
							"<td class=\"tbldata\">".$xml->channel->description."</td>" .
							"<td class=\"tbldata\"><a href\"\" onclick=\"window.open('".RSS_DIR."/".$f."','','status=no')\">Anzeigen</a></td></tr>";
				}
			}
			closedir($d);
		}

}
?>
