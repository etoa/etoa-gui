<?PHP

use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Support\Mail\MailSenderService;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;

/**
 * Provides methods for accessing user information
 * and changing it.
 */
class User implements \EtoA\User\UserInterface
{
    const tableName = "users";

    // Fields
    protected $id;    // Database record id
    protected $nick; // Unicke nickname
    protected $setup; // Cheker if account is propperly setup
    protected $isValid; // Checker if class instance belongs to valid user
    protected $maskMatrix; // Matrix for the "fog of war" effect in the space map
    protected $realName;
    protected $pw;
    protected $npc;
    protected $email;
    protected $emailFix;
    protected $d_email;   //Dual E-mail
    protected $d_realName; //Dual name
    protected $lastOnline;
    protected $acttime;
    protected $points;
    protected $blocked_from;
    protected $blocked_to;
    protected $ban_reason;
    protected $ban_admin_id;
    protected $hmode_from;
    protected $hmode_to;
    protected $holiday = null;
    protected $locked = null;
    protected $deleted;
    protected $monitored;
    protected $registered;
    protected $chatadmin;
    protected $admin;
    protected $developer;
    protected $ip;
    protected $visits;
    protected $profileImage;
    protected $profileText;
    protected $profileBoardUrl;
    protected $signature;
    protected $avatar;
    protected $allianceRankId;
    protected $allianceLeave;
    protected $rank;
    protected $rankHighest;
    protected $specialistId;
    protected $specialistTime;
    protected $boostBonusProduction;
    protected $boostBonusBuilding;
    protected $ghost;
    protected $lastInvasion;
    protected $allianceShippoints;
    protected $changedMainPlanet;

    protected $sittingDays;

    // Sub-objects and their id's
    protected $raceId;
    protected $allianceId;
    protected $rating = null;
    protected $properties = null;
    protected $changedFields;

    protected $isVerified;
    protected $verificationKey;

    protected $dmask;

    /**
     * The constructor initializes and loads
     * all importand data about this user
     */
    public function __construct($id)
    {
        $this->isValid = false;

        if ($id instanceof \EtoA\User\User) {
            $user = $id;
        } else {
            global $app;
            /** @var UserRepository $userRepository */
            $userRepository = $app[UserRepository::class];
            $user = $userRepository->getUser($id);
        }

        if ($user !== null) {
            $this->id = $user->id;
            $this->nick = $user->nick;
            $this->pw = $user->password;
            $this->realName = $user->name;
            $this->email = $user->email;
            $this->emailFix = $user->emailFix;

            $this->d_email = $user->dualEmail;
            $this->d_realName = $user->dualName;

            $this->lastOnline = $user->logoutTime;
            $this->acttime = null;
            $this->points = $user->points;

            $this->blocked_from = $user->blockedFrom;
            $this->blocked_to = $user->blockedTo;
            $this->ban_reason = $user->banReason;
            $this->ban_admin_id = $user->banAdminId;

            $this->hmode_from = $user->hmodFrom;
            $this->hmode_to = $user->hmodTo;

            $this->deleted = $user->deleted;

            $this->monitored = ($user->observe != "") ? true : false;

            $this->registered = $user->registered;
            $this->setup = $user->setup;
            $this->chatadmin = $user->chatAdmin == 1 ? true : false;
            $this->admin = $user->admin == 1 ? true : false;
            $this->npc = $user->npc == 1 ? true : false;
            $this->developer = $user->admin == 2 ? true : false;
            $this->ghost = $user->ghost;

            $this->ip = $_SERVER['REMOTE_ADDR'];

            $this->visits = $user->visits;

            $this->profileImage = $user->profileImage;
            $this->profileText = $user->profileText;
            $this->profileBoardUrl = $user->profileBoardUrl;
            $this->signature = $user->signature;
            $this->avatar = $user->avatar;


            $this->allianceId = $user->allianceId;
            $this->allianceRankId = $user->allianceRankId;
            $this->allianceLeave = $user->allianceLeave;

            $this->sittingDays = $user->sittingDays;

            $this->rank = $user->rank;
            $this->rankHighest = $user->rankHighest;

            $this->specialistId = $user->specialistId;
            $this->specialistTime = $user->specialistTime;

            $this->boostBonusProduction = $user->boosBonusProduction;
            $this->boostBonusBuilding = $user->boosBonusBuilding;

            $this->lastInvasion = $user->lastInvasion;

            $this->changedMainPlanet = $user->userChangedMainPlanet;

            $this->raceId = $user->raceId;

            $this->allianceShippoints = $user->allianceShipPoints;

            $this->changedFields = array();

            $this->isVerified = ($user->verificationKey == '');
            $this->verificationKey = $user->verificationKey;

            $this->isValid = true;
        } else {
            $this->id = $id;
            $this->nick = "Niemand";

            $this->points = 0;
            $this->acttime = time();
            $this->blocked_from = 0;
            $this->blocked_to = 0;
            $this->hmode_from = 0;
            $this->hmode_to = 0;
            $this->deleted = 0;
            $this->allianceId = 0;

            $this->rank = 0;
            $this->rankHighest = 0;

            $this->specialistId = 0;
            $this->specialistTime = 0;

            $this->lastInvasion = 0;

            $this->changedMainPlanet = 0;

            $this->raceId = 0;

            $this->isValid = false;
        }
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

    /**
     * Getter
     */
    public function __get($key)
    {
        try {
            if (!property_exists($this, $key))
                throw new EException("Property $key existiert nicht in " . __CLASS__);

            if ($key == "holiday" && $this->holiday == null) {
                $this->holiday = ($this->hmode_from != 0 && $this->hmode_to != 0) ? true : false;
            }
            if ($key == "locked" && $this->locked == null) {
                $this->locked = ($this->blocked_from < time() && $this->blocked_to > time()) ? true : false;
            }
            if ($key == "acttime" && $this->acttime == null) {
                $this->acttime = $this->loadLastAction();
            }
            return $this->$key;
        } catch (EException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * toString Function
     */
    function __toString()
    {
        return $this->nick;
    }

    final public function isSetup()
    {
        return $this->setup;
    }

    public function setSetup()
    {
        $this->setup = true;
    }

    public function setNotSetup()
    {
        $this->setup = false;
    }

    final public function allianceId(): int
    {
        return (int) $this->allianceId;
    }

    public function isInactiv()
    {
        if (!$this->admin) {
            if (!$this->holiday) {
                if ($this->lastOnline < time() - USER_INACTIVE_SHOW * 86400) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isNPC()
    {
        if ($this->npc)
            return true;
        return false;
    }

    public function isInactivLong()
    {
        if (!$this->admin) {
            if (!$this->holiday) {
                if ($this->lastOnline < time() - USER_INACTIVE_LONG * 86400) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns whether this user has changed their main planet
     * @return boolean
     */
    public function changedMainPlanet()
    {
        return $this->changedMainPlanet;
    }

    //
    // Methods
    //

    function loadLastAction()
    {
        // TODO
        global $app;

        /** @var UserSessionRepository $userSessionRepository */
        $userSessionRepository = $app[UserSessionRepository::class];

        $userSession = $userSessionRepository->find($this->id);
        if ($userSession !== null) {
            return $userSession->timeAction;
        }

        $sessionLogs = $userSessionRepository->getSessionLogs(UserSessionSearch::create()->userId($this->id), 1);
        if (count($sessionLogs) > 0) {
            return $sessionLogs[0]->timeAction;
        }

        return 1;
    }

    public function detailLink()
    {
        return "<a href=\"?page=userinfo&amp;id=" . $this->id . "\">" . $this->__toString() . "</a>";
    }

    public function isUserNoobProtected(User $u)
    {
        // check whether user points are outside limits
        // or this user or opponent is below minimum attack threshold
        return ($this->points * USER_ATTACK_PERCENTAGE > $u->points || $this->points / USER_ATTACK_PERCENTAGE < $u->points)
            || ($this->points <= USER_ATTACK_MIN_POINTS)
            || ($u->points <= USER_ATTACK_MIN_POINTS);
    }

    public function canAttackUser(User $u)
    {
        global $app;

        /** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
        $allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

        // neither does $u->locked, wtf

        // att allowed if war is active
        // or att allowed if target user is not noob protected
        // or att allowed if target user is inactive
        // or att allowed if target user is locked
        if ($this->allianceId() > 0 && $u->allianceId() > 0) {

            return $allianceDiplomacyRepository->isAtWar($this->allianceId(), $u->allianceId())
                || !$this->isUserNoobProtected($u)
                || $u->isInactiv()
                || $u->__get('locked')
                || $u->isNPC();
        } else {
            return !$this->isUserNoobProtected($u)
                || $u->isInactiv()
                || $u->__get('locked')
                || $u->isNPC();
        }
    }

    public function canAttackPlanet(Planet $p)
    {
        // Planet is attackable if user is attackable
        // or if last owner == this owner (invade time threshold)
        return $this->canAttackUser($p->owner()) || $this->id == $p->lastUserCheck();
    }
}
