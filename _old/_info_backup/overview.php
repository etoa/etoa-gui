<p>Bitte votet jeden Tag einmal f&uuml;r uns. Damit macht ihr das Spiel bekannter und es wird mehr User geben, die dieses Spiel spielen. Somit wird das Spiel interessanter und die Herausforderung an die Spitze zu kommen noch gr&ouml;sser.</p>

<?PHP
	$res=dbquery("SELECT * FROM ".$db_table['buttons'].";");
	if (mysql_num_rows($res)>0)
	{
		while($arr=mysql_fetch_array($res))
		{
			echo "<a href=\"".$arr['button_url']."\" target=\"_blank\"><img src=\"".$arr['button_img_url']."\" alt=\"".$arr['button_name']."\" /></a><br/> ";
		}
	}	
?>
