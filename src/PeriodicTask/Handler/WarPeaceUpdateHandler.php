<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyPoints;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceDiplomacySearch;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\WarPeaceUpdateTask;
use EtoA\User\UserRatingService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class WarPeaceUpdateHandler implements MessageHandlerInterface
{
    private AllianceDiplomacyRepository $allianceDiplomacyRepository;
    private UserRatingService $userRatingService;
    private AllianceHistoryRepository $allianceHistoryRepository;
    private MessageRepository $messageRepository;
    private AllianceRepository $allianceRepository;
    private ConfigurationService $config;

    public function __construct(AllianceDiplomacyRepository $allianceDiplomacyRepository, UserRatingService $userRatingService, AllianceHistoryRepository $allianceHistoryRepository, MessageRepository $messageRepository, AllianceRepository $allianceRepository, ConfigurationService $config)
    {
        $this->allianceDiplomacyRepository = $allianceDiplomacyRepository;
        $this->userRatingService = $userRatingService;
        $this->allianceHistoryRepository = $allianceHistoryRepository;
        $this->messageRepository = $messageRepository;
        $this->allianceRepository = $allianceRepository;
        $this->config = $config;
    }

    public function __invoke(WarPeaceUpdateTask $task): SuccessResult
    {
        $time = time();

        // Assign diplomacy points for pacts
        $pacts = $this->allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::BND_CONFIRMED)->pendingPoints()->dateBefore($time - AllianceDiplomacyPoints::POINTS_MIN_PACT_DURATION));
        foreach ($pacts as $diplomacy) {
            $reason = "Bündnis " . $diplomacy->alliance1Id . " mit " . $diplomacy->alliance2Id;
            $this->userRatingService->addDiplomacyRating(
                $diplomacy->diplomatId,
                $diplomacy->points,
                $reason
            );

            $this->allianceDiplomacyRepository->updateDiplomacy($diplomacy->id, AllianceDiplomacyLevel::PEACE, $diplomacy->name, 0);
        }

        $cnt = 0;

        // Wars
        $wars = $this->allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::WAR)->dateBefore($time - 3600 * $this->config->getInt('alliance_war_time')));
        $nr = count($wars);
        if ($nr > 0) {
            foreach ($wars as $war) {
                // Add log
                $text = "Der Krieg zwischen [b][" . $war->alliance1Tag . "] " . $war->alliance1Name . "[/b] und [b][" . $war->alliance2Tag . "] " . $war->alliance2Name . "[/b] ist zu Ende! Es folgt eine Friedenszeit von " . round($this->config->param1Int('alliance_war_time')) . " Stunden.";
                $this->allianceHistoryRepository->addEntry($war->alliance1Id, $text);
                $this->allianceHistoryRepository->addEntry($war->alliance2Id, $text);

                // Send message to leader
                $this->messageRepository->createSystemMessage($this->allianceRepository->getFounderId($war->alliance1Id), MessageCategoryId::ALLIANCE, "Krieg beendet", $text . " Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");
                $this->messageRepository->createSystemMessage($this->allianceRepository->getFounderId($war->alliance2Id), MessageCategoryId::ALLIANCE, "Krieg beendet", $text . " Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");

                // Assing diplomacy points
                $this->userRatingService->addDiplomacyRating(
                    $war->diplomatId,
                    $war->points,
                    "Krieg " . $war->alliance1Id . " gegen " . $war->alliance2Id
                );

                $this->allianceDiplomacyRepository->updateDiplomacy($war->id, AllianceDiplomacyLevel::PEACE, $war->name, 0, $time);
            }
            $cnt += $nr;
        }

        // Peaces
        $peace = $this->allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::PEACE)->dateBefore($time - $this->config->param1Int('alliance_war_time') * 3600));
        $nr = count($peace);
        if ($nr > 0) {
            foreach ($peace as $diplomacy) {
                // Add log
                $text = "Der Friedensvertrag zwischen [b][" . $diplomacy->alliance1Tag . "] " . $diplomacy->alliance1Name . "[/b] und [b][" . $diplomacy->alliance2Tag . "] " . $diplomacy->alliance2Name . "[/b] ist abgelaufen. Ihr könnt einander nun wieder Krieg erklären.";
                $this->allianceHistoryRepository->addEntry($diplomacy->alliance1Id, $text);
                $this->allianceHistoryRepository->addEntry($diplomacy->alliance2Id, $text);

                // Send message to leader
                $this->messageRepository->createSystemMessage($this->allianceRepository->getFounderId($diplomacy->alliance1Id), MessageCategoryId::ALLIANCE, "Friedensvertrag abgelaufen", $text);
                $this->messageRepository->createSystemMessage($this->allianceRepository->getFounderId($diplomacy->alliance2Id), MessageCategoryId::ALLIANCE, "Friedensvertrag abgelaufen", $text);

                $this->allianceDiplomacyRepository->deleteDiplomacy($diplomacy->id);
            }
            $cnt += $nr;
        }

        return SuccessResult::create(\sprintf("%s diplomatische Beziehungen (Krieg / Frieden) aktualisiert", $cnt));
    }
}
