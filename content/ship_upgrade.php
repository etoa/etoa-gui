<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: ship_upgrade.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Manages epic ships (leveling, additions)
	*
	* @package etoa_gameserver
	* @author Lamborghini <lamborghini@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	//
	// Upgrade menu eines spezial Schiffes
	//
	if (isset($_GET['id']) && $_GET['id']!="")
	{
		echo "<h1>Schiffs Upgrade Menu</h1>";

    //
    // Upgrade speichern
    //
    if(isset($_POST['submit_upgrade']) && $_POST['submit_upgrade']!="" && $_POST['id']!="" && $_POST['upgrade']!="" && checker_verify())
    {
        dbquery("
        UPDATE
        	".$db_table['shiplist']."
        SET
        	shiplist_special_ship_level=shiplist_special_ship_level+1,
        	shiplist_special_ship_bonus_".$_POST['upgrade']."=shiplist_special_ship_bonus_".$_POST['upgrade']."+1
        WHERE
        	shiplist_ship_id='".$_POST['id']."'
        	AND shiplist_user_id='".$cu->id()."';");

        echo "Upgrade erfolgreich duchgeführt!<br>";
    }


		//Liest alle notwendigen Daten für das Upgradende Schiff aus der DB heraus
    $res = dbquery("
    SELECT
        ships.ship_name,
        ships.special_ship_max_level,
        ships.special_ship_need_exp,
        ships.special_ship_exp_factor,
        ships.special_ship_bonus_weapon,
        ships.special_ship_bonus_structure,
        ships.special_ship_bonus_shield,
        ships.special_ship_bonus_heal,
        ships.special_ship_bonus_capacity,
        ships.special_ship_bonus_speed,
        ships.special_ship_bonus_pilots,
        ships.special_ship_bonus_tarn,
        ships.special_ship_bonus_antrax,
        ships.special_ship_bonus_forsteal,
        ships.special_ship_bonus_build_destroy,
        ships.special_ship_bonus_antrax_food,
        ships.special_ship_bonus_deactivade,

        shiplist.shiplist_special_ship_level,
        shiplist.shiplist_special_ship_exp,
        shiplist.shiplist_special_ship_bonus_structure,
        shiplist.shiplist_special_ship_bonus_shield,
        shiplist.shiplist_special_ship_bonus_weapon,
        shiplist.shiplist_special_ship_bonus_heal,
        shiplist.shiplist_special_ship_bonus_capacity,
        shiplist.shiplist_special_ship_bonus_speed,
        shiplist.shiplist_special_ship_bonus_pilots,
        shiplist.shiplist_special_ship_bonus_tarn,
        shiplist.shiplist_special_ship_bonus_antrax,
        shiplist.shiplist_special_ship_bonus_forsteal,
        shiplist.shiplist_special_ship_bonus_build_destroy,
        shiplist.shiplist_special_ship_bonus_antrax_food,
        shiplist.shiplist_special_ship_bonus_deactivade
    FROM
        	".$db_table['ships']." AS ships
        INNER JOIN
        	".$db_table['shiplist']." AS shiplist
        ON ships.ship_id=shiplist.shiplist_ship_id
        AND ships.special_ship='1'
        AND shiplist.shiplist_user_id='".$cu->id()."'
        AND shiplist.shiplist_ship_id='".intval($_GET['id'])."'  
        AND shiplist.shiplist_count>'0'
    ;");

		if(mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);

			$init_level = $arr['shiplist_special_ship_level'];
			$init_exp = $arr['shiplist_special_ship_exp'];
			$exp = $init_exp;

      $rest_exp = $exp;


      //Errechnet das Level aus den momentanen erfahrungen (exp)
      //Diese Schleife nicht löschen, die hat schon ihren Sinn, auch wenn nichts in der Klammer ist :P
      for ($level=0;$exp>=ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'],$level));$level++)
      {}

      //Errechnet die benötigten EXP für das nächste Level
      $exp_for_next_level = ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'],$level));


			echo "<form action=\"?page=$page&amp;id=".intval($_GET['id'])."\" method=\"post\">";
			checker_init();

			infobox_start($arr['ship_name'],1,1);
			echo "
			     <tr>
			     	<td class=\"tbltitle\" width=\"25%\">Level</td>";

						if($arr['special_ship_max_level']<=$init_level && $arr['special_ship_max_level']!=0)
						{
							echo "<td class=\"tbldata\" width=\"10%\">".$init_level." (max.)</td>";
						}
						else
						{
	            if($level-$init_level<=0)
	            {
	              echo "<td class=\"tbldata\" width=\"10%\">$init_level (+".($level-$init_level).")</td>";
	            }
	            else
	            {
	              echo "<td class=\"tbldata3\" width=\"10%\">$init_level (+".($level-$init_level).")</td>";
	            }
	          }
			echo "
			     	<td class=\"tbldata\"  width=\"65%\">Level des Schiffes</td>
			     </tr>
			     <tr>
			     	<td class=\"tbltitle\" width=\"25%\">Erfahrung</td>
			     	<td class=\"tbldata\" width=\"10%\">".nf($arr['shiplist_special_ship_exp'])."</td>
			     	<td class=\"tbldata\" width=\"65%\">Erfahrung des Schiffes</td>
			     </tr>
			     <tr>
			     	<td class=\"tbltitle\" width=\"25%\">Ben. Erfahrung</td>";

			     	if($arr['special_ship_max_level']<=$init_level && $arr['special_ship_max_level']!=0)
			     	{
			     		echo "<td class=\"tbldata\" width=\"10%\"> - </td>";
			     	}
			     	else
			     	{
			     		echo "<td class=\"tbldata\" width=\"10%\">".nf($exp_for_next_level)."</td>";
			     	}

			     	echo "<td class=\"tbldata\" width=\"65%\">Benötigte Erfahrung bis zum nächsten LevelUp</td>
			     </tr>

			     ";
			infobox_end(1);

			//Zeigt alle Bonis die das Schiff upgraden kann
			infobox_start("Bonis",1,1);
			echo "
			     <tr>
			     	<td class=\"tbltitle\" width=\"25%\">Skill</td>
			     	<td class=\"tbltitle\" width=\"10%\">Bonus</td>
			     	<td class=\"tbltitle\" width=\"63%\">Info</td>
			     	<td class=\"tbltitle\" width=\"2%\">LvL</td>
			     </tr>
			     ";


			// Waffentechnik Bonus
			if($arr['special_ship_bonus_weapon']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Waffen (".$arr['shiplist_special_ship_bonus_weapon'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_weapon']*$arr['special_ship_bonus_weapon']*100,1))."%</td>
				     	<td class=\"tbldata\">Waffenbonus im Kampf (".($arr['special_ship_bonus_weapon']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"weapon\" border=\"0\"></td>
				     </tr>";
			}
			// Struktur Bonus
			if($arr['special_ship_bonus_structure']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Panzerung (".$arr['shiplist_special_ship_bonus_structure'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_structure']*$arr['special_ship_bonus_structure']*100,1))."%</td>
				     	<td class=\"tbldata\">Struktur im Kampf (".($arr['special_ship_bonus_structure']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"structure\" border=\"0\"></td>
				     </tr>";
			}
			// Schild Bonus
			if($arr['special_ship_bonus_shield']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Schild (".$arr['shiplist_special_ship_bonus_shield'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_shield']*$arr['special_ship_bonus_shield']*100,1))."%</td>
				     	<td class=\"tbldata\">Schildbonus im Kampf (".($arr['special_ship_bonus_shield']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"shield\" border=\"0\"></td>
				     </tr>";
			}
			// kapazitäts Bonus
			if($arr['special_ship_bonus_capacity']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Kapazität (".$arr['shiplist_special_ship_bonus_capacity'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_capacity']*$arr['special_ship_bonus_capacity']*100,1))."%</td>
				     	<td class=\"tbldata\">Erhöht die Kapazität der ganzen Flotte (".($arr['special_ship_bonus_capacity']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"capacity\" border=\"0\"></td>
				     </tr>";
			}
			// Speed Bonus
			if($arr['special_ship_bonus_speed']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Speed (".$arr['shiplist_special_ship_bonus_speed'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_speed']*$arr['special_ship_bonus_speed']*100,1))."%</td>
				     	<td class=\"tbldata\">Erhöht den Speed der ganzen Flotte (".($arr['special_ship_bonus_speed']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"speed\" border=\"0\"></td>
				     </tr>";
			}
			// Tarn Bonus
			if($arr['special_ship_bonus_tarn']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Tarnung (".$arr['shiplist_special_ship_bonus_tarn'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_tarn']*$arr['special_ship_bonus_tarn']*100,1))."%</td>
				     	<td class=\"tbldata\">Ermöglicht eine absolute Tarnung der Flotte (".($arr['special_ship_bonus_tarn']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"tarn\" border=\"0\"></td>
				     </tr>";
			}
			// Piloten Bonus
			if($arr['special_ship_bonus_pilots']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Besatzung (".$arr['shiplist_special_ship_bonus_pilots'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_pilots']*$arr['special_ship_bonus_pilots']*100,1))."%</td>
				     	<td class=\"tbldata\">Verringert die benötigten Piloten der Flotte (".($arr['special_ship_bonus_pilots']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"pilots\" border=\"0\"></td>
				     </tr>";
			}
			// Heal Bonus
			if($arr['special_ship_bonus_heal']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Heilung (".$arr['shiplist_special_ship_bonus_heal'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_heal']*$arr['special_ship_bonus_heal']*100,1))."%</td>
				     	<td class=\"tbldata\">Heilbonus im Kampf (".($arr['special_ship_bonus_heal']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
				     </tr>";
			}
			// Giftgas Bonus
			if($arr['special_ship_bonus_antrax']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Giftgas (".$arr['shiplist_special_ship_bonus_antrax'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_antrax']*$arr['special_ship_bonus_antrax']*100,1))."%</td>
				     	<td class=\"tbldata\">Erhöht Giftgaseffekt (".($arr['special_ship_bonus_antrax']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
				     </tr>";
			}
			// Techklau Bonus
			if($arr['special_ship_bonus_forsteal']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Spionageangriff (".$arr['shiplist_special_ship_bonus_forsteal'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_forsteal']*$arr['special_ship_bonus_forsteal']*100,1))."%</td>
				     	<td class=\"tbldata\">Erhöht die Erfolgschancen beim Spionageangriff (".($arr['special_ship_bonus_forsteal']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"forsteal\" border=\"0\"></td>
				     </tr>";
			}
			// Bombardieren Bonus
			if($arr['special_ship_bonus_build_destroy']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Bombardieren (".$arr['shiplist_special_ship_bonus_build_destroy'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_build_destroy']*$arr['special_ship_bonus_build_destroy']*100,1))."%</td>
				     	<td class=\"tbldata\">Erhöht Bombardierungschancen (".($arr['special_ship_bonus_build_destroy']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"build_destroy\" border=\"0\"></td>
				     </tr>";
			}
			// Antrax Bonus
			if($arr['special_ship_bonus_antrax_food']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Antrax (".$arr['shiplist_special_ship_bonus_antrax_food'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_antrax_food']*$arr['special_ship_bonus_antrax_food']*100,1))."%</td>
				     	<td class=\"tbldata\">Erhöht Antraxeffekt (".($arr['special_ship_bonus_antrax_food']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"antrax_food\" border=\"0\"></td>
				     </tr>";
			}
			// Deaktivieren Bonus
			if($arr['special_ship_bonus_deactivade']>0)
			{
				echo "<tr>
				     	<td class=\"tbltitle\">Deaktivieren (".$arr['shiplist_special_ship_bonus_deactivade'].")</td>
				     	<td class=\"tbldata\">".(round($arr['shiplist_special_ship_bonus_deactivade']*$arr['special_ship_bonus_deactivade']*100,1))."%</td>
				     	<td class=\"tbldata\">Erhöht Deaktivierungschancen (".($arr['special_ship_bonus_deactivade']*100)."% pro Level)</td>
				     	<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"deactivade\" border=\"0\"></td>
				     </tr>";
			}




			infobox_end(1);

			//Level Button anzeigen, wenn genügend EXP vorhaden
			if($level-$init_level>0 && ($arr['special_ship_max_level']>$init_level || $arr['special_ship_max_level']==0))
			{
				echo "<input type=\"hidden\" name=\"id\" value=\"".intval($_GET['id'])."\">";
				echo "<input type=\"submit\" class=\"button\" name=\"submit_upgrade\" value=\"Gewähltes Upgrade duchführen\" /><br><br>";
			}
			echo "</form>";


			echo "<input type=\"button\" value=\"Zurück zur Übersicht\" onclick=\"document.location='?page=ship_upgrade'\" />";


		}
		else
		{
			echo "Du musst dieses Schiff zuerst bauen, oder auf den Planeten wechseln, auf dem sich das Schiff befindet!<br>";
		}


	}







	//
	// Spezial Schiffe Auflisten
	//
	else
	{
			echo "<h1>Spezialschiffe</h1>";

			//Listet alle spezial Schiffe auf die der user besitzt
      $res = dbquery("
      SELECT
        ships.ship_id,
        ships.ship_name,
        ships.ship_longcomment,
        ships.ship_race_id,
        ships.special_ship_max_level,
        ships.special_ship_need_exp,
        ships.special_ship_exp_factor,

        shiplist.shiplist_special_ship_level,
        shiplist.shiplist_special_ship_exp
      FROM
        ".$db_table['ships']." AS ships
        INNER JOIN
        ".$db_table['shiplist']." AS shiplist
        ON ships.ship_id=shiplist.shiplist_ship_id
        AND shiplist.shiplist_user_id='".$cu->id()."'
        AND ships.special_ship='1'
        AND shiplist.shiplist_count>'0'
      ORDER BY
      	ships.ship_name;");

			if(mysql_num_rows($res)>0)
			{
        while($arr = mysql_fetch_array($res))
        {
            $init_level = $arr['shiplist_special_ship_level'];
            $init_exp = $arr['shiplist_special_ship_exp'];
            $exp = $init_exp;
            $rest_exp = $exp;

			      //Errechnet das Level aus den momentanen erfahrungen (exp)
			      //Diese Schleife nicht löschen, die hat schon ihren Sinn, auch wenn nichts in der Klammer ist :P
			      for ($level=0;$exp>=ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'],$level));$level++)
			      {}

            //Errechnet die benötigten EXP
            $exp_for_next_level = ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'],$level));


            infobox_start($arr['ship_name'],1,1);

    				echo "
    					<tr>
    						<td class=\"tbltitle\" style=\"width:220px;\">
    							<a href=\"?page=ship_upgrade&amp;id=".$arr['ship_id']."\"><img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" alt=\"Klicke hier um ins Upgrade Menu zu gelangen\" title=\"Klicke hier um ins Upgrade Menu zu gelangen\" border=\"0\"/></a></td>
    						<td class=\"tbldata\" colspan=\"3\">".text2html($arr['ship_longcomment'])."</td>
    					</tr>";
                    echo "
                         <tr>
                            <td class=\"tbltitle\">Level</td>";

                            if($arr['special_ship_max_level']<=$init_level && $arr['special_ship_max_level']!=0)
                            {
                                 echo "<td class=\"tbldata\">$init_level (max.)</td>";
                            }
                            else
                            {
                              if($level-$init_level<=0)
                              {
                                echo "<td class=\"tbldata\">$init_level (+".($level-$init_level).")</td>";
                              }
                              else
                              {
                                echo "<td class=\"tbldata3\">$init_level (+".($level-$init_level).")</td>";
                              }
                            }
                    echo "
                            <td class=\"tbldata\">Level des Schiffes</td>
                         </tr>
                         <tr>
                            <td class=\"tbltitle\">Erfahrung</td>
                            <td class=\"tbldata\">".nf($arr['shiplist_special_ship_exp'])."</td>
                            <td class=\"tbldata\">Erfahrung des Schiffes</td>
                         </tr>
                         <tr>
                            <td class=\"tbltitle\">Ben. Erfahrung</td>";

                            if($arr['special_ship_max_level']<=$init_level && $arr['special_ship_max_level']!=0)
                                echo "<td class=\"tbldata\"> - </td>";
                            else
                                echo "<td class=\"tbldata\">".nf($exp_for_next_level)."</td>";

                            echo "<td class=\"tbldata\">Benötigte Erfahrung bis zum nächsten LevelUp</td>
                         </tr>

                         ";
                    infobox_end(1);

        }

      }
      else
      {
      	echo "Du bist noch nicht im Besitz eines Spezialschiffes!<br>";
      }
	}

?>
