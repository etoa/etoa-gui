<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Entity\ShipListItem;
use EtoA\Form\Type\Admin\EditShipListType;
use EtoA\Form\Type\Admin\EditSpecialShipListType;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipXpCalculator;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_ship_view')]
class ShipViewComponent extends AbstractEditComponent
{
    #[LiveProp(writable: true)]
    public ?ShipListItem $item = null;

    public function __construct(
        private readonly ShipListRepository $shipRepository,
    ) {}

    #[LiveAction]
    public function delete(): void
    {
        $this->shipRepository->remove($this->item);
        $this->shipRepository->save();
        $this->item = null;
    }

    public function getItem(): ?ShipListItem
    {
        return $this->item;
    }

    public function getLevel(): int|string
    {
        if (!$this->getItem()?->isSpecialShip()) {
            return 0;
        }

        $ship = $this->item->getShip();

        return ShipXpCalculator::levelByXp($ship->getSpecialNeedExp(), $ship->getSpecialExpFactor(), $this->getItem()->getSpecialShipExp()) . ' ' . time();
    }

    protected function instantiateForm(): FormInterface
    {
        if ($this->getItem()?->isSpecialShip()) {
            return $this->createForm(EditSpecialShipListType::class, $this->getItem());
        }

        return $this->createForm(EditShipListType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->shipRepository->save();
    }
}
