<?PHP

use EtoA\Ranking\GameStatsGenerator;
use EtoA\User\UserOnlineStatsRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserStats;
use Pimple\Container;

/**
 * Update user statistics
 */
class GenerateUserStatisticsTask implements IPeriodicTask
{
    private UserRepository $userRepository;
    private UserSessionRepository $userSessionRepository;
    private UserOnlineStatsRepository $userOnlineStatsRepository;
    private UserStats $userStats;

    public function __construct(Container $app)
    {
        $this->userRepository = $app[UserRepository::class];
        $this->userSessionRepository = $app[UserSessionRepository::class];
        $this->userOnlineStatsRepository = $app[UserOnlineStatsRepository::class];
        $this->userStats = $app[UserStats::class];
    }

    function run()
    {
        $userCount = $this->userRepository->count();
        $sessionCount = $this->userSessionRepository->count();
        $this->userOnlineStatsRepository->addEntry($userCount, $sessionCount);
        $this->userStats->generateImage(GameStatsGenerator::USER_STATS_FILE);
        $this->userStats->generateXml(GameStatsGenerator::XML_INFO_FILE);
        return "User-Statistik: " . $sessionCount . " User online, " . $userCount . " User registriert";
    }

    public static function getDescription()
    {
        return "User Statistik aktualisieren";
    }
}
