<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserSittingRepository;

/**
 * Provides methods for accessing
 * the current logged in user
 *
 * @author Nicolas Perrenoud<mrcage@etoa.ch>
 */
class CurrentUser extends User
{
    protected $property;

    /**
     * Constructor which calls the default parent constructor
     * and loads settings
     */
    public function __construct($userId)
    {
        parent::__construct($userId);
    }

    //
    // Methods
    //

    /**
     * Set setup status to false
     */
    public function setNotSetup()
    {
        $this->setup = false;
    }

    function setSetupFinished()
    {
        $sql = "
	    UPDATE
	    	users
	    SET
				user_setup=1
	    WHERE
	    	user_id='" . $this->id . "';";
        dbquery($sql);
        $this->setup = true;
    }

    function setPassword($oldPassword, $newPassword1, $newPassword2, &$returnMsg)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $res = dbquery("
			SELECT
				user_password
			FROM
				users
			WHERE
				user_id=" . $this->id . "
			LIMIT 1;");
        $arr = mysql_fetch_row($res);
        if (validatePasswort($oldPassword, $arr[0])) {
            /** @var UserSittingRepository $userSittingRepository */
            $userSittingRepository = $app[UserSittingRepository::class];
            if (!$userSittingRepository->existsEntry($this->id, md5($_POST['user_password1']))) {
                if ($newPassword1 == $newPassword2) {
                    if (strlen($newPassword1) >= $config->getInt('password_minlength')) {
                        if (dbquery("
								UPDATE
									users
								SET
									user_password='" . saltPasswort($newPassword1) . "'
								WHERE
									user_id='" . $this->id . "'
								;")) {
                            Log::add(3, Log::INFO, "Der Spieler [b]" . $this->nick . "[/b] &auml;ndert sein Passwort!");
                            $mail = new Mail("Passwortänderung", "Hallo " . $this->nick . "\n\nDies ist eine Bestätigung, dass du dein Passwort für deinen Account erfolgreich geändert hast!\n\nSolltest du dein Passwort nicht selbst geändet haben, so nimm bitte sobald wie möglich Kontakt mit einem Game-Administrator auf: http://www.etoa.ch/kontakt");
                            $mail->send($this->email);
                            $this->addToUserLog("settings", "{nick} ändert sein Passwort.", 0);
                            return true;
                        }
                    } else {
                        $returnMsg = "Das Passwort muss mindestens " . $config->getInt('password_minlength') . " Zeichen lang sein!";
                    }
                } else {
                    $returnMsg = "Die Eingaben m&uuml;ssen identisch sein!";
                }
            } else {
                $returnMsg = "Das Passwort darf nicht identisch mit dem Sitterpasswort sein!";
            }
        } else {
            $returnMsg = "Dein altes Passwort stimmt nicht mit dem gespeicherten Passwort &uuml;berein!";
        }
        return false;
    }
}
