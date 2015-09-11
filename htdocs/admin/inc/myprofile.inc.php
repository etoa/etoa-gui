<?PHP

	$tpl->setView("myprofile");
	$tpl->assign("title", "Mein Profil");
	
	if (isset($_POST['submitpw']))
	{
		if ($cu->checkEqualPassword($_POST['user_password_old']))
		{
			if ($_POST['user_password']==$_POST['user_password2'] && $_POST['user_password_old']!=$_POST['user_password'])
			{
				if (strlen($_POST['user_password'])>=PASSWORD_MINLENGHT)
				{
					$cu->setPassword($_POST['user_password']);
					$tpl->assign("msg", "Das Passwort wurde ge&auml;ndert!");
					add_log(8,$cu->id." ändert sein Passwort",time());
				}
				else
					$tpl->assign("msg", "Das Passwort ist zu kurz! Es muss mindestens ".PASSWORD_MINLENGHT." Zeichen lang sein!");			
			}
			else
				$tpl->assign("errmsg", "Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!");			
		}
		else
			$tpl->assign("errmsg", "Das alte Passwort stimmt nicht mit dem gespeicherten Wert &uuml;berein!");
	}

	if (isset($_POST['submitdata']))
	{
		$cu->name = $_POST['user_name'];
		$cu->email = $_POST['user_email'];
		$cu->boardUrl = $_POST['user_board_url'];
		$cu->userTheme = $_POST['user_theme'];
		$cu->ticketEmail = $_POST['ticketmail'];
		$cu->playerId = $_POST['player_id'];
		$cu->save();
		
		if ($cu->playerId != $_POST['player_id'])
		{
			dbquery("UPDATE
				users
			SET
				user_ghost='0'
			WHERE
				user_id='".$arr['player_id']."';");
			
			dbquery("UPDATE
				users
			SET
				user_ghost='1'
			WHERE
				user_id='".$_POST['player_id']."';");
		}
		
		$tpl->assign("msg", "Die Daten wurden ge&auml;ndert!");
		add_log(8,$cu->nick." ändert seine Daten");
	}
	
	$tpl->assign("user", $cu);
	$tpl->assign("users", Users::getArray());

?>