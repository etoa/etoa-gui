<?PHP

function allianceNewsLoad()
{
	global $db_table;
	ob_start();

		$res=dbquery("
		SELECT
			alliance_news_id,
			alliance_news_title,
			alliance_news_text,
			alliance_news_date,
			alliance_news_public,
			a.alliance_name,
			a.alliance_tag,
			a.alliance_id,
			e.alliance_name as e_alliance_name,
			e.alliance_tag as e_alliance_tag,
			e.alliance_id as e_alliance_id,
			user_nick,
			user_id			
		FROM 
			".$db_table['alliance_news']."
		LEFT JOIN
			".$db_table['alliances']." as a
			ON a.alliance_id=alliance_news_alliance_id
		LEFT JOIN
			".$db_table['alliances']." as e
			ON e.alliance_id=alliance_news_alliance_to_id
		LEFT JOIN
			".$db_table['users']." 
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
				echo ($arr['alliance_news_public']==1) ? 'color:#FF9900;' : 'color:#99FF99;' ;
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
  $objResponse->addAssign("newsBox","innerHTML", ob_get_contents());
	ob_end_clean();
	return $objResponse;		
}

function allianceNewsDel($id)
{
	global $db_table;
	dbquery("
	DELETE FROM
		".$db_table['alliance_news']."
	WHERE
		alliance_news_id='".$id."'
	;");	
	$objResponse = new xajaxResponse();
  $objResponse->addScript("xajax_allianceNewsLoad()");
  return $objResponse;		
}

function allianceNewsRemoveOld($ts)
{
	global $db_table;
	$t = time()-$ts;
	dbquery("
	DELETE FROM
		".$db_table['alliance_news']."
	WHERE
		alliance_news_date<'".$t."'
	;");	
	$objResponse = new xajaxResponse();
  $objResponse->addAlert(mysql_affected_rows()." Beiträge wurden gelöscht!");
  $objResponse->addScript("xajax_allianceNewsLoad()");
  return $objResponse;			
}

function allianceNewsEdit($id)
{
	global $db_table;
	$objResponse = new xajaxResponse();

	$res = dbquery("
	SELECT 
		alliance_news_id
	FROM
		".$db_table['alliance_news']."
	WHERE
		alliance_news_id!='".$id."'
	;");
	if (mysql_num_rows($res))
	{
		while ($arr=mysql_fetch_array($res))
		{
  		$objResponse->addAssign("news_".$arr['alliance_news_id']."_actions","innerHTML",$out);
  	}
	}
	mysql_free_result($res);

	$res = dbquery("
	SELECT 
		*
	FROM
		".$db_table['alliance_news']."
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
			".$db_table['alliances']."
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
  	$objResponse->addAssign("news_".$id."_alliance","innerHTML",$out);
		
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
  	$objResponse->addAssign("news_".$id."_alliance_to","innerHTML",$out);

		$out = '<input type="radio" name="public" value="1" ';
		if ($arr['alliance_news_public']==1)
		{
			$out.= ' checked="checked"';
		}
		$out.= '/> Öffentlich<br/>';
		$out.= '<input type="radio" name="public" value="0" ';
		if ($arr['alliance_news_public']==0)
		{
			$out.= ' checked="checked"';
		}
		$out.= '/> Privat';	
  	$objResponse->addAssign("news_".$id."_public","innerHTML",$out);

  	$objResponse->addAssign("news_".$id."_user","innerHTML",'Lade Spieler...');
  	$objResponse->addScript("xajax_allianceNewsLoadUserList(".$id.",".$ca.",".$arr['alliance_news_user_id'].");");

		$out = '<textarea name="text" rows="6" cols="45" >'.stripslashes($arr['alliance_news_text']).'</textarea>';
  	$objResponse->addAssign("news_".$id."_text","innerHTML",$out);
		
		$out = '<input type="text" name="title" size="45" value="'.stripslashes($arr['alliance_news_title']).'" />';
  	$objResponse->addAssign("news_".$id."_title","innerHTML",$out);
		
		$out = '<input type="button" onclick="xajax_allianceNewsSave('.$id.',xajax.getFormValues(\'newsForm\'))" value="Speichern" /><br/>
		<input type="button" onclick="xajax_allianceNewsLoad()" value="Abbrechen" />';
  	$objResponse->addAssign("news_".$id."_actions","innerHTML",$out);
 	}
  return $objResponse;		
}



function allianceNewsLoadUserList($nid,$aid,$uid)
{
	global $db_table;
	$objResponse = new xajaxResponse();

	if ($aid>0)
	{
		$out = '<select name="user_id"><option value="0">(keiner)</option>';
		$res = dbquery("
		SELECT
			user_id,
			user_nick
		FROM
			".$db_table['users']."
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

 	$objResponse->addAssign("news_".$nid."_user","innerHTML",$out);	

  return $objResponse;		
}

function allianceNewsSave($id,$form)
{
	global $db_table;
	dbquery("
	UPDATE
		".$db_table['alliance_news']."
	SET
		alliance_news_alliance_id='".$form['alliance_id']."',
		alliance_news_alliance_to_id='".$form['alliance_to_id']."',
		alliance_news_user_id='".$form['user_id']."',
		alliance_news_public='".$form['public']."',
		alliance_news_title='".addslashes($form['title'])."',
		alliance_news_text='".addslashes($form['text'])."'
	WHERE
		alliance_news_id='".$id."'
	");                	
	$objResponse = new xajaxResponse();
 	$objResponse->addScript("xajax_allianceNewsLoad()");
  return $objResponse;		
}

function allianceNewsSetBanTime($time,$text)
{
	global $db_table;
	dbquery("
	UPDATE
		".$db_table['config']."
	SET
		config_value='".$time."',
		config_param1='".addslashes($text)."'
	WHERE
		config_name='townhall_ban'
	");                	
	$objResponse = new xajaxResponse();
 	$objResponse->addAlert("Einstellungen gespeichert!");
  return $objResponse;		
}

$xajax->registerFunction("allianceNewsSave");
$xajax->registerFunction("allianceNewsLoad");
$xajax->registerFunction("allianceNewsEdit");
$xajax->registerFunction("allianceNewsLoadUserList");
$xajax->registerFunction("allianceNewsDel");
$xajax->registerFunction("allianceNewsRemoveOld");
$xajax->registerFunction("allianceNewsSetBanTime");



?>