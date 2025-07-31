<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Planet;
use EtoA\Fleet\FleetLaunch;
use EtoA\Form\Type\Core\CountType;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HavenController extends AbstractGameController
{
    private bool $hasMobileObjects;
    private Planet $cp;

    public function __construct(
        private readonly ShipTransformRepository $shipTransformRepository,
        private readonly PlanetRepository $planetRepository,
        private FleetLaunch $fleetLaunch,
        private readonly ShipListRepository $shipListRepository
    )
    {}

    #[Route('/game/haven/show', name: 'game.haven.show')]
    public function show(Request $request):Response
    {

        serialize($this->fleetLaunch);
dd('test');
        $error = $this->baseCheck();

        if(!$error) {
            /** @var Planet $cp */
            $this->cp = $this->planetRepository->find($request->getSession()->get('cpid'));
            $this->hasMobileObjects = $this->shipTransformRepository->hasUserTransformableObjects($this->getUser()->getData(), $this->cp);

            $ships = $this->shipListRepository->getEntityShipCounts($this->getUser()->getData(), $this->cp);

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
                $this->fleetLaunch->resetShips();
                foreach ($form->get('ships')->all() as $shipListItem) {
                    $cnt = $shipListItem->get('count')->getData();
                    if ($cnt) {
                        $this->fleetLaunch->addShip($shipListItem->getData(), $cnt);
                    }
                }
                if($this->fleetLaunch->fixShips()) {
                    $session = $request->getSession();
                    $session->set('fleetLaunch',serialize($this->fleetLaunch));
                    return $this->redirectToRoute('game.haven.target');
                }

                return $this->render('game/error.html.twig',[
                    'msg' => $this->fleetLaunch->error(),
                    'path' => $this->generateUrl('game.overview'),
                    'headline' => 'Raumschiffhafen'
                ]);
            }

            return $this->render('game/haven/show.html.twig',[
                'hasMobileObjects'=>$this->hasMobileObjects,
                'fleetLaunch' => $this->fleetLaunch,
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
            if(!$this->fleetLaunch->checkHaven()) {
               $error = $this->fleetLaunch->error();
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
        $this->fleetLaunch = unserialize($session->get('fleetLaunch'));

        dd($this->fleetLaunch);

        $form = $this->createFormBuilder()
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
            dd('test');
        }

        return $this->render('game/haven/target.html.twig',[
            'hasMobileObjects'=>$this->hasMobileObjects,
            'fleetLaunch' => $this->fleetLaunch,
            'form' => $form
        ]);
    }
}