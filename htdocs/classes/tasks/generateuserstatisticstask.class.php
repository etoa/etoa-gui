<?PHP

use EtoA\User\UserOnlineStatsRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use Pimple\Container;

/**
 * Update user statistics
 */
class GenerateUserStatisticsTask implements IPeriodicTask
{
    private UserRepository $userRepository;
    private UserSessionRepository $userSessionRepository;
    private UserOnlineStatsRepository $userOnlineStatsRepository;

    public function __construct(Container $app)
    {
        $this->userRepository = $app[UserRepository::class];
        $this->userSessionRepository = $app[UserSessionRepository::class];
        $this->userOnlineStatsRepository = $app[UserOnlineStatsRepository::class];
    }

    function run()
    {
        $userCount = $this->userRepository->count();
        $sessionCount = $this->userSessionRepository->count();
        $this->userOnlineStatsRepository->addEntry($userCount, $sessionCount);
        UserStats::generateImage(USERSTATS_OUTFILE);
        UserStats::generateXml(XML_INFO_FILE);
        return "User-Statistik: " . $sessionCount . " User online, " . $userCount . " User registriert";
    }

    function getDescription()
    {
        return "User Statistik aktualisieren";
    }
}
