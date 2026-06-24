<?php declare(strict_types=1);

namespace EtoA\Components\Core;

use Doctrine\Common\Util\ClassUtils;
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
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('techtree')]
class TechTreeComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    public function __construct(
        private readonly BuildingDataRepository          $buildingDataRepository,
        private readonly TechnologyDataRepository        $technologyDataRepository,
        private readonly ShipDataRepository              $shipDataRepository,
        private readonly DefenseDataRepository           $defenseDataRepository,
        private readonly MissileDataRepository           $missileDataRepository,
        private readonly RequirementRepositoryProvider   $requirementRepositoryProvider,
        private readonly BuildingRequirementRepository   $buildingRequirementRepository,
        private readonly TechnologyRequirementRepository $technologyRequirementRepository,
        private readonly ShipRequirementRepository       $shipRequirementRepository,
        private readonly DefenseRequirementRepository    $defenseRequirementRepository,
        private readonly MissileRequirementRepository    $missileRequirementRepository,
    ) {
    }

    public function getObject(): Missile|Ship|Defense|Technology|Building|null
    {
        return $this->form->get('obj')?->getData();
    }

    public function getRequiredObjects():array {
        return $this->form->get('obj')->getData()->getObjectRequirements()->getValues();
    }

    public function getAllowedObjects():array {
        $allowedObjects = [];
        $object = $this->getObject();

        $class = get_class($object);
        if (in_array($class, [Building::class, Technology::class], true)) {
            if ($class === Building::class) {
                $buildingRequirements = $this->buildingRequirementRepository->getRequiredByBuilding($object);
                $defenseRequirements = $this->defenseRequirementRepository->getRequiredByBuilding($object);
                $shipRequirements = $this->shipRequirementRepository->getRequiredByBuilding($object);
                $technologyRequirements = $this->technologyRequirementRepository->getRequiredByBuilding($object);
                $missileRequirements = $this->missileRequirementRepository->getRequiredByBuilding($object);
            } else {
                $buildingRequirements = $this->buildingRequirementRepository->getRequiredByTechnology($object);
                $defenseRequirements = $this->defenseRequirementRepository->getRequiredByTechnology($object);
                $shipRequirements = $this->shipRequirementRepository->getRequiredByTechnology($object);
                $technologyRequirements = $this->technologyRequirementRepository->getRequiredByTechnology($object);
                $missileRequirements = $this->missileRequirementRepository->getRequiredByTechnology($object);
            }

            $allowedObjects = array_merge($buildingRequirements,$defenseRequirements,$shipRequirements,$technologyRequirements,$missileRequirements);
        }

        return $allowedObjects;
    }

    #[LiveAction]
    public function selectObject(#[LiveArg] int $objectId, #[LiveArg] string $objectType): void
    {
        $repository = match($objectType) {
            'building' => $this->buildingDataRepository,
            'technology' => $this->technologyDataRepository,
            'ship' => $this->shipDataRepository,
            'defense' => $this->defenseDataRepository,
            'missile' => $this->missileDataRepository,
            default => throw new \InvalidArgumentException('Invalid object type'),
        };

        $object = $repository->find($objectId);
        if ($object) {
            $this->formValues['obj'] = match(ClassUtils::getClass($object)) {
                Building::class => 'b-' . $object->getId(),
                Technology::class => 't-' . $object->getId(),
                Ship::class => 's-' . $object->getId(),
                Defense::class => 'd-' . $object->getId(),
                Missile::class => 'm-' . $object->getId(),
                default => '',
            };
        }
    }

    public function getObjectType(mixed $object): string
    {
        return match(ClassUtils::getClass($object)) {
            Building::class => 'building',
            Technology::class => 'technology',
            Ship::class => 'ship',
            Defense::class => 'defense',
            Missile::class => 'missile',
            default => throw new \InvalidArgumentException('Unknown object type'),
        };
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('obj', TechTreeSelectionType::class, ['label' => false])
            ->getForm();
    }
}
