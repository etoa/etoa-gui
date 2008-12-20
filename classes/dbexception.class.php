<?PHP
	class DBException extends Exception
	{
		public function __toString()
		{
			global $cu;

			$str = "Datenbankfehler\nDatei: ".parent::getFile().", Zeile: ".parent::getLine()."\nAbfrage:".parent::getMessage()."\nFehlermeldung: ".mysql_error()."\nStack-Trace: ".parent::getTraceAsString()."";
			if (defined('ERROR_LOGFILE'))
			{
				$f = fopen(DBERROR_LOGFILE,"a+");
				fwrite($f,date("d.m.Y H:i:s").", ".$_SERVER['REMOTE_ADDR'].", ".$cu."\n".$str."\n\n");
				fclose($f);
			}
			if (!defined('USE_HTML') || USE_HTML)
			{
				$str = "<div class=\"errorBox\" style=\"text-align:left;\"><h2>Datenbankfehler</h2>
				<b>Datei:</b> ".parent::getFile().", <b>Zeile:</b> ".parent::getLine()."<br/>
				<b>Abfrage:</b> ".nl2br(parent::getMessage())."<br/>
				<b>Fehlermeldung:</b> ".nl2br(mysql_error())."<br/>				";
				$str.="<div style=\"text-align:left;border-top:1px solid #000;\">
				<b>Stack-Trace:</b><br/>".nl2br(parent::getTraceAsString())."<br/>";
				if (defined('BUGREPORT_URL'))
					$str.="<a href=\"".BUGREPORT_URL."\" target=\"_blank\">Fehler melden</a>";
				$str.="</div>
				</div>";				
				return $str;
			}
			return $str;
		}
	}


?>