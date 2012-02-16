<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
// $Id$
//////////////////////////////////////////////////////

if (isset($_GET['sendpass']))
{
	$tpl->assign('title', 'Passwort senden');
	if (isset($_POST['sendpass_submit']))
	{
		$user = AdminUser::findByNick($_POST['user_nick']);
		if ($user != null)
		{
			// TODO: Do not generate password immediately, but send confirmation token
		
			$pw = generatePasswort();
			$user->setPassword($pw, true);

			$msg = "Hallo ".$user->nick.".\n\nDu hast für die Administration der ".Config::getInstance()->roundname->v." von EtoA ein neues Passwort angefordert.\n\n";
			$msg.= "Das neue Passwort lautet: $pw\n\n";
			$msg.= "Diese Anfrage wurde am ".date("d.m.Y")." um ".date("H:i")." Uhr vom Computer ".Net::getHost($_SERVER['REMOTE_ADDR'])." aus in Auftrag gegeben.\nBitte denke daran, das Passwort nach dem ersten Login zu ändern!";
			$mail = new Mail("Neues Administrationspasswort",$msg);
			$mail->send($user->email);
			
			$tpl->assign('status_msg', "Das Passwort wurde geändert und dir per Mail zugestellt!");
			$tpl->assign('button_msg', "Zum Login");
			$tpl->assign('button_target', "?");
			
			add_log(8,"Der Administrator ".$user->nick." (ID: ".$user->id.") fordert per E-Mail (".$user->email.") von ".$_SERVER['REMOTE_ADDR']." aus ein neues Passwort an.",time());					
		}
		else
		{
			$tpl->assign('status_msg', "Dieser Benutzer existiert nicht!");
			$tpl->assign('button_msg', "Nochmals versuchen");
			$tpl->assign('button_target', "?sendpass=1");
		}
		$view = "admin/login_status";
	}
	else
	{
		$view = "admin/request_password";
	}
}
else
{
	if (AdminUser::countAll() == 0)
	{
		$tpl->assign('title', 'Admin-User erstellen');
		if (isset($_POST['newuser_submit']) && $_POST['user_email']!="" && $_POST['user_nick']!="" && $_POST['user_password']!='')
		{
			$nu = new AdminUser();
			$nu->email = $_POST['user_email'];
			$nu->nick = $_POST['user_nick'];
			$nu->name = $_POST['user_nick'];
			$nu->adminRank = 8;	// TODO: Constant
			$nu->save();
			$nu->setPassword($_POST['user_password']);

			$tpl->assign('status_msg', "Benutzer wurde erstellt!");
			$tpl->assign('button_msg', "Weiterfahren");
			$tpl->assign('button_target', "?");			

			$view = "admin/login_status";
		}
		else
		{
			$view = "admin/login_newuser";
		}			
	}
	else
	{
		$str = $s->lastError;
		$clr = 3;

		if ($str!="" && $s->lastErrorCode!="nologin")
		{
			if ($clr==3)
				$tpl->assign('msg_style', "warn");
			elseif ($clr==2)
				$tpl->assign('msg_style', "error");
			elseif ($clr==1)
				$tpl->assign('msg_style', "ok");
			$tpl->assign('msg', $str);
		}
		
		$tpl->assign('login_target', "?".$_SERVER['QUERY_STRING']);
		$tpl->assign('game_login_url', Config::getInstance()->loginurl->v);
		
		$view = "admin/login";
	}
}
$tpl->assign("content_for_layout", $tpl->fetch("views/".$view.".html"));
$tpl->assign("content_overflow", ob_get_clean());
?>