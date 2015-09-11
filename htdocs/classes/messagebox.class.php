<?php
class MessageBox
{
	static function get($type, $title, $message, $save=false) {
		static $messageId=0;
		$class = "info";
		switch ($type)
		{
			case "success":		
				$class = "success";
				break;			
			case "ok":		
				$class = "success";
				break;
			case "info":		
				$class = "info";
				break;
			case "error":		
				$class = "error";
				break;
			case "err":		
				$class = "error";
				break;			
			case "warn":		
				$class = "warning";
				break;
			case "warning":		
				$class = "warning";
				break;			
			case "validation":		
				$class = "validation";
				break;				
		}				
		$str = "<div class=\"messagebox\" id=\"messagebox".($messageId++)."\"><div class=\"".$class."\">";
		if ($title!="" && $title!=null) {
			$str.="<p class=\"messagetitle\">".$title."</p>";
		}
		$str.= $message."</div></div>";
		if ($save || $save == 1)
			$_SESSION['savedmessage'] = $str;		
		return $str;
	}

	static function ok($title, $message, $save=false)
	{
		return self::get("success", $title, $message, $save);
	}

	static function info($title, $message, $save=false)
	{
		return self::get("info", $title, $message, $save);
	}
	
	static function warning($title, $message, $save=false)
	{
		return self::get("warning", $title, $message, $save);
	}
	
	static function validation($title, $message, $save=false)
	{
		return self::get("validation", $title, $message, $save);
	}

	static function error($title, $message, $save=false)
	{
		return self::get("error", $title, $message, $save);
	}

	static function getSaved()
	{
		$msg = isset($_SESSION['savedmessage']) ? $_SESSION['savedmessage'] : "";
		unset($_SESSION['savedmessage']);
		return $msg;
	}
}
?>