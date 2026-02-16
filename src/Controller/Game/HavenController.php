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
            return $this->render('game/haven/target.html.twig',[
                'fleetLaunch' => $this->fleetLaunchService->getFleetLaunch(),
                'serializedFleetLaunch' => serialize($this->fleetLaunchService->getFleetLaunch())
            ]);
        }

        return $this->redirectToRoute('game.haven.show');
    }

    #[Route('/game/haven/action', name: 'game.haven.action')]
    public function action(Request $request): Response
    {
        $session = $request->getSession();
        if($session->has('fleetLaunch'))
            dd(unserialize($session->get('fleetLaunch')));
            $this->fleetLaunchService->setFleetLaunch(unserialize($session->get('fleetLaunch')));

        if ($this->fleetLaunchService->getFleetLaunch()->isShipsFixed() && $this->fleetLaunchService->checkTarget()) {
            return $this->render('game/haven/action.html.twig',[
                'fleet' => $this->fleetLaunchService->getFleetLaunch()
            ]);
        }

        return $this->redirectToRoute('game.haven.show');
    }
}