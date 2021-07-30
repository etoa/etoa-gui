<?PHP

/**
 * Provides methods for accessing
 * the current logged in user
 */
class CurrentUser extends User
{
    protected $property;

    public function __construct($userId)
    {
        parent::__construct($userId);
    }
}
