<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

use Doctrine\DBAL\Query\QueryBuilder;

/**
* Generates a page for editing table date with
* an advanced form
*
* @param string $module Module-key
*/
function advanced_form($module, $twig)
{
  require_once("inc/form_functions.php");
    require_once("forms/$module.php");
    require_once("inc/advanced_forms.php");
}

/**
* Generates a page for editing table date with
* a simple form
*
* @param string $module Module-key
*/
function simple_form($module, $twig)
{
    require_once("inc/form_functions.php");
    require_once("forms/$module.php");
    require_once("inc/simple_forms.php");
}

/**
* Displays a clickable edit button
*
* @param string $url Url of the link
* @param string $ocl Optional onclick value
*/
function edit_button($url, $ocl="")
{
    if ($ocl!="")
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"../images/icons/edit.png\" alt=\"Bearbeiten\" style=\"width:16px;height:18px;border:none;\" title=\"Bearbeiten\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"../images/icons/edit.png\" alt=\"Bearbeiten\" style=\"width:16px;height:18px;border:none;\" title=\"Bearbeiten\" /></a>";
}

/**
* Displays a clickable copy button
*
* @param string $url Url of the link
* @param string $ocl Optional onclick value
*/
function copy_button($url, $ocl="")
{
    if ($ocl!="")
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"../images/icons/copy.png\" alt=\"Kopieren\" style=\"width:16px;height:18px;border:none;\" title=\"Kopieren\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"../images/icons/copy.png\" alt=\"Kopieren\" style=\"width:16px;height:18px;border:none;\" title=\"Kopieren\" /></a>";
}

/**
* Displays a clickable delete button
*
* @param string $url Url of the link
* @param string $ocl Optional onclick value
*/
function del_button($url, $ocl="")
{
    if ($ocl!="")
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"../images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"../images/icons/delete.png\" alt=\"Löschen\" style=\"width:18px;height:15px;border:none;\" title=\"Löschen\" /></a>";
}

// Wandelt den Text in brauchbare HTML um
function encode_logtext($string)
{
    $string = preg_replace('/\[USER_ID=([0-9]*);USER_NICK=([^\[]*)\]/i', '<a href="?page=user&sub=edit&user_id=\1">\2</a>', $string);
    $string = preg_replace('/\[PLANET_ID=([0-9]*);PLANET_NAME=([^\[]*)\]/i', '<a href="?page=galaxy&sub=edit&id=\1">\2</a>', $string);

    return $string;
}

/**
* DEPRECATED!
* Displays a select box for choosing the search method
* for varchar/text mysql table fields ('contains', 'part of'
* and negotiations of those two)
*
* @param string $name Field name
*/
function fieldqueryselbox($name)
{
    echo "<select name=\"qmode[$name]\">";
    echo "<option value=\"LIKE '%\">enth&auml;lt</option>";
    echo "<option value=\"LIKE '\">ist gleich</option>";
    echo "<option value=\"NOT LIKE '%\">enth&auml;lt nicht</option>";
    echo "<option value=\"NOT LIKE '\">ist ungleich</option>";
    echo "<option value=\"< '\">ist kleiner</option>";
    echo "<option value=\"> '\">ist gr&ouml;sser</option>";
    echo "</select>";
}

function fieldComparisonSelectBox(string $name): string
{
    $options = [
        'like_wildcard' => 'enthält',
        'like' => 'ist gleich',
        'not_like_wildcard' => 'enthält nicht',
        'not_like' => 'ist ungleich',
        'lt' => 'ist kleiner',
        'gt' => 'ist grösser',
    ];
    $str = "<select name=\"comparisonMode[$name]\">";
    foreach ($options as $key => $value) {
        $str .= '<option value="'.$key.'">'.$value.'</option>';
    }
    $str .= "</select>";
    return $str;
}

function fieldComparisonQuery(QueryBuilder $qry, array $formData, string $column, string $formKey): QueryBuilder
{
    $value = $formData[$formKey];
    switch ($formData['comparisonMode'][$formKey]) {
        case 'like_wildcard':
            $comparator = 'LIKE';
            $value = "%$value%";
            break;
        case 'like':
            $comparator = 'LIKE';
            break;
        case 'not_like_wildcard':
            $comparator = 'NOT LIKE';
            $value = "%$value%";
            break;
        case 'not_like':
            $comparator = 'NOT LIKE';
            break;
        case 'lt':
            $comparator = '<';
            break;
        case 'gt':
            $comparator = '>';
            break;
        default:
            $comparator = '=';
    }
    $qry->andWhere("$column $comparator :$column")
        ->setParameter($column, $value);
    return $qry;
}

//DEPRECATED
function searchQuery($data)
{
    $str = null;
    foreach ($data as $k=>$v)
    {
        if(!isset($str))
        {
            $str=base64_encode($k).":".base64_encode($v);
        }
        else
        {
            $str.=";".base64_encode($k).":".base64_encode($v);
        }
    }
    return base64_encode($str);
}

// DEPRECATED
function searchQueryDecode($query)
{
    $str = explode(";",base64_decode($query, true));
    $res = array();
    foreach ($str as $s)
    {
        $t = explode(":",$s);
        $res[base64_decode($t[0], true)]=base64_decode($t[1], true);
    }
    return $res;
}

function searchQueryUrl($str)
{
    return "&amp;sq=".base64_encode($str);
}

/**
* Builds a search query and sort array
* based on GET,POST or SESSION data.
*
* @param array $arr Pointer to query array
* @param array $oarr Pointer to order/limit array
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
function searchQueryArray(&$arr,&$oarr)
{
    $arr = array();
    $oarr = array();

    if (isset($_GET['newsearch']))
    {
        searchQueryReset();
        return false;
    }

    if (isset($_GET['sq']))
    {
        $sq = base64_decode($_GET['sq'], true);
        $ob = explode(";",$sq);
        foreach ($ob as $o)
        {
            $oe = explode(":",$o);
            $arr[$oe[0]] = array($oe[1],$oe[2]);
        }
        if (!isset($oarr['limit']))
            $oarr['limit'] = 100;
        return true;
    }
    elseif(isset($_POST['search_submit']))
    {
        foreach ($_POST as $k => $v)
        {
            if (substr($k,0,7) == "search_" && $k!="search_submit" && $v!="")
            {
                $fname = substr($k,7);
                if ($fname == "order")
                {
                    if (stristr($v,":"))
                    {
                        $chk = explode(":", $v);
                        if ($chk[1]=="d")
                            $oarr[$chk[0]] = "d";
                        else
                            $oarr[$chk[0]] = "a";
                    }
                    else
                        $oarr[$v] = "a";
                    continue;
                }
                if ($fname == "limit")
                {
                    $oarr['limit'] = min(max(1,intval($v)),5000);
                    continue;
                }

                if (isset($_POST['qmode'][$fname]))
                {
                    $arr[$fname] = array($_POST['qmode'][$fname],$v);
                }
                elseif (isset($_POST['qmode'][$k]))
                {
                    $arr[$fname] = array($_POST['qmode'][$k],$v);
                }
                elseif (is_numeric($v))
                {
                    $arr[$fname] = array("=",$v);
                }
                else
                {
                    $arr[$fname] = array("%",$v);
                }
            }
        }
        if (!isset($oarr['limit']))
            $oarr['limit'] = 100;
        return true;
    }
    elseif(isset($_SESSION['search']['query']))
    {
        $arr = $_SESSION['search']['query'];
        $oarr = $_SESSION['search']['order'];
        if (isset($_POST['search_resubmit']))
        {
            if (isset($_POST['search_order']))
            {
                if (stristr($_POST['search_order'],":"))
                {
                    $chk = explode(":", $_POST['search_order']);
                    if ($chk[1]=="d")
                        $oarr[$chk[0]] = "d";
                    else
                        $oarr[$chk[0]] = "a";
                }
                else
                    $oarr[$_POST['search_order']] = "a";
            }
            if (isset($_POST['search_limit']))
            {
                $oarr['limit'] = min(max(1,intval($_POST['search_limit'])),5000);
            }
        }
        return true;
    }
    return false;
}

function searchQuerySave(&$sa,&$so)
{
    $_SESSION['search']['query'] = $sa;
    $_SESSION['search']['order'] = $so;
}

function searchQueryReset()
{
    unset($_SESSION['search']);
}


/**
* Displays a select box for choosing the search method
* for varchar/text mysql table fields ('contains', 'part of'
* and negotiations of those two)
*
* @param string $name Field name
*/
function searchFieldTextOptions($name)
{
    ob_start();
    echo "<select name=\"qmode[$name]\">";
    echo "<option value=\"%\">enth&auml;lt</option>";
    echo "<option value=\"!%\">enth&auml;lt nicht</option>";
    echo "<option value=\"=\">ist gleich</option>";
    echo "<option value=\"!=\">ist ungleich</option>";
    echo "</select>";
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

/**
* Displays a select box for choosing the search method
* for varchar/text mysql table fields ('contains', 'part of'
* and negotiations of those two)
*
* @param string $name Field name
*/
function searchFieldNumberOptions($name)
{
    ob_start();
    echo "<select name=\"qmode[$name]\">";
    echo "<option value=\"=\">=</option>";
    echo "<option value=\"!=\">!=</option>";
    echo "<option value=\"<\">&lt;</option>";
    echo "<option value=\"<=\">&lt;=</option>";
    echo "<option value=\">\">&gt;</option>";
    echo "<option value=\">=\">&gt;=</option>";
    echo "</select>";
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

/**
* Resolves the name of a given search operator
*
* @return string Operator name
*/
function searchFieldOptionsName($operator='')
{
    switch ($operator)
    {
        case "=":
            return "gleich";
        case "!=":
            return "ungleich";
        case "%":
            return "enthält";
        case "!%":
            return "enthält nicht";
        case "<":
            return "kleiner als";
        case "<=":
            return "kleiner gleich";
        case ">":
            return "grösser als";
        case ">=":
            return "grösser gleich";
        default:
            return "gleich";

    }
}

function searchFieldSql($item)
{
    $operator = $item[0];
    $value = $item[1];
    switch ($operator)
    {
        case "=":
            return " LIKE '".$value."' ";
        case "!=":
            return " NOT LIKE '".$value."' ";
        case "%":
            return " LIKE '%".$value."%' ";
        case "!%":
            return " NOT LIKE '%".$value."%' ";
        case "<":
            return " < ".intval($value)." ";
        case "<=":
            return " <= ".intval($value)." ";
        case ">":
            return " > ".intval($value)." ";
        case ">=":
            return " >= ".intval($value)." ";
        default:
            return " ='".$value."'";
    }
}



function tail($file, $num_to_get=10)
{
  if ($fp = fopen($file, 'r'))
  {
      $position = filesize($file);
      fseek($fp, $position-1);
      $data = '';
      $chunklen = 4096;
      while($position >= 0)
      {
        $position = $position - $chunklen;
        if ($position < 0) { $chunklen = abs($position); $position=0;}
        fseek($fp, $position);
        $data = fread($fp, $chunklen). $data;
        if (substr_count($data, "\n") >= $num_to_get + 1)
        {
           preg_match("!(.*?\n){".($num_to_get-1)."}$!", $data, $match);
           return $match[0];
        }
      }
      fclose($fp);
      return $data;
    }
    return false;
}

function DuplicateMySQLRecord ($table, $id_field, $id) {
    // load the original record into an array
    $result = dbquery("SELECT * FROM {$table} WHERE {$id_field}={$id}");
    $original_record = mysql_fetch_assoc($result);

    // insert the new record and get the new auto_increment id
    mysql_query("INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)");
    $newid = mysql_insert_id();

    // generate the query to update the new record with the previous values
    $query = "UPDATE {$table} SET ";
    foreach ($original_record as $key => $value) {
        if ($key != $id_field) {
            $query .= '`'.$key.'` = "'.str_replace('"','\"',$value).'", ';
        }
    }
    $query = substr($query,0,strlen($query)-2); # lop off the extra trailing comma
    $query .= " WHERE {$id_field}={$newid}";
    dbquery($query);

    // return the new id
    return $newid;
}

function drawTechTreeForSingleItem($type,$id)
{
    $rres = dbquery("
    SELECT
        r.*,
        b.building_name as bname,
        t.tech_name as tname
    FROM
        ".$type." r
    LEFT JOIN
        buildings b
        ON r.req_building_id = b.building_id
    LEFT JOIN
        technologies t
        ON r.req_tech_id = t.tech_id
    WHERE
        obj_id=".$id."
    ORDER BY
        tname,
        bname;");
    if (mysql_num_rows($rres)>0)
    {
        while($rarr = mysql_fetch_assoc($rres))
        {
            if ($rarr['req_building_id']>0)
            {
                $name= $rarr['bname'];
                $pn = "b:".$rarr['req_building_id'];
            }
            elseif ($rarr['req_tech_id']>0)
            {
                $name= $rarr['tname'];
                $pn = "t:".$rarr['req_tech_id'];
            }
            else {
                $name= "INVALID";
                $pn = '';
            }

            echo "<a href=\"javascript:;\" onclick=\"var nlvl = prompt('Level für ".$name." ändern:','".$rarr['req_level']."'); if (nlvl != '' && nlvl != null) xajax_addToTechTree('".$type."',".$id.",'".$pn."',nlvl);\">";
            echo $name." <b>".$rarr['req_level']."</b></a>";
            echo " &nbsp; <a href=\"javascript:;\" onclick=\"if (confirm('Anforderung löschen?')) xajax_removeFromTechTree('".$type."',".$id.",".$rarr['id'].")\">".icon("delete")."</a>";
            echo "<br/>";
        }
    }
    else
    {
        echo "<i>Keine Anforderungen</i>";
    }
}

function showLogs($args=null,$limit=0)
{
    $paginationLimit = 100;

    $cat = is_array($args) && isset($args['logcat']) ? $args['logcat'] : 0;
    $sev = is_array($args) && isset($args['logsev'])  ? $args['logsev'] : 0;
    $text = is_array($args)&& isset($args['searchtext'])   ? $args['searchtext'] : "";

    $order = "timestamp DESC";

    $sql1 = "SELECT ";
    $sql2 = " * ";

    $sql3= " FROM logs WHERE ";
    $sql3.= "1";
    if ($cat>0)
    {
        $sql3.=" AND facility=".$cat." ";
    }
    if ($text!="")
    {
        $sql3.=" AND message LIKE '%".$text."%' ";
    }
    if ($sev >0)
    {
        $sql3.=" AND severity >= ".$sev." ";
    }
    $sql3.= " ORDER BY $order";

    $res = dbquery($sql1." COUNT(id) as cnt ".$sql3);
    $arr = mysql_fetch_row($res);
    $total = $arr[0];

    $limit = max(0,$limit);
    $limit = min($total,$limit);
    $limit -= $limit % $paginationLimit;
    $limitstring = "$limit,$paginationLimit";

    $sql4 = " LIMIT $limitstring";

    $res = dbquery($sql1.$sql2.$sql3.$sql4);
    $nr = mysql_num_rows($res);
    if ($nr>0)
    {
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"4\">
        <div style=\"float:left;\">";

        if ($limit>0)
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"applyFilter(0)\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" onclick=\"applyFilter(".($limit-$paginationLimit).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" disabled=\"disabled\" /> ";
        }
        if ($limit < $total-$paginationLimit)
        {
            echo "<input type=\"button\" value=\"&gt;\" onclick=\"applyFilter(".($limit+$paginationLimit).")\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"applyFilter(".($total-($total%$paginationLimit)).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&gt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" disabled=\"disabled\" /> ";
        }

        echo "</div><div style=\"float:right\">
        ".($limit+1)." - ".($limit+$nr)." von $total
        </div><br style=\"clear:both;\" />
        </th></tr>";
        echo "<tr>
            <th style=\"width:140px;\">Datum</th>
            <th style=\"width:90px;\">Schweregrad</th>
            <th style=\"width:90px;\">Bereich</th>
            <th>Nachricht</th>
        </tr>";
        while ($arr = mysql_fetch_assoc($res))
        {
            echo "<tr>
            <td>".df($arr['timestamp'])."</td>
            <td>".Log::$severities[$arr['severity']]."</td>
            <td>".Log::$facilities[$arr['facility']]."</td>
            <td>".text2html($arr['message']);
            if ($arr['ip']!="")
                echo "<br/><br/><b>Host:</b> ".$arr['ip']." (".Net::getHost($arr['ip']).")";
            echo "</td>
            </tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p>Keine Daten gefunden!</p>";
    }
}

function showAttackAbuseLogs($args=null,$limit=-1,$load=true)
{
    global $app;

    /** @var \EtoA\User\UserRepository $userRepository */
    $userRepository = $app['etoa.user.repository'];

	$paginationLimit = 50;

	if ($load)
	{
		$action = is_array($args) && isset($args['flaction']) ? $args['flaction'] : 0;
		$sev = is_array($args) && isset($args['logsev'])  ? $args['logsev'] : 0;

		$landtime = is_array($args) ? mktime($args['searchtime_h'],$args['searchtime_i'],$args['searchtime_s'],$args['searchtime_m'],$args['searchtime_d'],$args['searchtime_y']) : time();

		$order = "timestamp ASC";

		$sql1 = "SELECT ";
		$sql2 = " * ";
		$sql3 = " FROM logs_battle l ";

		if (isset($args['searchfuser']) && $args['searchfuser']!="" && !is_numeric($args['searchfuser']))
		{
			$args['searchfuser'] = $userRepository->getUserIdByNick($args['searchfuser']);
		}
		if (isset($args['searcheuser']) && $args['searcheuser']!="" && !is_numeric($args['searcheuser']))
		{
			$args['searcheuser'] = $userRepository->getUserIdByNick($args['searcheuser']);
		}

		$sql3.= " WHERE fleet_weapon>0 AND landtime<='".$landtime."' AND landtime>'".($landtime-3600*24)."' ";
		if ($action!="")
		{
			$sql3.=" AND action='".$action."' ";
		}
		if ($sev >0)
		{
			$sql3.=" AND severity >= ".$sev." ";
		}
		if (isset($args['searchfuser']) && is_numeric($args['searchfuser']))
		{
			$sql3.=" AND l.user_id LIKE '%,".intval($args['searchfuser']).",%' ";
		}
		if (isset($args['searcheuser']) && is_numeric($args['searcheuser']))
		{
			$sql3.=" AND l.entity_user_id LIKE '%,".intval($args['searcheuser']).",%' ";
		}
		$sql3.= " ORDER BY $order";

		$res=dbquery($sql1.$sql2.$sql3);

		$bans = array();
		$actions = array();

		if (mysql_num_rows($res)>0)
		{
			$data = array();

			$waveMaxCnt = array(3,4);				// Max. 3er/4er Wellen...
			$waveTime = 15*60;						// ...innerhalb 15mins

			$attacksPerEntity = array(2,4);			// Max. 2/4 mal den gleichen Planeten angreiffen

			$attackedEntitiesMax = array(5,10);		// Max. Anzahl Planeten die angegriffen werden können...
			$timeBetweenAttacksOnEntity = 6*3600;	// ...innerhalb 6h

			$banRange = 24*3600;					// alle Regeln gelten innerhalb von 24h

			$first_ban_time = 12*3600;							// Sperrzeit beim ersten Vergehen: 12h
			$add_ban_time = 12*3600;								// Sperrzeit bei jedem weiteren Vergehen: 12h (wird immer dazu addiert)

			//Alle Daten werden in einem Array gespeichert, da mehr als 1 Angriffer möglich ist funktioniert das alte Tool nicht mehr
			while ($arr=mysql_fetch_array($res))
			{
				$uid = explode(",",$arr['user_id']);
				$euid = explode(",",$arr['entity_user_id']);
				$eUser = $euid[1];
				$entity = $arr['entity_id'];
				$time = $arr['landtime'];
				$war = $arr['war'];
				$action = $arr['action'];
				foreach ($uid as $fUser)
				{
					if ($fUser!="")
					{
						if (!isset($data[$fUser])) $data[$fUser] = array();
						if (!isset($data[$fUser][$eUser])) $data[$fUser][$eUser] = array();
						if (!isset($data[$fUser][$eUser][$entity])) $data[$fUser][$eUser][$entity] = array();
						array_push($data[$fUser][$eUser][$entity],array($time,$war,$action));
					}
				}
			}

			foreach ($data as $fUser=>$eUserArr)
			{
				foreach ($eUserArr as $eUser=>$eArr)
				{
					$firstTime = 0;
					$attackCntTotal = 0;
					$attackedEntities = count($eArr);

					foreach ($eArr as $entity=>$eDataArr)
					{
						$firstPlanetTime = 0;
						$lastPlanetTime = 0;
						$attackCntEntity = 0;
						$waveStart=0;
						$waveEnd = 0;

						foreach($eDataArr as $eData)
						{
							$ban = 0;
							$banReason = "";
							if ($firstTime==0) {
                                $firstTime = $eData[0];

                                // Wenn mehr als 5 Planeten angegrifen wurden
                                if ($attackedEntities>$attackedEntitiesMax[$eData[1]])
                                {
                                    $ban = 1;
                                    $banReason .= "Mehr als ".$attackedEntitiesMax[$eData[1]]." innerhalb von ".($banRange/3600)." Stunden.\nAnzahl: ".$attackedEntities."\n\n";
                                }
                            }
                            if ($firstPlanetTime==0) $firstPlanetTime = $eData[0];
                            if ($lastPlanetTime==0) $lastPlanetTime = $eData[0];

                            //Wellenreset
                            if ($waveStart==0 || $waveEnd<=$eData[0]-$waveTime)
                            {
                                $lastWave = $waveEnd;
                                $waveStart = $eData[0];
                                $waveEnd = $eData[0];
                                $waveCnt = 1;
                                ++$attackCntEntity;
                            }
                            else
                            {
                                ++$waveCnt;
                                $waveEnd = $eData[0];
                            }

                            //
                            // Überprüfungen
                            //

                            //Zu viele Angriffe in einer Welle
                            if ($waveCnt>$waveMaxCnt[$eData[1]])
                            {
                                $ban = 1;
                                $banReason .= "Mehr als ".$waveMaxCnt[$eData[1]]." Angriffe in einer Welle auf dem selben Ziel.<br />Anzahl Angriffe : ".$waveCnt."<br />Dauer der Welle: ".tf($waveEnd-$waveStart)."<br /><br />";
                            }
                            // Sperre keine 6h gewartet zwischen Angriffen auf einen Planeten
                            if ($waveCnt==1 && $eData[0]>$lastWave && $eData[0]<$lastWave+$timeBetweenAttacksOnEntity)
                            {
                                $ban = 1;
                                $banReason .= "Der Abstand zwischen 2 Angriffen/Wellen auf ein Ziel ist kleiner als ".($timeBetweenAttacksOnEntity/3600)." Stunden.<br />Dauer zwischen den beiden Angriffen: ".tf($eData[0]-$lastWave)."<br /><br />";
                            }
                            // Sperre wenn mehr als 2/4 Angriffe pro Planet
                            if ($waveCnt==1 && $attackCntEntity>$attacksPerEntity[$eData[1]])
                            {
                                $ban = 1;
                                $banReason .= "Mehr als ".$attacksPerEntity[$eData[1]]." Angriffe/Wellen auf ein Ziel.<br />Anzahl:".$attackCntEntity."<br /><br />";
                            }

                            // Es liegt eine Angriffsverletzung vor
                            if($ban==1)
                                array_push($bans,array("action"=>$eData[2],"timestamp"=>$eData[0],"fUser"=>$fUser,"eUser"=>$eUser,"entity"=>$entity,"ban"=>$banReason));
                        }
                    }
                }
            }
        }
        $_SESSION['logs']['attackObj'] = serialize($bans);
    }

    $bans = unserialize($_SESSION['logs']['attackObj']);
    $nr = count($bans);
    if ($nr>0)
    {
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"10\">
        <div style=\"float:left;\">";

        if ($limit>0)
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"applyFilter(0)\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" onclick=\"applyFilter(".($limit-$paginationLimit).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" disabled=\"disabled\" /> ";
        }
        if ($limit < $total-$paginationLimit)
        {
            echo "<input type=\"button\" value=\"&gt;\" onclick=\"applyFilter(".($limit+$paginationLimit).")\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"applyFilter(".($total-($total%$paginationLimit)).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&gt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" disabled=\"disabled\" /> ";
        }

        echo "</div><div style=\"float:right\">
        ".($limit+1)." - ".($limit+$nr)." von $nr
        </div><br style=\"clear:both;\" />
        </th></tr>";
        echo "<tr>
            <th style=\"width:140px;\">Datum</th>
            <th>Schweregrad</th>
            <th>Angreifer</th>
            <th>Verteidiger</th>
            <th>Ziel</th>
            <th>Aktion</th>
            <th>Sperrgrund</th>
        </tr>";
        foreach ($bans as $id=>$banData)
        {
            $fUser = new User($banData['fUser']);
            $eUser = new User($banData['eUser']);
            $action = FleetAction::createFactory($banData['action']);
            $entity = Entity::createFactoryById($banData['entity']);

            echo "<tr>
            <td>".df($banData['timestamp'])."</td>
            <td>".Log::$severities[$banData['severity']]."</td>
            <td>$fUser</td>
            <td>$eUser</td>
            <td>$entity</td>
            <td>$action</td>
            <td><a href=\"javascript:;\" onclick=\"toggleBox('details".$id."')\">Bericht</a></td>
            </tr>";
            echo "<tr id=\"details".$id."\" style=\"display:none;\"><td colspan=\"9\">".
            $banData['ban']."
            </td></tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p>Keine Daten gefunden!</p>";
    }
}



function showFleetLogs($args=null,$limit=0)
{
    global $resNames, $app;
    $paginationLimit = 50;

    $action = is_array($args) && isset($args['flaction']) ? $args['flaction'] : 0;
    $sev = is_array($args) && isset($args['logsev'])  ? $args['logsev'] : 0;

    $order = "timestamp DESC";

    $sql1 = "SELECT ";
    $sql2 = " * ";

    $sql3= " FROM logs_fleet l ";
    if (isset($args['searchuser']) && $args['searchuser']!="" && !is_numeric($args['searchuser']))
    {
        $sql3.=" INNER JOIN users u ON u.user_id=l.user_id AND u.user_nick LIKE '%".$args['searchuser']."%' ";
    }

    if (isset($args['searcheuser']) && $args['searcheuser']!="" && !is_numeric($args['searcheuser']))
    {
        $sql3.=" INNER JOIN users eu ON eu.user_id=l.entity_user_id AND eu.user_nick LIKE '%".$args['searcheuser']."%' ";
    }

    $sql3.= " WHERE 1 ";
    if ($action!="")
    {
        $sql3.=" AND action='".$action."' ";
    }
    if ($sev >0)
    {
        $sql3.=" AND severity >= ".$sev." ";
    }
    if (isset($args['logfac']) && is_numeric($args['logfac']))
    {
        $sql3.=" AND facility = ".$args['logfac']." ";
    }
    if (isset($args['searchuser']) && is_numeric($args['searchuser']))
    {
        $sql3.=" AND l.user_id=".intval($args['searchuser'])." ";
    }
    if (isset($args['searcheuser']) && is_numeric($args['searcheuser']))
    {
        $sql3.=" AND l.user_id=".intval($args['searcheuser'])." ";
    }
    $sql3.= " ORDER BY $order";

    $res = dbquery($sql1." COUNT(id) as cnt ".$sql3);
    $arr = mysql_fetch_row($res);
    $total = $arr[0];

    $limit = max(0,$limit);
    $limit = min($total,$limit);
    $limit -= $limit % $paginationLimit;
    $limitstring = "$limit,$paginationLimit";

    $sql4 = " LIMIT $limitstring";

    $res = dbquery($sql1.$sql2.$sql3.$sql4);
    $nr = mysql_num_rows($res);
    if ($nr>0)
    {
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"10\">
        <div style=\"float:left;\">";

        if ($limit>0)
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"applyFilter(0)\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" onclick=\"applyFilter(".($limit-$paginationLimit).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" disabled=\"disabled\" /> ";
        }
        if ($limit < $total-$paginationLimit)
        {
            echo "<input type=\"button\" value=\"&gt;\" onclick=\"applyFilter(".($limit+$paginationLimit).")\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"applyFilter(".($total-($total%$paginationLimit)).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&gt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" disabled=\"disabled\" /> ";
        }

        echo "</div><div style=\"float:right\">
        ".($limit+1)." - ".($limit+$nr)." von $total
        </div><br style=\"clear:both;\" />
        </th></tr>";
        echo "<tr>
            <th style=\"width:140px;\">Datum</th>
            <th>Schweregrad</th>
            <th>Facility</th>
            <th>Besitzer</th>
            <th>Aktion</th>
            <th>Start</th>
            <th>Ziel</th>
            <th>Startzeit</th>
            <th>Landezeit</th>
            <th>Flotte</th>
        </tr>";
        /** @var \EtoA\Ship\ShipDataRepository $shipDataRepository */
        $shipDataRepository = $app[\EtoA\Ship\ShipDataRepository::class];
        $shipNames = $shipDataRepository->getShipNames(true);
        while ($arr = mysql_fetch_assoc($res))
        {
            $owner = new User($arr['user_id']);
            $fa = FleetAction::createFactory($arr['action']);
            $startEntity = Entity::createFactoryById($arr['entity_from']);
            $endEntity = Entity::createFactoryById($arr['entity_to']);
            echo "<tr>
            <td>".df($arr['timestamp'])."</td>
            <td>".Log::$severities[$arr['severity']]."</td>
            <td>".FleetLog::$facilities[$arr['facility']]."</td>
            <td>$owner</td>
            <td>".$fa." [".FleetAction::$statusCode[$arr["status"] ]."]</td>
            <td>".$startEntity."<br/>".$startEntity->entityCodeString().", ".$startEntity->owner()."</td>
            <td>".$endEntity."<br/>".$endEntity->entityCodeString().", ".$endEntity->owner()."</td>
            <td>".df($arr['launchtime'])."</td>
            <td>".df($arr['landtime'])."</td>
            <td><a href=\"javascript:;\" onclick=\"toggleBox('details".$arr['id']."')\">Bericht</a></td>
            </tr>";
            echo "<tr id=\"details".$arr['id']."\" style=\"display:none;\"><td colspan=\"10\">";
            tableStart("",450);
            echo "<tr><th>Schiffe in der Flotte</th><th>Vor der Aktion</th><th>Nach der Aktion</th></tr>";
            $sship = array();
            $ssship = explode(",",$arr['fleet_ships_start']);
            foreach ($ssship as $sd)
            {
                $sdi = explode(":",$sd);
                $sship[$sdi[0] ]=$sdi[1];
            }
            $esship = explode(",",$arr['fleet_ships_end']);
            foreach ($esship as $sd)
            {
                $sdi = explode(":",$sd);
                if ($sdi[0]>0)
                    echo "<tr><td>".$shipNames[(int) $sdi[0] ]."</td><td>".nf($sdi[1])."</td><td>".nf($sship[$sdi[0] ])."</td></tr>";
            }
            echo tableEnd();
            tableStart("",450);
            echo "<tr><th>Schiffe auf dem Planeten</th><th>Vor der Aktion</th><th>Nach der Aktion</th></tr>";
            $sship = array();
            $ssship = explode(",",$arr['entity_ships_start']);
            foreach ($ssship as $sd)
            {
                $sdi = explode(":",$sd);
                $sship[$sdi[0] ]=$sdi[1];
            }
            $esship = explode(",",$arr['entity_ships_end']);
            foreach ($esship as $sd)
            {
                $sdi = explode(":",$sd);
                if ($sdi[0]>0)
                    echo "<tr><td>".$shipNames[(int) $sdi[0] ]."</td><td>".nf($sdi[1])."</td><td>".nf($sship[$sdi[0] ])."</td></tr>";
            }
            echo tableEnd();
            tableStart("",450);
            echo "<tr><th>Rohstoffe in der Flotte</th><th>Vor der Aktion</th><th>Nach der Aktion</th></tr>";
            $sres = array();
            $eres = array();
            $ssres = explode(":",$arr['fleet_res_start']);
            foreach ($ssres as $sd)
            {
                array_push($sres,$sd);
            }
            $esres = explode(":",$arr['fleet_res_end']);
            foreach ($esres as $sd)
            {
                array_push($eres,$sd);
            }
            foreach ($resNames as $k=>$v)
            {
                echo "<tr><td>".$v."</td><td>".nf($sres[$k])."</td><td>".nf($eres[$k])."</td></tr>";
            }
            echo "<tr><td>Bewoner</td><td>".nf($sres[5])."</td><td>".nf($eres[5])."</td></tr>";
            echo tableEnd();

            //Will not show Resmessage if entity was not touched (fleet cancel)
            if ($arr['entity_res_start']!="untouched" || $arr['entity_res_end']!="untouched")
            {
                tableStart("",450);
                echo "<tr><th>Rohstoffe auf der Entity</th><th>Vor der Aktion</th><th>Nach der Aktion</th></tr>";
                $sres = array();
                $eres = array();
                $ssres = explode(":",$arr['entity_res_start']);
                foreach ($ssres as $sd)
                {
                    array_push($sres,$sd);
                }
                $esres = explode(":",$arr['entity_res_end']);
                foreach ($esres as $sd)
                {
                    array_push($eres,$sd);
                }
                foreach ($resNames as $k=>$v)
                {
                    echo "<tr><td>".$v."</td><td>".nf($sres[$k])."</td><td>".nf($eres[$k])."</td></tr>";
                }
                echo "<tr><td>Bewoner</td><td>".nf($sres[5])."</td><td>".nf($eres[5])."</td></tr>";
                echo tableEnd();
            }
            echo $arr["message"];
            echo "</td></tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p>Keine Daten gefunden!</p>";
    }
}

function showDebrisLogs($args=null,$limit=0) {
    global $app;

    $adminUserRepo = $app['etoa.admin.user.repository'];

    $maxtime = is_array($args) ? mktime($args['searchtime_h'],$args['searchtime_i'],$args['searchtime_s'],$args['searchtime_m'],$args['searchtime_d'],$args['searchtime_y']) : time();

    $paginationLimit = 50;

    $sql = "SELECT * from logs_debris";
    $sql2 =" WHERE time<".$maxtime;

    if (isset($args['searchuser']) && trim($args['searchuser']) != '')
    {
        $sql2 .=" AND user_id = ".User::findIdByNick($args['searchuser'])." ";
    };
    if (isset($args['searchadmin']) && trim($args['searchadmin']) != '')
    {
        $admin = $adminUserRepo->findOneByNick($args['searchadmin']);
        $sql2 .=" AND admin_id=". ($admin != null ? $admin->id : 0);
    };

    $res = dbquery(" SELECT COUNT(id) as cnt from logs_debris".$sql2);
    $arr = mysql_fetch_row($res);
    $total = $arr[0];

    $limit = max(0,$limit);
    $limit = min($total,$limit);
    $limit -= $limit % $paginationLimit;
    $limitstring = "$limit,$paginationLimit";

    $res = dbquery($sql.$sql2);

    $nr = mysql_num_rows($res);
    if ($nr>0)
    {
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"10\">
        <div style=\"float:left;\">";

        if ($limit>0)
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"applyFilter(0)\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" onclick=\"applyFilter(".($limit-$paginationLimit).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" disabled=\"disabled\" /> ";
        }
        if ($limit < $total-$paginationLimit)
        {
            echo "<input type=\"button\" value=\"&gt;\" onclick=\"applyFilter(".($limit+$paginationLimit).")\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"applyFilter(".($total-($total%$paginationLimit)).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&gt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" disabled=\"disabled\" /> ";
        }

        echo "</div><div style=\"float:right\">
        ".($limit+1)." - ".($limit+$nr)." von $total
        </div><br style=\"clear:both;\" />
        </th></tr>";
        echo "<tr>
            <th style=\"width:140px;\">Datum</th>
            <th>Admin</th>
            <th>User</th>
            <th>Titan</th>
            <th>Silizium</th>
            <th>PVC</th>
        </tr>";
        while ($arr = mysql_fetch_assoc($res))
        {
            $admin = $adminUserRepo->find($arr['admin_id']);
            echo "<tr>
            <td>".date('d M Y H:i:s',$arr['time'])."</td>
            <td>".($admin != null ? $admin->nick : '?')."</td>
            <td>".new User($arr['user_id'])."</td>
            <td>".$arr['metal']."</td>
            <td>".$arr['crystal']."</td>
            <td>".$arr['plastic']."</td>
            </tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p>Keine Daten gefunden!</p>";
    }
}

function showGameLogs($args=null,$limit=0)
{
    global $app;

    $paginationLimit = 25;

    $cat = is_array($args) && isset($args['logcat']) ? $args['logcat'] : 0;
    $sev = is_array($args) && isset($args['logsev'])  ? $args['logsev'] : 0;
    $text = is_array($args)&& isset($args['searchtext'])   ? $args['searchtext'] : "";

    $order = "timestamp DESC";

    $sql1 = "SELECT ";
    $sql2 = " l.* ";

    $sql3= " FROM logs_game l ";
    if (isset($args['searchuser']) && $args['searchuser']!="" && !is_numeric($args['searchuser']))
    {
        $sql3.=" INNER JOIN users u ON u.user_id=l.user_id AND u.user_nick LIKE '%".$args['searchuser']."%' ";
    }
    if (isset($args['searchalliance']) && $args['searchalliance']!="" && !is_numeric($args['searchalliance']))
    {
        $sql3.=" INNER JOIN alliances a ON a.alliance_id=l.alliance_id AND a.alliance_name LIKE '%".$args['searchalliance']."%' ";
    }
    if (isset($args['searchentity']) && $args['searchentity']!="" && !is_numeric($args['searchentity']))
    {
        // TODO: this now only works for planets...
        $sql3.=" INNER JOIN planets e ON e.id=l.entity_id AND e.planet_name LIKE '%".$args['searchentity']."%' ";
    }
    $sql3.= " WHERE 1 ";

    if (isset($args['searchuser']) && is_numeric($args['searchuser']))
    {
        $sql3.=" AND l.user_id=".intval($args['searchuser'])." ";
    }
    if (isset($args['searchalliance']) && is_numeric($args['searchalliance']))
    {
        $sql3.=" AND l.alliance_id=".intval($args['searchalliance'])." ";
    }
    if (isset($args['searchentity']) && is_numeric($args['searchentity']))
    {
        $sql3.=" AND l.entity_id=".intval($args['searchentity'])." ";
    }
    if ($cat>0)
    {
        $sql3.=" AND facility=".$cat." ";
    }
    if ($text!="")
    {
        $sql3.=" AND message LIKE '%".$text."%' ";
    }
    if ($sev >0)
    {
        $sql3.=" AND severity >= ".$sev." ";
    }
    if (isset($args['object_id']) && $args['object_id']>0)
    {
        $sql3.=" AND object_id = ".$args['object_id']." ";
    }
    $sql3.= " ORDER BY $order";

    $res = dbquery($sql1." COUNT(l.id) as cnt ".$sql3);
    $arr = mysql_fetch_row($res);
    $total = $arr[0];

    $limit = max(0,$limit);
    $limit = min($total,$limit);
    $limit -= $limit % $paginationLimit;
    $limitstring = "$limit,$paginationLimit";

    $sql4 = " LIMIT $limitstring";

    $res = dbquery($sql1.$sql2.$sql3.$sql4);
    $nr = mysql_num_rows($res);
    if ($nr>0)
    {
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"10\">
        <div style=\"float:left;\">";

        if ($limit>0)
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"applyFilter(0)\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" onclick=\"applyFilter(".($limit-$paginationLimit).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&lt;&lt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&lt;\" disabled=\"disabled\" /> ";
        }
        if ($limit < $total-$paginationLimit)
        {
            echo "<input type=\"button\" value=\"&gt;\" onclick=\"applyFilter(".($limit+$paginationLimit).")\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"applyFilter(".($total-($total%$paginationLimit)).")\" /> ";
        }
        else
        {
            echo "<input type=\"button\" value=\"&gt;\" disabled=\"disabled\" /> ";
            echo "<input type=\"button\" value=\"&gt;&gt;\" disabled=\"disabled\" /> ";
        }

        echo "</div><div style=\"float:right\">
        ".($limit+1)." - ".($limit+$nr)." von $total
        </div><br style=\"clear:both;\" />
        </th></tr>";
        echo "<tr>
            <th style=\"width:140px;\">Datum</th>
            <th style=\"\">Schweregrad</th>
            <th style=\"\">Bereich</th>
            <th>User</th>
            <th>Allianz</th>
            <th>Raumobjekt</th>
            <th>Einheit</th>
            <th>Status</th>
            <th>Optionen</th>
        </tr>";
        while ($arr = mysql_fetch_assoc($res))
        {
            $tu = ($arr['user_id']>0) ? new User($arr['user_id']) : "-";
            $ta = ($arr['alliance_id']>0) ? new Alliance($arr['alliance_id']) : "-";
            $te = ($arr['entity_id']>0) ? Entity::createFactoryById($arr['entity_id']) : "-";
            switch ($arr['facility'])
            {
                case GameLog::F_BUILD:
                    $ob = new Building($arr['object_id'])." ".($arr['level']>0 ? $arr['level'] : '');
                    switch ($arr['status'])
                    {
                        case 1: $obStatus="Ausbau abgebrochen";break;
                        case 2: $obStatus="Abriss abgebrochen";break;
                        case 3: $obStatus="Ausbau";break;
                        case 4: $obStatus="Abriss";break;
                        default: $obStatus='-';
                    }
                    break;
                case GameLog::F_TECH:
                    $ob = new Technology($arr['object_id'])." ".($arr['level']>0 ? $arr['level'] : '');
                    switch ($arr['status'])
                    {
                        case 3: $obStatus="Erforschung";break;
                        case 0: $obStatus="Erforschung abgebrochen";break;
                        default: $obStatus='-';
                    }
                    break;
                case GameLog::F_SHIP:
                    $ob = $arr['object_id'] > 0 ? new Ship($arr['object_id']).' '.($arr['level']>0 ? $arr['level'].'x' : '') : '-';
                    switch ($arr['status'])
                    {
                        case 1: $obStatus="Bau";break;
                        case 0: $obStatus="Bau abgebrochen";break;
                        default: $obStatus='-';
                    }
                    break;
                case GameLog::F_DEF:
                    $ob = $arr['object_id'] > 0 ? new Defense($arr['object_id']).' '.($arr['level']>0 ? $arr['level'].'x' : '') : '-';
                    switch ($arr['status'])
                    {
                        case 1: $obStatus="Bau";break;
                        case 0: $obStatus="Bau abgebrochen";break;
                        default: $obStatus='-';
                    }
                    break;
                case GameLog::F_QUESTS:
                    $quest = $app['cubicle.quests.registry']->getQuest($arr['object_id']);
                    $questStates = array_flip(\EtoA\Quest\Log\QuestGameLog::TRANSITION_MAP);
                    $ob = $quest->getData()['title'];
                    $obStatus= str_replace('_', ' ', $questStates[$arr['status']]);
                    break;
                default:
                    $ob = "-";
                    $obStatus= "-";
            }

            echo "<tr>
            <td>".df($arr['timestamp'])."</td>
            <td>".GameLog::$severities[$arr['severity']]."</td>
            <td>".GameLog::$facilities[$arr['facility']]."</td>
            <td>".$tu."</td>
            <td>".$ta."</td>
            <td>".$te."</td>
            <td>".$ob."</td>
            <td>".$obStatus."</td>
            <td><a href=\"javascript:;\" onclick=\"toggleBox('details".$arr['id']."')\">Details</a></td>
            </tr>";
            echo "<tr id=\"details".$arr['id']."\" style=\"display:none;\"><td colspan=\"9\">".text2html($arr['message'])."
            <br/><br/>IP: ".$arr['ip']."</td></tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p>Keine Daten gefunden!</p>";
    }
}

    /**
    * Shows a datepicker
    */
    function showDatepicker($element_name, $def_val, $time=false, $seconds=false)
    {
        echo "<input type=\"text\" name=\"".$element_name."\" value=\"".date('d.m.Y', $def_val)."\" class=\"datepicker\" size=\"10\" />";
        if ($time) {
            if ($seconds) {
                echo ":<input type=\"text\" name=\"".$element_name."_time\" value=\"".date('H:i:s', $def_val)."\" size=\"7\" />";
            } else {
                echo ":<input type=\"text\" name=\"".$element_name."_time\" value=\"".date('H:i', $def_val)."\" size=\"5\" />";
            }
        }
    }

    /**
    * Parse value submitted by datepicker field
    */
    function parseDatePicker($element_name, $data) {

        $str = $data[$element_name];
        if (isset($data[$element_name."_time"])) {
            $str.= " ".$data[$element_name."_time"];
        }
        return strtotime($str);
    }

    /**
    * Create file downlad link
    */
    function createDownloadLink($file) {

        $encodedName = base64_encode($file);
        if (!isset($_SESSION['filedownload'][$encodedName])) {
            $_SESSION['filedownload'][$encodedName] = uniqid('', true);
        }
        return "dl.php?path=".$encodedName."&hash=".sha1($encodedName.$_SESSION['filedownload'][$encodedName]);
    }

    /**
    * $Parse file downlad link
    */
    function parseDownloadLink($arr) {

        if (isset($arr['path']) && $arr['path']!="" && isset($arr['hash']) && $arr['hash']!="")
        {
            $encodedName = $arr['path'];
            $file = base64_decode($encodedName, true);
            if (isset($_SESSION['filedownload'][$encodedName]) && $arr['hash'] == sha1($encodedName.$_SESSION['filedownload'][$encodedName])) {
                unset($_SESSION['filedownload'][$encodedName]);
                return $file;
            }
        }
        return false;
    }
