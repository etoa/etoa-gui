<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingBuildType;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Entity\BuildingListItem;
use EtoA\Form\Type\Admin\EditBuildingType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_building_view')]
class BuildingViewComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $building;
    #[LiveProp]
    public string $entity;
    private ?BuildingListItem $item = null;
    /** @var array<int, string> */
    public array $buildTypes;

    public function mount(FormView $view = null, BuildingListItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->id;
        }
    }

    public function __construct(
        private BuildingListItemRepository $buildingRepository,
    ) {
        $this->buildTypes = BuildingBuildType::all();
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->buildingRepository->removeEntry($this->itemId);
        $this->item = null;
    }

    public function getItem(): ?BuildingListItem
    {
        if ($this->item === null) {
            $this->item = $this->buildingRepository->getEntry($this->itemId);
        }

        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditBuildingType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->buildingRepository->save($this->item);
    }
}
