<?php

namespace EtoA\Components\Core;

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Entity;
use EtoA\Fleet\FleetLaunch;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent(template: 'components/haven_target.html.twig')]
class HavenTarget extends AbstractGameController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    private readonly Request $request;

    #[LiveProp]
    public string $costs = '';

    #[LiveProp]
    public string $food = '';

    #[LiveProp]
    public string $speed = '';

    #[LiveProp]
    public string $duration = '';

    #[LiveProp]
    public string $distance = '';

    #[LiveProp]
    public string $costsPerHundredAE = '';

    #[LiveProp]
    public bool $wormhole = false;

    #[LiveProp]
    public ?Entity $targetEntity = null;

    public ?FleetLaunch $fleetLaunch = null;

    public function __construct(
        private readonly ShipTransformRepository $shipTransformRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly ShipListRepository $shipListRepository,
        private readonly FleetLaunchService $fleetLaunchService,
        private readonly EntityRepository $entityRepository,
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly RequestStack $requestStack,
        private readonly ConfigurationService $configurationService,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityService $entityService,
        private readonly SerializerInterface $serializer
    )
    {
    }

    protected function instantiateForm(): FormInterface
    {
        $obj = $this;

        if(!$this->fleetLaunch) {
            $request = $this->requestStack->getCurrentRequest();
            $this->fleetLaunch = $this->serializer->deserialize($request->getSession()->get('fleetLaunch'), FleetLaunch::class, 'json', [
                'allow_extra_attributes' => true,
            ]);
        }

//dd($this->fleetLaunch);
        //$fleetLaunch = unserialize($this->serializedFleetLaunch);
//        $this->fleetLaunch = $fleetLaunch;
        if ($this->fleetLaunch->getTargetEntity()) {
            //TODO: make symfony serializer work and refactor
            $entity = $this->fleetLaunch->getTargetEntity();
            /*$csx = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getSx();
            $csy = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getSy();
            $ccx = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getCx();
            $ccy = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getCell()->getCy();
            $psp = $this->fleetLaunchService->getFleetLaunch()->getTargetEntity()->getPos();*/
            $csx = $entity->getCell()->getSx();
            $csy = $entity->getCell()->getSy();
            $ccx = $entity->getCell()->getCx();
            $ccy = $entity->getCell()->getCy();
            $psp = $entity->getPos();
        } else {
            $entity = $this->fleetLaunch->getSourceEntity()->getEntity();
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

        return $this->createFormBuilder()
            ->add(
                'csx',TextType::class, [
                'label' => false,
                'attr' => [
                    'onkeydown'=> "return nurZahlen(event);detectChangeRegister(this,'t2');",
                    'size'=>"1",
                    'maxlength'=>"1",
                    'title'=>"Sektor X-Koordinate",
                    'data-action'=>"live#action",
                    'data-live-action-param'=>"debounce(2000)|updateByField"
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
                    'data-action'=>"live#action",
                    'data-live-action-param'=>"debounce(2000)|updateByField"
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
                    'data-action'=>"live#action",
                    'data-live-action-param'=>"debounce(2000)|updateByField"
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
                    'data-action'=>"live#action",
                    'data-live-action-param'=>"debounce(2000)|updateByField"
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
                    'data-action'=>"live#action",
                    'data-live-action-param'=>"debounce(2000)|updateByField"
                ],
                'mapped' => false,
                'data' => $psp
            ])
            ->add('bookmark', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(static function () use ($obj): array {
                    $choices = [];
                    foreach ($obj->planetRepository->findBy(['user'=>$obj->fleetLaunch->getOwner()]) as $planet) {
                        $choices['Eigene Planeten'][] = $planet->getEntity();
                    }
                    foreach ($obj->bookmarkRepository->findBy(['user'=>$obj->fleetLaunch->getOwner()]) as $bookmark) {
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
                    'data-action'=>"live#action",
                    'data-live-action-param'=>"updateByFavorite"
                ]
            ])
            ->add('speed', ChoiceType::class, [
                'choices' => range(100, 1),
                'attr' => [
                    'data-action'=>"live#action",
                    'data-live-action-param'=>"updateSpeedPercent"
                ],
                'choice_label' => function ($choice, string $key, mixed $value): int {
                    return $value;
                },
                'choice_value' => function ($choice): ?int {
                    return $choice;
                },
                'data' => 100
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Weiter zur Aktionsauswahl >>>',
                'attr'=> [
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'chooseAction'
                ]
            ])
            ->getForm();
    }

    #[PreMount]
    public function preMount(): void
    {

    }

    #[LiveAction]
    public function updateByFavorite(): void
    {
        $entity = $this->entityRepository->find($this->formValues['bookmark']);

        $this->updateValues($entity);
    }

    #[LiveAction]
    public function updateSpeedPercent(): void
    {
        $this->updateValues($this->targetEntity);
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

    private function updateValues(Entity $entity): void
    {
        if(!$this->fleetLaunch) {
            $request = $this->requestStack->getCurrentRequest();
            $this->fleetLaunch = $this->serializer->deserialize($request->getSession()->get('fleetLaunch'), FleetLaunch::class, 'json', [
                'allow_extra_attributes' => true,
            ]);
        }

        $alliance = "";
        $target = false;
        $allianceStyle = 'none';
        $comment = "-";

        $absX = (($entity->getCell()->getSx() - 1) * $this->configurationService->param1Int('num_of_cells')) + $entity->getCell()->getCx();
        $absY = (($entity->getCell()->getSy() - 1) * $this->configurationService->param2Int('num_of_cells')) + $entity->getCell()->getCy();

        $owner = $this->fleetLaunch->getOwner();
        $code = $this->userUniverseDiscoveryService->discovered($owner, $absX, $absY) === false ? 'u' : '';

        if (!($code == 'u' && $entity->getPos() > 0)) {
            $this->setTarget($entity);
            $this->fleetLaunch->setSpeedPercent($this->formValues['speed']);
            $this->fleetLaunch->setLeader(null);
            $allianceAttack = "";
            $this->costs = StringUtils::formatNumber($this->fleetLaunch->getCosts()) . " t " . ResourceNames::FUEL;
            $this->distance = StringUtils::formatNumber($this->fleetLaunch->getDistance()) . " AE";
            $this->duration = StringUtils::formatTimespan($this->fleetLaunch->getDuration());
            $this->speed = StringUtils::formatNumber($this->fleetLaunch->getSpeed()) . " AE/h";
            $this->costsPerHundredAE = StringUtils::formatNumber($this->fleetLaunch->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL;
            $this->food = StringUtils::formatNumber($this->fleetLaunchService->getCostsFood()) . " t " . ResourceNames::FOOD;
            $this->targetEntity = $entity;
            $target = true;

            if ($entity->getCode() == 'w' && !$this->fleetLaunch->getWormholeEntryEntity() && $this->fleetLaunch->isWormholeEnable()) {
                $this->wormhole = true;
                $action = '<input id="setWormhole" tabindex="9" type="button" onclick="xajax_havenShowWormhole(xajax.getFormValues(\'targetForm\'))" value="Wurmloch auswählen">';
            } else {
                $action = "<input id=\"cooseAction\" tabindex=\"9\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp;";
            }

            if ($entity->getType()->getUser() && count($this->fleetLaunch->getAFleets()) > 0) {
                $alliance .= "<table style=\"width:100%;\">";
                $counter = 0;
                $fleetOwnerAlliance = $this->fleetLaunch->getOwner()->getAlliance();
                foreach ($this->fleetLaunch->getAFleets() as $f) {
                    if ($f->getEntityTo() == $this->fleetLaunch->getTargetEntity()) {
                        $counter++;
                        $alliance .= "<tr><input type=\"button\" style=\"width:100%;\" onclick=\"xajax_havenAllianceAttack(" . $f->id . ")\" name=\"" . $fleetOwnerAlliance->tag . "-" . $f->id . "\" value=\"Flottenleader: " . get_user_nick($f->userId) . " Ankunftszeit: " . date("d.m.y, H:i:s", $f->landTime) . "\"/></tr>";
                    }
                }
                $alliance .= "</table>";
                if ($counter > 0)
                    $allianceStyle = '';
            }
        } else {
            $this->fleetLaunch->setTargetEntity(null);
            $this->distance = 'Unbekannt';
        }

        if ($target)
            $submitButton = '&nbsp;<input tabindex="7" type="button" onclick="xajax_havenShowShips()" value="&lt;&lt; Zurück zur Schiffauswahl" />&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;' . $action;
        else
            $submitButton = '&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;';

        //$response->assign('submitbutton', 'innerHTML', $submitButton);
        //$response->assign('chooseAction', 'innerHTML', $action);
        //$response->assign('alliance', 'innerHTML', $alliance);
        //$response->assign('allianceAttacks', "style.display", $allianceStyle);
        //ob_end_clean();
    }

    /**
     * Shows information about the target
     */
    #[PostMount]
    public function postMount(): void
    {
        $this->updateByField();
    }

    #[LiveAction]
    public function chooseAction(): RedirectResponse
    {
        $this->submitForm();
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

        return $this->redirectToRoute('game.haven.target');
    }

    private function setTarget(Entity $ent, $speedPercent = 100): bool
    {
        if ($this->fleetLaunch->isShipsFixed()) {
            $this->fleetLaunch->setTargetEntity($ent);
            if ($this->fleetLaunch->getWormholeEntryEntity()) {
                $this->fleetLaunch->setDistance($this->entityService->distanceByCoords($this->fleetLaunch->getWormholeExitEntity()->getCoordinates(), $this->fleetLaunch->getTargetEntity()->getCoordinates()));
                $this->fleetLaunch->setDistance1($this->entityService->distanceByCoords($this->fleetLaunch->getSourceEntity()->getEntity()->getCoordinates(), $this->fleetLaunch->getWormholeEntryEntity()->getCoordinates()));
            } else {
                $this->fleetLaunch->setDistance($this->entityService->distanceByCoords($this->fleetLaunch->getSourceEntity()->getEntity()->getCoordinates(), $this->fleetLaunch->getTargetEntity()->getCoordinates()));
                $this->fleetLaunch->setDistance1(0);
            }

            $this->fleetLaunch->setSpeedPercent($speedPercent);

            return true;
        }
        return false;
    }
}