<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;

class UserRatingService
{
    private UserRatingRepository $userRatingRepository;
    private LogRepository $logRepository;

    public function __construct(UserRatingRepository $userRatingRepository, LogRepository $logRepository)
    {
        $this->userRatingRepository = $userRatingRepository;
        $this->logRepository = $logRepository;
    }

    public function addTradeRating(int $userId, int $rating, bool $sell = true, string $reason = ""): void
    {
        $this->userRatingRepository->addTradeRating($userId, $rating, $sell);

        if ($reason != "") {
            $this->logRepository->add(LogFacility::RANKING, LogSeverity::INFO, "HP: Der Spieler " . $userId . " erhält " . $rating . " Handelspunkte. Grund: " . $reason);
        }
    }

    public function addDiplomacyRating(int $userId, int $rating, string $reason = ""): void
    {
        $this->userRatingRepository->addDiplomacyRating($userId, $rating);
        if ($reason != "") {
            $this->logRepository->add(LogFacility::RANKING, LogSeverity::INFO, "DP: Der Spieler " . $userId . " erhält " . $rating . " Diplomatiepunkte. Grund: " . $reason);
        }
    }
}
