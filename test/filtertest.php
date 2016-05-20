<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta charset="utf-8">
	<title>Image filter test</title>
</head>
<body>
<?PHP

$dir="images/imagepacks/Discovery/buildings";
$filters = array('gray', 'red', 'orange', 'yellow', 'green', 'blue', 'negate', 'brightness', 'emboss', 'blur', 'removal', 'smooth', 'edgedetect');
$match = '/building[0-9]+\.png/';

$d=opendir("../htdocs/".$dir);
while ($f=readdir($d))
{
	if (is_file("../htdocs/".$dir."/".$f))
	{
		if (preg_match($match, $f))
		{
			echo "<img src=\"../htdocs/".$dir."/".$f."\" alt=\"Original\" title=\"Original\" /> ";
			foreach ($filters as $filter) {
				echo "<img src=\"../htdocs/misc/imagefilter.php?file=".$dir."/".$f."&amp;filter=".$filter."\" alt=\"Filter ".$filter."\" title=\"Filter ".$filter."\"/> \n";
			}
			echo "<br><br>";
			
		}
	}
}
closedir($dir);
?>
</body>
</html>
