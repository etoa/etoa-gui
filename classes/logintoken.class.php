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
	function verify($val,&$errorCode)
	{
		$hash = substr($val,0,40);
		$time = substr($val,40);
		
		if ($hash != sha1($_SERVER['REMOTE_ADDR'].$time))
		{
			$errorCode = 1;
			return false;			
		}
		
		$t = time();
		if ($time + 120 <= $t || $time - 120 >= $t)
		{
			$errorCode = 2;
			return false;					
		}		
		$errorCode = 0;
		return true;
	}	
}
?>