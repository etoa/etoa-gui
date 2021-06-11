<?PHP

		if (isset($_GET['alliance_id'])) {
			$id = $_GET['alliance_id'];
		}
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
		}

		if (isset($_POST['info_save']) && $_POST['info_save']!="")
		{
			//  Bild löschen wenn nötig
			$img_sql = "";
			if (isset($_POST['alliance_img_del']))
			{
				$arr = $app['db']
					->executeQuery("SELECT alliance_img
						FROM alliances
						WHERE alliance_id = ?;",
						[$id])
					->fetchAssociative();
				if ($arr != null)
				{
					if (file_exists('../'.ALLIANCE_IMG_DIR."/".$arr['alliance_img']))
					{
						unlink('../'.ALLIANCE_IMG_DIR."/".$arr['alliance_img']);
					}
					$img_sql = ",alliance_img=''";
				}
			}

			// Daten speichern
			$app['db']
				->executeStatement("UPDATE
					alliances
				SET
					alliance_name = :name,
					alliance_tag = :tag,
					alliance_text = :text,
					alliance_application_template = :template,
					alliance_url = :url,
					alliance_founder_id = :founder
					".$img_sql."
				WHERE
					alliance_id = :id
			;", [
				'name' => $_POST['alliance_name'],
				'tag' => $_POST['alliance_tag'],
				'text' => $_POST['alliance_text'],
				'template' => $_POST['alliance_application_template'],
				'url' => $_POST['alliance_url'],
				'founder' => $_POST['alliance_founder_id'],
				'id' => $id
			]);

			$twig->addGlobal('successMessage', 'Allianzdaten aktualisiert!');
		}
		elseif (isset($_POST['member_save']) && $_POST['member_save']!="")
		{
			// Mitgliederänderungen
			if (isset($_POST['member_kick']) && count($_POST['member_kick']) > 0) {
				foreach ($_POST['member_kick'] as $k => $v) {
					$app['db']
						->executeStatement("UPDATE
								users
							SET
								user_alliance_id = 0,
								user_alliance_rank_id = 0
							WHERE
								user_id = ?;",
							[$k]);
				}
			}
			if (count($_POST['member_rank']) > 0) {
				foreach ($_POST['member_rank'] as $k => $v) {
					$app['db']
						->executeStatement("UPDATE
								users
							SET
								user_alliance_rank_id = ?
							WHERE
								user_id = ?;",
							[$v, $k]);
				}
			}
			// Ränge speichern
			if (isset($_POST['rank_del']) && count($_POST['rank_del']) > 0) {
				foreach ($_POST['rank_del'] as $k => $v)
				{
					$app['db']
						->executeStatement("DELETE FROM alliance_ranks
							WHERE rank_id = ?;",
							[$k]);
					$app['db']
						->executeStatement("DELETE FROM alliance_rankrights
							WHERE rr_rank_id = ?;",
							[$k]);
				}
			}
			if (isset($_POST['rank_name']) && count($_POST['rank_name']) > 0) {
				foreach ($_POST['rank_name'] as $k => $v) {
					$app['db']
						->executeStatement("UPDATE
								alliance_ranks
							SET
								rank_name = ?,
								rank_level = ?
							WHERE
								rank_id = ?;",
								[$v, $_POST['rank_level'][$k], $k]);
				}
			}
			$twig->addGlobal('successMessage', 'Mitglieder aktualisiert!');
		}
		elseif (isset($_POST['bnd_save']) && $_POST['bnd_save']!="")
		{
			// Bündnisse / Kriege speichern
			if (isset($_POST['alliance_bnd_del']) && count($_POST['alliance_bnd_del']) > 0) {
				foreach ($_POST['alliance_bnd_del'] as $k => $v) {
					$app['db']
						->executeStatement("DELETE FROM alliance_bnd
							WHERE alliance_bnd_id = ?;",
							[$k]);
				}
			}
			if (count($_POST['alliance_bnd_level']) > 0)
			{
				foreach ($_POST['alliance_bnd_level'] as $k => $v)
				{
					$app['db']
						->executeStatement("UPDATE
								alliance_bnd
							SET
								alliance_bnd_level = ?,
								alliance_bnd_name = ?
							WHERE
								alliance_bnd_id = ?;",
							[
								$_POST['alliance_bnd_level'][$k],
								$_POST['alliance_bnd_name'][$k],
								$k
							]);
				}
			}
			$twig->addGlobal('successMessage', 'Diplomatie aktualisiert!');
		}
		elseif (isset($_POST['res_save']) && $_POST['res_save']!="")
		{
			$app['db']
				->executeStatement("UPDATE
						alliances
					SET
						alliance_res_metal = :metal,
						alliance_res_crystal = :crystal,
						alliance_res_plastic = :plastic,
						alliance_res_fuel = :fuel,
						alliance_res_food = :food,
						alliance_res_metal = alliance_res_metal + :addmetal,
						alliance_res_crystal = alliance_res_crystal + :addcrystal,
						alliance_res_plastic = alliance_res_plastic + :addplastic,
						alliance_res_fuel = alliance_res_fuel + :addfuel,
						alliance_res_food = alliance_res_food + :addfood
					WHERE
						alliance_id = :id
					LIMIT 1;", [
						'metal' => nf_back($_POST['res_metal']),
						'crystal' => nf_back($_POST['res_crystal']),
						'plastic' => nf_back($_POST['res_plastic']),
						'fuel' => nf_back($_POST['res_fuel']),
						'food' => nf_back($_POST['res_food']),
						'addmetal' => nf_back($_POST['res_metal_add']),
						'addcrystal' => nf_back($_POST['res_crystal_add']),
						'addplastic' => nf_back($_POST['res_plastic_add']),
						'addfuel' => nf_back($_POST['res_fuel_add']),
						'addfood' => nf_back($_POST['res_food_add']),
						'id' => $id,
					]);
			$twig->addGlobal('successMessage', 'Ressourcen aktualisiert!');
		}
		elseif (isset($_POST['buildings']) && $_POST['buildings']!="")
		{
			$test = $app['db']
				->executeQuery("SELECT alliance_buildlist_id
				FROM alliance_buildlist
				WHERE alliance_buildlist_alliance_id = ?
					AND alliance_buildlist_building_id = (
						SELECT alliance_building_id
						FROM alliance_buildings
						WHERE alliance_building_name = ?
					)",
					[$id, $_POST['selected']])
				->fetchAllAssociative();
			if (count($test) > 0)
			{
				$app['db']
					->executeStatement("UPDATE alliance_buildlist
						SET alliance_buildlist_current_level = :level,
							alliance_buildlist_member_for = :amount
						WHERE alliance_buildlist_alliance_id = :id
							AND alliance_buildlist_building_id = (
								SELECT alliance_building_id
								FROM alliance_buildings
								WHERE alliance_building_name = :selected
							);",
							[
								'level' => $_POST['level'],
								'amount' => $_POST['amount'],
								'id' => $id,
								'selected' => $_POST['selected'],
							]);
				$twig->addGlobal('successMessage','Datensatz erfolgreich bearbeitet!');
			}
			else
			{
				$app['db']
					->executeStatement("INSERT into alliance_buildlist
						(
							alliance_buildlist_alliance_id,
							alliance_buildlist_building_id,
							alliance_buildlist_current_level,
							alliance_buildlist_build_start_time,
							alliance_buildlist_build_end_time,
							alliance_buildlist_cooldown,
							alliance_buildlist_member_for
						) VALUES (
							:id,
							(
								SELECT alliance_building_id
								FROM alliance_buildings
								WHERE alliance_building_name = :selected
							),
							:level,
							0,
							1,
							0,
							:amount
						)",
						[
							'id' => $id,
							'selected' => $_POST['selected'],
							'level' => $_POST['level'],
							'amount' => $_POST['amount'],
						]);
				$twig->addGlobal('successMessage', 'Datensatz erfolgreich eingefügt!');
			}
		}
		elseif (isset($_POST['techs']) && $_POST['techs']!="")
		{
			$test = $app['db']
				->executeQuery("SELECT alliance_techlist_id
				FROM alliance_techlist
				WHERE alliance_techlist_alliance_id = ?
					AND alliance_techlist_tech_id = (
						SELECT alliance_tech_id
						FROM alliance_technologies
						WHERE alliance_tech_name = ?
					);",
					[$id, $_POST['selected_tech']])
				->fetchAllAssociative();
			if (count($test) > 0)
			{
				$app['db']
					->executeStatement("UPDATE alliance_techlist
						SET alliance_techlist_current_level = :level,
							alliance_techlist_member_for = :amount
						WHERE alliance_techlist_alliance_id = :id
							AND alliance_techlist_tech_id = (
								select alliance_tech_id
								from alliance_technologies
								where alliance_tech_name = :selected
						);",
					[
						'level' => $_POST['tech_level'],
						'amount' => $_POST['tech_amount'],
						'id' => $id,
						'selected' => $_POST['selected_tech'],
					]);
				$twig->addGlobal('successMessage','Datensatz erfolgreich bearbeitet!');
			}
			else
			{
				$app['db']
					->executeStatement("INSERT INTO alliance_techlist
						(
							alliance_techlist_alliance_id,
							alliance_techlist_tech_id,
							alliance_techlist_current_level,
							alliance_techlist_build_start_time,
							alliance_techlist_build_end_time,
							alliance_techlist_member_for
						) VALUES (
							:id,
							(
								SELECT alliance_tech_id
								FROM alliance_technologies
								WHERE alliance_tech_name = :selected),
							:level,
							0,
							1,
							:amount
						);",
						[
							'id' => $id,
							'selected' => $_POST['selected_tech'],
							'level' => $_POST['tech_level'],
							'amount' => $_POST['tech_amount'],
						]);
				$twig->addGlobal('successMessage', 'Datensatz erfolgreich eingefügt!');
			}
		}

		$arr = $app['db']
			->executeQuery("SELECT *
				FROM alliances
				WHERE alliance_id = ?;",
				[$id])
			->fetchAssociative();

		$twig->addGlobal('subtitle', "Allianz bearbeiten: [".$arr['alliance_tag']."] ".$arr['alliance_name']);

		$udata = $app['db']
			->executeQuery("SELECT
					user_id,
					user_nick,
					user_points,
					user_alliance_rank_id
				FROM
					users
				WHERE
					user_alliance_id = ?
				ORDER BY
					user_points DESC,
					user_nick;",
				[$id])
			->fetchAllAssociative();

		$members = array();
		if (count($udata) > 0)
		{
			foreach ($udata as $uarr)
			{
				$members[$uarr['user_id']] = $uarr;
			}
		}

		$rdata = $app['db']
			->executeQuery("SELECT
					rank_id,
					rank_level,
					rank_name
				FROM
					alliance_ranks
				WHERE
					rank_alliance_id=".$id."
				ORDER BY
					rank_level DESC;", [])
			->fetchAllAssociative();
		$ranks = array();
		if (count($rdata) > 0)
		{
			foreach ($rdata as $rarr)
			{
				$ranks[$rarr['rank_id']] = $rarr;
			}
		}

		echo "<form action=\"?page=$page&amp;sub=edit&amp;id=".$id."\" method=\"post\">";

		echo '<div class="tabs">
		<ul>
			<li><a href="#tabs-1">Info</a></li>
			<li><a href="#tabs-2">Mitglieder</a></li>
			<li><a href="#tabs-3">Diplomatie</a></li>
			<li><a href="#tabs-4">Geschichte</a></li>
			<li><a href="#tabs-5">Rohstoffe</a></li>
			<li><a href="#tabs-6">Einzahlungen</a></li>
			<li><a href="#tabs-7">Gebäude</a></li>
			<li><a href="#tabs-8">Technologien</a></li>
		</ul>
		<div id="tabs-1">';

			/**
			* Info
			*/
			tableStart();
			echo "<tr><th>ID</th><td>".$arr['alliance_id']."</td></tr>";
			echo "<tr><th>[Tag] Name</th><td>
					[<input type=\"text\" name=\"alliance_tag\" value=\"".$arr['alliance_tag']."\" size=\"6\" maxlength=\"6\" />]
					<input type=\"text\" name=\"alliance_name\" value=\"".$arr['alliance_name']."\" size=\"30\" maxlength=\"25\" />
				</td></tr>";
			echo "<tr><th>Gr&uuml;nder</th><td><select name=\"alliance_founder_id\">";
			echo "<option value=\"0\">(niemand)</option>";
			foreach ($members as $uid => $uarr)
			{
				echo "<option value=\"$uid\"";
				if ($arr['alliance_founder_id']==$uarr['user_id'])
					echo " selected=\"selected\"";
				echo ">".$uarr['user_nick']."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><th>Text</th><td><textarea cols=\"45\" rows=\"10\" name=\"alliance_text\">".stripslashes($arr['alliance_text'])."</textarea></td></tr>";
			echo "<tr><th>Gr&uuml;ndung</th><td>".date("Y-m-d H:i:s",$arr['alliance_foundation_date'])."</td></tr>";
			echo "<tr><th>Website</th><td><input type=\"text\" name=\"alliance_url\" value=\"".$arr['alliance_url']."\" size=\"40\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th>Bewerbungsvorlage</th><td><textarea cols=\"45\" rows=\"10\" name=\"alliance_application_template\">".stripslashes($arr['alliance_application_template'])."</textarea></td></tr>";
			echo "<tr><th>Bild</th><td>";
			if ($arr['alliance_img']!="")
			{
	        	echo '<img src="'.ALLIANCE_IMG_DIR.'/'.$arr['alliance_img'].'" alt="Profil" /><br/>';
	        	echo "<input type=\"checkbox\" value=\"1\" name=\"alliance_img_del\"> Bild l&ouml;schen<br/>";
			}
			else
			{
	      		echo "Keines";
			}
			echo "</td></tr>";
			echo "</table>";
			echo "<p><input type=\"submit\" name=\"info_save\" value=\"&Uuml;bernehmen\" /></p>";

			echo '</div><div id="tabs-2">';

			/*
			* Mitglieder
			**/

			tableStart();
			echo "<tr>
					<th>Mitglieder</th>
				<td>";
			if (count($members)>0)
			{
				echo "<table class=\"tb\">
					<tr>
						<th>Name</th>
						<th>Punkte</th>
						<th>Rang</th>
						<th>Mitgliedschaft beenden</th></tr>";
					foreach ($members as $uid => $uarr)
					{
						echo "<tr><td id=\"uifo".$uarr['user_id']."\" style=\"display:none;\"><a href=\"?page=user&amp;sub=edit&amp;id=".$uarr['user_id']."\">Daten</a><br/>
						".popupLink("sendmessage","Nachricht senden","","id=".$uarr['user_id'])."</td>
						<td><a href=\"?page=user&amp;sub=edit&amp;id=".$uarr['user_id']."\" ".cTT($uarr['user_nick'],"uifo".$uarr['user_id']."").">".$uarr['user_nick']."</a></td>
						<td>".nf($uarr['user_points'])." Punkte</td>
						<td><select name=\"member_rank[$uid]\"><option value=\"0\">-</option>";
						foreach ($ranks as $k=>$v)
						{
							echo "<option value=\"$k\"";
							if ($uarr['user_alliance_rank_id']==$k)
								echo " selected=\"selected\"";
							echo ">".$v['rank_name']."</option>";
						}
						echo "</select></td>";
						echo "<td><input type=\"checkbox\" name=\"member_kick[".$uid."]\" value=\"1\" /></td></tr>";
					}
					echo "</table>";
				}
				else
					echo "<b>KEINE MITGLIEDER!</b>";
				echo "</td></tr>";
				echo "<tr><th>R&auml;nge</th><td>";

				if (count($ranks)>0)
				{
					echo "<table class=\"tb\">";
					echo "<tr><th>Name</th><th>Level</th><th>L&ouml;schen</th></tr>";
					foreach($ranks as $rid => $rarr)
					{
						echo "<tr><td><input type=\"text\" size=\"35\" name=\"rank_name[".$rarr['rank_id']."]\" value=\"".$rarr['rank_name']."\" /></td>";
						echo "<td><select name=\"rank_level[".$rarr['rank_id']."]\">";
						for($x=0;$x<=9;$x++)
						{
							echo "<option value=\"$x\"";
							if ($rarr['rank_level']==$x) echo " selected=\"selected\"";
							echo ">$x</option>";
						}
						echo "</select></td>";
						echo "<td><input type=\"checkbox\" name=\"rank_del[".$rarr['rank_id']."]\" value=\"1\" /></td></tr>";
					}
					echo "</table>";
				}
				else
					echo "<b>Keine R&auml;nge vorhanden!</b>";
				echo "</td></tr>";
				tableEnd();
				echo "<p><input type=\"submit\" name=\"member_save\" value=\"&Uuml;bernehmen\" /></p>";

				echo '</div><div id="tabs-3">';


				/*
				* Krieg/Bündnisse
				*/

				$bdata = $app['db']
					->executeQuery("SELECT
							alliance_bnd_id,
							alliance_bnd_alliance_id1 as a1id,
							alliance_bnd_alliance_id2 as a2id,
							a1.alliance_name as a1name,
							a2.alliance_name as a2name,
							alliance_bnd_level as lvl,
							alliance_bnd_name as name,
							alliance_bnd_date as date
						FROM
							alliance_bnd
						LEFT JOIN
							alliances a1 on alliance_bnd_alliance_id1 = a1.alliance_id
						LEFT JOIN
							alliances a2 on alliance_bnd_alliance_id2 = a2.alliance_id
						WHERE
							alliance_bnd_alliance_id1 = :id
							OR alliance_bnd_alliance_id2 = :id
						ORDER BY
							alliance_bnd_level DESC,
							alliance_bnd_date DESC;",
						[
							'id' => $arr['alliance_id'],
						])
					->fetchAllAssociative();
				if (count($bdata) > 0)
				{
					echo "<table class=\"tb\">";
					echo "<tr>
					<th>Allianz</th>
					<th>Bezeichnung</th>
					<th>Status / Datum</th>
					<th>L&ouml;schen</th></tr>";
					foreach ($bdata as $barr)
					{
						$opId = ($id == $barr['a2id']) ? $barr['a1id'] : $barr['a2id'];
						$opName = ($id == $barr['a2id']) ? $barr['a1name'] : $barr['a2name'];
						echo "<tr>
							<td><a href=\"?page=alliances&amp;action=edit&amp;id=".$opId."\">".$opName."</a></td>
							<td><input type=\"text\" value=\"".$barr['name']."\" name=\"alliance_bnd_name[".$barr['alliance_bnd_id']."]\" /></td>";
						echo "<td>
						<select name=\"alliance_bnd_level[".$barr['alliance_bnd_id']."]\">";
						echo "<option value=\"0\">Bündnisanfrage</option>";
						echo "<option value=\"2\"";
						if ($barr['lvl']==2) echo " selected=\"selected\"";
						echo ">B&uuml;ndnis</option>";
						echo "<option value=\"3\"";
						if ($barr['lvl']==3) echo " selected=\"selected\"";
						echo ">Krieg</option>";
						echo "<option value=\"3\"";
						if ($barr['lvl']==4) echo " selected=\"selected\"";
						echo ">Frieden</option>";
						echo "</select>";
						echo " &nbsp; ".df($barr['date'])."</td>";
						echo "<td valign=\"top\"><input type=\"checkbox\" name=\"alliance_bnd_del[".$barr['alliance_bnd_id']."]\" value=\"1\" /></td></tr>";
					}
					echo "</table>";
					echo "<p><input type=\"submit\" name=\"bnd_save\" value=\"&Uuml;bernehmen\" /></p>";
				}
				else {
					echo "<p><b>Keine B&uuml;ndnisse/Kriege vorhanden!</b></p>";
				}

				echo '</div><div id="tabs-4">';

			/**
			* Geschichte
			*/
			tableStart();
			echo "<tr>
					<th style=\"width:120px;\">Datum / Zeit</th>
					<th>Ereignis</th></tr>";
			$hdata = $app['db']
				->executeQuery("SELECT
						*
					FROM
						alliance_history
					WHERE
						history_alliance_id = ?
					ORDER BY
						history_timestamp
					DESC;",
					[$arr['alliance_id']])
				->fetchAllAssociative();
			if (count($hdata) > 0)
			{
				foreach ($hdata as $harr)
				{
					echo "<tr><td>".date("d.m.Y H:i",$harr['history_timestamp'])."</td><td class=\"tbldata\">".text2html($harr['history_text'])."</td></tr>";
				}
			}
			else
			{
				echo "<tr><td colspan=\"3\" class=\"tbldata\"><i>Keine Daten vorhanden!</i></td></tr>";
			}
			tableEnd();

			echo '</div><div id="tabs-5">';

			/**
			* Rohstoffe
			*/
			echo '<table class="tb">';
			echo "<tr>
					<th class=\"resmetalcolor\">Titan</th>
					<td>
						<input type=\"text\" name=\"res_metal\" id=\"res_metal\" value=\"".nf($arr['alliance_res_metal'])."\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
					+/-: <input type=\"text\" name=\"res_metal_add\" id=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
			echo "<th class=\"rescrystalcolor\">Silizium</th>
					<td><input type=\"text\" name=\"res_crystal\" id=\"res_crystal\" value=\"".nf($arr['alliance_res_crystal'])."\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
					+/-: <input type=\"text\" name=\"res_crystal_add\" id=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
			echo "<tr><th class=\"resplasticcolor\">PVC</th>
					<td><input type=\"text\" name=\"res_plastic\" id=\"res_plastic\" value=\"".nf($arr['alliance_res_plastic'])."\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
					+/-: <input type=\"text\" name=\"res_plastic_add\" id=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
			echo "<th class=\"resfuelcolor\">Tritium</th>
					<td><input type=\"text\" name=\"res_fuel\" id=\"res_fuel\" value=\"".nf($arr['alliance_res_fuel'])."\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
					+/-: <input type=\"text\" name=\"res_fuel_add\" id=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
			echo "<tr><th class=\"resfoodcolor\">Nahrung</th>
					<td><input type=\"text\" name=\"res_food\" id=\"res_food\" value=\"".nf($arr['alliance_res_food'])."\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
					+/-: <input type=\"text\" name=\"res_food_add\" id=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td><td colspan=\"2\">";
			tableEnd();
			echo "<p><input type=\"submit\" name=\"res_save\" value=\"Übernehmen\" /></p>";

			echo '</div><div id="tabs-6">';

			/**
			* Einzahlungen
			*/

			echo "<form id=\"filterForm\">";
			tableStart("Filter");
			echo "<tr>
  					<th>Ausgabe:</th>
  					<td>
  						<input type=\"radio\" name=\"output\" id=\"output\" value=\"0\" checked=\"checked\"/> Einzeln / <input type=\"radio\" name=\"output\" id=\"output\" value=\"1\"/> Summiert
  					</td>
  				</tr><tr>
  					<th>Einzahlungen:</th>
  					<td>
		  				<select id=\"limit\" name=\"limit\">
							<option value=\"0\" checked=\"checked\">alle</option>
							<option value=\"1\">die letzte</option>
							<option value=\"5\">die letzten 5</option>
							<option value=\"20\">die letzten 20</option>
						</select>
					</td>
				</tr><tr>
  					<th>Von User:</th>
  					<td>
  						<select id=\"user_spends\" name=\"user_spends\">
							<option value=\"0\">alle</option>";
					  	// Allianzuser
							foreach($members as $mid => $data)
							{
					  		echo "<option value=\"".$mid."\">".$data['user_nick']."</option>";
					  	}
  			echo 		"</select>
  					</td>
	  			</tr><tr>";
			 tableEnd();
			 echo "<p><input type=\"button\" onclick=\"xajax_showSpend(".$arr['alliance_id'].",xajax.getFormValues('filterForm'))\" value=\"Anzeigen\"\"/></p>";
			 echo "</form>";

			 echo "<div id=\"spends\">&nbsp;</div>";

			echo '</div><div id="tabs-7">';

			/**
			* Gebäude
			*/
			$buildListData = $app['db']
				->executeQuery("SELECT
						alliance_buildlist.*,
						alliance_buildings.alliance_building_name
					FROM
						alliance_buildlist
					INNER JOIN
						alliance_buildings
					ON
						alliance_buildings.alliance_building_id = alliance_buildlist.alliance_buildlist_building_id
						AND	alliance_buildlist_alliance_id = ?;",
					[$id])
				->fetchAllAssociative();

			$buildings = $app['db']
				->executeQuery("SELECT
						alliance_building_id,
						alliance_building_name
					FROM
						alliance_buildings;")
				->fetchAllAssociative();

			tableStart();
			echo "<tr>
					<th>Gebäude</th><th>Stufe</th><th>Useranzahl</th><th>Status</th>
				</tr>";
			if (count($buildListData) > 0)
			{
				foreach ($buildListData as $arr)
				{
					echo "<tr><td>".$arr['alliance_building_name']."</td><td>".$arr['alliance_buildlist_current_level']."</td><td>".$arr['alliance_buildlist_member_for']."</td><td>";
					if ($arr['alliance_buildlist_build_end_time']>time()) echo "Bauen";
					elseif ($arr['alliance_buildlist_build_end_time']>0) echo "Bau abgeschlossen";
					else echo "Untätig";
					echo "</td>";
					echo "</tr>";
				}
			}
			else {
				echo "<tr><td colspan=\"4\">Keine Gebäude vorhanden!</td></tr>";
			}

			tableEnd();

		    echo '<br><h2>Gebäude hinzufügen</h2>';

			tableStart();

            echo "<tr>
					<th>Gebäude</th><th>Stufe</th><th>Useranzahl</th>
				</tr>";
			echo'<tr><td>';

            if (count($buildings) > 0)
			{
				echo'<select name="selected">';
				foreach ($buildings as $arr)
				{
					echo "<option>".$arr['alliance_building_name']."</option>";
				}
				echo"</select>";
			}

			echo '</td><td><input type=number value=1 name="level"></td><td><input type=number value=1 name="amount"></td></tr>';

			tableEnd();

			echo'<br><input type="submit" name="buildings">';

			echo '</div><div id="tabs-8">';

			/**
			* Technologien
			*/
			$techlistData = $app['db']
				->executeQuery("SELECT
						alliance_techlist.*,
						alliance_technologies.alliance_tech_name
					FROM
						alliance_techlist
					INNER JOIN
						alliance_technologies
					ON
						alliance_technologies.alliance_tech_id = alliance_techlist.alliance_techlist_tech_id
						AND	alliance_techlist_alliance_id = ?;",
					[$id])
				->fetchAllAssociative();

			$techs = $app['db']
				->executeQuery("SELECT
						alliance_tech_id,
						alliance_tech_name
					FROM
						alliance_technologies;")
				->fetchAllAssociative();

			tableStart();
			echo "<tr>
					<th>Technologie</th><th>Stufe</th><th>Useranzahl</th><th>Status</th>
				</tr>";
			if (count($techlistData) > 0)
			{
				foreach ($techlistData as $arr)
				{
					echo "<tr><td>".$arr['alliance_tech_name']."</td><td>".$arr['alliance_techlist_current_level']."</td><td>".$arr['alliance_techlist_member_for']."</td><td>";
					if ($arr['alliance_techlist_build_end_time']>time()) echo "Forschen";
					elseif ($arr['alliance_techlist_build_end_time']>0) echo "Forschen abgeschlossen";
					else echo "Untätig";
					echo "</td>";
					echo "</tr>";
				}
			}
			else {
				echo "<tr><td colspan=\"4\">Keine Technologien vorhanden!</td></tr>";
			}
			tableEnd();

			echo '<br><h2>Technologien hinzufügen</h2>';

			tableStart();

            echo "<tr>
					<th>Technologie</th><th>Stufe</th><th>Useranzahl</th>
				</tr>";
			echo'<tr><td>';

            if (count($techs) > 0)
			{
				echo'<select name="selected_tech">';
				foreach ($techs as $arr)
				{
					echo "<option>".$arr['alliance_tech_name']."</option>";
				}
				echo"</select>";
			}

			echo '</td><td><input type=number value=1 name="tech_level"></td><td><input type=number value=1 name="tech_amount"></td></tr>';

			tableEnd();

			echo'<br><input type="submit" name="techs">';

			echo '
				</div>
			</div>';


?>
