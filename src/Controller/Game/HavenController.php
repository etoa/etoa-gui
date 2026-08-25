<?php

namespace EtoA\Controller\Game;

use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Planet;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetLaunch;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Form\Type\Core\CountType;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class HavenController extends AbstractGameController
{
    public function __construct(
        private readonly ShipTransformRepository $shipTransformRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly ShipListRepository $shipListRepository,
        private readonly FleetLaunchService $fleetLaunchService,
        private readonly DefenseRepository $defenseRepository
    )
    {}

    #[Route('/game/haven/show', name: 'game.haven.show')]
    public function show(Request $request, SerializerInterface $serializer):Response
    {
        $error = $this->baseCheck();

        // A target given by another page (e.g. a favourite) is preselected in the target step
        $targetId = $request->query->getInt('target');
        if ($targetId > 0) {
            $request->getSession()->set('havenTarget', $targetId);
        }

        if(!$error) {
            /** @var Planet $cp */
            $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
            $hasMobileObjects = $this->shipTransformRepository->hasUserTransformableObjects($this->getUser()->getData(), $cp);

            $ships = $this->shipListRepository->getEntityShipCounts($this->getUser()->getData(), $cp);

            // Per-ship effective speed + bonus breakdown (race/specialist/speed techs) for the tooltip
            $shipSpeedInfo = [];
            foreach ($ships as $shipListItem) {
                $shipSpeedInfo[$shipListItem->getShip()->getId()] = $this->fleetLaunchService->getShipSpeedBreakdown($shipListItem->getShip(), $this->getUser()->getData());
            }

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
                    try {
                        //dd($this->fleetLaunchService->getFleetLaunch());
                        $serilizedData = $serializer->serialize($this->fleetLaunchService->getFleetLaunch(), 'json', [
                            'circular_reference_handler' => function ($object) {
                                if(is_a($object,AbstractEntity::class)) {
                                    return $object->getEntity()->getId();
                                }
                                return $object->getId();
                            },
                            'ignored_attributes' => ['__initializer__', '__cloner__', '__isInitialized__', 'lazyObjectState', 'lazyObjectInitialized', 'lazyObjectAsInitialized'],
                            'skip_null_values' => true,
                        ]);

                    }
                    catch (PartialDenormalizationException $e) {
                        $violations = new ConstraintViolationList();

                        /** @var NotNormalizableValueException $exception */
                        foreach ($e->getErrors() as $exception) {
                            $message = sprintf('The type must be one of "%s" ("%s" given).', implode(', ', $exception->getExpectedTypes()), $exception->getCurrentType());
                            $parameters = [];
                            if ($exception->canUseMessageForUser()) {
                                $parameters['hint'] = $exception->getMessage();
                            }
                            $violations->add(new ConstraintViolation($message, '', $parameters, null, $exception->getPath(), null));
                        }
                    }

                    $session->set('fleetLaunch',$serializer->serialize($this->fleetLaunchService->getFleetLaunch(), 'json',[
                        'circular_reference_handler' => function ($object) {
                            if (method_exists($object, 'getId')) {
                                return $object->getId();
                            }
                            return null;
                        },
                        'ignored_attributes' => ['__initializer__', '__cloner__', '__isInitialized__', 'lazyObjectState', 'lazyObjectInitialized', 'lazyObjectAsInitialized'],
                        'skip_null_values' => true,
                    ]));

                    //dd($session->get('fleetLaunch'));

                    return $this->redirectToRoute('game.haven.target');
                }

                return $this->render('game/error.html.twig',[
                    'msg' => $this->fleetLaunchService->getFleetLaunch()->getError(),
                    'path' => $this->generateUrl('game.haven.show'),
                    'headline' => 'Raumschiffhafen'
                ]);
            }

            return $this->render('game/haven/show.html.twig',[
                'hasMobileObjects'=>$hasMobileObjects,
                'fleetLaunch' => $this->fleetLaunchService->getFleetLaunch(),
                'form' => $form,
                'planet' => $cp,
                'fleetActions' => FleetAction::getAll(),
                'shipSpeedInfo' => $shipSpeedInfo
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
        /** @var Planet $cp */
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $user = $this->getUser()->getData();

        if (!$this->shipTransformRepository->hasUserTransformableObjects($user, $cp)) {
            return $this->render('game/haven/transship.html.twig', [
                'hasMobileObjects' => false,
                'planet' => $cp,
                'form' => null,
                'msg' => ['info' => 'Keine mobilen Anlagen vorhanden!'],
            ]);
        }

        // Build transform lookup maps: which defense packs into which carrier ship and vice versa
        $transformByDefense = [];
        $transformByShip = [];
        foreach ($this->shipTransformRepository->findAll() as $transform) {
            if ($transform->getDefense() !== null) {
                $transformByDefense[$transform->getDefense()->getId()] = $transform;
            }
            if ($transform->getShip() !== null) {
                $transformByShip[$transform->getShip()->getId()] = $transform;
            }
        }

        // The player's transformable defenses and ships on the current planet
        $loadMobileLists = function () use ($user, $cp, $transformByDefense, $transformByShip): array {
            $defenses = array_values(array_filter(
                $this->defenseRepository->getEntityDefenseCounts($user, $cp),
                fn ($item) => $item->getDefense() !== null && isset($transformByDefense[$item->getDefense()->getId()])
            ));
            $ships = array_values(array_filter(
                $this->shipListRepository->getEntityShipCounts($user, $cp),
                fn ($item) => $item->getShip() !== null && isset($transformByShip[$item->getShip()->getId()])
            ));

            return [$defenses, $ships];
        };

        $buildForm = function (array $mobileDefenses, array $mobileShips) {
            return $this->createFormBuilder(['defenses' => $mobileDefenses, 'ships' => $mobileShips])
                ->add('defenses', CollectionType::class, ['entry_type' => CountType::class, 'label' => false])
                ->add('dtransform', SubmitType::class, ['label' => 'Verladen'])
                ->add('ships', CollectionType::class, ['entry_type' => CountType::class, 'label' => false])
                ->add('stransform', SubmitType::class, ['label' => 'Ausladen und installieren'])
                ->getForm();
        };

        [$mobileDefenses, $mobileShips] = $loadMobileLists();
        $form = $buildForm($mobileDefenses, $mobileShips)->handleRequest($request);

        $msg = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $transformedCounter = 0;

            if ($form->get('dtransform')->isClicked()) {
                // Pack defenses onto carrier ships (defense -> ship)
                foreach ($form->get('defenses')->all() as $child) {
                    /** @var \EtoA\Entity\DefenseListItem $item */
                    $item = $child->getData();
                    $packcount = (int) min(max(0, StringUtils::parseFormattedNumber($child->get('count')->getData())), $item->getCount());
                    if ($packcount > 0) {
                        $removed = $this->defenseRepository->removeDefense($item, $packcount);
                        $this->shipListRepository->addShip($transformByDefense[$item->getDefense()->getId()]->getShip(), $removed, $user, $cp);
                        $transformedCounter += $packcount;
                    }
                }
                if ($transformedCounter > 0) {
                    $msg['success'] = "$transformedCounter Verteidigungsanlagen wurden verladen!";
                }
            } elseif ($form->get('stransform')->isClicked()) {
                // Unpack carrier ships into defenses (ship -> defense)
                foreach ($form->get('ships')->all() as $child) {
                    /** @var \EtoA\Entity\ShipListItem $item */
                    $item = $child->getData();
                    $packcount = (int) min(max(0, StringUtils::parseFormattedNumber($child->get('count')->getData())), $item->getCount());
                    if ($packcount > 0) {
                        $removed = $this->shipListRepository->removeShips($item, $packcount);
                        $this->defenseRepository->addDefense($transformByShip[$item->getShip()->getId()]->getDefense(), $removed, $user, $cp);
                        $transformedCounter += $packcount;
                    }
                }
                if ($transformedCounter > 0) {
                    $msg['success'] = "$transformedCounter Verteidigungsanlagen wurden installiert!";
                }
            }

            // Re-fetch fresh counts and rebuild the form for display after the mutation
            [$mobileDefenses, $mobileShips] = $loadMobileLists();
            $form = $buildForm($mobileDefenses, $mobileShips);
        }

        return $this->render('game/haven/transship.html.twig', [
            'hasMobileObjects' => true,
            'planet' => $cp,
            'form' => $form,
            'msg' => $msg,
        ]);
    }

    private function baseCheck():string
    {
        $error = '';
        // A (non-empty) verification key means the e-mail address is NOT yet confirmed
        if ($this->getUser()->getData()->getVerificationKey()) {
            $error = 'Solange deine E-Mail Adresse nicht bestätigt ist, kannst du keine Flotten versenden!';
        }
        else {
            if(!$this->fleetLaunchService->checkHaven()) {
               $error = $this->fleetLaunchService->getFleetLaunch()->getError();
            }
        }

        return $error;
    }

    #[Route('/game/haven/target', name: 'game.haven.target')]
    public function target(Request $request,SerializerInterface $serializer): Response
    {
        $session = $request->getSession();
        if($session->has('fleetLaunch')) {
            $this->fleetLaunchService->setFleetLaunch($serializer->deserialize($session->get('fleetLaunch'), FleetLaunch::class, 'json', [
                'allow_extra_attributes' => true,
            ]));
        }

        if($this->fleetLaunchService->getFleetLaunch()->isShipsFixed()) {
            return $this->render('game/haven/target.html.twig',[
                'fleetLaunch' => $this->fleetLaunchService->getFleetLaunch(),
                'planet' => $this->planetRepository->find($session->get('cpid')),
                'serializedFleetLaunch' => $serializer->serialize($this->fleetLaunchService->getFleetLaunch(), 'json', [
                    'circular_reference_handler' => function ($object) {
                        if(is_a($object,AbstractEntity::class)) {
                            return $object->getEntity()->getId();
                        }
                        return $object->getId();
                    }
                ])
            ]);
        }

        return $this->redirectToRoute('game.haven.show');
    }

    #[Route('/game/haven/action', name: 'game.haven.action')]
    public function action(Request $request, SerializerInterface $serializer): Response
    {
        $session = $request->getSession();
        if (!$session->has('fleetLaunch')) {
            return $this->redirectToRoute('game.haven.show');
        }

        /** @var FleetLaunch $fleetLaunch */
        $fleetLaunch = $serializer->deserialize($session->get('fleetLaunch'), FleetLaunch::class, 'json', [
            'allow_extra_attributes' => true,
        ]);

        // The interactive action selection, cargo loading and launch are handled by
        // the HavenAction live component (game/haven/action.html.twig).
        if (!$fleetLaunch->isShipsFixed() || $fleetLaunch->getTargetEntity() === null) {
            return $this->redirectToRoute('game.haven.show');
        }

        return $this->render('game/haven/action.html.twig', [
            'planet' => $this->planetRepository->find($session->get('cpid')),
        ]);
    }

}