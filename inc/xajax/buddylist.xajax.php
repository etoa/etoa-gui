<?PHP

$xajax->register(XAJAX_FUNCTION,'saveEdit');
$xajax->register(XAJAX_FUNCTION,'addAllianceMembers');

function saveEdit($id,$comment)
{
	$ajax = new xajaxResponse();
	
	$id = intval($id);
	$uid = $_SESSION['user_id'];
	$res = dbquery("SELECT bl_user_id FROM buddylist WHERE bl_id='".$id."' LIMIT 1;");
	
	if (mysql_num_rows($res))
	{
		$arr = mysql_fetch_row($res);
		if ($arr[0]==$uid)
		{
			dbquery("UPDATE buddylist SET bl_comment='".dbEscapeStr($comment)."' WHERE bl_id='".$id."' LIMIT 1;");
		}
		else
		{
			dbquery("UPDATE buddylist SET bl_comment_buddy='".dbEscapeStr($comment)."' WHERE bl_id=".$id." LIMIT 1;");
		}
	}
	return $ajax;
}

function addAllianceMembers($allianceId)
{
	$ajax = new xajaxResponse();

	$aid = (int)$allianceId;
	$uid = $_SESSION['user_id'];
	$users = array();
	$isAlliance = false;
	$count = 0;

	if ($aid > 0)
	{
		$res = dbquery("SELECT user_id FROM users WHERE user_alliance_id='".$aid."';");

		if (mysql_num_rows($res))
		{
			while ( $arr = mysql_fetch_row($res) )
			{
				array_push($users, $arr[0]);
				if ( $arr[0] == $uid ) $isAlliance = true;
			}

			if ( $isAlliance )
			{
				$bl = new Buddylist($uid);

				foreach ($users AS $id)
				{
					if ( $bl->addBuddy($id) ) $count++;
				}

				if ( $count )
					$out = 'Es wurden '.$count.' Mitglieder hinzugefügt.';
				else
					$out = 'Es befinden sich schon alle Allianzmitglieder in deiner Freundesliste.';
			}
			else
				$out = 'Du bist kein Mitglied dieser Allianz.';

		}
		else
			$out = 'Es befinden sich keine User in der Allianz.';
	}
	else
		$out = 'Keine gültige Allianz übergeben.';

	return $xajax;
}
?>