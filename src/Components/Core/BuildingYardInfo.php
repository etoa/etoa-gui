<?php

namespace EtoA\Components\Core;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingService;
use EtoA\Building\BuildList;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent(template: 'components/building_yard_info.html.twig')]
class BuildingYardInfo extends AbstractController
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
        private readonly BuildList                    $buildList
    )
    {
    }

    #[ExposeInTemplate]
    public function getWorking(): int
    {
        $base = $this->buildingListItemRepository->findOneBy(['entity' => $this->getCurrentEntity(), 'building' => BuildingId::BUILDING]);
        return $base?->getPeopleWorking() ?? 0;
    }

    #[ExposeInTemplate]
    public function getGenTechLevel(): int
    {
        return $this->technologyListItemRepository->getTechnologyLevel($this->user, TechnologyId::GEN) ?? 0;
    }

    #[ExposeInTemplate]
    public function baseAvailable(): bool
    {
        $planet = $this->getCurrentEntity();

        return !!$this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::BUILDING]);
    }

    #[ExposeInTemplate]
    public function isCurrentlyBuilding(): bool
    {
        return $this->buildingService->isUnderConstruction($this->getCurrentEntity());
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
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => "save"
                ]
            ])
            ->add('optimize', ButtonType::class, [
                'label' => 'Optimieren',
                'attr' => [
                    'onclick'=>"updatePeopleWorkingBox('".$this->getPeopleOptimized()."','-1','^-1')"
                ]
            ])
            ->getForm();
    }

    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        $this->submitForm();

        if (!$this->isCurrentlyBuilding()) {
            $planet = $this->getCurrentEntity();
            $base = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::BUILDING]);
            $peopleWorking = $this->getWorking();

            $free = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking;
            $people = (int)$this->getForm()->get('peopleWorking')->getData();

            if ($free >= $people) {
                $base->setPeopleWorking($people);
                $this->buildingListItemRepository->save();
                $this->addFlash('success', "Arbeiter zugeteilt!");
            } else {
                $this->addFlash('error', "Nicht genügend freie Arbeiter!");
            }
        } else {
            $this->addFlash('error', "Arbeiter konnten nicht zugeteilt werden!");
        }

        $referer = $this->requestStack->getCurrentRequest()?->headers->get('referer');

        return $referer ? new RedirectResponse($referer) : null;
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
        if($request->attributes->has('id')) {
            $item = $this->buildList->item($request->attributes->get('id'))->bl;
            return $this->buildingService->getPeopleOptimized($item);
        }
        return 0;
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
