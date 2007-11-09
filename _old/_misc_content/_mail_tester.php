<?php

    function send_mail($adress,$topic,$text,$style,$align)
    {
        global $db_table;
        $conf = get_all_config();

        if($style=="")
			$style=$conf['default_css_style']['v'];
	
		if($align=="")
			$align="center";
			
        $email_header = "From: Escape to Andromeda<mail@etoa.ch>\n";
        $email_header .= "Reply-To: mail@etoa.ch\n";
        $email_header .= "X-Mailer: PHP/" . phpversion(). "\n";
        $email_header .= "X-Sender-IP: ".$REMOTE_ADDR."\n";
        $email_header .= "Content-type: text/html\n";
        $email_header .= "Content-Style-Type: text/css\n";

        $email_text .= "
        <html>
        	<head>
				<link rel=\"stylesheet\" type=\"text/css\" href=\"http://etoa.h651984.serverkompetenz.net/etoatest/".$style."/style.css\" />
        	</head>
        	<body>
        	<center>
        		<div align=\"center\" style=\"width:600px\">
        			<img src=\"http://etoa.h651984.serverkompetenz.net/etoatest/images/game_logo.jpg\">
        			<hr size=2 width=\"100%\">
        			<br>";

        $email_text .= "<div class=\"infoboxtitle\">$topic</div>
        				<div class=\"infoboxcontent\"><div align=\"".$align."\">$text</div></div><br>";

		$email_text .= "<hr size=2 width=\"100%\">
					<br>
					Fusszeile
				</div>
			</center>
			</body>

        </html>
        ";


        mail($adress,$topic,$email_text,$email_header) or die("Die Mail konnte nicht versendet werden.");


        echo "E-Mail erfolgreich versandt!<br>";
    }


	$e_mail = "b_kurt@besonet.ch";
	$topic = "Registrierung Erfolgreich";
	$text = "Hallo Lamborghini<br>Deine Registration bei EtoA war erfolgreich, hier sind deine Login Daten:<br><br>User: Lamborghini<br>Passwort: testaccount<br><br>Das EtoA-Team wünscht dir viel Spass!<br><br>";
	$text .= "<br>Weitere mögliche Inhaltswiedergaben:<br>Ein link: <a href=\"http://www.etoa.ch/forum\">Forum</a><br><font color=\"red\">Farbiger</font> <font color=\"yellow\">Text</font><br>Bild:<br><img src=\"http://etoa.h651984.serverkompetenz.net/etoatest/images/logo.jpg\">";
	$style = "css_style/Dark";
	$align = "left";
	//npw@gmx.ch
	send_mail($e_mail,$topic,$text,$style,$align);
?>