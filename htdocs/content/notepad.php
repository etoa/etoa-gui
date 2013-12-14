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
	//
	
	/**
	* Personal notes management
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo "<h1>Notizen</h1>"; //Titel angepasst <h1> by Lamborghini
	$np = new Notepad($cu->id,1);		

	//
	// Neue Notiz
	//
	if (isset($_GET['action']) && $_GET['action']=="new")
	{
		echo "<form action=\"?page=$page\" method=\"post\">";
		tableStart("Neue Notiz");
		echo "<tr><th>Titel:</th>
		<td><input type=\"text\" name=\"note_subject\" value=\"\" size=\"40\" /></td></tr>";
		echo '<tr><th>Text:</th><td><textarea name="note_text" cols="50" rows="10"></textarea><br/>'.helpLink('textformat', 'Hilfe zur Formatierung').'</td></tr>';
		tableEnd();
		echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_new\" > &nbsp; ";
		echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /> &nbsp; ";			
		echo "</form><br/>";
	}

	//
	// Notiz bearbeiten
	//
	elseif (isset($_GET['action']) && $_GET['action']=="edit" && $_GET['id']>0)
	{
		if ($n = $np->get($_GET['id']))
		{
			echo "<form action=\"?page=$page\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"note_id\" value=\"".$n->id()."\" />";
			tableStart("Notiz bearbeiten");
			echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"note_subject\" value=\"".stripslashes($n->subject())."\" size=\"40\" /></td></tr>";
			echo '<tr><th>Text:</th><td><textarea name="note_text" cols="50" rows="10">'.stripslashes($n->text()).'</textarea><br/>'.helpLink('textformat', 'Hilfe zur Formatierung').'</td></tr>';
			tableEnd();
			echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_edit\" > &nbsp; ";
			echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /> &nbsp; ";			
			echo "</form><br/>";
		}
		else
		{
			error_msg("Notiz nicht vorhanden!");
		}
	}

	//
	// Übersicht
	//
	else
	{
		
		// Änderungen speichern
		if (isset($_POST['submit_new']) && $_POST['note_subject']!="")
		{
			$np->add($_POST['note_subject'],$_POST['note_text']);

		}
		// Änderungen speichern
		if (isset($_POST['submit_edit']) && $_POST['note_id']>0 && $_POST['note_subject']!="")
		{
			$np->set($_POST['note_id'],$_POST['note_subject'],$_POST['note_text']);

		}		
		// Notiz löschen
		elseif (isset($_GET['action']) && $_GET['action']=="delete" && $_GET['id']>0)
		{
			$np->delete($_GET['id']);
		}

		if ($np->numNotes()>0)
		{
			tableStart("Meine Notizen");			
			foreach ($np->getArray() as $id=>$n)
			{
				echo "<tr><td width=\"120px\"><b>".$n->subject()."</b>
				<br/>".df($n->timestamp())."</td>";
				echo "<td>".text2html($n->text())."</td>";
				echo "<td style=\"width:130px;\"><a href=\"?page=$page&amp;action=edit&amp;id=".$id."\">Bearbeiten</a> &nbsp; ";
				echo "<a href=\"?page=$page&amp;action=delete&amp;id=".$id."\" onclick=\"return confirm('Soll die Notiz ".$n->subject()." wirklich gel&ouml;scht werden?');\">L&ouml;schen</a></td></tr>";
			}
			tableEnd();
		}
		else
		{
			error_msg("Keine Notizen vorhanden!",1);
		}
		
		
		
		echo "<input type=\"button\" value=\"Neue Notiz\" onclick=\"document.location='?page=$page&amp;action=new'\" /> &nbsp; ";
	}

	unset($np);

?>
