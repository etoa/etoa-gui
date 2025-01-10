<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceNewsRepository;use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TownhallController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceNewsRepository $allianceNewsRepository,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository
    )
    {
    }

    #[Route('/game/townhall', name: 'game.townhall')]
    public function show(): Response {
        // Neuste Nachrichten
        $publicNews = $this->allianceNewsRepository->findBy(['toAlliance'=>null],['date'=>'DESC'],10);

        // Internal messages
        $internalNews = $this->allianceNewsRepository->findBy(['toAlliance'=>$this->getUser()->getData()->getAlliance()],['date'=>'DESC']);

        // Bündnisse
        $bnds = $this->allianceDiplomacyRepository->findBy(['level'=>AllianceDiplomacyLevel::BND_CONFIRMED],['date'=>'DESC'],15);

        // Kriege
        $wars = $this->allianceDiplomacyRepository->findBy(['level'=>AllianceDiplomacyLevel::WAR],['date'=>'DESC']);

        // Friedensabkommen
        $peace = $this->allianceDiplomacyRepository->findBy(['level'=>AllianceDiplomacyLevel::PEACE],['date'=>'DESC']);

        return $this->render('game/townhall/townhall.html.twig',[
            'publicNews' => $publicNews,
            'internalNews' => $internalNews,
            'bnds' => $bnds,
            'wars' => $wars,
            'peace' => $peace
        ]);
    }
}