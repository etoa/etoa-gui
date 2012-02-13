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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	// $Author$
	// $Date$
	// $Rev$
	//
if (Alliance::checkActionRights('applicationtemplate'))
{

	echo "<h2>Bewerbungsvorlage bearbeiten</h2>";
	echo "<form action=\"?page=$page\" method=\"post\">";
	checker_init();
	tableStart("Bewerbungsvorlage");
	echo "<tr><th>Text:</td>
	<td><textarea rows=\"15\" cols=\"60\" name=\"alliance_application_template\">".stripslashes($arr['alliance_application_template'])."</textarea></td></tr>";
	echo "<tr><th>Beispiel:</td><td>";
	echo nl2br('Dein Name:
Dein Alter:
Dein Sektor:
Deine Rasse:
Deine Punktezahl:
Deine Erfahrung:
Was du von uns erwartest:
Was kannst du für uns tun:
Der Grund deiner Bewerbung:');
	echo "</td></tr>";
	tableEnd();
	echo "<input type=\"submit\" name=\"applicationtemplatesubmit\" value=\"Speichern\" /> &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
}
?>