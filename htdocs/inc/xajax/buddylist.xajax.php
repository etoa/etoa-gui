<?PHP

$xajax->register(XAJAX_FUNCTION,'saveEdit');
$xajax->register(XAJAX_FUNCTION,'addAllianceMembers');

$xajax->register(XAJAX_FUNCTION,'acceptBuddy');
$xajax->register(XAJAX_FUNCTION,'declineBuddy');

//TODO: function needs to be outsourced because needed in other sites as well
$xajax->register(XAJAX_FUNCTION,'addNewBuddy');

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


function acceptBuddy($id, $callback=false)
{
	$ajax = new xajaxResponse();
	ob_start();
	$id = (int)$id;
	
	$uid = $_SESSION['user_id'];

	$bl = new Buddylist($uid);

	if ( true || $bl->acceptBuddy($id) )
	{
		Design::okMsg('Anfrage wurde angenommen.');
	}
	else
	{
		Design::errorMsg($bl->lastError);
		$callback = false;
	}
	
	$ajax->append('info','innerHTML', ob_get_contents() );
	ob_clean();

	if ($callback)
	{
		$data = $bl->getBuddy($id);
		ob_start();
		echo createBuddyRow($data['user'], $data['allow'], $data['comment']);
		$ajax->append('buddylist_body', 'innerHTML', ob_get_contents() );
		ob_clean();
	}
	return $ajax;
}

function declineBuddy($id, $callback=false)
{
	$ajax = new xajaxResponse();
	ob_start();

	$id = intval($id);
	$uid = $_SESSION['user_id'];

	$bl = new Buddylist($uid);

	if ( true || $bl->declineBuddy($id) ) {
		Design::okMsg('Anfrage wurde abgelehnt.');
	}
	else {
	 Design::errorMsg($bl->lastError);
	}
	$ajax->append('info','innerHTML', ob_get_contents() );
	ob_clean();
	return $ajax;
}

/*
 * function to add a buddy (send a request)
 * can be used in the buddylist itself as well as on other pages
 * @TODO: needs to be outsourced that you can access from any page on this function
 *
 * option 1:
 * @param <int> $form the userId of the requested user
 * 
 * option 2:
 * @param <array> $form an array including mandatory usernick and comment
 *
 */
function addNewBuddy($form)
{
	$ajax = new xajaxResponse();

	// init
	$userId = $_SESSION['user_id'];
	$bl = new Buddylist($userId);
	
	// if you add a buddy by id = param ist not an array
	// this means no changes at the page!!!
	if (!is_array($form))
	{
		$buddyId = (int)$form;

		// TODO: set the class for the ok/fail msg
		if ( $bl->addBuddy($buddyId) )
		{

		}
		else
		{

		}
		// set the msg
		$msg = $bl->lastError;
	}
	// if an array was sent
	else
	{
		// check if the data is avaiable
		if (isset($form['usernick']) && isset($form['comment']))
		{
			$userId = get_user_id($form['usernick']);

			// TODO: set the class for the ok/fail msg
			if ( $bl->addBuddy($userId, $form['comment']) )
			{
				// Dom scripting
				// @TODO check if there is already a row if not delete the std msg
				//$row = createBuddyRow();
				$ajax->append($sTarget, 'innerHTML', $row);

			}
			else
			{

			}
			// set the msg
			$msg = $bl->lastError;
		}

		// missing data error
		//$msg = '';

		return $ajax;
	}

}
?>