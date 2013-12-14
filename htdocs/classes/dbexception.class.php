<?PHP
	class DBException extends Exception
	{
		public function getErrStr() {
			return "Database error\n\nFile: ".parent::getFile().", line: ".parent::getLine()."\n\nQuery:\n\n".parent::getMessage()."\n\nError Message:\n\n".mysql_error()."\n\nStack-Trace:\n\n".parent::getTraceAsString()."\n";
		}

		public function __toString()
		{
			if (isset($_SERVER['SHELL'])) {
				return $this->getErrStr();
			} else {
				if (defined('ETOA_DEBUG') && ETOA_DEBUG==1) {
					$str = "<div class=\"errorBox\" style=\"text-align:left;\"><h2>Datenbankfehler</h2>
					<b>Datei:</b> ".parent::getFile().", <b>Zeile:</b> ".parent::getLine()."<br/>
					<b>Abfrage:</b> ".nl2br(parent::getMessage())."<br/>
					<b>Fehlermeldung:</b> ".nl2br(mysql_error())."<br/>				";
					$str.="<hr/><b>Stack-Trace:</b><br/>".nl2br(parent::getTraceAsString())."<br/><hr/>";
					$str.="<a href=\"".DEVCENTER_PATH."\" target=\"_blank\">Fehler melden</a>";
					$str.="</div>";
					return $str;
				} else {
					return "<div class=\"errorBox\" style=\"text-align:left;\"><h2>Datenbankfehler</h2>Die gewünschte Abfrage konnte nicht durchgeführt werden!<br/>
						Bitte versuchen Sie es später nochmals und <a href=\"".DEVCENTER_PATH."\" onclick=\"".DEVCENTER_ONCLICK.";return false;\">melden</a> Sie diesen Fehler falls er weiterhin auftritt!</div>";
				}
			}
		}
	}
?>
