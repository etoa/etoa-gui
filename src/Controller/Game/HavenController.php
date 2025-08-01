<?php

namespace EtoA\Controller\Game;

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Form\Type\Core\CountType;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HavenController extends AbstractGameController
{
    public function __construct(
        private readonly ShipTransformRepository $shipTransformRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly ShipListRepository $shipListRepository,
        private readonly FleetLaunchService $fleetLaunchService,
        private readonly EntityRepository $entityRepository,
        private readonly BookmarkRepository $bookmarkRepository
    )
    {}

    #[Route('/game/haven/show', name: 'game.haven.show')]
    public function show(Request $request):Response
    {
        $error = $this->baseCheck();

        if(!$error) {
            /** @var Planet $cp */
            $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
            $hasMobileObjects = $this->shipTransformRepository->hasUserTransformableObjects($this->getUser()->getData(), $cp);

            $ships = $this->shipListRepository->getEntityShipCounts($this->getUser()->getData(), $cp);

            $form = $this->createFormBuilder(['ships'=>$ships])
                ->add('ships', CollectionType::class, [
                    'entry_type' => CountType::class,
                    'label' => false
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Weiter zur Zielauswahl >>>',
                    'attr' => [
                        'title' => "Wenn du die Schiffe ausgewählt hast, klicke hier um das Ziel auszuwählen"
                    ]
                ])

                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->fleetLaunchService->resetShips();
                foreach ($form->get('ships')->all() as $shipListItem) {
                    $cnt = $shipListItem->get('count')->getData();
                    if ($cnt) {
                        $this->fleetLaunchService->addShip($shipListItem->getData(), $cnt);
                    }
                }

                if($this->fleetLaunchService->fixShips()) {
                    $session = $request->getSession();
                    $session->set('fleetLaunch',serialize($this->fleetLaunchService->getFleetLaunch()));
                    return $this->redirectToRoute('game.haven.target');
                }

                return $this->render('game/error.html.twig',[
                    'msg' => $this->fleetLaunchService->getFleetLaunch()->getError(),
                    'path' => $this->generateUrl('game.have.show'),
                    'headline' => 'Raumschiffhafen'
                ]);
            }

            return $this->render('game/haven/show.html.twig',[
                'hasMobileObjects'=>$hasMobileObjects,
                'fleetLaunch' => $this->fleetLaunchService->getFleetLaunch(),
                'form' => $form
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => $error,
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Raumschiffhafen'
        ]);
    }

    #[Route('/game/haven/transship', name: 'game.haven.transship')]
    public function transship(Request $request):Response
    {

    }

    private function baseCheck():string
    {
        $error = '';
        if ($this->getUser()->getData()->getVerificationKey()) {
            if(!$this->fleetLaunchService->checkHaven()) {
               $error = $this->fleetLaunchService->getFleetLaunch()->getError();
            }
        }
        else {
            $error = 'Solange deine E-Mail Adresse nicht bestätigt ist, kannst du keine Flotten versenden!';
        }

        return $error;
    }

    #[Route('/game/haven/target', name: 'game.haven.target')]
    public function target(Request $request): Response
    {
        $session = $request->getSession();
        if($session->has('fleetLaunch'))
            $this->fleetLaunchService->setFleetLaunch(unserialize($session->get('fleetLaunch')));

        if($this->fleetLaunchService->getFleetLaunch()->isShipsFixed()) {
            if ($this->fleetLaunchService->getFleetLaunch()->getTargetEntity()) {
                //TODO: make symfony serializer work and refactor
                $entity = $this->planetRepository->find($this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getId());
                /*$csx = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getSx();
                $csy = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getSy();
                $ccx = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getCx();
                $ccy = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getCy();
                $psp = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getPos();*/
                $csx = $entity->getEntity()->getCell()->getSx();
                $csy = $entity->getEntity()->getCell()->getSy();
                $ccx = $entity->getEntity()->getCell()->getCx();
                $ccy = $entity->getEntity()->getCell()->getCy();
                $psp = $entity->getEntity()->getPos();
            } else {
                $entity = $this->entityRepository->find($this->fleetLaunchService->getFleetLaunch()->getSourceEntity()->getId());
                /*$csx = $this->fleetLaunchService->getFleetLaunch()->getSourceEntity()->getEntity()->getCell()->getSx();
                $csy = $this->fleetLaunchService->getFleetLaunch()->getSourceEntity()->getEntity()->getCell()->getSy();
                $ccx = $this->fleetLaunchService->getFleetLaunch()->getSourceEntity()->getEntity()->getCell()->getCx();
                $ccy = $this->fleetLaunchService->getFleetLaunch()->getSourceEntity()->getEntity()->getCell()->getCy();
                $psp = $this->fleetLaunchService->getFleetLaunch()->getSourceEntity()->getEntity()->getPos();*/
                $csx = $entity->getCell()->getSx();
                $csy = $entity->getCell()->getSy();
                $ccx = $entity->getCell()->getCx();
                $ccy = $entity->getCell()->getCy();
                $psp = $entity->getPos();
            }

            $obj = $this;
            $form = $this->createFormBuilder()
                ->add(
                    'csx',TextType::class, [
                    'label' => false,
                    'attr' => [
                        'onkeydown'=> "return nurZahlen(event);detectChangeRegister(this,'t2');",
                        'size'=>"1",
                        'maxlength'=>"1",
                        'title'=>"Sektor X-Koordinate",
                        'onkeyup'=>"if (detectChangeTest(this,'t2')) { showLoader('targetinfo');}"
                    ],
                    'mapped' => false,
                    'data' => $csx
                ])
                ->add(
                    'csy',TextType::class, [
                    'label' => false,
                    'attr' => [
                        'onkeydown'=> "return nurZahlen(event);detectChangeRegister(this,'t2')",
                        'size'=>"1",
                        'maxlength'=>"1",
                        'title'=>"Sektor X-Koordinate",
                        'onkeyup'=>"if (detectChangeTest(this,'t2')) {showLoader('targetinfo')}"
                    ],
                    'mapped' => false,
                    'data' => $csy
                ])
                ->add(
                    'ccx',TextType::class, [
                    'label' => false,
                    'attr' => [
                        'onkeydown'=> "return nurZahlen(event);detectChangeRegister(this,'t2');",
                        'size'=>"1",
                        'maxlength'=>"1",
                        'title'=>"Sektor X-Koordinate",
                        'onkeyup'=>"if (detectChangeTest(this,'t2')) {showLoader('targetinfo')}"
                    ],
                    'mapped' => false,
                    'data' => $ccx
                ])
                ->add(
                    'ccy',TextType::class, [
                    'label' => false,
                    'attr' => [
                        'onkeydown'=> "return nurZahlen(event);detectChangeRegister(this,'t2');",
                        'size'=>"1",
                        'maxlength'=>"1",
                        'title'=>"Sektor X-Koordinate",
                        'onkeyup'=>"if (detectChangeTest(this,'t2')) {showLoader('targetinfo')}"
                    ],
                    'mapped' => false,
                    'data' => $ccy
                ])
                ->add(
                    'psp',TextType::class, [
                    'label' => false,
                    'attr' => [
                        'onkeydown'=> "return nurZahlen(event);detectChangeRegister(this,'t2');",
                        'size'=>"1",
                        'maxlength'=>"1",
                        'title'=>"Sektor X-Koordinate",
                        'onkeyup'=>"if (detectChangeTest(this,'t2')) { showLoader('submitbutton');showLoader('targetinfo');}"
                    ],
                    'mapped' => false,
                    'data' => $psp
                ])
                ->add('bookmark', ChoiceType::class, [
                    'choice_loader' => new CallbackChoiceLoader(static function () use ($obj): array {
                        $choices = [];
                        foreach ($obj->planetRepository->findBy(['user'=>$obj->fleetLaunchService->getFleetLaunch()->getOwner()]) as $planet) {
                            $choices['Eigene Planeten'][] = $planet->getEntity();
                        }
                        foreach ($obj->bookmarkRepository->findBy(['user'=>$obj->fleetLaunchService->getFleetLaunch()->getOwner()]) as $bookmark) {
                            $choices['Favoriten'][] = $bookmark->getEntity();
                        }

                        return $choices;
                    }),
                    'choice_label' => function (?Entity $entity): string {
                        return $entity->toString();
                    },
                    'choice_value' => 'id',
                    'placeholder' => 'Wählen...',
                    'required' => false
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Weiter zur Aktionsauswahl >>>',
                ])

                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //dd($this->fleetLaunchService->getFleetLaunch());
            }

            return $this->render('game/haven/target.html.twig',[
                'fleetLaunch' => $this->fleetLaunchService->getFleetLaunch(),
                'form' => $form
            ]);
        }

        return $this->redirectToRoute('game.haven.show');
    }
}