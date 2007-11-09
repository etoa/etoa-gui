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
	// 	File: notepad.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Personal notes management
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	class notepad
	{
		function loadList()
		{
			$this->note=array();
			$this->note=array();

			$res=dbquery("SELECT * FROM ".$this->db_table." WHERE note_user_id=".$this->user_id." ORDER BY note_timestamp DESC;");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_array($res))
				{
					$this->note[$arr['note_id']]->subject=$arr['note_subject'];
					$this->note[$arr['note_id']]->text=$arr['note_text'];
					$this->note[$arr['note_id']]->timestamp=$arr['note_timestamp'];
				}
			}
		}

		function showList()
		{
			global $page;
			if (count($this->note)>0)
			{
				infobox_start("Meine Notizen",1);
				foreach ($this->note as $k=>$i)
				{
					echo "<tr><td class=\"tbldata\" width=\"120px\"><b>".text2html(stripslashes($i->subject))."</b><br/>".date("d.m.Y H:i",$i->timestamp)."</td>";
					echo "<td class=\"tbldata\">".text2html(stripslashes($i->text))."</td>";
					echo "<td class=\"tbldata\" style=\"width:130px;\"><a href=\"?page=$page&amp;action=edit&amp;id=$k\">Bearbeiten</a> &nbsp; ";
					echo "<a href=\"?page=$page&amp;action=delete&amp;id=$k\" onclick=\"return confirm('Soll die Notiz ".text2html(stripslashes($i->subject))." wirklich gel&ouml;scht werden?');\">L&ouml;schen</a></td></tr>";
				}
				infobox_end(1);
			}
			else
				echo "<i>Es sind noch keine Notizen vorhanden</i><br/><br/>";
		}

		function showForm()
		{
			global $page;
			echo "<form action=\"?page=$page\" method=\"post\">";
			if ($this->currentNote->id>0)
			{
				echo "<input type=\"hidden\" name=\"note_id\" value=\"".$this->currentNote->id."\" />";
				infobox_start("Notiz bearbeiten",1);
			}
			else
				infobox_start("Neue Notiz",1);
			echo "<tr><th class=\"tbltitle\">Titel:</th><td class=\"tbldata\"><input type=\"text\" name=\"note_subject\" value=\"".stripslashes($this->currentNote->subject)."\" size=\"40\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Text:</th><td class=\"tbldata\"><textarea name=\"note_text\" cols=\"50\" rows=\"10\">".stripslashes($this->currentNote->text)."</textarea></td></tr>";
			infobox_end(1);
			echo "<input type=\"submit\" value=\"Speichern\" name=\"submit\" > &nbsp; ";
			echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /> &nbsp; ";			
			echo "</form><br/>";
		}

		function saveNote()
		{
			if ($this->currentNote->id >0)
				dbquery("UPDATE ".$this->db_table." SET note_subject='".addslashes($this->currentNote->subject)."',note_text='".addslashes($this->currentNote->text)."',note_timestamp='".time()."' WHERE note_user_id=".$this->user_id." AND note_id='".$this->currentNote->id."';");
			else
				dbquery("INSERT INTO ".$this->db_table." (note_user_id,note_subject,note_text,note_timestamp) VALUES ('".$this->user_id."','".addslashes($this->currentNote->subject)."','".addslashes($this->currentNote->text)."','".time()."');");
			ok_msg("Notiz gespeichert!");
		}

		function loadNote($id)
		{
			$res=dbquery("SELECT * FROM ".$this->db_table." WHERE note_id=".$id." AND note_user_id=".$this->user_id.";");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				$this->currentNote->id=$id;
				$this->currentNote->subject=$arr['note_subject'];
				$this->currentNote->text=$arr['note_text'];
				return true;
			}
			else
			{
				err_msg("Datensatz nicht gefunden!");
				return false;
			}
		}

		function deleteNote($id)
		{
			dbquery("DELETE FROM ".$this->db_table." WHERE note_user_id=".$this->user_id." AND note_id=".$id.";");
		}
	}


	echo "<h1>Notizen</h1>"; //Titel angepasst <h1> by Lamborghini

	$n=new notepad();
	$n->db_table=$db_table['notepad'];
	$n->user_id=$s['user']['id'];

	//
	// Neue Notiz
	//
	if ($_GET['action']=="new")
	{
		$n->showForm();
	}

	//
	// Notiz bearbeiten
	//
	elseif ($_GET['action']=="edit" && $_GET['id']>0)
	{
		if ($n->loadNote($_GET['id']))
			$n->showForm();
	}

	//
	// Übersicht
	//
	else
	{
		// Änderungen speichern
		if ($_POST['submit'])
		{
			if ($_POST['note_id']>0)
				$n->currentNote->id=$_POST['note_id'];
			$n->currentNote->subject=$_POST['note_subject'];
			$n->currentNote->text=$_POST['note_text'];
			$n->saveNote();
		}
		// Notiz löschen
		elseif ($_GET['action']=="delete" && $_GET['id']>0)
		{
			$n->deleteNote($_GET['id']);
		}

		// Notizen anzeigen
		$n->loadList();
		$n->showList();
		echo "<input type=\"button\" value=\"Neue Notiz\" onclick=\"document.location='?page=$page&amp;action=new'\" /> &nbsp; ";
	}

?>
