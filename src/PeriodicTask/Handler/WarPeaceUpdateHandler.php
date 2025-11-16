<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyPoints;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceDiplomacySearch;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\WarPeaceUpdateTask;
use EtoA\User\UserRatingService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class WarPeaceUpdateHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly UserRatingService $userRatingService,
        private readonly AllianceHistoryRepository $allianceHistoryRepository,
        private readonly MessageRepository $messageRepository,
        private readonly ConfigurationService $config,
        private readonly MessageCategoryRepository $messageCategoryRepository
    ){}

    public function __invoke(WarPeaceUpdateTask $task): SuccessResult
    {
        $time = time();

        // Assign diplomacy points for pacts
        $pacts = $this->allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::BND_CONFIRMED)->pendingPoints()->dateBefore($time - AllianceDiplomacyPoints::POINTS_MIN_PACT_DURATION));
        foreach ($pacts as $diplomacy) {
            $reason = "Bündnis " . $diplomacy->getAlliance1()->getId() . " mit " . $diplomacy->getAlliance2()->getId();
            $this->userRatingService->addDiplomacyRating(
                $diplomacy->getDiplomat(),
                $diplomacy->getPoints(),
                $reason
            );

            $this->allianceDiplomacyRepository->updateDiplomacy($diplomacy, AllianceDiplomacyLevel::PEACE, $diplomacy->getName(), 0);
        }

        $cnt = 0;

        // Wars
        $wars = $this->allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::WAR)->dateBefore($time - 3600 * $this->config->getInt('alliance_war_time')));
        $nr = count($wars);
        if ($nr > 0) {
            foreach ($wars as $war) {
                // Add log
                $text = "Der Krieg zwischen [b][" . $war->getAlliance1()->getTag() . "] " . $war->getAlliance1()->getName() . "[/b] und [b][" . $war->getAlliance2()->getTag() . "] " . $war->getAlliance2()->getName() . "[/b] ist zu Ende! Es folgt eine Friedenszeit von " . round($this->config->param1Int('alliance_war_time')) . " Stunden.";
                $this->allianceHistoryRepository->addEntry($war->getAlliance1(), $text);
                $this->allianceHistoryRepository->addEntry($war->getAlliance2(), $text);

                // Send message to leader
                $this->messageRepository->createSystemMessage($war->getAlliance1()->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Krieg beendet", $text . " Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");
                $this->messageRepository->createSystemMessage($war->getAlliance2()->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Krieg beendet", $text . " Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");

                // Assing diplomacy points
                $this->userRatingService->addDiplomacyRating(
                    $war->getDiplomat(),
                    $war->getPoints(),
                    "Krieg " . $war->getAlliance1()->getId() . " gegen " . $war->getAlliance2()->getId()
                );

                $this->allianceDiplomacyRepository->updateDiplomacy($war, AllianceDiplomacyLevel::PEACE, $war->getName(), 0, $time);
            }
            $cnt += $nr;
        }

        // Peaces
        $peace = $this->allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::PEACE)->dateBefore($time - $this->config->param1Int('alliance_war_time') * 3600));
        $nr = count($peace);
        if ($nr > 0) {
            foreach ($peace as $diplomacy) {
                // Add log
                $text = "Der Friedensvertrag zwischen [b][" . $diplomacy->getAlliance1()->getTag() . "] " . $diplomacy->getAlliance1()->getName() . "[/b] und [b][" . $diplomacy->getAlliance2()->getTag() . "] " . $diplomacy->getAlliance2()->getName() . "[/b] ist abgelaufen. Ihr könnt einander nun wieder Krieg erklären.";
                $this->allianceHistoryRepository->addEntry($diplomacy->getAlliance1(), $text);
                $this->allianceHistoryRepository->addEntry($diplomacy->getAlliance2(), $text);

                // Send message to leader
                $this->messageRepository->createSystemMessage($diplomacy->getAlliance1()->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Friedensvertrag abgelaufen", $text);
                $this->messageRepository->createSystemMessage($diplomacy->getAlliance2()->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Friedensvertrag abgelaufen", $text);

                $this->allianceDiplomacyRepository->deleteDiplomacy($diplomacy);
            }
            $cnt += $nr;
        }

        return SuccessResult::create(\sprintf("%s diplomatische Beziehungen (Krieg / Frieden) aktualisiert", $cnt));
    }
}
