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
	// $Author$
	// $Date$
	// $Rev$
	//
	
	/**
	* Provides game admin contact infos
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo '<h1>Kontakt</h1>';

	if (isset($_GET['rcpt']) && $_GET['rcpt']>0)	
	{
		echo '<h2>Mail an Game-Administrator</h2>
		<form action="?page='.$page.'&amp;sendrcpt='.intval($_GET['rcpt']).'" method="post"><div>';
		$res=dbquery("
		SELECT
			user_nick,
			user_email
		FROM
			admin_users
		WHERE
			user_id=".intval($_GET['rcpt']).";
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
				user_id=".$_GET['sendrcpt'].";
			");
			if (mysql_num_rows($res)>0)
			{
				$text = "InGame-Anfrage ".ROUNDID."\n----------------------\n\n";
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
		
		iBoxStart("Wichtig");
		echo text2html($conf['contact_message']['v']);
		iBoxEnd();
		
		$res = dbquery("
			SELECT 
				user_id,
				user_nick,
				user_email,
				group_name,
				user_board_url
			FROM 
				admin_users
			INNER JOIN
				admin_groups
				ON user_admin_rank=group_id
				AND group_level<3
		;");
		if (mysql_num_rows($res)>0)
		{
			tableStart('Kontaktpersonen für diese Runde');
			while ($arr = mysql_fetch_array($res))
			{
				echo '<tr><td>'.$arr['user_nick'].'</td>';
				echo '<td>'.$arr['group_name'].'</td>';
				if (stristr($arr['user_email'],"@etoa.ch"))
				{
					echo '<td><a href="mailto:'.$arr['user_email'].'">'.$arr['user_email'].'</a></td>';
				}
	      else
	      {
	      	echo '<td>-</td>';
	      }
				if ($arr['user_email']!='')
				{
	      	echo '<td><a href="?page='.$page.'&amp;rcpt='.$arr['user_id'].'">Mailformular</a></td>';
	      }
	      else
	      {
	      	echo '<td>-</td>';
	      }
				if ($arr['user_board_url']!='')
				{
					echo '<td><a href="'.$arr['user_board_url'].'" onclick="window.open(\''.$arr['user_board_url'].'\');return false;">Foren-Profil</a></td>';
				}
	      else
	      {
	      	echo '<td>-</td>';
	      }	      
				echo '</tr>';
			}
			tableEnd();
		}
		else
			error_msg("Keine Kontaktpersonen vorhanden!");

		iBoxStart('Impressum');
		echo 'EtoA Gaming<br/>Grenzweg 4<br/>3377 Walliswil-Wangen<br/>Schweiz<br/><a href="mailto:mail@etoa.ch">mail@etoa.ch</a>';     
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
                                                    
                                                    