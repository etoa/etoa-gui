<?php

namespace EtoA\Controller\Game;

use EtoA\Support\FileUtils;
use EtoA\Text\TextRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\CommonMark\ConverterInterface;

class CreditsController extends AbstractGameController
{
    public function __construct(
        private readonly TextRepository     $textRepository,
        private readonly FileUtils $fileUtils
    )
    {
    }

    #[Route('/game/credits', name: 'game.credits')]
    public function list(): Response
    {
        $credits = $this->textRepository->find('credits');
        $thirdparty = $this->fileUtils->fetchJsonConfig("thirdparty.json");

        return $this->render('game/credits/overview.html.twig', [
            'credits' => $credits,
            'thirdparty' => $thirdparty
        ]);

    }
}