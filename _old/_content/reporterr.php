<?PHP

	//////////////////////////////////////////
	// CONTACTFORM PHP-SKRIPT by dysign.ch  //
	// Created 2004 by Nicolas Perrenoud		//
	// Version 1.1a 												//
	//////////////////////////////////////////

	// INFORMATION //
	
	/*	
		DB-Table-Names are stored in $db_table and are definded in file conf.inc.php
		Style-Information ist stored in style.css
	*/
	
	// DEFINITIONS //

	define(RETURN_PAGE,$page);
	define(TABLE_WIDTH,500);
	define(TABLE_ALIGN,"");
	define(SITE_NAME,"Browsergame");
	define(SITE_URL,get_config_p2("site_email"));
	define(LANG,"dd");		// Sprache: dd für du (persönlich), ds für sie (geschäftlich), e für englisch
	define(REQ_EMAIL,0);
	define(REQ_PHONE,0);
	define(EMAIL_TO,get_config_val("site_email"));
	define(EMAIL_FROM_MAIL,get_config_val("site_email"));
	define(EMAIL_FROM_NAME,get_config_p1("site_email")." (".get_config_p2("site_email").")");

	// LANGUAGE //

	$text['title']['dd']="Fehler melden";
	$text['title']['ds']="Kontaktformular";
	$text['title']['e']="Contactform";

	$text['sendsuccess']['dd']="Vielen Dank, deine Anfrage wurde übermittelt!";
	$text['sendsuccess']['ds']="Vielen Dank, Ihre Anfrage wurde übermittelt!";
	$text['sendsuccess']['e']="Thank you, your request was sent to us!";

	$text['senderror']['dd']="<b>Fehler:</b> Deine Anfrage konnte aufgrund eines Problems mit dem Mailserver nicht gesendet werden!";
	$text['senderror']['ds']="<b>Fehler:</b> Ihre Anfrage konnte aufgrund eines Problems mit dem Mailserver nicht gesendet werden!";
	$text['senderror']['e']="<b>Error:</b> We weren't able to send your request because of a problem with the mailserver!";

	$text['jsforgotname']['dd']="Gib bitte deinen Namen ein!";
	$text['jsforgotname']['ds']="Geben Sie bitte Ihren Namen ein!";
	$text['jsforgotname']['e']="Please enter your name!";

	$text['jsforgotemail']['dd']="Du musst eine E-Mail-Adresse eingeben!";
	$text['jsforgotemail']['ds']="Sie müssen eine E-Mail-Adresse eingeben!";
	$text['jsforgotemail']['e']="You have to enter a e-mail address!";

	$text['jsforgotphone']['dd']="Du musst eine Telefonnummer eingeben!";
	$text['jsforgotphone']['ds']="Sie müssen eine Telefonnummer eingeben!";
	$text['jsforgotphone']['e']="You have to enter a phone number!";

	$text['jsforgotsubject']['dd']="Du musst einen Titel eingeben!";
	$text['jsforgotsubject']['ds']="Sie müssen einen Titel eingeben!";
	$text['jsforgotsubject']['e']="You have to enter a subject!";

	$text['jsforgottext']['dd']="Der Text fehlt noch!";
	$text['jsforgottext']['ds']="Sie haben vergessen, einen Text einzugeben!";
	$text['jsforgottext']['e']="You forgot to enter a text!";

	$text['fieldtitlename']['dd']="Name";
	$text['fieldtitlename']['ds']="Name";
	$text['fieldtitlename']['e']="Name";
	
	$text['fieldtitleemail']['dd']="E-Mail";
	$text['fieldtitleemail']['ds']="E-Mail";
	$text['fieldtitleemail']['e']="E-Mail";

	$text['fieldtitlephone']['dd']="Telefon";
	$text['fieldtitlephone']['ds']="Telefon";
	$text['fieldtitlephone']['e']="Phone";

	$text['fieldtitlesubject']['dd']="Art des Fehlers";
	$text['fieldtitlesubject']['ds']="Titel";
	$text['fieldtitlesubject']['e']="Subject";

	$text['fieldtitletext']['dd']="Beschreibung des Fehlers";
	$text['fieldtitletext']['ds']="Text";
	$text['fieldtitletext']['e']="Text";

	$text['goalong']['dd']="Weiter";
	$text['goalong']['ds']="Weiter";
	$text['goalong']['e']="Go along";

	$text['button']['dd']="Senden!";
	$text['button']['ds']="Senden!";
	$text['button']['e']="Send!";
	
	$text['ip']['dd']="Deine IP-Adresse";
	$text['ip']['ds']="Ihre IP-Adresse";
	$text['ip']['e']="Your IP-Address";

	// BEGIN SKRIPT //

	echo "<h4 align=\"center\">: ".$text['title'][LANG]." :</h4>";
	if ((isset($_POST['form_submit']) && $_POST['form_submit']!=""))
	{
		$email_to = EMAIL_TO;
		$email_from_mail = EMAIL_FROM_MAIL;
		$email_from_name = EMAIL_FROM_NAME;
		$email_subject = SITE_NAME." - Kontaktanfrage";
		$email_body = "Kontaktanfrage\n--------------\n\nWebsite: ".get_config_p2("site_email")."\n\nDatum: ".date("d.m.Y")." um ".date("H:i:s")." Uhr\nIP: ".$HTTP_SERVER_VARS['REMOTE_ADDR']." (".gethostbyaddr($HTTP_SERVER_VARS['REMOTE_ADDR']).")\n\nName: ".$_POST['contact_name']."\nE-Mail: ".$_POST['contact_email']."\nTelefon: ".$_POST['contact_phone']."\nTitel: ".$_POST['contact_subject']."\n\nText:\n\n".$_POST['contact_text'];
		
		$email_header = "From:$email_from_name<$email_from_mail>\n";
		$email_header .= "Reply-To: ".$_POST['contact_email']."\n"; 
		$email_header .= "X-Mailer: PHP/" . phpversion(). "\n";          
		$email_header .= "X-Sender-IP: $REMOTE_ADDR\n"; 				
		
		if (@mail($email_to,$email_subject,$email_body,$email_header))
		{
			echo "<p>".$text['sendsuccess'][LANG]."<br/><br/><a href=\"?page=".RETURN_PAGE."\">".$text['goalong'][LANG]."</a></p>";
		}
		else
		{
			echo "<p>".$text['senderror'][LANG]."<br/><br/><a href=\"?page=".RETURN_PAGE."\">".$text['goalong'][LANG]."</a></p>";
		}
	}
	else
	{
		echo "<script type=\"text/javascript\">
		function form_check()
		{
			f = document.forms['php_form'];
			if (f.contact_name.value==\"\")
			{
				alert(\"".$text['jsforgotname'][LANG]."\");
				f.contact_name.focus();
				return false;
			}";
			if (REQ_EMAIL==1)
			{
				echo "if (f.contact_email.value==\"\")
				{
					alert(\"".$text['jsforgotemail'][LANG]."\");
					f.contact_email.focus();
					return false;
				}";						
			}
			if (REQ_PHONE==1)
			{
				echo "if (f.contact_phone.value==\"\")
				{
					alert(\"".$text['jsforgotphone'][LANG]."\");
					f.contact_phone.focus();
					return false;
				}";						
			}			
			echo "if (f.contact_subject.value==\"\")
			{
				alert(\"".$text['jsforgotsubject'][LANG]."\");
				f.contact_subject.focus();
				return false;
			}
			if (f.contact_text.value==\"\")
			{
				alert(\"".$text['jsforgottext'][LANG]."\");
				f.contact_text.focus();
				return false;
			}
			return true;	
		}
		</script>";
		echo "<form name=\"php_form\" action=\"?page=$page\" method=\"POST\">";
		echo "<table class=\"tbborder\" cellspacing=\"0\" cellpadding=\"2\" width=\"".TABLE_WIDTH."\" align=\"".TABLE_ALIGN."\">";
		echo "<tr><th align=\"left\">".$text['fieldtitlename'][LANG]."*:</th><td><input type=\"text\" name=\"contact_name\" value=\"\"></td></tr>";
		echo "<tr><th align=\"left\">".$text['fieldtitleemail'][LANG];
		if (REQ_EMAIL==1) echo "*";
		echo ":</th><td><input type=\"text\" name=\"contact_email\" value=\"\" size=\"35\"></td></tr>";
		//echo "<tr><th align=\"left\">".$text['fieldtitlephone'][LANG];
		//if (REQ_PHONE==1) echo "*";
		//echo ":</th><td><input type=\"text\" name=\"contact_phone\" value=\"\" size=\"35\"></td></tr>";
		echo "<tr><th align=\"left\">".$text['fieldtitlesubject'][LANG]."*:</th><td><input type=\"text\" name=\"contact_subject\" value=\"\" size=\"40\"></td></tr>";
		echo "<tr><th align=\"left\">".$text['fieldtitletext'][LANG]."*:</th><td><textarea name=\"contact_text\" cols=\"30\" rows=\"7\"></textarea></td></tr>";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "<tr><td colspan=\"2\" style=\"font-size:7pt;\" align=\"center\"><input type=\"submit\" name=\"form_submit\" value=\"".$text['button'][LANG]."\" class=\"button\" onClick=\"return form_check();\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$text['ip'][LANG].": ".gethostbyaddr($HTTP_SERVER_VARS['REMOTE_ADDR'])."</td></tr>";
		echo "</table></form>";
	}
	
	// END SKRIPT //
?>
