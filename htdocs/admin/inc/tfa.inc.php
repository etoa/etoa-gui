<?php	
	$tpl->assign("title", "Mein Profil");

	$tfa = new RobThree\Auth\TwoFactorAuth(APP_NAME);
	if (isset($_POST['tfa_activate']))
	{
		if (!empty($_POST['tfa_challenge']) && $tfa->verifyCode($_SESSION['tfa_activate_secret'], $_POST['tfa_challenge'])) {
			$cu->tfaSecret = $_SESSION['tfa_activate_secret'];
			$cu->save();
			unset($_SESSION['tfa_activate_secret']);
			add_log(8,$cu->nick." aktiviert Zwei-Faktor-Authentifizierung");
			forward('?myprofile');
		} else {
			$secret = $_SESSION['tfa_activate_secret'];
			$tpl->assign("errmsg", "Der eigegebene Code ist ungütig! Bitte wiederhole den Vorgang!");
		}
	} else if (isset($_POST['tfa_disable'])) {
		if (!empty($_POST['tfa_challenge']) && $tfa->verifyCode($cu->tfaSecret, $_POST['tfa_challenge'])) {
			$cu->tfaSecret = "";
			$cu->save();
			unset($_SESSION['tfa_activate_secret']);
			add_log(8,$cu->nick." deaktiviert Zwei-Faktor-Authentifizierung");
			forward('?myprofile');
		} else {
			$tpl->assign("errmsg", "Der eigegebene Code ist ungütig! Bitte wiederhole den Vorgang!");
		}
	}

	if (empty($cu->tfaSecret)) {
		if (!isset($secret)) {
			$secret = $tfa->createSecret();
			$_SESSION['tfa_activate_secret'] = $secret;
		}
		$label = Config::getInstance()->roundname->v . ' : ' . $cu->name;
		$tpl->assign('tfa_qr_code', $tfa->getQRCodeImageAsDataUri($label, $secret));
		$tpl->setView("tfa_activate");
	} else {
		$tpl->setView("tfa_disable");
	}
?>