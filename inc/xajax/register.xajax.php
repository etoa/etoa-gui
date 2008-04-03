<?PHP

	$xajax->register(XAJAX_FUNCTION,'registerCheckName');
	$xajax->register(XAJAX_FUNCTION,'registerCheckNick');
	$xajax->register(XAJAX_FUNCTION,'registerCheckEmail');

//Überprüft die Korrektheit der Eingabe von Vor- und Nachname
function registerCheckName($val)
{
	global $db_table, $db;

	$objResponse = new xajaxResponse();
	if (checkValidName($val))
	{
		if (ereg("^.+ .+( .*)*$", $val))
		{
  		$objResponse->assign('nameStatus', 'innerHTML', "Ok");
  		$objResponse->assign('nameStatus', 'style.color', "#0f0");
  	}
  	else
  	{
			$objResponse->assign('nameStatus', 'innerHTML', "Der Name muss Vor- und Nachname enthalten!");
			$objResponse->assign('nameStatus', 'style.color', "#f90");
  	}
	}
	else
	{
		$objResponse->assign('nameStatus', 'innerHTML', "Der Name darf keine ungültigen Zeichen enthalten!");
		$objResponse->assign('nameStatus', 'style.color', "#f90");
	}
	
	$objResponse->assign('nameStatus', 'style.fontWeight', "bold");
	return $objResponse;
}

//Überprüft die Korrektheit des Nicks und prüft ob dieser schon vorhanden ist
function registerCheckNick($val)
{
	global $db_table, $db;
	
	$objResponse = new xajaxResponse();
	$objResponse->assign('nickStatus', 'style.fontWeight', "bold");
	if (checkValidNick($val))
	{
		if (strlen($val)>=NICK_MINLENGHT)
		{
			$res=$db->query("SELECT user_id FROM ".$db_table['users']." WHERE user_nick='$val';");
            if (mysql_num_rows($res)>0)
            {
                $objResponse->assign('nickStatus', 'innerHTML', "Dieser Benutzername wird bereits benutzt!");
                $objResponse->assign('nickStatus', 'style.color', "#f90");
            }
            else
            {
                $objResponse->assign('nickStatus', 'innerHTML', "Ok");
                $objResponse->assign('nickStatus', 'style.color', "#0f0");
            }
        }
        else
        {
            $objResponse->assign('nickStatus', 'innerHTML', "Der Benutzername ist noch zu kurz!");
            $objResponse->assign('nickStatus', 'style.color', "#f90");
        }
    }
    else
    {
    	$objResponse->assign('nickStatus', 'innerHTML', "Der Benutzername ist nicht korrekt!");
    	$objResponse->assign('nickStatus', 'style.color', "#f90");
    }
    
	return $objResponse;
}

//Überprüft die Korrektheit der Eingabe von der Email Adresse und prüft ob diese schon vorhanden ist
function registerCheckEmail($val)
{
	global $db_table, $db;
	$objResponse = new xajaxResponse();
	if (checkEmail($val))
	{
		$res=$db->query("SELECT user_id FROM ".$db_table['users']." WHERE user_email='$val' OR user_email_fix='$val';");
        if (mysql_num_rows($res)>0)
        {
            $objResponse->assign('emailStatus', 'innerHTML', "Diese E-Mail-Adresse wird bereits benutzt!");
            $objResponse->assign('emailStatus', 'style.color', "#f90");
            $objResponse->assign('emailStatus', 'style.fontWeight', "bold");
        }
        else
        {
            $objResponse->assign('emailStatus', 'innerHTML', "Ok");
            $objResponse->assign('emailStatus', 'style.color', "#0f0");
            $objResponse->assign('emailStatus', 'style.fontWeight', "bold");
        }
    }
    else
    {
        $objResponse->assign('emailStatus', 'innerHTML', "Du musst eine korrekte E-Mail-Adresse eingeben!");
        $objResponse->assign('emailStatus', 'style.color', "#f90");
        $objResponse->assign('emailStatus', 'style.fontWeight', "bold");
    }
    
	return $objResponse;
}





?>