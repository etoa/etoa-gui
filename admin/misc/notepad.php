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
	// 	Dateiname: notepad.php
	// 	Topic: Admin Notepad
	// 	Autor: Yanneck Boss alias Yanneck
	// 	Erstellt: 27.12.2007
	// 	Bearbeitet von: Yanneck Boss alias Yanneck
	// 	Bearbeitet am: 01.01.2008
	// 	Kommentar: 	Notepad für den Admin Modus
	//
	
	session_start();

	
	//CSS Style
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../style.css\">";
	echo "<link rel=\"stylesheet\" href=\"../../css/general.css\" type=\"text/css\">";
	
	// Funktionen und Config einlesen
	require("../../conf.inc.php");
	require("../../functions.php");
	
	// Mit der DB verbinden
	dbconnect();

	define('SESSION_NAME',"adminsession");
	$s = $_SESSION[SESSION_NAME];
	$ID = $s['user_id'];
	
	if ($ID >0)
	{
	
	if ($_GET['func'] == 'editieren')
	{
		$Titel = $_POST['Titel'];
		$Text = $_POST['Text'];
		Mysql_Query("update admin_notes set text = '".$Text."', titel = '".$Titel."' where admin_id = ".$ID."");
		echo "<meta http-equiv=\"refresh\" content=\"0; URL=notepad.php?id=".$ID."\">";
	}
	else
	{
		if ($_GET['chk'] == 'edit')
		{
			$res = Mysql_Query("select * from admin_notes where admin_id ='".$ID."'");
			$row = mysql_fetch_array($res);

			echo "<h1>Notizen</h1><br>";
			echo "<form action=\"notepad.php?id=".$ID."&func=editieren\" method=\"post\">";
			echo "<input type=\"Text\" name=\"Titel\" value=\"".$row['titel']."\"></input><br><br>";
			echo "<textarea name=\"Text\" cols=\"50\" rows=\"10\">".$row['text']."</textarea><br><br>";
			echo "<input type=\"Submit\" name=\"&#196ndern\" value=\"&#196ndern\"></input>";
			echo "</form>";
		}
		else
		{
			if ($_GET['func'] == 'del')
			{
				$PID = $_GET['pid'];
		
				Mysql_Query("delete from admin_notes where notes_id = ".$PID."");
				echo "<meta http-equiv=\"refresh\" content=\"0; URL=notepad.php?id=".$ID."\">";
			}	
			else
			{
		
				if ($_GET['chk'] == 'del')
				{
					$PID = $_GET['pid'];
						
						?>
						
							<script language="JavaScript1.2" type="text/javascript">

							var id = "<?php echo $ID; ?>";
							var pid = "<?php echo $PID; ?>";

							Check = confirm("Wollen Sie den Eintrag entfernen?");
							
							if(Check == false)
							{
								history.back();
							}
							else
							{
								document.location.href ="notepad.php?id=" + id + "&func=del&pid=" + pid + ""; 
							}
							</script>
						
						<?php

				}
				else 
				{
					if($_GET['func'] == 'new')
					{
						$time = time();
						$Titel = $_POST['Titel'];
						$Text = $_POST['Text'];
						
						Mysql_Query("insert into admin_notes (admin_id, titel, text, date) values ('$ID', '$Titel', '$Text', '$time');");
						echo "<meta http-equiv=\"refresh\" content=\"0; URL=notepad.php?id=".$ID."\">";
					}
					else
					{
			
						if ($_GET['chk'] == 'new')
						{
							echo "<h1>Notizen</h1><br>";
							echo "<form action=\"notepad.php?id=".$ID."&func=new\" method=\"post\">";
							echo "<input type=\"Text\" name=\"Titel\"></input><br><br>";
							echo "<textarea name=\"Text\" cols=\"50\" rows=\"10\"></textarea><br><br>";
							echo "<input type=\"Submit\" name=\"Einf&#252gen\" value=\"Einf&#252gen\"></input>";
							echo "</form>";
						}
						else
						{
	
							$res = Mysql_Query("select * from admin_notes where admin_id ='".$ID."'");
	
							echo "<h1>Notizen</h1><br>";
	
							if (mysql_num_rows($res) == 0)
							{
								echo "Keine Notiz vorhanden";
							
								echo "<form action=\"notepad.php?id=".$ID."&chk=new\" method=\"post\">";
	 							echo "<br><input type=\"Submit\" name=\"Neue Notiz\" value=\"Neue Notiz\"></input>";
	 							echo "</form>";
							}
							else
							{
								echo "<br><table><tr><td class=\"tbltitle\" colspan=\"3\" width=\"300\">Notizen</td></tr>";
								while ($row = mysql_fetch_array($res))
								{
									$Time = $row['date'];
									$datum = date("d.m.Y",$Time);
     							$uhrzeit = date("H:i",$Time);
		
									$PID = $row['notes_id'];
		
									echo "<tr><td class=\"tbldata\" width=\"120\">".text2html($row['titel'])."<br><br>".$datum." ".$uhrzeit."</td>";
									echo "<td class=\"tbldata\" width=\"350\">".text2html($row['text'])."</td>";
									echo "<td class=\"tbldata\"><a href=\"".$_SERVER['PHP_SELF']."?id=".$ID."&chk=edit\">Bearbeiten</a><br><a href=\"".$_SERVER['PHP_SELF']."?id=".$ID."&chk=del&pid=".$PID."\">L&#246schen</a></td></tr>";
	 							}
	 							echo "</table>";
	 							echo "<form action=\"notepad.php?id=".$ID."&chk=new\" method=\"post\">";
	 							echo "<br><input type=\"Submit\" name=\"Neue Notiz\" value=\"Neue Notiz\"></input>";
	 							echo "</form>";
							}
						}
					}
				}
			}
		}
	}
}
else
{
	echo "Ungültige ID";
}
?>