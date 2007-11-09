<?php

$site = $_GET['site'];
$user = $_GET['user'];

		infobox_start("Bilder",1);
		echo "
		<tr>
			<td class=\"tbltitle\" colspan=\"5\">lambo</td>

		</tr>
		<td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=buildings&user=lambo\">Gebäude</a></td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=research&user=lambo\">Forschung</a></td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=ships&user=lambo\">Schiffe</a></td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=defense&user=lambo\">Verteidigung</a></td>
		</tr>";

		echo "
		<tr>
			<td class=\"tbltitle\" colspan=\"5\">greaser</td>

		</tr>
		<td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=buildings&user=greaser\">Gebäude</a></td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=research&user=greaser\">Forschung</a></td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=ships&user=greaser\">Schiffe</a></td>
			<td class=\"tbldata\" width=\"25%\"><a href=\"?page=$page&site=defense&user=greaser\">Verteidigung</a></td>
		</tr>";
		infobox_end(1);


if($_GET['site']=='buildings')
{
	define(ORIGINAL_PIC_DIRECTORY,"images/themes/test_theme/original/".$user."/buildings");
}
elseif($_GET['site']=='ships')
{
	define(ORIGINAL_PIC_DIRECTORY,"images/themes/test_theme/original/".$user."/ships");
}
elseif($_GET['site']=='research')
{
	define(ORIGINAL_PIC_DIRECTORY,"images/themes/test_theme/original/".$user."/research");
}
elseif($_GET['site']=='defense')
{
	define(ORIGINAL_PIC_DIRECTORY,"images/themes/test_theme/original/".$user."/defense");
}


if($site!="")
{
    $verz=opendir(ORIGINAL_PIC_DIRECTORY);


    while($file = readdir($verz)){
      if($file != '.' && $file != '..')
        echo "<a href=\"".ORIGINAL_PIC_DIRECTORY."/".$file."\">$file<br><img src=\"".ORIGINAL_PIC_DIRECTORY."/".$file."\" border=\"0\" width=\"220px\" hight=\"220px\"></a> <br><br>";
    }

    closedir($verz);
}


?>