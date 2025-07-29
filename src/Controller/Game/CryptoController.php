<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetScanService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CryptoController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly ConfigurationService $configurationService,
        private readonly FleetScanService $fleetScanService,
        private readonly AllianceService $allianceService
    )
    {
    }

    #[Route('/game/crypto', name: 'game.crypto')]
    public function crypto(Request $request):Response
    {
        $cryptoCenterLevel = $this->allianceBuildListRepository->findOneBy(['alliance'=>$this->getUser()->getData()?->getAlliance(),'allianceBuilding'=>AllianceBuildingId::CRYPTO])?->getLevel();

        // Allg. deaktivierung
        if ($this->configurationService->getBoolean('crypto_enable')) {
            // Prüfen ob Gebäude gebaut ist
            if ($cryptoCenterLevel > 0) {
                $userAlliancePermission = $this->allianceService->getUserAlliancePermissions($this->getUser()->getData()->getAlliance(), $this->getUser()->getData());
                if($userAlliancePermission->hasRights(AllianceRights::CRYPTO_MINISTER)) {
                    return $this->render('game/crypto/crypto.html.twig',[
                        'level' => $cryptoCenterLevel,
                        'userCooldownDifference' => $this->fleetScanService->getUserCooldownDifference($this->getUser()->getData())
                    ]);
                }

                return $this->render('game/error.html.twig',[
                    'msg' => 'Du besitzt nicht die notwendigen Rechte!',
                    'path' => $this->generateUrl('game.overview'),
                    'headline' => 'Kryptocenter'
                ]);
            }

            return $this->render('game/error.html.twig',[
                'msg' => 'Das Kryptocenter wurde noch nicht gebaut!',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Kryptocenter'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Aufgrund eines intergalaktischen Moratoriums der Völkerföderation der Galaxie Andromeda
    sind sämtliche elektronischen Spionagetätigkeiten zurzeit nicht erlaubt!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Kryptocenter'
        ]);
    }
}