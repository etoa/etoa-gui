<?php

namespace EtoA\Components\Core;

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Entity;
use EtoA\Fleet\FleetLaunch;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Form\Type\Core\MissileItemType;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

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
        private readonly MissileRepository            $missileRepository
    )
    {
    }

    protected function instantiateForm(): FormInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $entity = $this->entityRepository->find($request->getSession()->get('cpid'));
        $obj = $this;
        $missilelist = $entity->getPlanet()->getMissilelist()->getValues();
        $user = $entity->getPlanet()->getUser();

        $csx = $entity->getCell()->getSx();
        $csy = $entity->getCell()->getSy();
        $ccx = $entity->getCell()->getCx();
        $ccy = $entity->getCell()->getCy();
        $psp = $entity->getPos();

        return $this->createFormBuilder(['missilelist' => $missilelist])
            ->add(
                'csx', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Sektor X-Koordinate",
                    'data-action' => "live#action",
                    'data-live-action-param' => "debounce(2000)|updateByField"
                ],
                'mapped' => false,
                'data' => $csx
            ])
            ->add(
                'csy', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2')",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Sektor X-Koordinate",
                    'data-action' => "live#action",
                    'data-live-action-param' => "debounce(2000)|updateByField"
                ],
                'mapped' => false,
                'data' => $csy
            ])
            ->add(
                'ccx', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Sektor X-Koordinate",
                    'data-action' => "live#action",
                    'data-live-action-param' => "debounce(2000)|updateByField"
                ],
                'mapped' => false,
                'data' => $ccx
            ])
            ->add(
                'ccy', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Sektor X-Koordinate",
                    'data-action' => "live#action",
                    'data-live-action-param' => "debounce(2000)|updateByField"
                ],
                'mapped' => false,
                'data' => $ccy
            ])
            ->add(
                'psp', TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown' => "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size' => "2",
                    'maxlength' => "2",
                    'title' => "Sektor X-Koordinate",
                    'data-action' => "live#action",
                    'data-live-action-param' => "debounce(2000)|updateByField"
                ],
                'mapped' => false,
                'data' => $psp
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
                    'data-action' => "live#action",
                    'data-live-action-param' => "updateByFavorite"
                ]
            ])
            ->add('missilelist', CollectionType::class, [
                'entry_type' => MissileItemType::class,
            ])
            ->add('launch', SubmitType::class, [
                'label' => 'Starten',
                'attr' => [
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'launch'
                ]
            ])
            ->getForm();
    }

    #[LiveAction]
    public function updateByFavorite(): void
    {
        $entity = $this->entityRepository->find($this->formValues['bookmark']);

        $this->updateValues($entity);
    }

    #[LiveAction]
    public function updateByField(): void
    {
        $sx = $this->formValues['csx'];
        $sy = $this->formValues['csy'];
        $cx = $this->formValues['ccx'];
        $cy = $this->formValues['ccy'];
        $pos = $this->formValues['psp'];

        if ($sx > 0 && $sy > 0 && $cx > 0 && $cy > 0 && $pos >= 0) {
            $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));

            $this->updateValues($entity);
        }
    }

    #[LiveAction]
    public function launch(): RedirectResponse
    {
        $this->submitForm();/*
        if($this->fleetLaunch->getTargetEntity()) {
            $this->fleetLaunch->setSpeedPercent($this->formValues['speed']);
            $this->fleetLaunch->setLeader(null);
            $request = $this->requestStack->getCurrentRequest();
            $session = $request->getSession();

            $session->set('fleetLaunch',$this->serializer->serialize($this->fleetLaunch, 'json', [
                'circular_reference_handler' => function ($object) {
                    if(is_a($object,AbstractEntity::class)) {
                        return $object->getEntity()->getId();
                    }
                    return $object->getId();
                },
                'ignored_attributes' => ['__initializer__', '__cloner__', '__isInitialized__', 'lazyObjectState', 'lazyObjectInitialized', 'lazyObjectAsInitialized'],
                'skip_null_values' => true,
            ]));

            return $this->redirectToRoute('game.haven.action');
        }
*/
        return $this->redirectToRoute('game.haven.target');
    }

    private function updateValues(?Entity $entity): void
    {
        $this->submitForm();
        // Total selected missiles
        $total = 0;
        $speed = 0;
        $range = 0;

        foreach ($this->formValues['missilelist'] as $item) {
            // Calc speed
            if (!isset($speed) && $item['count'] > 0) {
                $speed = $item['speed'];
                $total += $item['count'];
            } elseif ($item['count'] > 0) {
                $speed = min($item['speed'], $speed);
                $total += $item['count'];
            }

            // Calc range
            if (!isset($range) && $item['count'] > 0) {
                $range = $item['range'];
            } elseif ($item['count'] > 0) {
                $range = min($item['range'], $range);
            }
        }

        if($total > 0) {
            if ($entity->getCode() === EntityType::PLANET) {
                $request = $this->requestStack->getCurrentRequest();
                $planet = $entity->getPlanet();
                $this->info = $planet->getName() ? '<b>Planet:</b> ' . $planet->getName() : '<i>Unbenannter Planet</i>';
                $ownEntity = $this->entityRepository->find($request->getSession()->get('cpid'));

                if ($planet->getUser()) {
                    $this->info .= '<b>Besitzer:</b> ' . $planet->getUser()->getNick();
                    if ($ownEntity->getPlanet() == $planet) {
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

                if ($entity) {
                    $targetCoordinates = new EntityCoordinates($entity->getCell()->getSx(), $entity->getCell()->getSy(), $entity->getCell()->getCx(), $entity->getCell()->getCy(), $entity->getPos());
                    $sourceCoordinates = new EntityCoordinates($ownEntity->getCell()->getSx(), $ownEntity->getCell()->getSy(), $ownEntity->getCell()->getCx(), $ownEntity->getCell()->getCy(), $ownEntity->getPos());

                    $distanceValue = $this->entityService->distanceByCoords($sourceCoordinates, $targetCoordinates);
                    $timeforflight = $distanceValue / $speed * 3600;
                    $distance = sprintf('%s AE', StringUtils::formatNumber($distanceValue));
                } else {
                    $distanceValue = -1;
                    $distance = '-';
                    $timeforflight = null;
                }

                $this->duration = StringUtils::formatTimespan($timeforflight);
                $this->distance = $distance;

                if ($distanceValue === -1) {
                    $this->color['distance'] = '#f00';
                    $launch = false;
                } elseif ($distanceValue > $range) {
                    $this->color['distance'] = '#f00';
                    $this->distance .= ' (zu weit entfernt, ' . StringUtils::formatNumber($range) . ' max)';
                    $launch = false;
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
            $launch = false;
        }
    }
}