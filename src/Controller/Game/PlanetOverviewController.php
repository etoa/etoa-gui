<?php

namespace EtoA\Controller\Game;

use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlanetOverviewController extends AbstractGameController
{
    public function __construct(
        private readonly PlanetRepository $planetRepository
    )
    {
    }

    #[Route('/game/planetoverview/overview', name: 'game.planetoverview.overview')]
    public function overview(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        return $this->render('game/planetoverview/overview_overview.html.twig',[
            'planet' => $cp
        ]);
    }

    #[Route('/game/planetoverview/name', name: 'game.planetoverview.name')]
    public function name(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        $form = $this->createFormBuilder($cp)
            ->add('name', TextType::class, [
                'attr' => [
                    'maxlength' => 15
                ],
                'constraints'=> new NotBlank([
                    'message' => 'Du musst einen Text eingeben!',
                ]),
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'cols' => 30
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { }

        return $this->render('game/planetoverview/overview_name.html.twig',[
            'planet' => $cp,
            'form' => $form
        ]);
    }

    #[Route('/game/planetoverview/fields', name: 'game.planetoverview.fields')]
    public function fields(Request $request): Response
    {

    }

    #[Route('/game/planetoverview/ships', name: 'game.planetoverview.ships')]
    public function ships(Request $request): Response
    {

    }

    #[Route('/game/planetoverview/defense', name: 'game.planetoverview.defense')]
    public function defense(Request $request): Response
    {

    }
}