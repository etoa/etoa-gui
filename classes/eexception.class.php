<?PHP
	class EException extends Exception
	{
		public function __toString()
		{
			if (USE_HTML)
			{
				$str = "<div class=\"errorBox\"><b>Allgemeiner Fehler:</b> ".parent::getMessage()."<br/>
				<b>Datei:</b> ".parent::getFile().", <b>Zeile:</b> ".parent::getLine()."";
				$str.="<div style=\"text-align:left;border-top:1px solid #000;\">
				<b>Stack-Trace:</b><br/>".nl2br(parent::getTraceAsString())."<br/>
				<a href=\"".BUGREPORT_URL."\" target=\"_blank\">Fehler melden</a></div>
				</div>";				
				return $str;
			}
			$str = "Allgemeiner Fehler: ".parent::getMessage()."\n\nStack-Trace: ".parent::getTraceAsString()."";
			return $str;
		}
		 
		

	}


?>