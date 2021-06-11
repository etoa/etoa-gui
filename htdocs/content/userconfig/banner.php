<?PHP
	$id = $cu->id;
	
	iBoxStart("Banner");
	echo 'Hilf mit, EtoA bekannter zu machen und binde unser Banner auf deiner Website ein! 
	Hier findest du den Quellcode um das Banner einzubinden:<br><br>';
	
	$name = Ranking::getUserBannerPath($id);
	if (file_exists($name))
	{
		echo '<div style="text-align: center;">
		<img src="'.$name.'" alt="Banner"><br><br>
		HTML:<br/><textarea readonly="readonly" rows="2" cols="65">&lt;a href="'.USERBANNER_LINK_URL.'"&gt;&lt;img src="'.$cfg->roundurl.'/'.$name.'" width="'.USERBANNER_WIDTH.'" height="'.USERBANNER_HEIGTH.'" alt="EtoA Online-Game" border="0" /&gt;&lt;/a&gt;</textarea><br/>
		BBCode:<br/><textarea readonly="readonly" rows="1" cols="65">[url='.USERBANNER_LINK_URL.'][img]'.$cfg->roundurl.'/'.$name.'[/img][/url]</textarea>';				
	}
	else
	{
		echo "Momentan ist kein Banner verfügbar!";
	}
	iBoxEnd();
?>
