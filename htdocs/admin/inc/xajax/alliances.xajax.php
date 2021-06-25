<?PHP

use EtoA\Core\Configuration\ConfigurationService;

$xajax->register(XAJAX_FUNCTION,"allianceNewsSave");
$xajax->register(XAJAX_FUNCTION,"allianceNewsLoad");
$xajax->register(XAJAX_FUNCTION,"allianceNewsEdit");
$xajax->register(XAJAX_FUNCTION,"allianceNewsLoadUserList");
$xajax->register(XAJAX_FUNCTION,"allianceNewsDel");
$xajax->register(XAJAX_FUNCTION,"allianceNewsRemoveOld");
$xajax->register(XAJAX_FUNCTION,"allianceNewsSetBanTime");

$xajax->register(XAJAX_FUNCTION,"showSpend");

function allianceNewsLoad()
{
	ob_start();

		$res=dbquery("
		SELECT
			alliance_news_id,
			alliance_news_title,
			alliance_news_text,
			alliance_news_date,
			a.alliance_name,
			a.alliance_tag,
			a.alliance_id,
			e.alliance_name as e_alliance_name,
			e.alliance_tag as e_alliance_tag,
			e.alliance_id as e_alliance_id,
			user_nick,
			user_id
		FROM
			alliance_news
		LEFT JOIN
			alliances as a
			ON a.alliance_id=alliance_news_alliance_id
		LEFT JOIN
			alliances as e
			ON e.alliance_id=alliance_news_alliance_to_id
		LEFT JOIN
			users
			ON user_id=alliance_news_user_id
		ORDER BY
			alliance_news_date DESC
		");
		if (mysql_num_rows($res)>0)
		{
			echo '<table class="tb">';
			echo '<tr>
			<th>Datum</th>
			<th>Absender</th>
			<th>Empfänger</th>
			<th>Titel / Text</th>
			</tr>';
			while ($arr=mysql_fetch_array($res))
			{
				echo '<tr>
				<td rowspan="2">'.df($arr['alliance_news_date']).'</td>';
				echo '<td id="news_'.$arr['alliance_news_id'].'_alliance" style="border-bottom:1px dotted #999;"><b>';
				if ($arr['alliance_tag']!='')
				{
					echo '['.$arr['alliance_tag'].'] '.$arr['alliance_name'];
				}
				else
				{
					echo '<span style="color:#999;">Allianz existiert nicht!</span>';
				}
				echo '</b></td>';
				echo '<td id="news_'.$arr['alliance_news_id'].'_alliance_to" style="border-bottom:none;"><b>';
				if ($arr['e_alliance_tag']!='')
				{
					echo '['.$arr['e_alliance_tag'].'] '.$arr['e_alliance_name'];
				}
				else
				{
					echo '<span style="color:#999;">Allianz existiert nicht!</span>';
				}
				echo '</b></td>';
				echo '<td id="news_'.$arr['alliance_news_id'].'_title" style="border-bottom:1px dotted #999;';
				echo '"><b>'.stripslashes($arr['alliance_news_title']).'</b></td>';
				echo '<td rowspan="2" id="news_'.$arr['alliance_news_id'].'_actions">
				<a href="javascript:;" onclick="xajax_allianceNewsEdit('.$arr['alliance_news_id'].');"><img src="../images/edit.gif" alt="Edit" style="border:none;" /></a>
				<a href="javascript:;" onclick="if (confirm(\'Beitrag löschen?\')) xajax_allianceNewsDel('.$arr['alliance_news_id'].');"><img src="../images/delete.gif" alt="Delete" style="border:none;" /></a>';
				if ($arr['user_id']>0)
				{
					echo '<a href="javascript:;" onclick="if (confirm(\'Benutzer sperren?\')) xajax_lockUser('.$arr['user_id'].',document.getElementById(\'ban_timespan\').options[document.getElementById(\'ban_timespan\').selectedIndex].value,document.getElementById(\'ban_text\').value);"><img src="../images/lock.png" alt="Lock" style="border:none;" /></a>';
				}
				echo '</td>';
				echo '</tr><tr>';
				echo '<td style="border-top:none;" id="news_'.$arr['alliance_news_id'].'_user">';
				if ($arr['user_nick']!='')
				{
					echo $arr['user_nick'];
				}
				else
				{
					echo '<span style="color:#999;">Spieler existiert nicht!</span>';
				}
				echo '</td>';
				echo '<td style="border-top:none;" id="news_'.$arr['alliance_news_id'].'_public"></td>';
				echo '<td style="border-top:none;" id="news_'.$arr['alliance_news_id'].'_text">'.stripslashes($arr['alliance_news_text']).'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		else
		{
			echo '<i>Keine News vorhanden!</i>';
		}

	$objResponse = new xajaxResponse();
  $objResponse->assign("newsBox","innerHTML", ob_get_contents());
	ob_end_clean();
	return $objResponse;
}

function allianceNewsDel($id)
{
	dbquery("
	DELETE FROM
		alliance_news
	WHERE
		alliance_news_id='".$id."'
	;");
	$objResponse = new xajaxResponse();
  $objResponse->script("xajax_allianceNewsLoad()");
  return $objResponse;
}

function allianceNewsRemoveOld($ts)
{
	$t = time()-$ts;
	dbquery("
	DELETE FROM
		alliance_news
	WHERE
		alliance_news_date<'".$t."'
	;");
	$objResponse = new xajaxResponse();
  $objResponse->alert(mysql_affected_rows()." Beiträge wurden gelöscht!");
  $objResponse->script("xajax_allianceNewsLoad()");
  return $objResponse;
}

function allianceNewsEdit($id)
{
	$objResponse = new xajaxResponse();

	$res = dbquery("
	SELECT
		alliance_news_id
	FROM
		alliance_news
	WHERE
		alliance_news_id!='".$id."'
	;");
	if (mysql_num_rows($res))
	{
		while ($arr=mysql_fetch_array($res))
		{
  		$objResponse->assign("news_".$arr['alliance_news_id']."_actions","innerHTML",'');
  	}
	}
	mysql_free_result($res);

	$res = dbquery("
	SELECT
		*
	FROM
		alliance_news
	WHERE
		alliance_news_id='".$id."'
	;");
	if (mysql_num_rows($res)>0)
	{
		$arr=mysql_fetch_array($res);
		$ares = dbquery("
		SELECT
			alliance_id,
			alliance_name,
			alliance_tag
		FROM
			alliances
		ORDER BY
			alliance_tag
		;");
		$alliances = array();
		if (mysql_num_rows($ares)>0)
		{
			while ($aarr=mysql_fetch_array($ares))
			{
				$alliances[$aarr['alliance_id']]='['.$aarr['alliance_tag'].'] '.$aarr['alliance_name'];
			}
		}

		$out = '<select name="alliance_id" onchange="xajax_allianceNewsLoadUserList('.$id.',this.options[this.selectedIndex].value,0);"><option value="0">(keine)</option>';
		$ca = 0;
		foreach ($alliances as $k => $v)
		{
			$out.= '<option value="'.$k.'"';
			if ($k==$arr['alliance_news_alliance_id'])
			{
				$ca = $k;
				$out.= ' selected="selected"';
			}
			$out.= '>'.$v.'</option>';
		}
		$out.= '</select>';
  	$objResponse->assign("news_".$id."_alliance","innerHTML",$out);

		$out = '<select name="alliance_to_id"><option value="0">(keine)</option>';
		foreach ($alliances as $k => $v)
		{
			$out.= '<option value="'.$k.'"';
			if ($k==$arr['alliance_news_alliance_to_id'])
			{
				$out.= ' selected="selected"';
			}
			$out.= '>'.$v.'</option>';
		}
		$out.= '</select>';
  	$objResponse->assign("news_".$id."_alliance_to","innerHTML",$out);

  	$objResponse->assign("news_".$id."_public","innerHTML",$out);

  	$objResponse->assign("news_".$id."_user","innerHTML",'Lade Spieler...');
  	$objResponse->script("xajax_allianceNewsLoadUserList(".$id.",".$ca.",".$arr['alliance_news_user_id'].");");

		$out = '<textarea name="text" rows="6" cols="45" >'.stripslashes($arr['alliance_news_text']).'</textarea>';
  	$objResponse->assign("news_".$id."_text","innerHTML",$out);

		$out = '<input type="text" name="title" size="45" value="'.stripslashes($arr['alliance_news_title']).'" />';
  	$objResponse->assign("news_".$id."_title","innerHTML",$out);

		$out = '<input type="button" onclick="xajax_allianceNewsSave('.$id.',xajax.getFormValues(\'newsForm\'))" value="Speichern" /><br/>
		<input type="button" onclick="xajax_allianceNewsLoad()" value="Abbrechen" />';
  	$objResponse->assign("news_".$id."_actions","innerHTML",$out);
 	}
  return $objResponse;
}



function allianceNewsLoadUserList($nid,$aid,$uid)
{
	$objResponse = new xajaxResponse();
    $out = '';
	if ($aid>0)
	{
		$out = '<select name="user_id"><option value="0">(keiner)</option>';
		$res = dbquery("
		SELECT
			user_id,
			user_nick
		FROM
			users
		WHERE
			user_alliance_id='".$aid."'
		;");
		if (mysql_num_rows($res))
		{
			while ($arr=mysql_fetch_array($res))
			{
				$out.= '<option value="'.$arr['user_id'].'"';
				if ($uid==$arr['user_id'])
				{
					$out.= ' selected="selected"';
				}
				$out.= '>'.$arr['user_nick'].'</option>';
			}
		}
		$out.= '</select>';

  }
  else
  {
  	$out.='<option value="0">(Keine Allianz gewählt)</option>';
  }
	$out.='</select>';

 	$objResponse->assign("news_".$nid."_user","innerHTML",$out);

  return $objResponse;
}

function allianceNewsSave($id,$form)
{
	dbquery("
	UPDATE
		alliance_news
	SET
		alliance_news_alliance_id='".$form['alliance_id']."',
		alliance_news_alliance_to_id='".$form['alliance_to_id']."',
		alliance_news_user_id='".$form['user_id']."',
		alliance_news_title='".addslashes($form['title'])."',
		alliance_news_text='".addslashes($form['text'])."'
	WHERE
		alliance_news_id='".$id."'
	");
	$objResponse = new xajaxResponse();
 	$objResponse->script("xajax_allianceNewsLoad()");
  return $objResponse;
}

function allianceNewsSetBanTime($time, $text)
{
    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app['etoa.config.service'];

    $config->set('townhall_ban', $time, $text);

    $objResponse = new xajaxResponse();
    $objResponse->alert("Einstellungen gespeichert!");
    return $objResponse;
}

function showSpend($allianceId,$form)
{
	ob_start();

	$ures = dbquery("SELECT
					user_id,
					user_nick,
					user_points,
					user_alliance_rank_id
				FROM
					users
				WHERE
					user_alliance_id=".$allianceId."
				ORDER BY
					user_points DESC,
					user_nick;");
	$members = array();
	if (mysql_num_rows($ures)>0)
	{
		while($uarr=mysql_fetch_array($ures))
		{
			$members[$uarr['user_id']] = $uarr;
		}
	}

	$sum = false;
	$user = 0;
    $limit = 0;

	// Summierung der Einzahlungen
	if($form['output']==1)
  	{
  		$sum = true;
	}

	// Limit
	if($form['limit']>0)
	{
		$limit = $form['limit'];
	}

	// User
	if($form['user_spends']>0)
	{
		$user = $form['user_spends'];
	}

	if($sum)
	{
  		if($user>0)
		{
	  		$user_sql = "AND alliance_spend_user_id='".$user."'";
	  		$user_message = "von ".$members[$user]['user_nick']." ";
		}
		else
		{
			$user_sql = "";
			$user_message = "";
		}

		echo "Es werden die bisher eingezahlten Rohstoffe ".$user_message." angezeigt.<br><br>";

		// Läd Einzahlungen
		$res = dbquery("
		SELECT
			SUM(alliance_spend_metal) AS metal,
			SUM(alliance_spend_crystal) AS crystal,
			SUM(alliance_spend_plastic) AS plastic,
			SUM(alliance_spend_fuel) AS fuel,
			SUM(alliance_spend_food) AS food
		FROM
			alliance_spends
		WHERE
			alliance_spend_alliance_id='".$allianceId."'
			".$user_sql.";");

		if(mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_assoc($res);

			tableStart("Total eingezahlte Rohstoffe ".$user_message."");
			echo "<tr>
							<th class=\"resmetalcolor\" style=\"width:20%\">".RES_METAL."</th>
							<th class=\"rescrystalcolor\" style=\"width:20%\">".RES_CRYSTAL."</th>
							<th class=\"resplasticcolor\" style=\"width:20%\">".RES_PLASTIC."</th>
							<th class=\"resfuelcolor\" style=\"width:20%\">".RES_FUEL."</th>
							<th class=\"resfoodcolor\" style=\"width:20%\">".RES_FOOD."</th>
						</tr>";
			echo "<tr>
							<td>".nf($arr['metal'])."</td>
							<td>".nf($arr['crystal'])."</td>
							<td>".nf($arr['plastic'])."</td>
							<td>".nf($arr['fuel'])."</td>
							<td>".nf($arr['food'])."</td>
						</tr>";
			tableEnd();
		}
		else
		{
			iBoxStart("Einzahlungen");
			echo "Es wurden noch keine Rohstoffe eingezahlt!";
			iBoxEnd();
		}
	}
	// Einzahlungen werden einzelen ausgegeben
	else
	{
		if($user>0)
		{
			$user_sql = "AND alliance_spend_user_id='".$user."'";
			$user_message = "von ".$members[$user]['user_nick']." ";
		}
		else
		{
			$user_sql = "";
			$user_message = "";
		}

		if($limit>0)
		{
			if($limit==1)
			{
				echo "Es wird die letzte Einzahlung ".$user_message."gezeigt.<br><br>";
			}
			else
			{
				echo "Es werden die letzten ".$limit." Einzahlungen ".$user_message."gezeigt.<br><br>";
			}

			$limit_sql = "LIMIT ".$limit."";
		}
		else
		{
			echo "Es werden alle bisherigen Einzahlungen ".$user_message."gezeigt.<br><br>";
			$limit_sql = "";
		}

		// Läd Einzahlungen
		$res = dbquery("
		SELECT
			*
		FROM
			alliance_spends
		WHERE
			alliance_spend_alliance_id='".$allianceId."'
			".$user_sql."
		ORDER BY
			alliance_spend_time DESC
		".$limit_sql.";");
		if(mysql_num_rows($res)>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				tableStart("".$members[$arr['alliance_spend_user_id']]['user_nick']." - ".df($arr['alliance_spend_time'])."");
				echo "<tr>
								<th class=\"resmetalcolor\" style=\"width:20%\">".RES_METAL."</th>
								<th class=\"rescrystalcolor\" style=\"width:20%\">".RES_CRYSTAL."</th>
								<th class=\"resplasticcolor\" style=\"width:20%\">".RES_PLASTIC."</th>
								<th class=\"resfuelcolor\" style=\"width:20%\">".RES_FUEL."</th>
								<th class=\"resfoodcolor\" style=\"width:20%\">".RES_FOOD."</th>
							</tr>";
				echo "<tr>
								<td>".nf($arr['alliance_spend_metal'])."</td>
								<td>".nf($arr['alliance_spend_crystal'])."</td>
								<td>".nf($arr['alliance_spend_plastic'])."</td>
								<td>".nf($arr['alliance_spend_fuel'])."</td>
								<td>".nf($arr['alliance_spend_food'])."</td>
							</tr>";
				tableEnd();
			}

		}
		else
		{
			iBoxStart("Einzahlungen");
			echo "Es wurden noch keine Rohstoffe eingezahlt!";
			iBoxEnd();
		}
	}

	$objResponse = new xajaxResponse();
	$objResponse->assign("spends","innerHTML",ob_get_contents());
	ob_end_clean();
  	return $objResponse;
}


?>
