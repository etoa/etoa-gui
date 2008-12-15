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
	// 	File: userconfig.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Configure personal settings
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo "<h1>Einstellungen</h1>"; 
		
/****************/
/* Menu			*/
/****************/

	$mode = (isset($_GET['mode']) && $_GET['mode']!="") ? $_GET['mode'] : 'general';
	
	$tabitems = array("general"=>"Profil",
 														"game"=>"Spiel",
 														"messages"=>"Nachrichten",
 														"design"=>"Design",
 														"sitting"=>"Sitting",
 														"password"=>"Passwort",
 														"logins"=>"Logins",
 														"misc"=>"Sonstiges");

	$ures = dbquery("
	SELECT
		COUNT(warning_id)
	FROM
		user_warnings
	WHERE
		warning_user_id=".$cu->id."
	");
	$uarr = mysql_fetch_row($ures);
	if ($uarr[0]>0)
		$tabitems['warnings'] = "Verwarnungen";
	
 	show_tab_menu("mode",$tabitems);
 	echo '<br/>';


    /****************/
    /* Spiel    */
    /****************/
		if($mode=='game')
		{
 			require("content/userconfig/game.php");
		}
		
    /****************/
    /* Nachrichten    */
    /****************/
		elseif($mode=='messages')
		{
      if($s['sitter_active']==0)
      {
      	require("content/userconfig/messages.php");
      }
      else
      {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
      }
		}
		
    /****************/
    /* Verwarnungen    */
    /****************/
		elseif($mode=='warnings')
		{
      if($s['sitter_active']==0)
      {
      	tableStart("Ausgesprochene Verwarnungne");
      	echo "
					<tr>
						<th>Text</th>
						<th>Datum</th>
						<th>Verwarnt von</th>
					</tr>";
						$ures = dbquery("
						SELECT
							warning_text,
							warning_date,
							user_nick as anick,
							user_id as aid,
							warning_id
						FROM
							user_warnings
						LEFT JOIN
							admin_users
						ON
							user_id=warning_admin_id
						WHERE
							warning_user_id=".$cu->id."
						ORDER BY
							warning_date DESC
						");
						while ($uarr = mysql_Fetch_array($ures))	
						{
							echo "<tr>
								<td>".stripslashes(nl2br($uarr['warning_text']))."</td>
								<td>".df($uarr['warning_date'])."</td>	
								<td><a href=\"?page=contact&rcpt=".$uarr['aid']."\">".$uarr['anick']."</a>
								</td>
							</tr>";
						}			
				
				echo "</table>";      	
      	
      	
      }
      else
      {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
      }
		}		
		

    /****************/
    /* Design     	*/
    /****************/
		elseif($mode=='design')
		{
			require("content/userconfig/design.php");
    }
     
		/****************/
		/* Sitting			*/
		/****************/
   	elseif($mode=='sitting')
    {
      if($s['sitter_active']==0)
      {
      	require("content/userconfig/sitting.php");
      }
      else
      {
        echo "Im Sittermodus ist dieser Bereich gesperrt!<br><br>";
        
        // Aktive Sittingdaten Anzeigen
        tableStart("Sitter Einstellungen");
        echo "<tr><td class=\"tbldata\"><div align=\"center\"><b>Modus aktiv!</b></div></td></tr>";

        $date_res = dbquery("
        SELECT
            *
        FROM
            user_sitting_date
        WHERE
            user_sitting_date_user_id='".$cu->id."'
            AND user_sitting_date_from!=0
            AND user_sitting_date_to!=0
        ORDER BY
            user_sitting_date_from;");

        echo "<tr><td class=\"tbldata\"><div align=\"center\">";
        while ($date_arr=mysql_fetch_array($date_res))
        {
            if($date_arr['user_sitting_date_to']<time())
            {
                echo "<span style=\"color:#f00\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
            }
            elseif($date_arr['user_sitting_date_from']<time() && $date_arr['user_sitting_date_to']>time())
            {
                echo "<span style=\"color:#0f0\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
            }
            else
            {
                echo "Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."<br>";
            }
        }
        echo "</td></tr>";
        tableEnd();
      }
    }

		/****************/
		/* Passwort			*/
		/****************/
		elseif($mode=='password')
  	{
      if($s['sitter_active']==0)
      {
      	require("content/userconfig/password.php");
    	}
      else
      {
      	echo "Im Sittermodus ist dieser Bereich gesperrt!";
      }
    }

		/****************/
		/* Sonstiges		*/
		/****************/
		elseif($mode=='misc')
		{
        if($s['sitter_active']==0)
        {
        	require("content/userconfig/misc.php");
      	}
        else
        {
        	echo "Im Sittermodus ist dieser Bereich gesperrt!";
        }
		}
		
		/****************/
		/* Logins		*/
		/****************/
		
		elseif ($mode=="logins")
		{
    	require("content/userconfig/logins.php");
		}
		
    /****************/
    /* Userdaten    */
    /****************/
		else
		{
        if($s['sitter_active']==0)
        {
					require("content/userconfig/general.php");
        }
        else
        {
          echo "Im Sittermodus ist dieser Bereich gesperrt!";
        }
		}
		
	

?>