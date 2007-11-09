<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: contact.php
	// 	Topic: Kontakt
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: x.x.200x
	// 	Bearbeitet von: Lamborghini
	// 	Bearbeitet am: 10.05.2006
	// 	Kommentar:
	//

	echo "<h1>Kontakt</h1>";

	if (isset($_GET['rcpt']) && $_GET['rcpt']>0)	
	{
		echo '<h2>Mail an Game-Administrator</h2>
		<form action="?page='.$page.'" method="post"><div>';
		$res=dbquery("
		SELECT
			user_nick,
			user_email
		FROM
			".$db_table['admin_users']."
		WHERE
			user_id=".$_GET['rcpt'].";
		");
		$arr=mysql_fetch_row($res);
		echo '<input type="hidden" name="mail_rcpt" value="'.$arr[0].'<'.$arr[1].'>" />';
		echo '<table class="tb">';
		echo '<tr><th>Sender:</th><td>'.$_SESSION[ROUNDID]['user']['nick'].'&lt;'.$_SESSION[ROUNDID]['user']['email'].'&gt;</td></tr>';
		echo '<tr><th>Empfänger:</th><td>'.$arr[0].'&lt;'.$arr[1].'&gt;</td></tr>';
		echo '<tr><th>Titel:</th><td><input type="text" name="mail_subject" value="" size="50" /></td></tr>';
		echo '<tr><th>Text:</th><td><textarea name="mail_text" rows="6" cols="80"></textarea></td></tr>';
		echo '</table><br/><input type="submit" name="submit" value="Senden" /> &nbsp; 
		<input type="button" onclick="document.location=\'?page='.$page.'\'" value="Abbrechen" />
		</div></form>';		
	}
	else
	{	
		if (isset($_POST['submit']))
		{
      $email_header = "From: Escape to Andromeda<mail@etoa.net>\n";
      $email_header .= "Reply-To: ".$_SESSION[ROUNDID]['user']['nick']."<".$_SESSION[ROUNDID]['user']['email'].">\n";
      $email_header .= "X-Mailer: PHP/" . phpversion(). "\n";
      $email_header .= "X-Sender-IP: ".$REMOTE_ADDR."\n";
      $email_header .= "Content-type: text/html\n";
      $email_header .= "Content-Style-Type: text/css\n";					
			mail_queue($_POST['mail_rcpt'],"EtoA InGame-Anfrage (".GAMEROUND_NAME."): ".$_POST['mail_subject'],$_SESSION[ROUNDID]['user']['nick']." schreibt: ".$_POST['mail_text'],$email_header);
			echo '<div style="color:#0f0"><b>Vielen Dank!</b> Deine Nachricht wurde gesendet!</div><br/>';
		}
		
		echo text2html($conf['contact_message']['v'])."<br/><br/>";
		$res = dbquery("
			SELECT 
				user_id,
				user_nick,
				user_email,
				group_name
			FROM 
				".$db_table['admin_users']."
			INNER JOIN
				".$db_table['admin_groups']."
				ON user_admin_rank=group_id
				AND group_level<3
		;");
		if (mysql_num_rows($res)>0)
		{
			infobox_start('Kontaktpersonen für diese Runde',1);
			while ($arr = mysql_fetch_array($res))
			{
				echo '<tr><td class="tbldata">'.$arr['user_nick'].'</td>';
				echo '<td class="tbldata">'.$arr['group_name'].'</td>';
				if (stristr($arr['user_email'],"@etoa.ch"))
					echo '<td class="tbldata"><a href="mailto:'.$arr['user_email'].'">'.$arr['user_email'].'</a></td>';
	      else
	      	echo '<td class="tbldata">-</td>';
	      echo '<td class="tbldata"><a href="?page='.$page.'&amp;rcpt='.$arr['user_id'].'">Mailformular</a></td>';
				echo '</tr>';
			}
			infobox_end(1);
		}
		else
			echo "<i>Keine Kontaktpersonen vorhanden!</i>";

		infobox_start('Impressum');
		echo 'EtoA Gaming<br/>Grenzweg 4<br/>3377 Walliswil-Wangen<br/>Schweiz<br/><a href="mailto:mail@etoa.ch">mail@etoa.ch</a>';     
		infobox_end();   

		infobox_start('Powered by');
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/php.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/mysql.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/apache.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/gentoo.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/xhtml.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/css.png" alt="PHP" />';     
		infobox_end();                                    
	}
?>                                                  
                                                    
                                                    