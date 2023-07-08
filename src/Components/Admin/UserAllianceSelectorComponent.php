<?php

namespace EtoA\Components\Admin;

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_user_alliance_selector')]
class UserAllianceSelectorComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public string $name;

    #[LiveProp]
    public string $rankName;

    #[LiveProp(writable: true)]
    public int $userAllianceId = 0;
    
    #[LiveProp(writable: true)]
    public int $userAllianceRankId = 0;

    public function __construct(
        private readonly AllianceRepository     $allianceRepository,
        private readonly AllianceRankRepository $allianceRankRepository,
    )
    {
    }

    public function getAllianceNamesWithTags(): array
    {
        return $this->allianceRepository->getAllianceNamesWithTags();
    }

    public function getRanks(): array
    {
        if ($this->userAllianceId == 0) {
            return [];
        }

        $ranks = [];
        foreach ($this->allianceRankRepository->getRanks($this->userAllianceId) as $rank) {
            $ranks[$rank->id] = $rank->name;
        }
        return $ranks;
    }
}