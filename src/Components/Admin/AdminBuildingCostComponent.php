<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingCostContext;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingCostCalculator;
use EtoA\Form\Request\Admin\AdminBuildingLevelCostRequest;
use EtoA\Form\Type\Admin\BuildingLevelCostType;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\PreciseResources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_building_cost')]
class AdminBuildingCostComponent extends AbstractController
{
    use ComponentWithFormTrait;

    public PreciseResources|BaseResources $costs;
    public AdminBuildingLevelCostRequest $request;

    public function __construct(
        private BuildingDataRepository $buildingDataRepository,
        private BuildingCostCalculator $buildingCostCalculator,
    ) {
        $this->costs = new BaseResources();
        $this->request = new AdminBuildingLevelCostRequest();
    }

    public function __invoke(): void
    {
        $this->submitForm();

        if ($this->request->itemId === null || $this->request->level === null) {
            return;
        }

        $building = $this->buildingDataRepository->getBuilding($this->request->itemId);
        if ($building === null) {
            return;
        }

        $this->costs = $this->buildingCostCalculator->calculate($building, $this->request->level, BuildingCostContext::admin());
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(BuildingLevelCostType::class, $this->request);
    }
}
