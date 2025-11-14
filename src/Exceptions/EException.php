<?PHP

namespace EtoA\Exceptions;

use EtoA\Support\ExternalUrl;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;

class EException extends Exception
{
    public const LOG_FILE = __DIR__ . '/../log/errors.log';

    public function __construct(
        private readonly Security $security
    )
    {
        parent::__construct();
    }

    public function __toString(): string
    {
        $cu = $this->security->getUser()->getData();

        $str = "Allgemeiner Fehler: " . parent::getMessage() . "\nDatei: " . parent::getFile() . ", Zeile: " . parent::getLine() . "\nStack-Trace: " . parent::getTraceAsString();
        $f = fopen(self::LOG_FILE, "a+");
        fwrite($f, date("d.m.Y H:i:s") . ", " . $_SERVER['REMOTE_ADDR'] . ", " . $cu->getNick() . "\n" . $str . "\n\n");
        fclose($f);
        if (!isCLI()) {
            $str = "<div class=\"criticalErrorBox\"><h2>Allgemeiner Fehler</h2><div>" . parent::getMessage() . "<br/>
                <b>Datei:</b> " . parent::getFile() . ", <b>Zeile:</b> " . parent::getLine() . "";
            $str .= "<div style=\"text-align:left;border-top:1px solid #000;\">
                <b>Stack-Trace:</b><br/>" . nl2br(parent::getTraceAsString()) . "<br/>
                <a href=\"" . ExternalUrl::DEV_CENTER . "\" target=\"_blank\">Fehler melden</a></div>
                </div></div>";
            return $str;
        }
        return $str;
    }
}
