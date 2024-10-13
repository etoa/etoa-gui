<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceRepository $allianceRepository,
        private readonly ConfigurationService $config,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/game/alliance/info/{id}', name: 'game.alliance.info')]
    public function info($id): Response {
        $infoAlliance = $this->allianceRepository->getAlliance($id);
        if ($infoAlliance !== null) {
            $cu = $this->getUser()->getData();
            if ($cu->getAllianceId() !== $infoAlliance->id) {
                $this->allianceRepository->addVisit($infoAlliance->id, true);
            }
        }

        return $this->render('game/alliance/info.html.twig',[
            'allianceRepository' => $this->allianceRepository,
            'allianceDiplomacyRepository' => $this->allianceDiplomacyRepository,
            'id' => $id,
            'userRepository' => $this->userRepository
        ]);
    }

}