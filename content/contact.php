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
	// 	File: contact.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Provides game admin contact infos
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

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
			admin_users
		WHERE
			user_id=".$_GET['rcpt'].";
		");
		$arr = mysql_fetch_row($res);
		echo '<input type="hidden" name="mail_rcpt" value="'.$arr[0].'<'.$arr[1].'>" />';
		echo '<table class="tb">';
		echo '<tr><th>Sender:</th><td>'.$cu->nick().'&lt;'.$cu->email().'&gt;</td></tr>';
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
			$text = "InGame-Anfrage ".GAMEROUND_NAME."\n----------------------\n\n";
			$text.= "Nick: ".$cu->nick()."\n";
			$text.= "ID: ".$s['user_id']."\n";
			$text.= "IP/Host: ".$_SERVER['REMOTE_ADDR']." (".gethostbyaddr($_SERVER['REMOTE_ADDR']).")\n";
			$text.= "Titel: ".$_POST['mail_subject']."\n\n";
			$text.= $_POST['mail_text'];
			
      $email_header = "From: Escape to Andromeda<etoa@orion.etoa.net>\n";
      $email_header .= "Reply-To: ".$cu->nick()."<".$cu->email().">\n";
      $email_header .= "X-Mailer: PHP/" . phpversion(). "\n";
      $email_header .= "X-Sender-IP: ".$REMOTE_ADDR."\n";
      //$email_header .= "Content-type: text/html\n";
      $email_header .= "Content-Style-Type: text/css\n";					
			mail($_POST['mail_rcpt'],"EtoA InGame-Anfrage (".GAMEROUND_NAME."): ".$_POST['mail_subject'],$text,$email_header);
			echo '<div style="color:#0f0"><b>Vielen Dank!</b> Deine Nachricht wurde gesendet!</div><br/>';
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
				echo '<tr><td class="tbldata">'.$arr['user_nick'].'</td>';
				echo '<td class="tbldata">'.$arr['group_name'].'</td>';
				if (stristr($arr['user_email'],"@etoa.ch"))
				{
					echo '<td class="tbldata"><a href="mailto:'.$arr['user_email'].'">'.$arr['user_email'].'</a></td>';
				}
	      else
	      {
	      	echo '<td class="tbldata">-</td>';
	      }
				if ($arr['user_email']!='')
				{
	      	echo '<td class="tbldata"><a href="?page='.$page.'&amp;rcpt='.$arr['user_id'].'">Mailformular</a></td>';
	      }
	      else
	      {
	      	echo '<td class="tbldata">-</td>';
	      }
				if ($arr['user_board_url']!='')
				{
					echo '<td class="tbldata"><a href="'.$arr['user_board_url'].'" onclick="window.open(\''.$arr['user_board_url'].'\');return false;">Foren-Profil</a></td>';
				}
	      else
	      {
	      	echo '<td class="tbldata">-</td>';
	      }	      
				echo '</tr>';
			}
			tableEnd();
		}
		else
			echo "<i>Keine Kontaktpersonen vorhanden!</i>";

		iBoxStart('Impressum');
		echo 'EtoA Gaming<br/>Grenzweg 4<br/>3377 Walliswil-Wangen<br/>Schweiz<br/><a href="mailto:mail@etoa.ch">mail@etoa.ch</a>';     
		iBoxEnd();

		iBoxStart('Powered by');
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/php.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/mysql.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/apache.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/gentoo.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/smarty.png" alt="Smarty" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/pma.png" alt="phpMyAdmin" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/xhtml.png" alt="PHP" />';
		echo '<img style="border:1px solid #000;margin:5px 10px;" src="images/powered/css.png" alt="PHP" />';     
		iBoxEnd();
	}
?>                                                  
                                                    
                                                    