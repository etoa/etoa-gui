<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\Mail\MailSenderService;
use EtoA\User\UserService;
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
}
