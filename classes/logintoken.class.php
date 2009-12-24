<?PHP
class LoginToken
{
	private $token; 
	function __construct()
	{
		$t = time();
		$this->token = sha1($_SERVER['REMOTE_ADDR'].$t).$t;
	}
	function get()
	{
		return $this->token;
	}	
	function verify()
	{
		$val = $_POST['token'];
		$hash = substr($val,0,40);
		$time = substr($val,40);
		
		if (isset($_POST['token']))
		{
			if ($hash == sha1($_SERVER['REMOTE_ADDR'].$time))
			{
				$t = time();
				if ($time + 3600 >= $t && $time - 3600 <= $t)
				{
					return true;
				}
				else
				{
					$errStr = "Invalid time! Given ".date("d.m.Y, H:i:s",$time).", difference: ".tf(abs($t-$time))."";
				}
			}
			else
			{
				$errStr = "Invalid hash $hash, should be ".sha1($_SERVER['REMOTE_ADDR'].$time);
			}
		}
		else
		{
			$errStr = "No token supplied!";
		}
		
			$tpost = $_POST;
			unset($tpost['login_pw']);
			$text = "[".date("d.m.Y, H:i:s")."] ".$errStr."\n";
			$text.= "Host: ".$_SERVER['REMOTE_ADDR']."\n";
			$text.= "User: ".$tpost['login_nick']."\n";
			$text.= "Token: ".$_POST['token']."\n";
			$text.= "Agent: ".$_SERVER['HTTP_USER_AGENT']."\n";
			$text.= "Referer: ".$_SERVER['HTTP_REFERER']."\n";
			$text.= var_export($tpost,true)."\n"; 
			if (count($_GET)>0)
				$text.= var_export($_GET,true)."\n"; 
			
			file_put_contents("cache/log/logintoken.log", $text."\n", FILE_APPEND);			
		
		return $false;
	}	
}
?>
