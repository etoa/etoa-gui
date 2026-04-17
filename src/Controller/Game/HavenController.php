<?php

namespace EtoA\Controller\Game;

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Entity\Planet;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetLaunch;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Form\Type\Core\CountType;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        private readonly EntityRepository $entityRepository,
        private readonly BookmarkRepository $bookmarkRepository
    )
    {}

    #[Route('/game/haven/show', name: 'game.haven.show')]
    public function show(Request $request, SerializerInterface $serializer):Response
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
    public function action(Request $request,SerializerInterface $serializer): Response
    {
        $session = $request->getSession();
        if($session->has('fleetLaunch')) {
            $fleetLaunch = $serializer->deserialize($session->get('fleetLaunch'), FleetLaunch::class, 'json', [
                'allow_extra_attributes' => true,
            ]);

            //workaround since the serializer does not work with the factory from entity class
            $targetEntity = $fleetLaunch->getTargetEntity();
            $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates($targetEntity->getCell()->getSx(),$targetEntity->getCell()->getSy(),$targetEntity->getCell()->getCx(),$targetEntity->getCell()->getCy(), $targetEntity->getPos()));

            $fleetLaunch->setTargetEntity($entity);

            $this->fleetLaunchService->setFleetLaunch($fleetLaunch);
        }

        if ($this->fleetLaunchService->getFleetLaunch()->isShipsFixed()) {
            if ($this->fleetLaunchService->checkTarget()) {
                if ($this->fleetLaunchService->getFleetLaunch()->isShipsFixed() && $this->fleetLaunchService->checkTarget()) {
                    $form = $this->createFormBuilder()
                        ->add('actions', ChoiceType::class, [
                            'choices'  => $this->fleetLaunchService->getAllowedActions(),
                            'expanded' => true,
                            'choice_label' => function (FleetAction $choice): string {
                                return $choice->name();
                            },
                            'choice_value' => function (?FleetAction $choice): ?string {
                                return $choice?->code();
                            },
                            'required' => true
                        ])
                        ->add('res1',TextType::class, [
                            'label' =>false,
                            'attr' => [
                                'size'=>12,
                            ],
                            'data'=>$this->fleetLaunchService->getFleetLaunch()->getLoadedRes(1),
                            'required' => false
                        ])
                        ->add('res2',TextType::class, [
                            'label' =>false,
                            'attr' => [
                                'size'=>12,
                            ],
                            'data'=>$this->fleetLaunchService->getFleetLaunch()->getLoadedRes(2),
                            'required' => true
                        ])
                        ->add('res3',TextType::class, [
                            'label' =>false,
                            'attr' => [
                                'size'=>12,
                            ],
                            'data'=>$this->fleetLaunchService->getFleetLaunch()->getLoadedRes(3),
                            'required' => true
                        ])
                        ->add('res4',TextType::class, [
                            'label' =>false,
                            'attr' => [
                                'size'=>12,
                            ],
                            'data'=>$this->fleetLaunchService->getFleetLaunch()->getLoadedRes(4),
                            'required' => true
                        ])
                        ->add('res5',TextType::class, [
                            'label' =>false,
                            'attr' => [
                                'size'=>12,
                            ],
                            'data'=>$this->fleetLaunchService->getFleetLaunch()->getLoadedRes(5),
                            'required' => true
                        ])
                        ->add('res6',TextType::class, [
                            'label' =>false,
                            'attr' => [
                                'size'=>12,
                            ],
                            'data' => 0,
                            'required' => true
                        ])
                        ->add('send', SubmitType::class, ['label' => 'Start'])
                        ->getForm()->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        if ($this->fleetLaunchService->setAction($form->get('actions')->getData()->code())) {
                            $fleet = $this->fleetLaunchService->getFleetLaunch();
                            if($form->get('actions')->getData()->code() === "fetch") {
                                $fleet->fetchResource(1, StringUtils::parseFormattedNumber($form->get('res1')->getData()));
                                $fleet->fetchResource(2, StringUtils::parseFormattedNumber($form->get('res2')->getData()));
                                $fleet->fetchResource(3, StringUtils::parseFormattedNumber($form->get('res3')->getData()));
                                $fleet->fetchResource(4, StringUtils::parseFormattedNumber($form->get('res4')->getData()));
                                $fleet->fetchResource(5, StringUtils::parseFormattedNumber($form->get('res5')->getData()));
                                $fleet->fetchResource(6, StringUtils::parseFormattedNumber($form->get('res6')->getData()));
                                $fleet->loadResource(1, 0);
                                $fleet->loadResource(2, 0);
                                $fleet->loadResource(3, 0);
                                $fleet->loadResource(4, 0);
                                $fleet->loadResource(5, 0);
                                $fleet->loadPeople(0);
                            }
                            else {
                                $fleet->loadResource(1, StringUtils::parseFormattedNumber($form->get('res1')->getData()));
                                $fleet->loadResource(2, StringUtils::parseFormattedNumber($form->get('res2')->getData()));
                                $fleet->loadResource(3, StringUtils::parseFormattedNumber($form->get('res3')->getData()));
                                $fleet->loadResource(4, StringUtils::parseFormattedNumber($form->get('res4')->getData()));
                                $fleet->loadResource(5, StringUtils::parseFormattedNumber($form->get('res5')->getData()));
                            }

                            $duration = $fleet->getDistance() / $fleet->getSpeed();    // Calculate duration
                            $duration *= 3600;    // Convert to seconds
                            $duration = ceil($duration);
                            $maxTime = 0.0;
                            if (count($fleet->getAFleets()) > 0) {
                                $maxTime = $fleet->getAFleets()[0]->getLandTime() - time() - $fleet->getTimeLaunchLand() - $fleet->getDuration1();
                            }

                            //check for alliance+time to join
                            if (($duration < $maxTime) || $form->get('actions')->getData()->code() !== "alliance" || $maxTime < 0) {
                                $this->fleetLaunchService->setFleetLaunch($fleet);
                                if ($fid = $this->fleetLaunchService->launch()) {
                                    $ac = FleetAction::createFactory($form->get('actions')->getData()->code());

                                    // bugfix - check for alliance added by river
                                    if ($form->get('actions')->getData()->code() === "alliance" && $fleet->getLeader() == 0 && $fleet->getOwner()->getAlliance() && count($form->get('msgUser')->getData()) > 0) {

                                        /** @var \EtoA\Message\MessageRepository $messageRepository */
                                        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                                        /** @var AllianceRepository $allianceRepository */
                                        $allianceRepository = $app[AllianceRepository::class];

                                        $fleetOwnerAlliance = $allianceRepository->getAlliance($fleet->owner->allianceId());
                                        $subject = "Allianzangriff (" . $fleet->targetEntity . ")";
                                        $text = "[b]Angriffsdaten:[/b][table][tr][td]Flottenkennzeichen:[/td][td]" . $fleetOwnerAlliance->tag . "-" . $fid . "[/td][/tr][tr][td]Flottenleader:[/td][td]" . $fleet->owner->nick . "[/td][/tr][tr][td]Zielplanet:[/td][td]" . $fleet->targetEntity . "[/td][/tr][tr][td]Ankunftszeit:[/td][td]" . date("d.m.y, H:i:s", $fleet->landTime) . "[/td][/tr][/table]" . $form['message_text'];
                                        foreach ($form['msgUser'] as $uid) {
                                            $messageRepository->sendFromUserToUser(
                                                (int)$fleet->ownerId(),
                                                (int)$uid,
                                                $subject,
                                                $text,
                                                6,
                                                $fid
                                            );
                                        }
                                    }

                                    $this->fleetLaunchService->getFleetLaunch()->setShipsFixed(false);

                                    return $this->render('game/haven/launch.html.twig', [
                                        'fleet' => $this->fleetLaunchService->getFleetLaunch(),
                                        'color' => FleetAction::$attitudeColor[$ac->attitude()],
                                        'name' => $ac->name()
                                    ]);
                                }
                            }
                        }
                    }

                    return $this->render('game/haven/action.html.twig', [
                        'fleet' => $this->fleetLaunchService->getFleetLaunch(),
                        'form' => $form
                    ]);
                }
            }
        }


        return $this->redirectToRoute('game.haven.show');
    }



}