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
                            "dual"=>"Dual",
 														"password"=>"Passwort",
 														"logins"=>"Logins",
 														"banner"=>"Banner",
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
			// todo: sitter
      if($s->sittingActive==0)
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
      if($s->sittingActive==0)
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
						while ($uarr = mysql_fetch_array($ures))
						{
							echo "<tr>
								<td>".stripslashes(nl2br($uarr['warning_text']))."</td>
								<td>".df($uarr['warning_date'])."</td>	
								<td><a href=\"?page=contact&rcpt=".$uarr['aid']."\">".$uarr['anick']."</a>
								</td>
							</tr>";
						}

				tableEnd();


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
      if(!$s->sittingActive || $s->falseSitter)
      {
      	require("content/userconfig/sitting.php");
      }
      else
      {
        echo "Im Sittermodus ist dieser Bereich gesperrt!<br><br>";


      }
    }

    /****************/
		/* Dual			*/
		/****************/
		elseif($mode=='dual')
  	{
      if(!$s->sittingActive)
      {
      	require("content/userconfig/dual.php");
    	}
      else
      {
      	echo "Im Sittermodus ist dieser Bereich gesperrt!";
      }
    }

		/****************/
		/* Passwort			*/
		/****************/
		elseif($mode=='password')
  	{
      if(!$s->sittingActive)
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
        if(!$s->sittingActive)
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
		/* Banner		*/
		/****************/

		elseif ($mode=="banner")
		{
    	require("content/userconfig/banner.php");
		}

    /****************/
    /* Userdaten    */
    /****************/
		else
		{
        if(!$s->sittingActive)
        {
					require("content/userconfig/general.php");
        }
        else
        {
          echo "Im Sittermodus ist dieser Bereich gesperrt!";
        }
		}



?>
