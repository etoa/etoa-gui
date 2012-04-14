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

	echo "<h1>Kontakt</h1>";
	echo "<div style=\"margin:0px auto;width:600px;\">";
	echo text2html($cfg->get('contact_message'))."<br/><br/>";
	
	$admins = AdminUser::getAll();
	if (count($admins) > 0)
	{
		tableStart('Kontaktpersonen für diese Runde');
		foreach ($admins as $arr)
		{
			if ($arr->isContact) {
				echo '<tr><td class="tbldata">'.$arr->nick.'</td>';
				if (stristr($arr->email, "@etoa.ch")) {
					echo '<td class="tbldata"><a href="mailto:'.$arr->email.'">'.$arr->email.'</a></td>';
				} else {
					echo '<td class="tbldata">-</td>';
				}
				echo '</tr>';
			}
		}
		tableEnd();
	} else {
		echo "<i>Keine Kontaktpersonen vorhanden!</i>";
	}

	echo "</div>";
?>