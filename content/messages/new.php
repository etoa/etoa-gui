<?PHP
		if ($_POST['submit']!="" && checker_verify())
		{
			$time = time();
			$uid = get_user_id(rawurldecode($_POST['message_user_to']));
			if ($uid>0)
			{
				// Prüfe Flooding
				$flood_interval = time()-FLOOD_CONTROL;
				if (!isset($s['messages']['sent'][$uid]) || $s['messages']['sent'][$uid] < $flood_interval)
				{
					// Prüfe Ignore
					$res = dbquery("
					SELECT 
						COUNT(ignore_id)
					FROM
						".$db_table['message_ignore']."
					WHERE
						ignore_owner_id=".$uid."
						AND ignore_target_id=".$cu->id()."
					;");
					$arr=mysql_fetch_row($res);
					if ($arr[0]==0)
					{					
						// Prüfe Titel
						$check_subject=check_illegal_signs($_POST['message_subject']);
						if($check_subject=="")
						{
								$s['messages']['sent'][$uid]=$time;
         		    dbquery("
         		    INSERT INTO 
         		    	".$db_table['messages']." 
         		    (
         		    	message_user_from,
         		    	message_user_to,
         		    	message_timestamp,
         		    	message_cat_id,
         		    	message_subject,
         		    	message_text
         		    ) 
         		   	VALUES 
         		   	(
         		   		'".$cu->id()."',
         		   		'".$uid."',
         		   		".$time.",
         		   		".USER_MSG_CAT_ID.",
         		   		'".addslashes($_POST['message_subject'])."',
         		   		'".addslashes($_POST['message_text'])."'
         		   	);");
         		    infobox_start("Nachrichtenversand",1);
         		    echo "Nachricht wurde an <b>".$_POST['message_user_to']."</b> gesendet!";
         		    infobox_end();
         		    $_POST['message_user_to']=null;
         		}
         		else
         		{
       		    infobox_start("Nachrichtenversand");
         			echo "Du hast ein unerlaubtes Zeichen ( ".$check_subject." ) im Betreff!";
       		    infobox_end();
         		}
         	}
					else
					{
						infobox_start("Nachrichtenversand");
						echo "<b>Fehler:</b> Dieser Benutzer hat dich ignoriert, die Nachricht wurde nicht gesendet!";
						infobox_end();
					}         	
				}
				else
				{
					infobox_start("Nachrichtenversand");
					echo "<b>Flood-Kontrolle!</b> Du kannst erst nach ".FLOOD_CONTROL." Sekunden eine neue Nachricht an ".$_POST['message_user_to']." schreiben!";
					infobox_end();
				}
			}
			else
			{
				infobox_start("Nachrichtenversand");
				echo "<b>Fehler:</b>: Dieser Benutzer existiert nicht!";
				infobox_end();
			}
		}
			
			// User zuweisen
			// Wenn Username durch Link weitergegeben wird (z.b. Stats -> mail)
			if(isset($_GET['message_user_to']))
			{
				$user = get_user_nick(intval($_GET['message_user_to']));
			}
			// Username löschen falls auf "Weiterleiten" geklcikt wurde
			elseif (isset($_POST['remit']))
			{
				$user = '';
			}
			//Der Username wird übernommen wenn dieser angegeben ist
			elseif (isset($_POST['message_user_to']))
			{
				$user = get_user_nick(intval($_POST['message_user_to']));
			}
			else
			{
				$user = ''; 
			}
			
			// Betreff zuweisen
			if (isset($_GET['message_subject']))
			{
				$subj = base64_decode($_GET['message_subject']);
			}
			elseif (isset($_POST['message_subject']))
			{
				// Weiterleiten
				if (isset($_POST['remit']))
				{
					$subj = 'Fw: '.stripslashes($_POST['message_subject']);
				}
				// Antworten
				elseif (isset($_POST['answer']))
				{
					$subj = 'Re: '.stripslashes($_POST['message_subject']);
				}
				else
				{
					$subj = stripslashes($_POST['message_subject']);
				}
			}
			else
			{
				$subj = '';
			}				
			
			// Text zuweisen
			/*if (isset($_GET['body']))
			{
				$text = stripslashes(base64_decode($_GET['body']));
			}	*/		
			if (isset($_POST['message_text']))
			{
				if (isset($_POST['message_sender']))
				{
					$text = "\n\n[b]Nachricht von ".$_POST['message_sender'].":[/b]\n\n".stripslashes($_POST['message_text'])."";
				}
				else
				{
					$text = "\n\n".stripslashes($_POST['message_text'])."";
				}
			}
			elseif (isset($_GET['message_text']))
			{
				if (isset($_GET['message_sender']))
				{
					$text = "\n\n[b]Nachricht von ".base64_decode($_GET['message_sender']).":[/b]\n\n".base64_decode(stripslashes($_GET['message_text']))."";
				}
				else
				{
					$text = "\n\n".base64_decode(stripslashes($_GET['message_text']))."";
				}
			}			
	    else
	    {
	    	$text = '';
	    }			

			if ($s['user']['msgsignature'])
	    {
	    	$text = "\n\n".$s['user']['msgsignature'].$text;
	    }
			echo "<form action=\"?page=".$page."&mode=".$mode."\" method=\"POST\" name=\"msgform\">";
			checker_init();
			echo "<table width=\"300\" align=\"center\" class=\"tbl\">";
			echo "<tr>
			     	<td class=\"tbltitle\" colspan=\"3\">Neue Nachricht</td>
				</tr>";
			echo "<tr>
					<td class=\"tbltitle\" width=\"50\" valign=\"top\">Empf&auml;nger:</td>
					<td class=\"tbldata\" width=\"250\"  colspan=\"2\">
						<input type=\"text\" name=\"message_user_to\" id=\"user_nick\" autocomplete=\"off\" value=\"";
					echo $user;
					echo "\" size=\"30\" maxlength=\"255\" onkeyup=\"xajax_searchUser(this.value);\"><br/>
					<div class=\"citybox\" id=\"citybox\">&nbsp;</div>

					</td>
			     </tr>";
			echo "<tr>
					<td class=\"tbltitle\" width=\"50\" valign=\"top\">Betreff:</td>
					<td class=\"tbldata\" width=\"250\" colspan=\"2\">
						<input type=\"text\" name=\"message_subject\" value=\"".$subj."\" size=\"30\" maxlength=\"255\">
					</td>
			     </tr>";
				echo "<tr>
					<td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:</td>
					<td class=\"tbldata\" width=\"250\"><textarea name=\"message_text\" id=\"message\" rows=\"10\" cols=\"60\" ";
					if ($msgcreatpreview)
					{
						echo "onkeyup=\"xajax_messagesNewMessagePreview(this.value)\"";
					}
					echo ">".$text."</textarea></td>";
					
					if ($msgcreatpreview)
					{
						$prevstr="xajax_messagesNewMessagePreview(document.getElementById('message').value)";
					}
					else
					{
						$prevstr="";
					}
					echo "<td class=\"tbldata\">
					<input type=\"button\" onclick=\"bbcode(this.form,'b','');".$prevstr."\" value=\"B\" style=\"font-weight:bold;\">
					<input type=\"button\" onclick=\"bbcode(this.form,'i','');".$prevstr."\" value=\"I\" style=\"font-style:italic;\">
					<input type=\"button\" onclick=\"bbcode(this.form,'u','');".$prevstr."\" value=\"U\" style=\"text-decoration:underline\">
					<input type=\"button\" onclick=\"bbcode(this.form,'c','');".$prevstr."\" value=\"Center\" style=\"text-align:center\"> <br/><br/>
					<input type=\"button\" onclick=\"namedlink(this.form,'url');".$prevstr."\" value=\"Link\">
					<input type=\"button\" onclick=\"namedlink(this.form,'email');".$prevstr."\" value=\"E-Mail\">
					<input type=\"button\" onclick=\"bbcode(this.form,'img','http://');".$prevstr."\" value=\"Bild\"> <br/><br/>";
					?>
					<select id="sizeselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'size');<?PHP echo $prevstr;?>">
					  <option value="0">Gr&ouml;sse</option>
					  <option value="7">winzig</option>
						<option value="10">klein</option>
						<option value="12">mittel</option>
						<option value="16">groß</option>
						<option value="20">riesig</option>
					</select>
					<select id="colorselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'color');<?PHP echo $prevstr;?>">
					  <option value="0">Farbe</option>
					  <option value="skyblue" style="color: skyblue;">sky blue</option>
						<option value="royalblue" style="color: royalblue;">royal blue</option>
						<option value="blue" style="color: blue;">blue</option>
						<option value="darkblue" style="color: darkblue;">dark-blue</option>
						<option value="orange" style="color: orange;">orange</option>
						<option value="orangered" style="color: orangered;">orange-red</option>

						<option value="crimson" style="color: crimson;">crimson</option>
						<option value="red" style="color: red;">red</option>
						<option value="firebrick" style="color: firebrick;">firebrick</option>
						<option value="darkred" style="color: darkred;">dark red</option>
						<option value="green" style="color: green;">green</option>
						<option value="limegreen" style="color: limegreen;">limegreen</option>
						<option value="seagreen" style="color: seagreen;">sea-green</option>
						<option value="deeppink" style="color: deeppink;">deeppink</option>
						<option value="tomato" style="color: tomato;">tomato</option>

						<option value="coral" style="color: coral;">coral</option>
						<option value="purple" style="color: purple;">purple</option>
						<option value="indigo" style="color: indigo;">indigo</option>
						<option value="burlywood" style="color: burlywood;">burlywood</option>
						<option value="sandybrown" style="color: sandybrown;">sandy brown</option>
						<option value="sienna" style="color: sienna;">sienna</option>
						<option value="chocolate" style="color: chocolate;">chocolate</option>
						<option value="teal" style="color: teal;">teal</option>
						<option value="silver" style="color: silver;">silver</option>
					</select>
				<?php
					echo "</td>";
			echo "</tr>";
			if ($msgcreatpreview)
			{
			echo "<tr>
						<td class=\"tbltitle\">Vorschau:</td>
						<td class=\"tbldata\" colspan=\"2\" id=\"msgPreview\">Vorschau wird geladen...</td>
					</tr>";
			}
			echo "</table><br/>";
			echo "<script type=\"text/javascript\">";
			if ($msgcreatpreview)
			{
				echo "xajax_messagesNewMessagePreview(document.getElementById('message').value);";
			}
			echo "document.getElementById('user_nick').focus()";
			echo "</script>";
			echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" onclick=\"if (document.getElementById('message_user_to').value=='') {window.alert('Empf&auml;nger fehlt!');document.getElementById('message_user_to').focus();return false;}\">";
			echo "</form>";
?>