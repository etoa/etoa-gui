<?PHP

function showTimeBox($parent,$name,$value,$show=1)
{
	$or = new xajaxResponse();
	ob_start();
	if ($show>0)
	{
		show_timebox($name,intval($value),1);				
	}
	else
	{
		echo "-";
	}
	$out = ob_get_contents();
	ob_end_clean();	
	$or->addAssign($parent,"innerHTML",$out);
	return $or;	
}

function allianceRankSelector($parent,$name,$value=0,$aid=0)
{
	$or = new xajaxResponse();
	ob_start();				
	if ($aid!=0)
	{
		$rres = dbquery("
		SELECT
			rank_id,
			rank_name
		FROM
			alliance_ranks
		WHERE
			rank_alliance_id=".$aid."");
		if (mysql_num_rows($rres)>0)
		{
			echo "<select name=\"".$name."\"><option value=\"0\">(Kein Rang)</option>";
			while ($rarr=mysql_fetch_array($rres))
			{
				echo "<option value=\"".$rarr['rank_id']."\"";
				if ($value==$rarr['rank_id'])
				{
					echo " selected=\"selected\"";
				}
				echo ">".$rarr['rank_name']."</option>";
			}
			echo "</select>";
		}
		else
		{
			echo "-";
		}
	}
	else
	{
		echo "-";
	}	
	$out = ob_get_contents();
	ob_end_clean();	
	$or->addAssign($parent,"innerHTML",$out);
	return $or;							
}

function userPointsTable($uid,$target,$length=100,$start=-1,$end=-1)
{
	$t = time();
	if ($start==-1)
	{
		$start = $t-172800;
	}
	if ($end==-1)
	{
		$end = $t;
	}
	
	$or = new xajaxResponse();
	ob_start();
	$limitarr = array(10,20,30,50,100,200);

	echo "<div id=\"pointGraphDetail\" style=\"text-align:center;margin-bottom:6px;\">
	<img src=\"../misc/stats.image.php?user=".$uid."&amp;limit=".$length."&amp;start=".$start."&amp;end=".$end."\" alt=\"Diagramm\" />
	<br/>";
	echo "Zeige maximal <select id=\"pointsLimit\" onchange=\"xajax_userPointsTable($uid,'$target',
	document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
	document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
	document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
	);\">";
	foreach($limitarr as $x)
	{
		echo "<option value=\"$x\"";
		if ($x==$length) echo " selected=\"selected\"";
		echo ">$x</option>";
	}
	echo "</select> Datensätze von <select id=\"pointsTimeStart\" onchange=\"xajax_userPointsTable($uid,'$target',
	document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
	document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
	document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
	);\">";
	for ($x = $t-86400; $x > $t-(14*86400);$x-=86400)
	{
		echo "<option value=\"$x\"";
		if ($x<=$start+300 && $x>=$start-300) echo " selected=\"selected\"";
		echo ">".df($x)."</option>";
	}
	echo "</select> bis <select id=\"pointsTimeEnd\" onchange=\"xajax_userPointsTable($uid,'$target',
	document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
	document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
	document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
	);\">";
	for ($x = $t; $x > $t-(13*86400);$x-=86400)
	{
		echo "<option value=\"$x\"";
		if ($x<=$end+300 && $x>=$end-300) echo " selected=\"selected\"";		
		echo ">".df($x)."</option>";
	}
	echo "</select> 
	
	<br/></div>";
	echo "<table class=\"tb\">";	
	$lres=dbquery("
	SELECT 
		* 
	FROM 
		user_points
	WHERE
		point_user_id=".$uid."
		AND point_timestamp > ".$start."
		AND point_timestamp < ".$end."
	ORDER BY 
		point_timestamp DESC
	LIMIT ".$length."
	;");
	if (mysql_num_rows($lres)>0)
	{
		echo "<tr>
			<th>Datum</th>
			<th>Zeit</th>
			<th>Punkte</th>
			<th>Gebäude</th>
			<th>Forschung</th>
			<th>Flotte</th>
		</tr>";
		while ($larr=mysql_fetch_array($lres))
		{
			echo "<tr>
				<td class=\"tbldata\">".date("d.m.Y",$larr['point_timestamp'])."</td>
				<td class=\"tbldata\">".date("H:i",$larr['point_timestamp'])."</td>
				<td class=\"tbldata\">".nf($larr['point_points'])."</td>
				<td class=\"tbldata\">".nf($larr['point_building_points'])."</td>
				<td class=\"tbldata\">".nf($larr['point_tech_points'])."</td>
				<td class=\"tbldata\">".nf($larr['point_ship_points'])."</td>
			</tr>";   
		}           
	}             
	else          
	{             
		echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
	}             
	echo "</table>";

	$out = ob_get_contents();
	ob_end_clean();	
	$or->addAssign($target,"innerHTML",$out);
	return $or;
}	

function userTickets($uid,$target)
{
	global $abuse_cats;
	global $abuse_status;
	
	$or = new xajaxResponse();
	ob_start();
	echo "<table class=\"tb\">";	
	$lres=dbquery("
	SELECT 
		* 
	FROM 
		abuses
	LEFT JOIN
		admin_users
	ON
		abuse_admin_id=user_id
	WHERE
		abuse_user_id=".$uid."
	ORDER BY 
		abuse_timestamp DESC
	;");
	if (mysql_num_rows($lres)>0)
	{
		echo "<tr>
			<th>ID</th>
			<th>Datum</th>
			<th>Kategorie</th>
			<th>Status</th>
			<th>Admin</th>
			<th>Bearbeitet</th>
			<th>Optionen</th>
		</tr>";
		while ($larr=mysql_fetch_array($lres))
		{
			echo "<tr>
				<td class=\"tbldata\">#".$larr['abuse_id']."</td>
				<td class=\"tbldata\">".df($larr['abuse_timestamp'])."</td>
				<td class=\"tbldata\">".$abuse_cats[$larr['abuse_cat']]."</td>
				<td class=\"tbldata\">".$abuse_status[$larr['abuse_status']]."</td>
				<td class=\"tbldata\">".$larr['user_nick']."</td>
				<td class=\"tbldata\">".df($larr['abuse_admin_timestamp'])."</td>
				<td class=\"tbldata\">[<a href=\"?page=user&sub=tickets&view=".$larr['abuse_id']."\">Details</a>]</td>
			</tr>";   
		}           
	}             
	else          
	{             
		echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
	}             
	echo "</table>";

	$out = ob_get_contents();
	ob_end_clean();	
	$or->addAssign($target,"innerHTML",$out);
	return $or;
}	


function sendUrgendMsg($uid,$subject,$text)
{
	$or = new xajaxResponse();
	if ($text!="" && $subject!="")
	{
		send_msg($uid,USER_MSG_CAT_ID,$subject,$text);
	
		$or->addAlert("Nachricht gesendet!");
		$or->addAssign('urgendmsgsubject',"value","");
		$or->addAssign('urgentmsg',"value","");
	}
	else
	{
		$or->addAlert("Titel oder Text fehlt!");
	}
	return $or;
}	

function showLast5Messages($uid,$target,$limit=5)
{
	$or = new xajaxResponse();
	ob_start();
	echo "<table class=\"tb\">";	
	$lres=dbquery("
	SELECT 
		user_nick,
		message_subject,
		message_text,
		message_id,
		message_timestamp,
		message_read 
	FROM 
		messages
	LEFT JOIN
		users
	ON
		message_user_from=user_id
	ORDER BY
		message_timestamp DESC
	LIMIT
		$limit
	;");
	if (mysql_num_rows($lres)>0)
	{
		echo "<tr>
			<th>Datum</th>
			<th>Sender</th>
			<th>Titel</th>
			<th>Text</th>
			<th>Gelesen</th>
			<th>Optionen</th>
		</tr>";
		while ($larr=mysql_fetch_array($lres))
		{
			echo "<tr>
				<td class=\"tbldata\">".df($larr['message_timestamp'])."</td>
				<td class=\"tbldata\">".$larr['user_nick']."</td>
				<td class=\"tbldata\">".$larr['message_subject']."</td>
				<td class=\"tbldata\">".text2html($larr['message_text'])."</td>
				<td class=\"tbldata\">".($larr['message_read']==1 ? "Ja" : "Nein")."</td>
				<td class=\"tbldata\">[<a href=\"?page=messages&sub=edit&message_id=".$larr['message_id']."\">Details</a>]</td>
			</tr>";   
		}           
	}             
	else          
	{             
		echo "<tr><td class=\"tbldata\">Keine Nachrichten vorhanden!</td></tr>";
	}             
	echo "</table>";

	$out = ob_get_contents();
	ob_end_clean();	
	$or->addAssign($target,"innerHTML",$out);
	return $or;
}


$xajax->registerFunction("showTimeBox");
$xajax->registerFunction("allianceRankSelector");
$xajax->registerFunction("userPointsTable");

$xajax->registerFunction("userTickets");
$xajax->registerFunction("sendUrgendMsg");
$xajax->registerFunction("showLast5Messages");

?>