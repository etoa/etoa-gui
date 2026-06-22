<?php

namespace EtoA\Components\Core;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingService;
use EtoA\Building\BuildList;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent(template: 'components/ship_yard_info.html.twig')]
class ShipYardInfo extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public User|null $user = null;

    public function __construct(
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly ConfigurationService         $configurationService,
        private readonly RequestStack                 $requestStack,
        private readonly PlanetRepository             $planetRepository,
        private readonly BuildingService              $buildingService,
        private readonly BuildList                    $buildList,
        private readonly ShipQueueRepository          $shipQueueRepository
    )
    {
    }

    #[ExposeInTemplate]
    public function getWorking(): int
    {
        $base = $this->buildingListItemRepository->findOneBy(['entity' => $this->getCurrentEntity(), 'building' => BuildingId::SHIPYARD]);
        return $base?->getPeopleWorking() ?? 0;
    }

    #[ExposeInTemplate]
    public function getGenTechLevel(): int
    {
        return $this->technologyListItemRepository->getTechnologyLevel($this->user, TechnologyId::GEN) ?? 0;
    }

    #[ExposeInTemplate]
    public function isCurrentlyBuilding(): bool
    {
        return !!$this->shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($this->getCurrentEntity())->endAfter(time()));
    }

    protected function instantiateForm(): FormInterface
    {
        $planet = $this->getCurrentEntity();
        $peopleWorking = $this->getWorking();
        $peopleFree = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking;
        $workDone = $this->configurationService->getInt('people_work_done');
        $foodRequired = $this->configurationService->getInt('people_food_require');

        //TODO: move calculation for people optimization from js to here
        return $this->createFormBuilder()
            ->add('peopleOptimized', HiddenType::class, [
                'data' => 0,
            ])
            ->add('peopleFree', HiddenType::class, [
                'data' => $peopleFree
            ])
            ->add('foodAvailable', HiddenType::class, [
                'data' => $planet->getResFood()
            ])
            ->add('foodRequired', HiddenType::class, [
                'data' => $foodRequired
            ])
            ->add('workDone', HiddenType::class, [
                'data' => $workDone
            ])
            ->add('peopleWorking', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox(this.value,'-1','-1')"
                ],
                'data' => StringUtils::formatNumber($peopleWorking)
            ])
            ->add('timeReduction', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox('-1',this.value,'-1')"
                ],
                'data' => StringUtils::formatTimespan($workDone * $peopleWorking)
            ])
            ->add('foodUsing', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox('-1','-1',this.value);"
                ],
                'data' => StringUtils::formatNumber($foodRequired * $peopleWorking)
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => [
                    'data-action' => 'live#action:render',
                    'data-live-action-param' => "save"
                ]
            ])
            ->getForm();
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();

        if (!$this->isCurrentlyBuilding()) {
            $planet = $this->getCurrentEntity();
            $building = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::SHIPYARD]);
            $peopleWorking = $this->getWorking();

            $free = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking;
            $people = (int)$this->getForm()->get('peopleWorking')->getData();

            if ($free >= $people) {
                $building->setPeopleWorking($people);
                $this->buildingListItemRepository->save();
                $this->addFlash('success', "Arbeiter zugeteilt!");
            } else {
                $this->addFlash('error', "Nicht genügend freie Arbeiter!");
            }
        } else {
            $this->addFlash('error', "Arbeiter konnten nicht zugeteilt werden!");
        }
    }

    #[ExposeInTemplate]
    public function getCurrentEntity(): Planet
    {
        $request = $this->requestStack->getCurrentRequest();
        return $this->planetRepository->find($request?->getSession()?->get('cpid'));
    }

    #[ExposeInTemplate]
    public function getTimeBonusString(): string
    {
        $shipyard = $this->buildingListItemRepository->findOneBy(['entity' => $this->getCurrentEntity(), 'building' => BuildingId::SHIPYARD]);
        $need_bonus_level = $shipyard->getCurrentLevel() - $this->configurationService->param1Int('build_time_boni_schiffswerft');

        if ($need_bonus_level <= 0) {
            $time_boni_factor = 1;
        } else {
            $time_boni_factor = 1 - ($need_bonus_level * ($this->configurationService->getInt('build_time_boni_schiffswerft') / 100));
        }

        if ($need_bonus_level >= 0) {
            return StringUtils::formatPercentString($time_boni_factor) . " durch Stufe " . $shipyard->getCurrentLevel();
        } else {
            return "Stufe " . $this->configurationService->getInt('build_time_boni_schiffswerft') . " erforderlich!";
        }
    }

    #[ExposeInTemplate]
    public function getReturnString(): string
    {
        $shipyard = $this->buildingListItemRepository->findOneBy(['entity' => $this->getCurrentEntity(), 'building' => BuildingId::SHIPYARD]);

        $cancelMinLevel = $this->configurationService->getInt('shipqueue_cancel_min_level');
        $cancelEnd = $this->configurationService->getFloat('shipqueue_cancel_end');
        $cancelFactor = $this->configurationService->getFloat('shipqueue_cancel_factor');
        $cancelStart = $this->configurationService->getFloat('shipqueue_cancel_start');

        // Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
        if ($shipyard->getCurrentLevel() >= $cancelMinLevel) {
            $cancel_res_factor = min($cancelEnd, $cancelStart + (($shipyard->getCurrentLevel() - $cancelMinLevel) * $cancelFactor));
        } else {
            $cancel_res_factor = 0;
        }

        if ($cancel_res_factor > 0) {
            return "<td>Ressourcenrückgabe bei Abbruch:</td><td>" . ($cancel_res_factor * 100) . "% (ohne " . ResourceNames::FOOD . ", " . ($cancelEnd * 100) . "% maximal)</td>";
        } else {
            return "<td>Abbruchmöglichkeit:</td><td>Stufe " . $cancelMinLevel . " erforderlich!</td>";
        }
    }

    #[PreMount]
    public function preMount(): void
    {
        $this->user = $this->getUser()->getData();
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }
}
