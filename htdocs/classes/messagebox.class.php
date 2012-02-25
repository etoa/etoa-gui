<?php
class MessageBox
{
	static function ok($message,$save=0)
	{
		static $messageId=0;
		$str = "<div class=\"messagebox\" id=\"messagebox".($messageId++)."\">
			<div class=\"success\">
		   ".$message."</div></div>";
		if ($save == 1)
			$_SESSION['savedmessage'] = $str;
		return $str;
	}

	static function info($message,$save=0)
	{
		static $messageId=0;
		$str = "<div class=\"messagebox\" id=\"messagebox".($messageId++)."\">
			<div class=\"info\">
		   ".$message."</div></div>";
		if ($save == 1)
			$_SESSION['savedmessage'] = $str;
		return $str;
	}

	static function warning($message,$save=0)
	{
		static $messageId=0;
		$str = "<div class=\"messagebox\" id=\"messagebox".($messageId++)."\">
			<div class=\"warning\">
			<b>Warning:</b> ".$message."</div></div>";
		if ($save == 1)
			$_SESSION['savedmessage'] = $str;
		return $str;
	}
	
	static function validation($message,$save=0)
	{
		static $messageId=0;
		$str = "<div class=\"messagebox\" id=\"messagebox".($messageId++)."\">
			<div class=\"validation\">
			<b>Validation error:</b> ".$message."</div></div>";
		if ($save == 1)
			$_SESSION['savedmessage'] = $str;
		return $str;
	}
	

	static function error($message,$save=0,$die=0)
	{
		static $messageId=0;
		$str = "<div class=\"messagebox\" id=\"messagebox".($messageId++)."\">
			<div class=\"error\">
			<b>".(defined('LANG_Error') ? LANG_Error : 'Error: ').":</b> ".$message."</div></div>";
		if ($die==1)
			die($str);
		if ($save == 1)
			$_SESSION['savedmessage'] = $str;
		return $str;
	}
	
	static function errorWithTitle($title,$message,$save=0,$die=0)
	{
		static $messageId=0;
		$str = "<div class=\"messagebox\" id=\"messagebox".($messageId++)."\">
			<div class=\"error\"><p class=\"messagetitle\">".$title."</p> ".$message."</div></div>";
		if ($die==1)
			die($str);
		if ($save == 1)
			$_SESSION['savedmessage'] = $str;
		return $str;
	}	

	static function saved()
	{
		$msg = isset($_SESSION['savedmessage']) ? $_SESSION['savedmessage'] : "";
		unset($_SESSION['savedmessage']);
		return $msg;
	}
}
?>
