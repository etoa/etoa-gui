<?php

namespace EtoA\Controller\Game;

use EtoA\Entity\Planet;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechtreeController extends AbstractGameController
{


    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly RequestStack $requestStack
    )
    {
    }

    #[Route('/game/techtree/general', name: 'game.techtree.general')]
    public function general(): Response
    {
        return $this->render('game/techtree/general.html.twig', [
            'planet' => $this->getCurrentPlanet()
        ]);
    }

    #[Route('/game/techtree/building', name: 'game.techtree.building')]
    public function building(): Response
    {
        return $this->render('game/techtree/building.html.twig');
    }

    #[Route('/game/techtree/tech', name: 'game.techtree.tech')]
    public function tech(): Response
    {
        return $this->render('game/techtree/tech.html.twig');
    }

    #[Route('/game/techtree/ships', name: 'game.techtree.ships')]
    public function ships(): Response
    {
        return $this->render('game/techtree/ships.html.twig');
    }

    #[Route('/game/techtree/defense', name: 'game.techtree.defense')]
    public function defense(): Response
    {
        return $this->render('game/techtree/defense.html.twig');
    }

    #[Route('/game/techtree/missile', name: 'game.techtree.missile')]
    public function missile(): Response
    {
        return $this->render('game/techtree/missile.html.twig');
    }

    private function getCurrentPlanet(): Planet
    {
        $request = $this->requestStack->getCurrentRequest();
        return $this->planetRepository->find($request->getSession()->get('cpid'));
    }
}