<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
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
			
			$tpl->assign('msg_style', "color_ok");
			$tpl->assign('status_msg', "Das Passwort wurde geändert und dir per Mail zugestellt!");
			$tpl->assign('button_msg', "Zum Login");
			$tpl->assign('button_target', "?");
			
			add_log(8,"Der Administrator ".$user->nick." (ID: ".$user->id.") fordert per E-Mail (".$user->email.") von ".$_SERVER['REMOTE_ADDR']." aus ein neues Passwort an.",time());					
		}
		else
		{
			$tpl->assign('msg_style', "color_warn");
			$tpl->assign('status_msg', "Dieser Benutzer existiert nicht!");
			$tpl->assign('button_msg', "Nochmals versuchen");
			$tpl->assign('button_target', "?sendpass=1");
		}
		$tpl->setView("login_status");
	}
	else
	{
		$tpl->setView("request_password");
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
			$nu->roles = array('master');
			$nu->save();
			$nu->setPassword($_POST['user_password']);

			$tpl->assign('msg_style', "color_ok");
			$tpl->assign('status_msg', "Benutzer wurde erstellt!");
			$tpl->assign('button_msg', "Weiterfahren");
			$tpl->assign('button_target', "?");			

			$tpl->setView("login_status");
		}
		else
		{
			$tpl->setView("login_newuser");
		}
	}
	else
	{
		$str = $s->lastError;
		if ($s->lastErrorCode == "tfa_challenge") {
			$tpl->setView("tfa_challenge");
		} else {
			$tpl->setView("login");
		}
		if ($str!="" && $s->lastErrorCode!="nologin")
		{
			$tpl->assign('msg_style', "color_warn");
			$tpl->assign('msg', $str);
		}
		$tpl->assign('login_target', "?".$_SERVER['QUERY_STRING']);
	}
}
$tpl->assign("content_overflow", ob_get_clean());
$tpl->assign("body_class", "login");
$tpl->setLayout("default/login");
$tpl->render();

?>