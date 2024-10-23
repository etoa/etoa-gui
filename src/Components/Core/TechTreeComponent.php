<?php declare(strict_types=1);

namespace EtoA\Components\Core;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRequirementRepository;
use EtoA\Defense\Defense;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Entity\Building;
use EtoA\Entity\Technology;
use EtoA\Form\Type\Core\TechTreeSelectionType;
use EtoA\Missile\Missile;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Requirement\RequirementRepositoryProvider;
use EtoA\Ship\Ship;
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
    /** @var mixed[] */
    public array $requiredObjects = [];
    /** @var mixed[] */
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
        $id = $this->formValues['id'] ?? '';
        [$cat, $id] = explode(':', $id);
        $id = (int) $id;
        $repository = $this->requirementRepositoryProvider->getRepositoryForCategory($cat);
        $requirements = $repository->getRequirements($id);

        $buildings = $this->buildingDataRepository->searchBuildings();
        $technologies = $this->technologyDataRepository->getTechnologies();
        $ships = $this->shipDataRepository->getAllShips(false);
        $defenses = $this->defenseDataRepository->getAllDefenses();
        $missiles = $this->missileDataRepository->getMissiles();

        $this->requiredObjects = [];
        foreach ($requirements->getBuildingRequirements($id) as $requirement) {
            $building = $buildings[$requirement->requiredBuildingId];
            $this->requiredObjects[] = ['item' => $building, 'level' => $requirement->requiredLevel, 'category' => 'b'];
        }

        foreach ($requirements->getTechnologyRequirements($id) as $requirement) {
            $technology = $technologies[$requirement->requiredTechnologyId];
            $this->requiredObjects[] = ['item' => $technology, 'level' => $requirement->requiredLevel, 'category' => 't'];
        }

        switch ($cat) {
            case 'b':
                $this->object = $buildings[$id];

                break;
            case 't':
                $this->object = $technologies[$id];

                break;
            case 's':
                $this->object = $ships[$id];

                break;
            case 'd':
                $this->object = $defenses[$id];

                break;
            case 'm':
                $this->object = $missiles[$id];

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

            foreach ($buildingRequirements as $requirement) {
                if (isset($buildings[$requirement->objectId])) {
                    $this->allowedObjects[] = ['item' => $buildings[$requirement->objectId], 'level' => $requirement->requiredLevel, 'category' => 'b'];
                }
            }
            foreach ($technologyRequirements as $requirement) {
                if (isset($technologies[$requirement->objectId])) {
                    $this->allowedObjects[] = ['item' => $technologies[$requirement->objectId], 'level' => $requirement->requiredLevel, 'category' => 't'];
                }
            }
            foreach ($shipRequirements as $requirement) {
                if (isset($ships[$requirement->objectId])) {
                    $this->allowedObjects[] = ['item' => $ships[$requirement->objectId], 'level' => $requirement->requiredLevel, 'category' => 's'];
                }
            }
            foreach ($defenseRequirements as $requirement) {
                if (isset($defenses[$requirement->objectId])) {
                    $this->allowedObjects[] = ['item' => $defenses[$requirement->objectId], 'level' => $requirement->requiredLevel, 'category' => 'd'];
                }
            }
            foreach ($missileRequirements as $requirement) {
                if (isset($missiles[$requirement->objectId])) {
                    $this->allowedObjects[] = ['item' => $missiles[$requirement->objectId], 'level' => $requirement->requiredLevel, 'category' => 'm'];
                }
            }
        }

        return $this->object;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder(['id' => $this->id ?? 'b:6'])
            ->add('id', TechTreeSelectionType::class, ['label' => false])
            ->getForm();
    }
}
