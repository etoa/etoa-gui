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
	// 	Dateiname: contact.php
	// 	Topic: Kontakt
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: x.x.200x
	// 	Bearbeitet von: MrCage
	// 	Bearbeitet am: 05.06.2006
	// 	Kommentar:
	//

	showTitle('Kontakt');
	
	echo "<div style=\"margin:0px auto;width:600px;\">";

		echo text2html($conf['contact_message']['v'])."<br/><br/>";
		$res = dbquery("
			SELECT 
				user_id,
				user_nick,
				user_email,
				group_name
			FROM 
				admin_users
			INNER JOIN
				admin_groups
				ON user_admin_rank=group_id
				AND group_level<3
		;");
		if (mysql_num_rows($res)>0)
		{
			tableStart('Kontaktpersonen für diese Runde');
			while ($arr = mysql_fetch_array($res))
			{
				echo '<tr><td class="tbldata">'.$arr['user_nick'].'</td>';
				echo '<td class="tbldata">'.$arr['group_name'].'</td>';
				if (stristr($arr['user_email'],"@etoa.ch"))
					echo '<td class="tbldata"><a href="mailto:'.$arr['user_email'].'">'.$arr['user_email'].'</a></td>';
	      else
	      	echo '<td class="tbldata">-</td>';
				echo '</tr>';
			}
			tableEnd();
		}
		else
			echo "<i>Keine Kontaktpersonen vorhanden!</i>";

	echo "</div>";
	
?>
