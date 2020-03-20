<?PHP

	function admin_create_new_dataset($db_fields)
	{
		foreach ($db_fields as $k=>$a)
		{
			switch ($a['type'])
			{
				case "readonly":
				break;

				case "text":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$a['def_val']."\" /></td></tr>";
				break;
				case "hidden":
					echo "<input type=\"hidden\" name=\"".$a['name']."\" value=\"".$a['def_val']."\" />";
				break;
				case "email":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$a['def_val']."\" /></td></tr>";
				break;
				case "url":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$a['def_val']."\" /></td></tr>";
				break;
				case "numeric":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$a['def_val']."\" /></td></tr>";
				break;
				case "password":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"password\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$a['def_val']."\" /></td></tr>";
				break;
				case "timestamp":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$a['def_val']."\" /></td></tr>";
				break;
				case "textarea":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><textarea name=\"".$a['name']."\" rows=\"".$a['rows']."\" cols=\"".$a['cols']."\">".$a['def_val']."</textarea></td></tr>";
				break;
				case "radio":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">";
					foreach ($a['rcb_elem'] as $rk=>$rv)
					{
						echo $rk.": <input name=\"".$a['name']."\" type=\"radio\" value=\"$rv\"";
						if ($a['rcb_elem_chekced']==$rv) echo " checked=\"checked\"";
						echo " /> ";
					}
					echo "</td></tr>";
				break;
				case "checkbox":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">";
					foreach ($a['rcb_elem'] as $rk=>$rv)
					{
						echo $rk.": <input name=\"".$a['name']."\" type=\"checkbox\" value=\"$rv\"";
						if (in_array($rv,$a['rcb_elem_chekced'])) echo " checked=\"checked\"";
						echo " /> ";
					}
					echo "</td></tr>";
				break;
				case "select":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><select name=\"".$a['name']."\">";
					foreach ($a['select_elem'] as $rk=>$rv)
					{
						echo "<option value=\"$rv\"";
						if (isset($a['select_elem_chekced']) && $a['select_elem_chekced']==$rv) echo " selected=\"selected\"";
						echo ">$rk</option> ";
					}
					echo "</td></tr>";
				break;
				case "dbimage":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"file\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" /></td></tr>";
				break;
				case "fleetaction":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">";
					$actions = FleetAction::getAll();
					foreach ($actions as $ac)
					{
						echo "<input name=\"".$a['name']."[]\" type=\"checkbox\" value=\"".$ac->code()."\"";
						echo " /> ".$ac."<br/>";
					}
					echo "</td></tr>";
					break;
				default:
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$a['def_val']."\" /></td></tr>";
				break;
			}
		}
	}


	function admin_create_new_dataset_query($db_fields)
	{
		global $_FILES;
		$type="";
		$form_data="";

		$cnt = 1;
		$fsql = "";
		$vsql = "";
		$vsqlsp = "";
		foreach ($db_fields as $k=>$a)
		{
			if ($a['type']!="readonly")
			{
				$fsql .= "`".$a['name']."`";
				if ($cnt < sizeof($db_fields))
					$fsql .= ",";
			}
			$cnt++;
		}
		$cnt = 1;
		foreach ($db_fields as $k=>$a)
		{
			switch ($a['type'])
			{
				case "readonly":
				break;
				case "text":
					$vsql .= "'".addslashes($_POST[$a['name']])."'";
				break;
				case "email":
					$vsql .= "'".$_POST[$a['name']]."'";
				break;
				case "url":
					$vsql .= "'".$_POST[$a['name']]."'";
				break;
				case "numeric":
					$vsql .= "'".$_POST[$a['name']]."'";
				break;
				case "password":
					$vsql .= "'".md5($_POST[$a['name']])."'";
				break;
				case "timestamp":
					$vsql .= "UNIX_TIMESTAMP('".$_POST[$a['name']]."')";
				break;
				case "textarea":
					$vsql .= "'".addslashes($_POST[$a['name']])."'";
				break;
				case "radio":
					$vsql .= "'".$_POST[$a['name']]."'";
				break;
				case "checkbox":
					$vsql .= "'".$_POST[$a['name']]."'";
				break;
				case "select":
					$vsql .= "'".$_POST[$a['name']]."'";
				break;
				case "dbimage":

					if ($_FILES[$a['name']]['name']!="")
					{
						$image_type=$_FILES[$a['name']]['type'];
						if (stristr($type,"image/"))
						{
							$iminfo = getimagesize($_FILES[$a['name']]['tmp_name']);
							$imdata = addslashes(fread(fopen($form_data, "r"), filesize($form_data)));

							$image = imagecreatefromjpeg($_FILES[$a['name']]['tmp_name']);
							$image1 = imagecreate(150,150*$iminfo['1']/$iminfo['0']);
							$farbe_body=imagecolorallocate($image1,51,51,51);
							imagecopyresized($image1, $image, 0,0, 0, 0,150,150*$iminfo['1']/$iminfo['0'], $iminfo['0'],$iminfo['1']);
							imagejpeg($image1,$_FILES[$a['name']]['tmp_name']);
							$imtdata = addslashes(fread(fopen($_FILES[$a['name']]['tmp_name'], "r"), filesize($form_data)));
						}
						else
						{
							die ("Sorry, this file is not an image!<br/><br/><a href=\"?\">Back</a>");
						}
					}
					else
					{
						die ("Sorry, you haven't choosen a file!<br/><br/><a href=\"?\">Back</a>");
					}


					$fsql .= ",`".$a['db_image_thumb_field']."`,`".$a['db_image_type_field']."`";
					$vsqlsp .= ",'".$imtdata."','".$_FILES[$a['name']]['type']."'";
					$vsql .= "'".$imdata."'";
				break;
				case "fleetaction":
					if (is_array($_POST[$a['name']]))
						$str = implode(",",$_POST[$a['name']]);
					else
						$str = "";
					$vsql .= "'".$str."'";
				break;
				default:
					$vsql .= "'".addslashes($_POST[$a['name']])."'";
				break;
			}
			if ($cnt < sizeof($db_fields) && $a['type']!="readonly")
				$vsql .= ",";
			$cnt++;
		}

		$sql = "INSERT INTO ".DB_TABLE." (";
		$sql.= $fsql;
		$sql.= ") VALUES(";
		$sql.= $vsql.$vsqlsp;
		$sql.= ");";
		return $sql;
	}


	function admin_edit_dataset($db_fields,$arr)
	{
		$hidden_rows = array();

		echo "<tr><td style=\"vertical-align:top;\"><table style=\"width:100%;\">";
		foreach ($db_fields as $k=>$a)
		{
			echo "<tr id=\"row_".$a['name']."\"";
			if (in_array($a['name'],$hidden_rows))
				echo " style=\"display:none;\"";

			echo ">\n<th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>\n";
			echo "<td class=\"tbldata\" width=\"200\">\n";
			$stl = (isset($a['def_val']) && $arr[$a['name']]!=$a['def_val'] ? ' class="changed"' : '');
			switch ($a['type'])
			{
				case "readonly":
					echo $arr[$a['name']];
				break;
				case "text":
					echo "<input $stl type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".stripslashes($arr[$a['name']])."\" />";
				break;
				case "hidden":
					echo "<input type=\"hidden\" name=\"".$a['name']."\" value=\"".$arr[$a['name']]."\" />";
				break;
				case "email":
					echo "<input $stl type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$arr[$a['name']]."\" />";
				break;
				case "url":
					echo "<input $stl type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$arr[$a['name']]."\" />";
				break;
				case "numeric":
					echo "<input $stl type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".$arr[$a['name']]."\" />";
				break;
				case "password":
					echo "<input $stl type=\"password\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"\" />";
				break;
				case "timestamp":
					echo "<input $stl type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".date(DATE_FORMAT,$arr[$a['name']])."\" />";
				break;
				case "textarea":
					echo "<textarea $stl name=\"".$a['name']."\" rows=\"".$a['rows']."\" cols=\"".$a['cols']."\">".stripslashes($arr[$a['name']])."</textarea>";
				break;
				case "radio":
					foreach ($a['rcb_elem'] as $rk=>$rv)
					{
						echo $rk.": <input name=\"".$a['name']."\" type=\"radio\" value=\"$rv\"";
						if ($arr[$a['name']]==$rv)
							echo " checked=\"checked\"";

							$onclick_actions = array();

						// Zeige andere Elemente wenn Einstellung aktiv
						if (isset($a['show_hide']))
						{
							foreach ($a['show_hide'] as $sh)
							{
								$onclick_actions[]= "document.getElementById('row_".$sh."').style.display='".($rv==1 ? "" : "none")."';";
							}
						}

						// Verstecke andere Elemente wenn Einstellung aktiv
						if (isset($a['hide_show']))
						{
							foreach ($a['hide_show'] as $sh)
							{
								$onclick_actions[]= "document.getElementById('row_".$sh."').style.display='".($rv==1?"none":"")."';";
							}
						}	
                                                
						if(count($onclick_actions)>0){
								echo " onclick=\"".implode("", $onclick_actions)."\"";
						}
                                                
						echo " /> ";
					}
					if (isset($a['show_hide']) && $arr[$a['name']]==$rv)
					{
						$hidden_rows = $a['show_hide'];
					}
					if (isset($a['hide_show']) && $arr[$a['name']]!=$rv)					
					{
						$hidden_rows = $a['hide_show'];
					}	
				break;
				case "checkbox":
					foreach ($a['rcb_elem'] as $rk=>$rv)
					{
						echo $rk.": <input name=\"".$a['name']."\" type=\"checkbox\" value=\"$rv\"";
						if (in_array($rv,explode(";",$arr[$a['name']])))
							echo " checked=\"checked\"";
						echo " /> ";
					}
					echo "";
				break;
				case "select":
					echo "<select name=\"".$a['name']."\">";
					echo "<option value=\"\">(leer)</option>";
					foreach ($a['select_elem'] as $rk=>$rv)
					{
						echo "<option value=\"$rv\"";
						if ($arr[$a['name']]==$rv) echo " selected=\"selected\"";
						echo ">$rk</option> ";
					}
					echo "";
				break;
				case "fleetaction":
					echo "";
					$keys = explode(",",$arr[$a['name']]);
					$actions = FleetAction::getAll();
					foreach ($actions as $ac)
					{
						echo "<input name=\"".$a['name']."[]\" type=\"checkbox\" value=\"".$ac->code()."\"";
						if (in_array($ac->code(),$keys))
							echo " checked=\"checked\"";
						echo " /> ".$ac."<br/>";
					}
					echo "";
					break;
				default:
					echo "<input type=\"text\" name=\"".$a['name']."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" value=\"".stripslashes($arr[$a['name']])."\" />";
			}
			echo "</td>\n</tr>\n";
			if (isset($a['line']) && $a['line']==1)
			{
				echo "<tr><td style=\"height:4px;background:#000\" colspan=\"2\"></td></tr>";
			}
			if (isset($a['columnend']) && $a['columnend']==1)
			{
				echo "</table></td><td style=\"vertical-align:top;\"><table style=\"width:100%;\">";
			}
		}
		echo "</table></td></tr>";
	}

	function admin_edit_dataset_query($db_fields)
	{
		$sql = "UPDATE ".DB_TABLE." SET ";
		$cnt = 1;
		foreach ($db_fields as $k=>$a)
		{
			$cntadd = 1;
			switch ($a['type'])
			{
				case "readonly":
					//Case readonly: do *nothing* with the field!
					//but instead do *not* add a comma
					$cntadd = 0;
				break;
				case "text":
					$sql .= "`".$a['name']."` = '".addslashes($_POST[$a['name']])."'";
				break;
				case "email":
					$sql .= "`".$a['name']."` = '".$_POST[$a['name']]."'";
				break;
				case "url":
					$sql .= "`".$a['name']."` = '".$_POST[$a['name']]."'";
				break;
				case "numeric":
					$sql .= "`".$a['name']."` = '".$_POST[$a['name']]."'";
				break;
				case "password":
					if ($_POST[$a['name']]!="")
						$sql .= "`".$a['name']."` = '".md5($_POST[$a['name']])."'";
					else
						$cntadd = 0;
				break;
				case "timestamp":
					$sql .= "`".$a['name']."` = UNIX_TIMESTAMP('".$_POST[$a['name']]."')";
				break;
				case "textarea":
					$sql .= "`".$a['name']."` = '".addslashes($_POST[$a['name']])."'";
				break;
				case "radio":
					$sql .= "`".$a['name']."` = '".$_POST[$a['name']]."'";
				break;
				case "checkbox":
					$sql .= "`".$a['name']."` = '".$_POST[$a['name']]."'";
				break;
				case "select":
					$sql .= "`".$a['name']."` = '".$_POST[$a['name']]."'";
				break;
				case "fleetaction":
					if (is_array($_POST[$a['name']]))
						$str = implode(",",$_POST[$a['name']]);
					else
						$str = "";
					$sql .= "`".$a['name']."` = '".$str."'";
				break;
				default:
					$sql .= "`".$a['name']."` = '".addslashes($_POST[$a['name']])."'";
				break;
			}
			if ($cntadd==1)
			{
				if ($cnt < sizeof($db_fields)) $sql .= ",";
			}
			$cnt++;
		}
		$sql.= " WHERE ".DB_TABLE_ID."='".$_POST[DB_TABLE_ID]."';";
		return $sql;
	}

	function admin_delete_dataset($db_fields,$arr)
	{
		foreach ($db_fields as $k=>$a)
		{
			switch ($a['type'])
			{
				case "text":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".$arr[$a['name']]."</td></tr>";
				break;
				case "email":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".$arr[$a['name']]."</td></tr>";
				break;
				case "url":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".$arr[$a['name']]."</td></tr>";
				break;
				case "numeric":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".$arr[$a['name']]."</td></tr>";
				break;
				case "password":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".$arr[$a['name']]."</td></tr>";
				break;
				case "timestamp":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".date(DATE_FORMAT,$arr[$a['name']])."</td></tr>";
				break;
				case "textarea":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".stripslashes(nl2br($arr[$a['name']]))."</td></tr>";
				break;
				case "radio":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">";
					foreach ($a['rcb_elem'] as $rk=>$rv)
					{
						if ($arr[$a['name']]==$rv) echo $rk;
					}
					echo "</td></tr>";
				break;
				case "checkbox":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">";
					$cb_temp_arr = array();
					foreach ($a['rcb_elem'] as $rk=>$rv)
					{
						if (in_array($rv,explode(";",$arr[$a['name']]))) array_push($cb_temp_arr,$rk);
					}
					for ($cbx=0;$cbx<count($cb_temp_arr);$cbx++)
					{
						echo $cb_temp_arr[$cbx];
						if ($cbx=count($cb_temp_arr)-1) echo "<br/>";
					}
					echo "</td></tr>";
				break;
				case "select":
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".$arr[$a['name']]."</td></tr>";
				break;
				default:
					echo "<tr><th class=\"tbltitle\" width=\"200\">".$a['text'].":</th>";
					echo "<td class=\"tbldata\" width=\"200\">".$arr[$a['name']]."</td></tr>";
				break;
			}
		}
	}



	function admin_show_overview($db_fields,$arr)
	{
		foreach ($db_fields as $k=>$a)
		{
			if ($a['show_overview']==1)
			{
				echo "<td class=\"tbldata\">";
				if (isset($a['link_in_overview']) && $a['link_in_overview']==1)
				{
					echo "<a href=\"?".URL_SEARCH_STRING."&amp;action=edit&amp;id=".$arr[DB_TABLE_ID]."\">";
				}

				switch ($a['type'])
				{
					case "readonly":
						echo "".$arr[$a['name']]."";
					break;
					case "text":
						echo "".$arr[$a['name']]."";
					break;
					case "email":
						echo "".$arr[$a['name']]."";
					break;
					case "url":
						echo "".$arr[$a['name']]."";
					break;
					case "numeric":
						echo "".$arr[$a['name']]."";
					break;
					case "password":
						echo "".$arr[$a['name']]."";
					break;
					case "timestamp":
						echo "".date(DATE_FORMAT,$arr[$a['name']])."";
					break;
					case "textarea":
						echo "";
						//if (strlen($arr[$a['name']])>$a['overview_length'])
						//	echo stripslashes(substr($arr[$a['name']],0,$a['overview_length']-2)."...");
						//else
							echo stripslashes($arr[$a['name']]);
						echo "";
					break;
					case "radio":
						echo "";
						foreach ($a['rcb_elem'] as $rk=>$rv)
						{
							if ($arr[$a['name']]==$rv) echo $rk;
						}
						echo "";
					break;
					case "checkbox":
						echo "";
						$cb_temp_arr = array();
						foreach ($a['rcb_elem'] as $rk=>$rv)
						{
							if (in_array($rv,explode(";",$arr[$a['name']]))) array_push($cb_temp_arr,$rk);
						}
						for ($cbx=0;$cbx<count($cb_temp_arr);$cbx++)
						{
							echo $cb_temp_arr[$cbx];
							if ($cbx=count($cb_temp_arr)-1) echo ";";
						}
						echo "";
					break;
					case "select":
						echo "";
						foreach ($a['select_elem'] as $sd=>$sv)
						{
							if ($sv==$arr[$a['name']])
							echo $sd;
						}
						echo "";
					break;
					default:
						echo "".$arr[$a['name']]."";
					break;
				}
				echo "</td>";
			}
		}
	}

	function admin_get_select_elements($table, $value_field, $text_field, $order,$additional_values=Null)
	{
		$r_array = array();
		if ($additional_values && count($additional_values)>0)
		{
			foreach ($additional_values as $val=>$key)
			{
				$r_array[$key]=$val;
			}
		}
		$res = dbquery("SELECT `$value_field`,`$text_field` FROM $table ORDER BY $order;");
		while ($arr = mysql_fetch_array($res))
		{
			$r_array[$arr[$text_field]]=$arr[$value_field];
		}
		return $r_array;
	}

	function admin_get_user_rank($user_rank_id)
	{
		$res = dbquery("SELECT rank_desc FROM admin_user_ranks WHERE rank_id='".$user_rank_id."';");
		while ($arr = mysql_fetch_array($res))
		$user_rank_name = $arr['rank_desc'];
		return $user_rank_name;
	}

?>
