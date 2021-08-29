<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;

class AllianceWingService
{
    private AllianceHistoryRepository $allianceHistoryRepository;
    private MessageRepository $messageRepository;
    private AllianceRepository $allianceRepository;

    public function __construct(AllianceHistoryRepository $allianceHistoryRepository, MessageRepository $messageRepository, AllianceRepository $allianceRepository)
    {
        $this->allianceHistoryRepository = $allianceHistoryRepository;
        $this->messageRepository = $messageRepository;
        $this->allianceRepository = $allianceRepository;
    }

    public function addWingRequest(Alliance $alliance, Alliance $wing): bool
    {
        if ($wing->motherRequest > 0 || $wing->motherId > 0) {
            return false;
        }

        $this->allianceRepository->setMotherOrRequest($wing->id, 0, $alliance->id);
        $this->messageRepository->createSystemMessage($alliance->founderId, MessageCategoryId::ALLIANCE, "Wing-Anfrage", "Die Allianz [b]" . $wing->nameWithTag . "[/b] möchte eure Allianz als Wing hinzufügen. [page alliance action=wings]Anfrage beantworten[/page]");

        return true;
    }

    public function acceptWingRequest(Alliance $alliance, Alliance $wing): bool
    {
        if ($alliance->id !== $wing->motherRequest) {
            return false;
        }

        $this->allianceRepository->setMotherOrRequest($wing->id, $alliance->id, 0);
        $this->allianceHistoryRepository->addEntry($alliance->id, "[b]" . $wing->nameWithTag . "[/b] wurde als neuer Wing hinzugefügt.");
        $this->allianceHistoryRepository->addEntry($wing->id, "Wir sind nun ein Wing von [b]" . $alliance->nameWithTag . "[/b]");

        $this->messageRepository->createSystemMessage($alliance->founderId, MessageCategoryId::ALLIANCE, "Neuer Wing", "Die Allianz [b]" . $wing->nameWithTag . "[/b] ist nun ein Wing von [b]" . $alliance->nameWithTag . "[/b]");

        $wing->motherRequest = 0;
        $wing->motherId = $alliance->id;

        return true;
    }

    public function cancelWingRequest(Alliance $alliance, Alliance $wing): bool
    {
        if ($alliance->id !== $wing->motherRequest) {
            return false;
        }

        $this->allianceRepository->setMotherOrRequest($wing->id, 0, 0);
        $this->messageRepository->createSystemMessage($wing->founderId, MessageCategoryId::ALLIANCE, "Wing-Anfrage zurückgezogen", "Die Allianz [b]" . $wing->nameWithTag . "[/b] hat die Wing-Anfrage zurückgezogen.");

        return true;
    }

    public function declineWingRequest(Alliance $alliance, Alliance $wing): bool
    {
        if ($alliance->id !== $wing->motherRequest) {
            return false;
        }

        $this->allianceRepository->setMotherOrRequest($wing->id, 0, 0);

        $this->messageRepository->createSystemMessage($alliance->founderId ,MessageCategoryId::ALLIANCE, "Wing-Anfrage zurückgewiesen", "Die Allianz [b]" . $wing->nameWithTag . "[/b] hat die Wing-Anfrage zurückgewiesen.");

        $wing->motherRequest = 0;
        $wing->motherId = 0;

        return true;
    }

    public function removeWing(Alliance $alliance, Alliance $wing): bool
    {
        if ($alliance->id !== $wing->motherId) {
            return false;
        }

        $this->allianceRepository->setMotherOrRequest($wing->id, 0, 0);
        $this->allianceHistoryRepository->addEntry($alliance->id, $wing->nameWithTag . " ist nun kein Wing mehr von uns");
        $this->allianceHistoryRepository->addEntry($wing->id, "Wir sind nun kein Wing mehr von [b]" . $alliance->nameWithTag . "[/b]");

        $this->messageRepository->createSystemMessage($alliance->founderId, MessageCategoryId::ALLIANCE, "Wing aufgelöst", "Die Allianz [b]" . $wing->nameWithTag . "[/b] ist kein Wing mehr von [b]" . $alliance->nameWithTag . "[/b]");
        $this->messageRepository->createSystemMessage($wing->founderId, MessageCategoryId::ALLIANCE, "Wing aufgelöst", "Die Allianz [b]" . $wing->nameWithTag . "[/b] ist kein Wing mehr von [b]" . $alliance->nameWithTag . "[/b]");

        return true;
    }
}
