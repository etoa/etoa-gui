<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\GenerateUserStatisticsTask;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\User\UserOnlineStatsRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserStats;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateUserStatisticsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserSessionRepository $userSessionRepository,
        private readonly UserOnlineStatsRepository $userOnlineStatsRepository,
        private readonly UserStats $userStats,
        private readonly string $cacheDir
    )
    {}

    public function __invoke(GenerateUserStatisticsTask $task): SuccessResult
    {
        $userCount = $this->userRepository->count([]);
        $sessionCount = $this->userSessionRepository->count([]);
        $this->userOnlineStatsRepository->addEntry($userCount, $sessionCount);
        $this->userStats->generateImage($this->cacheDir .GameStatsGenerator::USER_STATS_FILE);
        $this->userStats->generateXml($this->cacheDir . GameStatsGenerator::XML_INFO_FILE);

        return SuccessResult::create("User-Statistik: " . $sessionCount . " User online, " . $userCount . " User registriert");
    }
}
