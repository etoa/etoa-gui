<?PHP

	include("conf.inc.php");

	include(CLASS_DIR."/timer.class.php");
	include(CLASS_DIR."/mysql.class.php");
	include(CLASS_DIR."/session.class.php");
	
	$tmr = new Timer();

	//
	// Connect to database and validate session
	//

	$db = new MySQL(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);
	$s = new Session();
	
	if (isset($_GET['logout']))
	{
		$s->logout();
	}

	//
	// Log user in if data is submitted
	//
	if (isset($_POST['login_submit']))
	{
		$s->login($_POST['login_nick'],$_POST['login_password'],false);
	}
	
	//
	// Actions to take if user is not logged in
	//
	if (! $s->loggedIn())
	{
		if ($s->hasError())
		{
			$err = $s->getError();
		}
		else
		{
			$err = 'You are not logged in';
		}
		header("Location: ".LOGIN_PATH."?error=".base64_encode($err)."&hash=".md5($err));
		echo "<b>Error:</b> ".$err."<br/><br/>";
		echo "<a href=\"".LOGIN_PATH."?error=".base64_encode($err)."&amp;hash=".md5($err)."\">Login</a>";		
		exit;
	}
	
	//
	// Display content
	//

	echo "Logged in as ".$s->userName()."!<br/>
	<a href=\"?logout\">Logout</a> | <a href=\"?\">Reload</a><br/>
	Session duration: ".$s->getDuration();
	echo "<br/>Info: ";
	print_r($s->info());		
	echo "<br/><br/>Name: ".$s->info('name');	
	
	echo "<br/><br/>Render time:</b> ".$tmr->getRoundedTime()." seconds";
	


?>
