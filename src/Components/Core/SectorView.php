<?php

namespace EtoA\Components\Core;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\SectorMapRenderer;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent(template: 'components/sector_view.html.twig')]
class SectorView extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly CellRepository $cellRepository,
        private readonly ConfigurationService $config,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityRepository $entityRepository,
        private readonly UserRepository $userRepository
    )
    {}

    #[LiveProp(writable: true, onUpdated: 'updateNeighbours')]
    public string $xy;
    #[LiveProp]
    public array $neighbours;
    #[LiveProp]
    public int $userId;

    private function calcNeighbours(string $xy):array
    {
        $sx_num = $this->config->param1Int('num_of_sectors');
        $sy_num = $this->config->param2Int('num_of_sectors');

        list($sx, $sy) = explode(",", $xy);

        // Validate coordinates
        if ($sx > $sx_num) {
            $sx = $sx_num;
        }
        if ($sy > $sy_num) {
            $sy = $sy_num;
        }
        if ($sx < 1) {
            $sx = 1;
        }
        if ($sy < 1) {
            $sy = 1;
        }

        return [
            'sx_tl' => $sx - 1,
            'sx_tc' => $sx,
            'sx_tr' => $sx + 1,
            'sx_ml' => $sx - 1,
            'sx_mr' => $sx + 1,
            'sx_bl' => $sx - 1,
            'sx_bc' => $sx,
            'sx_br' => $sx + 1,
            'sy_tl' => $sy + 1,
            'sy_tc' => $sy + 1,
            'sy_tr' => $sy + 1,
            'sy_ml' => $sy,
            'sy_mr' => $sy,
            'sy_bl' => $sy - 1,
            'sy_bc' => $sy - 1,
            'sy_br' => $sy - 1,
            'sx_num' => $sx_num,
            'sy_num' => $sy_num,
            'sx' => $sx,
            'sy' => $sy
        ];
    }

    public function updateNeighbours(): void
    {
        $this->neighbours = $this->calcNeighbours($this->xy);
    }

    #[PreMount]
    public function preMount(array $data): array
    {
        $data['userId'] = $this->getUser()->getId();
        $data['neighbours'] = $this->calcNeighbours($data['xy']);

        return $data;
    }

    #[LiveAction]
    public function renderMap(): string
    {
        $sectorMap = new SectorMapRenderer($this->config->param1Int('num_of_cells'), $this->config->param2Int('num_of_cells'));
        $sectorMap->enableRuler(true);
        $sectorMap->enableTooltips(true);
        $sectorMap->setUserCellIDs($this->cellRepository->getUserCellIds($this->userId));
        $sectorMap->setImpersonatedUser($this->userRepository->getUser($this->userId));
        $sectorMap->setCellUrl('cell/');
        $sectorMap->setUndiscoveredCellJavaScript("xajax_launchExplorerProbe('##ID##')");
        return $sectorMap->render($this->neighbours['sx'], $this->neighbours['sy'], $this->userUniverseDiscoveryService, $this->entityRepository);
    }
}