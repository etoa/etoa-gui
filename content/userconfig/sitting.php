<?PHP

	//Abgelaufene Sittings löschen
  $check_res = dbquery("
  SELECT
      user_sitting_date_to
  FROM
      user_sitting_date
    INNER JOIN
      user_sitting
    ON user_sitting_date_user_id=user_sitting_user_id
 WHERE
 		user_sitting_user_id='".$cu->id()."'
    AND user_sitting_active='1'
 ORDER BY
    user_sitting_date_to DESC
  LIMIT 1;");
  $check_arr=mysql_fetch_assoc($check_res);

  if(mysql_num_rows($check_res)>0 && $check_arr['user_sitting_date_to']<time())
  {
      dbquery("
      UPDATE
          user_sitting
      SET
          user_sitting_active='0',
          user_sitting_sitter_user_id='0',
          user_sitting_sitter_password='0',
          user_sitting_date='0'
      WHERE
          user_sitting_user_id='".$cu->id()."';");

      //löscht alle gespeichertet Sittingdaten des users
      dbquery("DELETE FROM user_sitting_date WHERE user_sitting_date_user_id='".$cu->id()."';");
  }


            //
            // Neuer user anlegen, der am gleichen PC sitzt (multi)
            //

            if (isset($_POST['new_multi'])!="" && checker_verify())
            {
                dbquery("
                INSERT INTO
                	user_multi
                (user_multi_user_id)
                VALUES
                ('".$cu->id()."');");

                echo "Neuer User angelegt<br>";
            }


            //
            // Daten Speichern (multi)
            //

            if (isset($_POST['data_submit_multi']) && checker_verify())
            {

                $user=array_unique($_POST['user_multi_multi_nick']); //löscht alle users die mehrfach eingetragen wurden
                foreach ($user as $id=>$data)
                {
                    if ($user[$id]!="")
                    {
                        //Ist dieser User existent
                        if (get_user_id($user[$id])==0)
                        {
                            $msg = "<b>Fehler:</b> Dieser User exisitert nicht!<br><br>";
                        }
                        //ist der eigene nick eingetragen
                        elseif (get_user_id($user[$id])==$cu->id())
                        {
                            $msg = "<b>Fehler:</b> Du kannst nicht dich selber eintragen!<br><br>";
                        }
                        else
                        {
                            // Daten Speichern
                            dbquery("
                            UPDATE
                                ".$db_table['user_multi']."
                            SET
                                user_multi_multi_user_id='".addslashes(get_user_id($user[$id]))."',
                                user_multi_connection='".addslashes($_POST['user_multi_connection'][$id])."'
                            WHERE
                                user_multi_id=$id;");

                        }

                    }

                }

                //Löscht alle angekreuzten user
                if($_POST['del_multi'])
                {
                    // User löschen
                    foreach ($_POST['del_multi'] as $id=>$data)
                    {
                        if ($_POST['del_multi'][$id]==1)
                        {
                            dbquery("DELETE FROM ".$db_table['user_multi']." WHERE user_multi_id=$id;");

                            // Speichert jeden gelöschten multi (soll vor missbrauch schützen -> mutli erstellen -> löschen -> erstellen -> löschen etc.)
                            dbquery("
                            UPDATE
                                ".$db_table['users']."
                            SET
                                user_multi_delets=user_multi_delets+1
                            WHERE
                                user_id='".$cu->id()."';");
                        }
                    }
                }

                if($msg!="")
                    echo $msg;
                else
                    echo "Daten &uuml;bernommen<br>";
            }



            //
            // Neues Sitting Datum erzeugen
            //

            if (isset($_POST['new_sitting_date']) && checker_verify())
            {
                dbquery("
                INSERT INTO
                ".$db_table['user_sitting_date']."
                (user_sitting_date_user_id)
                VALUES
                ('".$cu->id()."');");

                echo "Neues Datumfeld erzeugt!<br><br>";
            }


            //
            // Daten Speichern (sitting)
            //

            if (isset($_POST['data_submit_sitting']) && checker_verify())
            {
                //überprüft ob der angegebene user wirklich vorhanden ist
                if (get_user_id($_POST['user_sitting_sitter_nick'])!=0 || $_POST['user_sitting_sitter_nick']=="")
                {
                    if(get_user_id($_POST['user_sitting_sitter_nick'])!=$cu->id())
                    {
                        $pw1 = md5($_POST['user_sitting_sitter_password1']);
                        $pw2 = md5($_POST['user_sitting_sitter_password2']);

                        if($pw1==$pw2)
                        {
                            if (strlen($_POST['user_sitting_sitter_password1'])>=PASSWORD_MINLENGHT || $_POST['user_sitting_sitter_password1']=="")
                            {
                                //überprüft, ob das eingegebene sitterpasswort nicht gleich dem normalen passwort ist
                                if(mysql_num_rows(dbquery("SELECT user_password FROM ".$db_table['users']." WHERE user_id='".$cu->id()."' AND user_password='$pw1';"))>0)
                                {
                                    echo "<b>Fehler:</b> Du kannst nicht das gleiche Passwort nehmen wie beim Account!<br><br>";
                                }
                                else
                                {

                                    if($_POST['user_sitting_sitter_password1']=="")
                                        $password = 0;
                                    else
                                        $password = $pw1;


                                    $check_res = dbquery("
                                    SELECT
                                        user_sitting_id
                                    FROM
                                        ".$db_table['user_sitting']."
                                    WHERE
                                        user_sitting_user_id='".$cu->id()."';");
                                    if (mysql_num_rows($check_res)>0)
                                    {
                                        // Daten Speichern
                                        dbquery("
                                        UPDATE
                                            ".$db_table['user_sitting']."
                                        SET
                                            user_sitting_sitter_user_id='".get_user_id($_POST['user_sitting_sitter_nick'])."',
                                            user_sitting_sitter_password='".$password."'
                                        WHERE
                                            user_sitting_user_id='".$cu->id()."';");

                                    }
                                    else
                                    {
                                        dbquery("
                                        INSERT INTO
                                        ".$db_table['user_sitting']."
                                        (user_sitting_user_id,
                                        user_sitting_sitter_user_id,
                                        user_sitting_sitter_password)
                                        VALUES
                                        ('".$cu->id()."',
                                        '".get_user_id($_POST['user_sitting_sitter_nick'])."',
                                        '".$password."');");
                                    }
                                }

                            }
                            else
                            {
                                echo "<b>Fehler:</b> Das Passwort muss mindestens ".PASSWORD_MINLENGHT." Zeichen lang sein!<br><br>";
                            }
                        }
                        else
                        {
                            echo "<b>Fehler:</b> Die beiden Passw&ouml;rter m&uuml;ssen identisch sein!<br><br>";
                        }
                    }
                    else
                    {
                        echo "<b>Fehler:</b> Du kannst nicht dich selbst zum Sitter ernennen!<br><br>";
                    }

                }
                else
                {
                    echo "<b>Fehler:</b> Ein Spieler mit dem Nick \"".$_POST['user_sitting_sitter_nick']."\" wurde nicht gefunden!<br><br>";
                }


								//Sitting Daten löschen
                if($_POST['del_sitting_date'])
                {
                    // Sittingdaten löschen
                    foreach ($_POST['del_sitting_date'] as $id=>$data)
                    {
                        if ($_POST['del_sitting_date'][$id]==1)
                            dbquery("DELETE FROM ".$db_table['user_sitting_date']." WHERE user_sitting_date_id=".$id.";");
                    }
                }


                if($_POST['user_sitting_time']==0 && $_POST['user_sitting_time']!=NULL)
                {
                    echo "<b>Fehler:</b> Du must eine Zeitdauer beim Sitterdatum angeben!<br><br>";
                }
                else
                {
                    $sitting_from = mktime($_POST['user_sitting_from_h'],$_POST['user_sitting_from_i'],0,$_POST['user_sitting_from_m'],$_POST['user_sitting_from_d'],$_POST['user_sitting_from_y']);
                    $sitting_to = $sitting_from + ($_POST['user_sitting_time']*3600*24);

                    //überprüft, ob das eingegebene datum nach dem letzten ist
                    $date_check_res = dbquery("
                    SELECT
                        user_sitting_date_to
                    FROM
                        ".$db_table['user_sitting_date']."
                    WHERE
                        user_sitting_date_user_id='".$cu->id()."'
                    ORDER BY
                        user_sitting_date_to DESC;");
                    $date_check_row = mysql_fetch_object($date_check_res);
                    $last_date = $date_check_row->user_sitting_date_to;

										//überprüft, ob das neue Datum nicht ein älteres schneidet
                    if($last_date<$sitting_from)
                    {
                        // Sittingzeit Speichern
                        dbquery("
                        UPDATE
                            ".$db_table['user_sitting_date']."
                        SET
                            user_sitting_date_from='".$sitting_from."',
                            user_sitting_date_to='".$sitting_to."'
                        WHERE
                            user_sitting_date_user_id='".$cu->id()."'
                            AND user_sitting_date_from='0'
                            AND user_sitting_date_to='0';");
                    }
                    else
                    {
                        if($_POST['user_sitting_time']!=NULL)
                        {
                            echo "<b>Fehler:</b> Das Startdatum muss sp&auml;ter als das Enddatum vom vorherigen Datum sein!<br><br>";
                        }
                    }
                }

            }

            //Sittermodus aktivieren

            if (isset($_POST['sitting_activade']) && checker_verify())
            {
                //Errechnet Anzahl der Sittertage
                $date_res = dbquery("
                SELECT
                    user_sitting_date_from,
                    user_sitting_date_to
                FROM
                    ".$db_table['user_sitting_date']."
                WHERE
                    user_sitting_date_user_id='".$cu->id()."'
                    AND user_sitting_date_from!=0
                    AND user_sitting_date_to!=0
                ORDER BY
                    user_sitting_date_from;");

                $sitting_from=0;
                $sitting_to=0;
                while ($date_arr=mysql_fetch_array($date_res))
                {
                    $sitting_from+=$date_arr['user_sitting_date_from'];
                    $sitting_to+=$date_arr['user_sitting_date_to'];
                }
                $sitting_days=($sitting_to-$sitting_from)/3600/24;

                //Speichert alle nötigen Daten
                dbquery("
                UPDATE
                    ".$db_table['user_sitting']."
                SET
                    user_sitting_active='1',
                    user_sitting_date='".time()."'
                WHERE
                    user_sitting_user_id='".$cu->id()."';");

                dbquery("
                UPDATE
                    ".$db_table['users']."
                SET
                    user_sitting_days=user_sitting_days-".$sitting_days."
                WHERE
                    user_id='".$cu->id()."';");

                //löscht daten beidenen keine zeit festgelegt ist
                dbquery("DELETE FROM ".$db_table['user_sitting_date']." WHERE user_sitting_date_from=0 AND user_sitting_date_to=0;");

                echo "<b>Der Sittermodus wurde aktiviert!</b><br><br>";
            }


            //
            // Multierkennung
            //
            echo "<form action=\"?page=$page&mode=sitting\" method=\"post\">";
            $cstr = checker_init();

            $res = dbquery("
            SELECT
                *
            FROM
                ".$db_table['user_multi']."
            WHERE
                user_multi_user_id='".$cu->id()."'
            ORDER BY
                user_multi_id;");

            $user_res = dbquery("
            SELECT
                user_sitting_days
            FROM
                ".$db_table['users']."
            WHERE
                user_id='".$cu->id()."';");
            $user_arr = mysql_fetch_array($user_res);

            tableStart("Multierkennung [<a href=\"?page=help&site=multi_sitting\">Info</a>]");

            echo "<tr>
                    <th class=\"tbltitle\" width=\"35%\">User</th>
                    <th class=\"tbltitle\" width=\"55%\">Beziehung</th>
                    <th class=\"tbltitle\" width=\"10%\">L&ouml;schen</th>
                    ";

                    $unused_multi=0;
                    while ($arr = mysql_fetch_array($res))
                    {


                        echo "<tr><td class=\"tbldata\">";

                        if($arr['user_multi_multi_user_id']!=0)
                        {
                            echo "<input type=\"text\" name=\"user_multi_multi_nick[".$arr['user_multi_id']."]\" maxlength=\"20\" size=\"20\" value=\"".stripslashes(get_user_nick($arr['user_multi_multi_user_id']))."\" readonly=\"readonly\">";
                        }
                        else
                        {
                            echo "<input type=\"text\" name=\"user_multi_multi_nick[".$arr['user_multi_id']."]\" id=\"user_nick_multi\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"Usernick\" onkeyup=\"xajax_searchUser(this.value,'user_nick_multi','citybox_multi');\"><br/>
            								<div class=\"citybox\" id=\"citybox_multi\">&nbsp;</div>";
                            $unused_multi++;
                        }

                        echo "</td>";
                        echo "<td class=\"tbldata\">";

                        if($arr['user_multi_connection']!='0')
                        {
                            echo "<input type=\"text\" name=\"user_multi_connection[".$arr['user_multi_id']."]\" maxlength=\"50\" size=\"50\" value=\"".stripslashes($arr['user_multi_connection'])."\" readonly=\"readonly\">";
                        }
                        else
                        {
                            echo "<input type=\"text\" name=\"user_multi_connection[".$arr['user_multi_id']."]\" maxlength=\"50\" size=\"50\" value=\"\">";
                        }

                        echo "</td>";
                        echo "<td class=\"tbldata\" style=\"text-align:center;\">";
                        echo "<input type=\"checkbox\" name=\"del_multi[".$arr['user_multi_id']."]\" value=\"1\" />";
                        echo "</td></tr>";

                    }
                    if($unused_multi<1 && $s['sitter_active']==0)
                    {
                        echo "<tr><td class=\"tbldata\" style=\"text-align:center;\" colspan=\"3\"><input type=\"submit\" name=\"new_multi\" value=\"User hinzuf&uuml;gen\"/></td></tr>";
                    }

            tableEnd();

            echo "<input type=\"submit\" name=\"data_submit_multi\" value=\"&Uuml;bernehmen\"/></form><br/><br/><br>";



            //
            // Sitter einstellungen
            //
            //überprüft ob der user noch Sittertage zur verfügung hat
            if(intval($user_arr['user_sitting_days'])>0)
            {

                $res = dbquery("
                SELECT
                    *
                FROM
                    ".$db_table['user_sitting']."
                WHERE
                    user_sitting_user_id='".$cu->id()."'
                ORDER BY
                    user_sitting_id;");
                $arr = mysql_fetch_array($res);


                //Sperrt Formular wenn der Modus aktiv ist
                if($arr['user_sitting_active']==0)
                {
                    tableStart("Sitter Einstellungen [<a href=\"?page=help&site=multi_sitting\">Info</a>]");
                    echo "<form action=\"?page=$page&mode=sitting\" method=\"post\">";
                    echo $cstr;

                    $nick_class="tbldata2";
                    $nick_check=0;
                    $pw_class="tbldata2";
                    $pw_check=0;
                    $date_check=0;

        						//Prüft, ob bereits ein User angegen wurde
                    if($arr['user_sitting_sitter_user_id']!='0' && $arr['user_sitting_sitter_user_id']!=NULL)
                    {
                        $nick_class="tbldata";
                        $nick_check=1;
                    }

        						//Prüft, ob bereits ein passwort angegen wurde
                    if($arr['user_sitting_sitter_password']!='0' && $arr['user_sitting_sitter_password']!=NULL)
                    {
                        $pw_class="tbldata";
                        $pw_check=1;
                    }

                    //Listet alle eingetragenen daten aus, und ev. noch ein ausfüllfeld dazu
                    $date_res = dbquery("
                    SELECT
                        *
                    FROM
                        ".$db_table['user_sitting_date']."
                    WHERE
                        user_sitting_date_user_id='".$cu->id()."'
                        AND user_sitting_date_from!=0
                        AND user_sitting_date_to!=0
                    ORDER BY
                        user_sitting_date_from;");

                    $date_cnt = mysql_num_rows($date_res);
                    $rowspan=0;
                    $activade=0;
                    $sitting_from=0;
                    $sitting_to=0;

                    //Es ist noch kein Datum festgelegt worden
                    if($date_cnt==0)
                    {
                      //Zeichnet "Sitting Daten" farbig und dahinter eine leere Zeile
                        echo "<tr>
                            <td class=\"tbldata2\" width=\"35%\">Sitting Daten</td>
                            <td class=\"tbldata\" width=\"65%\" colspan=\"2\"></td>
                             </tr>";
                    }
                    //Min. 1 Datum ist festgelegt -> Anzeigen der übrigen Formularfelder
                    else
                    {
                        //Sitter Nick
                        echo "<tr>
                                <td class=\"$nick_class\" width=\"35%\">Sitter Nick</td>
                                <td class=\"tbldata\" width=\"65%\" colspan=\"2\" ".tm("Sitter Nick","Gib hier den Nick des Users an, welcher dein Account Sitten soll.").">";

                                if($arr['user_sitting_sitter_user_id']!=0)
                                {
                                    echo "<input type=\"text\" name=\"user_sitting_sitter_nick\" maxlength=\"20\" size=\"20\" value=\"".stripslashes(get_user_nick($arr['user_sitting_sitter_user_id']))."\" id=\"user_nick_sitting\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick_sitting','citybox_sitting');\"><br/>
                                    <div class=\"citybox\" id=\"citybox_sitting\">&nbsp;</div>";
                                }
                                else
                                {
                                    echo "<input type=\"text\" name=\"user_sitting_sitter_nick\" maxlength=\"20\" size=\"20\" value=\"\" id=\"user_nick_sitting\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick_sitting','citybox_sitting');\"><br/>
                                    <div class=\"citybox\" id=\"citybox_sitting\">&nbsp;</div>";
                                }
                                echo "</td>";
                        echo "</tr>";

                        //Sitter Passwort
                        echo "<tr>
                                <td class=\"$pw_class\" width=\"35%\">Sitter Passwort</td>
                                <td class=\"tbldata\" width=\"65%\" colspan=\"2\" ".tm("Sitter Passwort","Definiere hier das Passwort, mit dem sich dein Sitter einlogen kann.").">
                                    <input type=\"password\" name=\"user_sitting_sitter_password1\" maxlength=\"20\" size=\"20\" value=\"\">
                                </td>
                             </tr>";

                        //Sitter Passwort (wiederholen)
                        echo "<tr>
                                <td class=\"$pw_class\" width=\"35%\">Sitter Passwort (wiederholen)</td>
                                <td class=\"tbldata\" width=\"65%\" colspan=\"2\" ".tm("Sitter Passwort (wiederholen)","Zur SIcherheit, musst du hier das Passwort noch einmal hinschreiben.").">
                                    <input type=\"password\" name=\"user_sitting_sitter_password2\" maxlength=\"20\" size=\"20\" value=\"\">
                                </td>
                             </tr>";


                        echo "<td class=\"tbldata\" width=\"35%\" rowspan=\"$date_cnt\">Sitting Daten</td>";

                        //Listet alle festgelegten daten auf
                        while ($date_arr=mysql_fetch_array($date_res))
                        {

                            if(mysql_num_rows($date_res)!=$date_cnt)
                            {
                                echo "<tr>";
                                echo "<td class=\"tbldata\" width=\"35%\" rowspan=\"$date_cnt\">&nbsp;</td>";
                            }

                            echo "<td class=\"tbldata\" width=\"60%\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</td>";
                            echo "<td class=\"tbldata\" width=\"5%\" style=\"text-align:center;\" ".tm("Datum löschen","Setz ein Häckchen, wenn du dieses Datum löschen willst.")."><input type=\"checkbox\" name=\"del_sitting_date[".$date_arr['user_sitting_date_id']."]\" value=\"1\"/></td></tr>";

                            $sitting_from+=$date_arr['user_sitting_date_from'];
                            $sitting_to+=$date_arr['user_sitting_date_to'];
                        }
                        $date_check=1;
                        $date_cnt--;

               			}

                  	echo "<tr><td class=\"tbldata\" colspan=\"3\"><div align=\"center\">";

                    $date_field_res = dbquery("
                    SELECT
                        user_sitting_date_id
                    FROM
                        ".$db_table['user_sitting_date']."
                    WHERE
                        user_sitting_date_user_id='".$cu->id()."'
                        AND user_sitting_date_to=0;");

                    $rest_days = $user_arr['user_sitting_days'];
                    $prof_rest_days = $rest_days - (($sitting_to-$sitting_from)/3600/24);
                    if(mysql_num_rows($date_field_res)==1)
                    {
                        echo "Von ";
                        show_timebox("user_sitting_from",time());
                        echo " Dauer (Tage) ";

                        // erstellt ein Optionsfeld mit den anzahl sitting tagen die der user noch zur verfügung hat
                        echo "<select name=\"user_sitting_time\">";
                        for ($x=0;$prof_rest_days>=$x;$x++)
                        {
                            if($prof_rest_days==0)
                                echo "<option value=\"0\">-</option>";
                            else
                                echo "<option value=\"".$x."\">".$x."</option>";
                        }
                        echo "</select>";
                        echo "<br>";
                    }

                    if(mysql_num_rows($date_field_res)==0)
                    {
                        if($user_arr['user_sitting_days']!=0 && $prof_rest_days>0)
                        {
                            echo "<br><input type=\"submit\" name=\"new_sitting_date\" value=\"Datum hinzuf&uuml;gen\" ".tm("Datum hinzufügen","F&uuml;ge eine neue Sitterzugriffszeit ein.")."/>";
                        }
                        else
                        {
                            echo "<br>Dir stehen keine weiteren Sittertage zur verf&uuml;gung!";
                        }
                    }
                    //wenn user,passwort und datum korrekt eingegeben sind, zeige button zum aktivieren
                    if($nick_check==1 && $pw_check==1 && $date_check==1)
                    {
                        echo "<br><br><input type=\"submit\" name=\"sitting_activade\" style=\"color:#0f0\" value=\"Sittingmodus aktivieren\" ".tm("Sittingmodus aktivieren","Aktiviert den Sittmodus mit den momentanen Daten.")." onclick=\"return confirm('Wenn du diese Info best&auml;tigst wird der Modus mit sofortiger Wirkung aktiviert und kann nicht mehr ge&auml;ndert werden.\nDu solltest dir also sicher sein, dass die Daten richtig eingegeben sind!');\" />
                        	</td>
                        </tr>
                        <tr>
                        	<td class=\"tbldata\" colspan=\"3\">Alle Sittingdaten sind nun korrekt eingestellt. Der Sittingmodus kann nun mit einem Klick auf \"Sittingmodus aktivieren\" gestartet werden oder es können noch Änderungen angebracht werden!";
                    }
                    else
                    {
                     echo "</td>
                        </tr>
                        <tr>
                        	<td class=\"tbldata2\" colspan=\"3\">Es sind nicht alle benötigten Daten angegeben!";
                    }

                    echo "</div></td></tr>";
                    tableEnd();

                    echo "<input type=\"submit\" name=\"data_submit_sitting\" value=\"&Uuml;bernehmen\" ".tm("Übernehmen","Speichert die angegebenen Daten. Mit diesem Button wird der Sittingmodus aber NICHT aktiviert!")."/>";
                    echo "</form><br/><br/><br>";
                }
                else
                {
                    tableStart("Sitter Einstellungen");
                    echo "<tr><td class=\"tbldata\"><div align=\"center\"><b>Modus aktiv!</b></div></td></tr>";

                    $date_res = dbquery("
                    SELECT
                        *
                    FROM
                        ".$db_table['user_sitting_date']."
                    WHERE
                        user_sitting_date_user_id='".$cu->id()."'
                        AND user_sitting_date_from!=0
                        AND user_sitting_date_to!=0
                    ORDER BY
                        user_sitting_date_from;");

                    echo "<tr><td class=\"tbldata\"><div align=\"center\">";
                    while ($date_arr=mysql_fetch_array($date_res))
                    {
                        if($date_arr['user_sitting_date_to']<time())
                        {
                            echo "<span style=\"color:#f00\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
                        }
                        elseif($date_arr['user_sitting_date_from']<time() && $date_arr['user_sitting_date_to']>time())
                        {
                            echo "<span style=\"color:#0f0\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
                        }
                        else
                        {
                            echo "Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."<br>";
                        }
                    }
                    echo "</td></tr>";
                    tableEnd();
                }
            }
            //Dem User stehen keine Sittertage mehr zur Verfügung
            else
            {
              tableStart("Sitter Einstellungen");
              echo "<tr><td class=\"tbldata2\"><div align=\"center\">Dir stehen keine weiteren Sittertage zur verf&uuml;gung!</div></td></tr>";
              tableEnd();
            }

?>