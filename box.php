<?PHP
	session_start();
	require_once("conf.inc.php");
	require_once("functions.php");

	dbconnect();

	$conf = get_all_config();
	include("def.inc.php");
	
	if (isset($_SESSION[ROUNDID]))
	{
		$s = $_SESSION[ROUNDID];
		if (isset($s['user_id']))
		{
			$cu = new CurrentUser($s['user_id']);
			if (isset($s['cpid']))
			{
				$cpid = $s['cpid'];
				$cp = new Planet($cpid);

				if (isset($cu->css_style) && $cu->css_style!="")
				{
					define("CSS_STYLE",$cu->css_style);
				}
				else
				{
					define("CSS_STYLE",$conf['default_css_style']['v']);
				}
			
				if (isset($cu->image_url) && $cu->image_url!='' && $cu->image_ext!='')
				{
					define('IMAGE_PATH',$cu->image_url);
					define('IMAGE_EXT',$cu->image_ext);
				}
				else
				{
					define("IMAGE_PATH",$conf['default_image_path']['v']);
					define("IMAGE_EXT","gif");
				}		
				
				if (isset($_GET['page']))
				{
					$page = $_GET['page'];
					include("content/".$page.".php");
				}
				else
				{
					echo "<h1>Error</h1>";
				}
			}
			else
			{
				echo "Fehler: Aktueller Planet nicht gefunden!";
			}
		}
		else
		{
			echo "Fehler: Aktueller Benutzer nicht gefunden!";			
		}
	}
	else
	{
			echo "Fehler: Ungültige Session!";			
	}


			
				


	dbclose();
?>