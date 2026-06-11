<?php

namespace EtoA\Components\Core;

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\DTO\MissileLaunchDto;
use EtoA\Entity\Entity;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Missile\MissileFlightRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(template: 'components/missile_target.html.twig')]
class MissileTarget extends AbstractGameController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    private readonly Request $request;

    #[LiveProp]
    public string $info = 'Wähle bitte ein Ziel...';

    #[LiveProp]
    public string $speed = '-';

    #[LiveProp]
    public string $duration = '-';

    #[LiveProp]
    public string $distance = '-';

    #[LiveProp]
    public array $color = [];

    #[LiveProp]
    public bool $launchable = true;

    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public ?int $csx = null;

    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public ?int $csy = null;

    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public ?int $ccx = null;

    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public ?int $ccy = null;

    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public ?int $psp = null;

    #[LiveProp(writable: true, onUpdated: 'updateByFavorite')]
    public ?int $bookmark = null;

    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public array $count = [];

    public function __construct(
        private readonly ShipTransformRepository      $shipTransformRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly ShipListRepository           $shipListRepository,
        private readonly FleetLaunchService           $fleetLaunchService,
        private readonly EntityRepository             $entityRepository,
        private readonly BookmarkRepository           $bookmarkRepository,
        private readonly RequestStack                 $requestStack,
        private readonly ConfigurationService         $configurationService,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityService                $entityService,
        private readonly SerializerInterface          $serializer,
        private readonly MissileRepository            $missileRepository,
        private readonly MissileFlightRepository      $missileFlightRepository
    )
    {
    }

    /*
        For some reason form values don't update properly after entering a missile number.
        TODO: refactor, maybe use a DTO
    */
    protected function instantiateForm(): FormInterface
    {
        $entity = $this->getCurrentEntity();
        $obj = $this;
        $missilelist = $this->getMissilelist();

        $user = $entity->getPlanet()->getUser();

        $this->csx = $this->csx ?? $entity->getCell()->getSx();
        $this->csy = $this->csy ?? $entity->getCell()->getSy();
        $this->ccx = $this->ccx ?? $entity->getCell()->getCx();
        $this->ccy = $this->ccy ?? $entity->getCell()->getCy();
        $this->psp = $this->psp ?? $entity->getPos();

        $form = $this->createFormBuilder()
            ->add(
                'csx', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Sektor X-Koordinate",
                    'data-model' => "debounce(1000)|csx"
                ],
                'data' => $this->csx
            ])
            ->add(
                'csy', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2')",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Sektor Y-Koordinate",
                    'data-model' => "debounce(1000)|csy"
                ],
                'data' => $this->csy
            ])
            ->add(
                'ccx', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Cell X-Koordinate",
                    'data-model' => "debounce(1000)|ccx"
                ],
                'data' => $this->ccx
            ])
            ->add(
                'ccy', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Cell Y-Koordinate",
                    'data-model' => "debounce(1000)|ccy"
                ],
                'data' => $this->ccy
            ])
            ->add(
                'psp', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Position",
                    'data-model' => "debounce(1000)|psp"
                ],
                'data' => $this->psp
            ])
            ->add('bookmark', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(static function () use ($obj, $user): array {
                    $choices = [];
                    foreach ($obj->planetRepository->findBy(['user' => $user]) as $planet) {
                        $choices['Eigene Planeten'][] = $planet->getEntity();
                    }
                    foreach ($obj->bookmarkRepository->findBy(['user' => $user]) as $bookmark) {
                        $choices['Favoriten'][] = $bookmark->getEntity();
                    }

                    return $choices;
                }),
                'choice_label' => function (?Entity $entity): string {
                    return $entity->toString();
                },
                'choice_value' => 'id',
                'placeholder' => 'Wählen...',
                'required' => false,
                'attr' => [
                    'data-model' => "bookmark"
                ],
                'data' => $this->bookmark
            ]);

        //TODO: use collection type instead
        foreach ($missilelist as $item) {
            if ($item->getMissile()->isLaunchable() && $item->getCount() > 0) {
                if (!array_key_exists($item->getId(), $this->count))
                    $this->count[$item->getId()] = 0;
                $form = $form->add(
                    $item->getId(), TextType::class, [
                    'label' => false,
                    'attr' => [
                        'size' => 4,
                        'onkeyup' => "FormatNumber(this.id,this.value,'" . $item->getCount() . "','','')",
                        'data-model' => "count[" . $item->getId() . "]",
                        'data-action' => "live#action",
                        'data-live-action-param' => "debounce(1000)|updateByField"
                    ],
                ]);
            }
        }

        return $form->add('launch', SubmitType::class, [
            'label' => 'Starten',
            'attr' => [
                'data-action' => 'live#action:prevent',
                'data-live-action-param' => 'launch'
            ]
        ])
            ->getForm();
    }

    #[ExposeInTemplate]
    public function getMissilelist(): array
    {
        $entity = $this->getCurrentEntity();
        return $entity->getPlanet()->getMissilelist()->getValues();
    }

    public function updateByFavorite($previousValue): void
    {
        if ($this->bookmark && $this->bookmark !== $previousValue) {
            $entity = $this->entityRepository->find($this->bookmark);

            $this->updateValues($entity);
        }
    }

    #[LiveAction]
    public function updateByField(): void
    {
        if ($this->csx > 0 && $this->csy > 0 && $this->ccx > 0 && $this->ccy > 0 && $this->psp >= 0) {
            $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates($this->csx, $this->csy, $this->ccx, $this->ccy, $this->psp));

            $this->updateValues($entity);
        }
    }

    #[LiveAction]
    public function launch()
    {
        $launch = array();
        $speed = 0;

        foreach ($this->count as $id => $v) {
            $number = StringUtils::parseFormattedNumber($v);

            if ($number > 0) {
                $ownEntity = $this->getCurrentEntity();
                $item = $this->missileRepository->findOneBy(['id' => $id, 'entity' => $ownEntity->getPlanet()]);
                if ($item) {
                    $t = min($item->getCount(), $number);
                    if ($t > 0) {
                        if ($speed === 0) {
                            $speed = $item->getMissile()->getSpeed();
                        } else {
                            $speed = min($item->getMissile()->getSpeed(), $speed);
                        }

                        $launchItem = new MissileLaunchDto();
                        $launchItem->setMissile($item->getMissile());
                        $launchItem->setCount($t);
                        $launch[] = $launchItem;
                    }
                }
            }
        }

        if (count($launch) > 0) {
            $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates($this->csx, $this->csy, $this->ccx, $this->ccy, $this->psp));

            $targetCoordinates = new EntityCoordinates($entity->getCell()->getSx(), $entity->getCell()->getSy(), $entity->getCell()->getCx(), $entity->getCell()->getCy(), $entity->getPos());
            $sourceCoordinates = new EntityCoordinates($ownEntity->getCell()->getSx(), $ownEntity->getCell()->getSy(), $ownEntity->getCell()->getCx(), $ownEntity->getCell()->getCy(), $ownEntity->getPos());

            $distanceValue = $this->entityService->distanceByCoords($sourceCoordinates, $targetCoordinates);
            $timeforflight = $distanceValue / $speed * 3600;

            // Save flight
            $this->missileFlightRepository->startFlight($ownEntity->getPlanet(), $entity->getPlanet(), $timeforflight, $launch);

            foreach ($launch as $item) {
                // Update list
                $this->missileRepository->removeMissile($item->getMissile(), $item->getCount(),  $ownEntity->getPlanet());
            }
            $this->addFlash('success','Raketen gestartet!');

            return $this->redirectToRoute('game.missiles');
        } else {
            $this->addFlash('error','Raketen konnten nicht gestartet werden, keine Raketen gewählt!');
        }
    }

    private function updateValues(?Entity $entity): void
    {
        // Total selected missiles
        $total = 0;
        $speed = 0;
        $range = 0;
        $this->launchable = true;

        $ownEntity = $this->getCurrentEntity();

        foreach ($this->count as $id => $v) {
            $item = $this->missileRepository->findOneBy(['id' => $id, 'entity' => $ownEntity->getPlanet()]);

            if ($item?->getMissile()->isLaunchable()) {
                $number = StringUtils::parseFormattedNumber($v);

                // Calc speed
                if ($speed === 0 && $number > 0) {
                    $speed = $item->getMissile()->getSpeed();
                    $total += min($number, $item->getCount());
                } elseif ($number > 0) {
                    $speed = min($item->getMissile()->getSpeed(), $speed);
                    $total += $total += min($number, $item->getCount());
                }

                // Calc range
                if ($range === 0 && $number > 0) {
                    $range = $item->getMissile()->getRange();
                } elseif ($number > 0) {
                    $range = min($item->getMissile()->getRange(), $range);
                }
            }
        }

        if ($total > 0) {
            if ($entity?->getCode() === EntityType::PLANET) {
                $planet = $entity->getPlanet();
                $this->info = $planet->getName() ? '<b>Planet:</b> ' . $planet->getName() : '<i>Unbenannter Planet</i>';

                if ($planet->getUser()) {
                    $this->info .= ' <b>Besitzer:</b> ' . $planet->getUser()->getNick();
                    if ($ownEntity->getPlanet() === $planet) {
                        $this->info .= ' (Eigener Planet)';
                        $this->color['targetinfo'] = '#f00';
                        $this->launchable = false;
                    } else {
                        $this->color['targetinfo'] = '#0f0';
                    }
                } else {
                    $this->launchable = false;
                    $this->color['targetinfo'] = '#f00';
                }

                $targetCoordinates = new EntityCoordinates($entity->getCell()->getSx(), $entity->getCell()->getSy(), $entity->getCell()->getCx(), $entity->getCell()->getCy(), $entity->getPos());
                $sourceCoordinates = new EntityCoordinates($ownEntity->getCell()->getSx(), $ownEntity->getCell()->getSy(), $ownEntity->getCell()->getCx(), $ownEntity->getCell()->getCy(), $ownEntity->getPos());

                $distanceValue = $this->entityService->distanceByCoords($sourceCoordinates, $targetCoordinates);
                $timeforflight = $distanceValue / $speed * 3600;
                $distance = sprintf('%s AE', StringUtils::formatNumber($distanceValue));

                $this->duration = StringUtils::formatTimespan($timeforflight);
                $this->distance = $distance;

                if ($distanceValue === -1) {
                    $this->color['distance'] = '#f00';
                    $this->launchable = false;
                } elseif ($distanceValue > $range) {
                    $this->color['distance'] = '#f00';
                    $this->distance .= ' (zu weit entfernt, ' . StringUtils::formatNumber($range) . ' max)';
                    $this->launchable = false;
                } else {
                    $this->color['distance'] = '#0f0';
                }

                $this->speed = round($speed, 2) . " AE/h";
            } else {
                $this->info = 'Hier existiert kein Planet!';
                $this->distance = '';
                $this->duration = '';
                $this->color['targetinfo'] = '#f00';
                $this->launchable = false;
            }
        } else {
            $this->info = 'Keine Raketen gewählt!';
            $this->launchable = false;
        }
    }

    private function getCurrentEntity(): Entity
    {
        $request = $this->requestStack->getCurrentRequest();
        return $this->entityRepository->find($request?->getSession()?->get('cpid'));
    }
}