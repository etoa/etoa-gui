<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingBuildType;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Entity\BuildingListItem;
use EtoA\Form\Type\Admin\EditBuildingType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_building_view')]
class BuildingViewComponent extends AbstractEditComponent
{
    #[LiveProp(writable: true)]
    public ?BuildingListItem $item = null;

    /** @var array<int, string> */
    public array $buildTypes;

    public function __construct(
        private readonly BuildingListItemRepository $buildingRepository,
    ) {
        $this->buildTypes = BuildingBuildType::all();
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->buildingRepository->remove($this->item);
        $this->item = null;
    }

    public function getItem(): ?BuildingListItem
    {
        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditBuildingType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->buildingRepository->save();
    }
}
