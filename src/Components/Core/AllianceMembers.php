<?php

namespace EtoA\Components\Core;

use EtoA\Alliance\AllianceRepository;
use EtoA\Entity\Alliance;
use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(template: 'components/alliance_members.html.twig')]
class AllianceMembers
{
    public function __construct(
        private readonly UserStatRepository $userStatRepository,
        private readonly AllianceRepository $allianceRepository
    ){}

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $allianceId;
    #[LiveProp(writable: true)]
    public bool $show = false;
    #[LiveProp]
    public array $entries = [];

    #[LiveAction]
    public function showMembers(): void
    {
        $search = UserStatSearch::points()->allianceId($this->allianceId);
        $this->show = true;
        $this->entries = $this->userStatRepository->searchStats($search);
    }

    #[ExposeInTemplate]
    public function alliance():Alliance
    {
        return $this->allianceRepository->find($this->allianceId);
    }
}