<?PHP
$dir="images/themes/Discovery/buildings";
$d=opendir("../".$dir);
while ($f=readdir($d))
{
	if (is_file("../".$dir."/".$f))
	{
		if (stristr($f,".png"))
		{
			echo "<img src=\"../".$dir."/".$f."\" alt=\"original\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=gray\" alt=\"grey\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=red\" alt=\"red\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=orange\" alt=\"orange\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=yellow\" alt=\"yellow\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=green\" alt=\"green\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=blue\" alt=\"blue\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=negate\" alt=\"negate\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=brightness\" alt=\"brightness\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=emboss\" alt=\"emboss\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=blur\" alt=\"blur\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=removal\" alt=\"removal\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=smooth\" alt=\"smooth\"/> ";
			echo "<img src=\"imagefilter.php?file=".$dir."/".$f."&filter=edgedetect\" alt=\"edgedetect\"/> ";
			echo "<br><br>";
			
		}
	}
}
closedir($dir);


?>