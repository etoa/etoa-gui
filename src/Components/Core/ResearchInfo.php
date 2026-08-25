<?php

namespace EtoA\Components\Core;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Planet;
use EtoA\Entity\TechnologyListItem;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Technology\TechnologyService;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
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

#[AsLiveComponent(template: 'components/research_info.html.twig')]
class ResearchInfo extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public User|null $user = null;

    #[LiveProp]
    public ?int $techId = null;

    public function __construct(
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly ConfigurationService         $configurationService,
        private readonly RequestStack                 $requestStack,
        private readonly PlanetRepository             $planetRepository,
        private readonly TechnologyService            $technologyService,
        private readonly TechnologyDataRepository     $technologyDataRepository
    )
    {
    }

    #[ExposeInTemplate]
    public function getWorking(): int
    {
        $base = $this->buildingListItemRepository->findOneBy(['entity' => $this->getCurrentEntity(), 'building' => BuildingId::TECHNOLOGY]);
        return $base?->getPeopleWorking() ?? 0;
    }

    #[ExposeInTemplate]
    public function getWorkingGen(): int
    {
        $base = $this->buildingListItemRepository->findOneBy(['entity' => $this->getCurrentEntity(), 'building' => BuildingId::PEOPLE]);
        return $base?->getPeopleWorking() ?? 0;
    }

    #[ExposeInTemplate]
    public function getGenTechLevel(): int
    {
        return $this->technologyListItemRepository->getTechnologyLevel($this->user, TechnologyId::GEN) ?? 0;
    }

    #[ExposeInTemplate]
    public function isCurrentlyResearching(): bool
    {
        return $this->technologyService->isCurrentlyResearching();
    }

    #[ExposeInTemplate]
    public function isCurrentlyGenResearching(): bool
    {
        return $this->technologyService->isCurrentlyGenResearching();
    }

    protected function instantiateForm(): FormInterface
    {
        $planet = $this->getCurrentEntity();
        $peopleWorking = $this->isGen() ? $this->getWorkingGen() : $this->getWorking();
        $peopleFree = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $this->getWorkingGen() + $this->getWorking();
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
            ->add('optimize', ButtonType::class, [
                'label' => 'Optimieren',
                'attr' => [
                    'onclick' => "updatePeopleWorkingBox('" . $this->getPeopleOptimized() . "','-1','^-1')"
                ]
            ])
            ->getForm();
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();
        $lab = null;
        $planet = $this->getCurrentEntity();

        if ($this->techId) {
            if ($this->isGen()) {
                if (!$this->isCurrentlyGenResearching())
                    $lab = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::PEOPLE]);
            } else {
                if (!$this->isCurrentlyResearching())
                    $lab = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::TECHNOLOGY]);
            }
        }

        if ($lab) {
            $peopleWorking = $this->getWorking();

            $free = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking;
            $people = (int)$this->getForm()->get('peopleWorking')->getData();

            if ($free >= $people) {
                $lab->setPeopleWorking($people);
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
    public function showOptimization(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request->attributes->has('id');
    }

    public function getPeopleOptimized(): float
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->attributes->has('id')) {
            $item = $this->technologyListItemRepository->findOneBy(['technology' => $request->attributes->get('id'), 'user' => $this->user]);

            if (!$item) {
                $item = new TechnologyListItem();
                $item->setCurrentLevel(0);
                $item->setTechnology($this->technologyDataRepository->find($request->attributes->get('id')));
            }

            return $this->technologyService->getPeopleOptimized($item);
        }
        return 0;
    }

    #[PreMount]
    public function preMount(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->attributes->has('id')) {
            $this->techId = $request->attributes->get('id');
        }
        $this->user = $this->getUser()->getData();
    }

    #[ExposeInTemplate]
    public function getTimeBonusString(): string
    {
        $lab = $this->buildingListItemRepository->findOneBy(['entity' => $this->getCurrentEntity(), 'building' => BuildingId::TECHNOLOGY]);
        $need_bonus_level = $lab->getCurrentLevel() - $this->configurationService->param1Int('build_time_boni_forschungslabor');

        if ($need_bonus_level <= 0) {
            $time_boni_factor = 1;
        } else {
            $time_boni_factor = 1 - ($need_bonus_level * ($this->configurationService->getInt('build_time_boni_forschungslabor') / 100));
        }

        if ($need_bonus_level >= 0) {
            return StringUtils::formatPercentString($time_boni_factor) . " durch Stufe " . $lab->getCurrentLevel() . " (-" . ((1 - $this->configurationService->param2Float('build_time_boni_forschungslabor')) * 100) . "% maximum)";
        } else {
            return "Stufe " . $this->configurationService->getInt('build_time_boni_forschungslabor') . " erforderlich!";
        }
    }

    #[ExposeInTemplate]
    public function isGen(): bool
    {
        if ($this->techId === TechnologyId::GEN) {
            return true;
        }

        return false;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }
}
