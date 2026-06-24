<?php

namespace EtoA\Controller\Game;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\CommonMark\ConverterInterface;

class ChangelogController extends AbstractGameController
{
    public function __construct(
        private readonly ConverterInterface $converter,
        private string                      $projectDir
    )
    {
    }

    #[Route('/game/changelog', name: 'game.changelog')]
    public function list(): Response
    {
        $changelogFile = $this->projectDir."/Changelog_public.md";
        if (is_file($changelogFile)) {
            $data = $this->converter->convert(file_get_contents($changelogFile));
            return $this->render('game/changelog/overview.html.twig', [
                'data' => $data
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Changelog nicht verfügbar!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Changelog'
        ]);
    }
}