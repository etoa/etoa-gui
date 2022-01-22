<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\Building;
use EtoA\Building\BuildingCostCalculator;
use EtoA\Building\BuildingCostContext;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingSort;
use EtoA\Universe\Resources\PreciseResources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_building_cost_calculator')]
class AdminBuildingCostCalculatorComponent extends AbstractController
{
    use ComponentWithFormTrait;

    /** @var Building[] */
    public array $buildings;
    /** @var PreciseResources[] */
    public array $buildingCosts;
    public PreciseResources $totalCosts;
    /** @var int[] */
    public array $levels = [];

    public function __construct(
        private BuildingCostCalculator $buildingCostCalculator,
        BuildingDataRepository $buildingDataRepository,
    ) {
        $this->buildings = $buildingDataRepository->searchBuildings(null, BuildingSort::type());
        $this->totalCosts = new PreciseResources();
        $this->buildingCosts = [];
        foreach ($this->buildings as $building) {
            $this->buildingCosts[$building->id] = new PreciseResources();
        }
    }

    public function __invoke(): void
    {
        $this->submitForm();

        $this->levels = array_map(fn ($value) => (int) $value, $this->getFormValues());

        $context = BuildingCostContext::admin();
        foreach ($this->buildings as $building) {
            if (($this->levels[$building->id] ?? 0) > 0) {
                $level = $this->levels[$building->id];
                while ($level > 0) {
                    $this->buildingCosts[$building->id] = $this->buildingCosts[$building->id]->add($this->buildingCostCalculator->calculate($building, $level, $context));
                    $level--;
                }
                $this->totalCosts = $this->totalCosts->add($this->buildingCosts[$building->id]);
            } else {
                $this->buildingCosts[$building->id] = new PreciseResources();
            }
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder($this->levels);

        foreach ($this->buildings as $building) {
            $formBuilder->add((string) $building->id, IntegerType::class, [
                'label' => false,
            ]);
        }

        return $formBuilder->getForm();
    }
}
