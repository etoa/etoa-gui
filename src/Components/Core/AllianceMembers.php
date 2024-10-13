<?php

namespace EtoA\Components\Core;

use EtoA\Design\DesignsService;
use EtoA\Support\StringUtils;
use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent(template: 'components/alliance_members.html.twig')]
class AllianceMembers
{
    public function __construct(
        private readonly UserStatRepository $userStatRepository
    ){}

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $allianceId;
    #[LiveProp(writable: true)]
    public bool $show = false;
    #[LiveProp]
    public array $entries;

    #[PreMount]
    public function preMount(array $data): array
    {
        $search = UserStatSearch::points()->allianceId($data['allianceId']);
        $data['entries'] = $this->userStatRepository->searchStats($search);
        return $data;
    }
}