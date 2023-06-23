<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Form\Type\Admin\EditShipListType;
use EtoA\Form\Type\Admin\EditSpecialShipListType;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListItem;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipXpCalculator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_ship_view')]
class ShipViewComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $ship;
    #[LiveProp]
    public string $entity;
    private ?ShipListItem $item = null;

    public function mount(FormView $view = null, ShipListItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->id;
        }
    }

    public function __construct(
        private ShipRepository $shipRepository,
        private ShipDataRepository $shipDataRepository
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->shipRepository->removeEntry($this->itemId);
        $this->item = null;
    }

    public function getItem(): ?ShipListItem
    {
        if ($this->item === null) {
            $this->item = $this->shipRepository->find($this->itemId);
        }

        return $this->item;
    }

    public function getLevel(): int|string
    {
        if (!$this->getItem()->specialShip) {
            return 0;
        }

        $ship = $this->shipDataRepository->getShip($this->getItem()->shipId, false);

        return ShipXpCalculator::levelByXp($ship->specialNeedExp, $ship->specialExpFactor, $this->getItem()->specialShipExp) . ' ' . time();
    }

    protected function instantiateForm(): FormInterface
    {
        if ($this->getItem()->specialShip) {
            return $this->createForm(EditSpecialShipListType::class, $this->getItem());
        }

        return $this->createForm(EditShipListType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->shipRepository->saveItem($this->item);
    }
}
