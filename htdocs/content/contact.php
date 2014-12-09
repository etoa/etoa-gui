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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//
	
	/**
	* Provides game admin contact infos
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo '<h1>Kontakt</h1>';

	if (isset($_GET['rcpt']) && intval($_GET['rcpt'])>0)	
	{
		$rcpt = intval($_GET['rcpt']);
		
		echo '<h2>Mail an Game-Administrator</h2>
		<form action="?page='.$page.'&amp;sendrcpt='.$rcpt.'" method="post"><div>';
		$res=dbquery("
		SELECT
			user_nick,
			user_email
		FROM
			admin_users
		WHERE
			user_id=".$rcpt.";
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			echo '<input type="hidden" name="mail_rcpt" value="'.$arr[0].'<'.$arr[1].'>" />';
			tableStart();
			echo '<tr><th>Sender:</th><td>'.$cu->nick.'&lt;'.$cu->email.'&gt;</td></tr>';
			echo '<tr><th>Empfänger:</th><td>'.$arr[0].'&lt;'.$arr[1].'&gt;</td></tr>';
			echo '<tr><th>Titel:</th><td><input type="text" name="mail_subject" value="" size="50" /></td></tr>';
			echo '<tr><th>Text:</th><td><textarea name="mail_text" rows="6" cols="80"></textarea></td></tr>';
			tableEnd();
			echo '<input type="submit" name="submit" value="Senden" /> &nbsp;
			<input type="button" onclick="document.location=\'?page='.$page.'\'" value="Abbrechen" />
			</div></form>';
		}

	}
	else
	{	
		if (isset($_POST['submit']))
		{
			$res=dbquery("
			SELECT
				user_nick,
				user_email
			FROM
				admin_users
			WHERE
				user_id=".intval($_GET['sendrcpt']).";
			");
			if (mysql_num_rows($res)>0)
			{
				$text = "InGame-Anfrage ".Config::getInstance()->roundname->v."\n----------------------\n\n";
				$text.= "Nick: ".$cu->nick."\n";
				$text.= "ID: ".$cu->id."\n";
				$text.= "IP/Host: ".$_SERVER['REMOTE_ADDR']." (".Net::getHost($_SERVER['REMOTE_ADDR']).")\n\n";
				$text.= $_POST['mail_text'];

				$mail = new Mail("InGame-Anfrage: ".$_POST['mail_subject'],$text);
				if ($mail->send($_POST['mail_rcpt'],$cu->nick."<".$cu->email.">"))
				{
					echo '<div style="color:#0f0"><b>Vielen Dank!</b> Deine Nachricht wurde gesendet!</div><br/>';
				}
			}
		}
		
		$tm = new TextManager();
		$contactText = $tm->getText('contact_message');
		if ($contactText->enabled && !empty($contactText->content))
		{
			iBoxStart("Wichtig");
			echo text2html($contactText->content);
			iBoxEnd();
		}
		
		$admins = AdminUser::getAll();
		if (count($admins) > 0)
		{
			tableStart('Kontaktpersonen für diese Runde');
			foreach ($admins as $arr)
			{
				if ($arr->isContact) {
					echo '<tr><td class="tbldata">'.$arr->nick.'</td>';
					if (stristr($arr->email, "@etoa.ch")) {
						echo '<td class="tbldata"><a href="mailto:'.$arr->email.'">'.$arr->email.'</a></td>';
						echo '<td><a href="?page='.$page.'&amp;rcpt='.$arr->id.'">Mailformular</a></td>';
					} else {
						echo '<td class="tbldata">-</td>';
						echo '<td class="tbldata">-</td>';
					}
					if ($arr->board_url !='') {
						echo '<td><a href="'.$arr->board_url.'" onclick="window.open(\''.$arr->board_url.'\');return false;">Foren-Profil</a></td>';
					} else {
						echo '<td>-</td>';
					}
					echo '</tr>';
				}
			}
			tableEnd();
		} else {
			echo "<i>Keine Kontaktpersonen vorhanden!</i>";
		}		
		
		iBoxStart('Impressum');
		$tm = new TextManager();
		$impressum = $tm->getText('impressum');
		if ($impressum->enabled && !empty($impressum->content))
		{
			echo text2html($impressum->content);
		}
		iBoxEnd();

		iBoxStart('Powered by');
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/php.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/mysql.png" alt="MySQL" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/apache.png" alt="Apache" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/xajax.png" alt="XAJAX" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/smarty.png" alt="Smarty" />';
		iBoxEnd();
	}
?>                                                  
                                                    
                                                    
