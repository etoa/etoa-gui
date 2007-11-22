<?PHP

 /**
 * Logout file. The current users session is written to the database
 * and the session cookies are deleted.
 *
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */	

 	dbquery ("UPDATE ".$db_table['user_log']." SET log_logouttime=".time()." WHERE log_user_id='".$_SESSION[ROUNDID]['user']['id']."' AND log_session_key='".$_SESSION[ROUNDID]['key']."';");
	$_SESSION[ROUNDID]="";
	session_destroy();
	unset($sc);
	$_SESSION[ROUNDID]=Null;
	header('Location: '.LOGINSERVER_URL.'?page=logout');
	echo "<h1>Logout</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=logout\">hier</a> klicken...";
	exit;
?>