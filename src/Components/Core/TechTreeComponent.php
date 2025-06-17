<?php declare(strict_types=1);

namespace EtoA\Components\Core;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRequirementRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Entity\Building;
use EtoA\Entity\Defense;
use EtoA\Entity\Missile;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
use EtoA\Form\Type\Core\TechTreeSelectionType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Requirement\RequirementRepositoryProvider;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRequirementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('techtree')]
class TechTreeComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $id = null;
    private null|Missile|Ship|Defense|Technology|Building $object = null;
    /** @var array */
    public array $requiredObjects = [];
    /** @var array */
    public array $allowedObjects = [];

    public function __construct(
        private BuildingDataRepository $buildingDataRepository,
        private TechnologyDataRepository $technologyDataRepository,
        private ShipDataRepository $shipDataRepository,
        private DefenseDataRepository $defenseDataRepository,
        private MissileDataRepository $missileDataRepository,
        private RequirementRepositoryProvider $requirementRepositoryProvider,
        private BuildingRequirementRepository $buildingRequirementRepository,
        private TechnologyRequirementRepository $technologyRequirementRepository,
        private ShipRequirementRepository $shipRequirementRepository,
        private DefenseRequirementRepository $defenseRequirementRepository,
        private MissileRequirementRepository $missileRequirementRepository,
    ) {
    }

    public function getObject(): Missile|Ship|Defense|Technology|Building
    {
        $id = $this->formView->vars['attr']['id'] ?? '';
        [$cat, $id] = explode(':', $id);
        $id = (int) $id;
        $repository = $this->requirementRepositoryProvider->getRepositoryForCategory($cat);
        $requirements = $repository->getRequirements($id);

        $this->requiredObjects = $requirements;

        switch ($cat) {
            case 'b':
                $this->object = $this->buildingDataRepository->find($id);

                break;
            case 't':
                $this->object = $this->technologyDataRepository->find($id);

                break;
            case 's':
                $this->object = $this->shipDataRepository->find($id);

                break;
            case 'd':
                $this->object = $this->defenseDataRepository->find($id);

                break;
            case 'm':
                $this->object = $this->missileDataRepository->find($id);

                break;
            default:
                throw new \InvalidArgumentException('Unknown category:' . $cat);
        }

        $this->allowedObjects = [];
        if (in_array($cat, ['b', 't'], true)) {
            if ($cat === 'b') {
                $buildingRequirements = $this->buildingRequirementRepository->getRequiredByBuilding($id);
                $defenseRequirements = $this->defenseRequirementRepository->getRequiredByBuilding($id);
                $shipRequirements = $this->shipRequirementRepository->getRequiredByBuilding($id);
                $technologyRequirements = $this->technologyRequirementRepository->getRequiredByBuilding($id);
                $missileRequirements = $this->missileRequirementRepository->getRequiredByBuilding($id);
            } else {
                $buildingRequirements = $this->buildingRequirementRepository->getRequiredByTechnology($id);
                $defenseRequirements = $this->defenseRequirementRepository->getRequiredByTechnology($id);
                $shipRequirements = $this->shipRequirementRepository->getRequiredByTechnology($id);
                $technologyRequirements = $this->technologyRequirementRepository->getRequiredByTechnology($id);
                $missileRequirements = $this->missileRequirementRepository->getRequiredByTechnology($id);
            }

            $this->allowedObjects = array_merge($buildingRequirements,$defenseRequirements,$shipRequirements,$technologyRequirements,$missileRequirements);

        }
        return $this->object;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => $this->id ?? 'b:6']])
            ->add('id', TechTreeSelectionType::class, ['label' => false])
            ->getForm();
    }
}
