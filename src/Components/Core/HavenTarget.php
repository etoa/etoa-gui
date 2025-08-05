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
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
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
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService
    )
    {
    }

    protected function instantiateForm(): FormInterface
    {
        $obj = $this;
//dd($this->fleetLaunch);
        //$fleetLaunch = unserialize($this->serializedFleetLaunch);
//        $this->fleetLaunch = $fleetLaunch;
        if ($this->fleetLaunch->getTargetEntity()) {
            //TODO: make symfony serializer work and refactor
            $entity = $this->planetRepository->find($this->fleetLaunch->getTargetEntity()->getId());
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
            $entity = $this->entityRepository->find($this->fleetLaunch->getSourceEntity()->getId());
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
                'required' => false
            ])
            ->add('speed', ChoiceType::class, [
                'choices' => range(100, 1),
                'attr' => [
                    'onchange'=>"showLoader('duration')"
                ],
                'choice_label' => function ($choice, string $key, mixed $value): int {
                    return $value;
                },
                'choice_value' => function ($choice): ?int {
                    return $choice;
                },
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Weiter zur Aktionsauswahl >>>',
            ])

            ->getForm();

    }

    /**
     * Shows information about the target
     */
    public function havenTargetInfo()
    {
        //dd($this->formValues);

        $alliance = "";
        $target = false;
        $allianceStyle = 'none';
        $comment = "-";

        $sx = $this->formValues['csx'];
        $sy = $this->formValues['csy'];
        $cx = $this->formValues['ccx'];
        $cy = $this->formValues['ccy'];
        $pos = $this->formValues['psp'];
        if ($sx > 0 && $sy > 0 && $cx > 0 && $cy > 0 && $pos >= 0) {
            $absX = (($sx - 1) * $this->configurationService->param1Int('num_of_cells')) + $cx;
            $absY = (($sy - 1) * $this->configurationService->param2Int('num_of_cells')) + $cy;

            $owner = $this->fleetLaunch->getOwner();
            $code = $this->userUniverseDiscoveryService->discovered($owner, $absX, $absY) == 0 ? 'u' : '';

            $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));
            if ($entity && !($code == 'u' && $pos > 0)) {
                $this->fleetLaunchService->setTarget($entity);
                $this->fleetLaunch->setSpeedPercent($this->formValues['speed']);
                $this->fleetLaunch->setLeader(null);
                $allianceAttack = "";

                $this->costs = StringUtils::formatNumber($this->fleetLaunch->getCosts()) . " t " . ResourceNames::FUEL;
                $this->distance = StringUtils::formatNumber($this->fleetLaunch->getDistance()) . " AE";
                $this->duration = StringUtils::formatTimespan($this->fleetLaunch->getDuration());
                $this->speed = StringUtils::formatNumber($this->fleetLaunch->getSpeed()) . " AE/h";
                $this->costsPerHundredAE = StringUtils::formatNumber($this->fleetLaunch->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL;
                $this->food = StringUtils::formatNumber($this->fleetLaunch->getCostsFood()) . " t " . ResourceNames::FOOD;

                $target = true;

                if ($entity->getCode() == 'w' && !$this->fleetLaunch->getWormholeEntryEntity()&& $this->fleetLaunch->isWormholeEnable()) {
                    $this->wormhole = true;
                    $action = '<input id="setWormhole" tabindex="9" type="button" onclick="xajax_havenShowWormhole(xajax.getFormValues(\'targetForm\'))" value="Wurmloch auswählen">';
                } else {
                    $action = "<input id=\"cooseAction\" tabindex=\"9\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp;";
                }

                if ($entity->getType()->getUser() && count($this->fleetLaunch->getAFleets()) > 0) {
                    $alliance .= "<table style=\"width:100%;\">";
                    $counter = 0;
                    $fleetOwnerAlliance = $allianceRepository->getAlliance($fleet->owner->allianceId());
                    foreach ($fleet->aFleets as $f) {
                        if ($f->entityTo == $ent->id()) {
                            $counter++;
                            $alliance .= "<tr><input type=\"button\" style=\"width:100%;\" onclick=\"xajax_havenAllianceAttack(" . $f->id . ")\" name=\"" . $fleetOwnerAlliance->tag . "-" . $f->id . "\" value=\"Flottenleader: " . get_user_nick($f->userId) . " Ankunftszeit: " . date("d.m.y, H:i:s", $f->landTime) . "\"/></tr>";
                        }
                    }
                    $alliance .= "</table>";
                    if ($counter > 0)
                        $allianceStyle = '';
                }
            } else {
                $this->distance = 'Unbekannt';
            }

            if ($target)
                $submitButton = '&nbsp;<input tabindex="7" type="button" onclick="xajax_havenShowShips()" value="&lt;&lt; Zurück zur Schiffauswahl" />&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;' . $action;
            else
                $submitButton = '&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;';

            $response->assign('submitbutton', 'innerHTML', $submitButton);
            $response->assign('targetinfo', 'innerHTML', ob_get_contents());
            $response->assign('chooseAction', 'innerHTML', $action);
            $response->assign('alliance', 'innerHTML', $alliance);
            $response->assign('allianceAttacks', "style.display", $allianceStyle);
            ob_end_clean();
        }
        return $response;

    }

    #[PreMount]
    public function preMount(): void
    {
        //$this->fleetLaunchService->setFleetLaunch($this->fleetLaunch);
    }
}