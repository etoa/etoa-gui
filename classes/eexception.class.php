<?PHP
	class EException extends Exception
	{
		public function __toString()
		{
			global $cu;
			
			$str = "Allgemeiner Fehler: ".parent::getMessage()."\nDatei: ".parent::getFile().", Zeile: ".parent::getLine()."\nStack-Trace: ".parent::getTraceAsString()."";
			$f = fopen(ERROR_LOGFILE,"a+");
			fwrite($f,date("d.m.Y H:i:s").", ".$_SERVER['REMOTE_ADDR'].", ".$cu."\n".$str."\n\n");
			fclose($f);
			if (USE_HTML)
			{
				$str = "<div class=\"errorBox\" style=\"text-align:left;\"><h2>Allgemeiner Fehler</h2> ".parent::getMessage()."<br/>
				<b>Datei:</b> ".parent::getFile().", <b>Zeile:</b> ".parent::getLine()."";
				$str.="<div style=\"text-align:left;border-top:1px solid #000;\">
				<b>Stack-Trace:</b><br/>".nl2br(parent::getTraceAsString())."<br/>
				<a href=\"".BUGREPORT_URL."\" target=\"_blank\">Fehler melden</a></div>
				</div>";				
				return $str;
			}
			return $str;
		}
	}


?>