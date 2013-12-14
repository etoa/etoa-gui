<?PHP
	class DBException extends Exception
	{
		public function getErrStr() {
			return "Datenbankfehler\nDatei: ".parent::getFile().", Zeile: ".parent::getLine()."\nAbfrage:".
				parent::getMessage()."\nFehlermeldung: ".mysql_error()."\nStack-Trace: ".parent::getTraceAsString();
		}

		public function __toString()
		{
			if (!(defined('ETOA_DEBUG') && ETOA_DEBUG==1)) {
				return "<div class=\"errorBox\" style=\"text-align:left;\"><h2>Datenbankfehler</h2>Die gewünschte Abfrage konnte nicht durchgeführt werden!<br/>
					Bitte versuchen Sie es später nochmals und <a href=\"".DEVCENTER_PATH."\" onclick=\"".DEVCENTER_ONCLICK.";return false;\">melden</a> Sie diesen Fehler falls er weiterhin auftritt!</div>";
			}

			if (!defined('USE_HTML') || USE_HTML)
			{
				$str = "<div class=\"errorBox\" style=\"text-align:left;\"><h2>Datenbankfehler</h2>
				<b>Datei:</b> ".parent::getFile().", <b>Zeile:</b> ".parent::getLine()."<br/>
				<b>Abfrage:</b> ".nl2br(parent::getMessage())."<br/>
				<b>Fehlermeldung:</b> ".nl2br(mysql_error())."<br/>				";
				$str.="<hr/><b>Stack-Trace:</b><br/>".nl2br(parent::getTraceAsString())."<br/><hr/>";
				$str.="<a href=\"".DEVCENTER_PATH."\" target=\"_blank\">Fehler melden</a>";
				$str.="</div>";
				return $str;
			}

			return $this->getErrStr();
		}
	}
?>
