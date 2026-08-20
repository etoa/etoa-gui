<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Entity\ShipQueueItem;
use EtoA\Form\Type\Admin\EditShipQueueType;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipQueueRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_ship_queue_view')]
class ShipQueueViewComponent extends AbstractEditComponent
{
    #[LiveProp(writable: true)]
    public ?ShipQueueItem $item = null;

    public function __construct(
        private readonly ShipQueueRepository $shipQueueRepository,
        private readonly ShipListRepository      $shipListRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->shipQueueRepository->remove($this->item);
        $this->shipQueueRepository->save();
        $this->item = null;
    }

    #[LiveAction]
    public function finish(): void
    {
        $item = $this->getItem();
        if ($item !== null) {
            $this->shipListRepository->addShip($item->getShip(), $item->getCount(), $item->getUser(), $item->getEntity());
            $this->delete();
        }
    }

    public function getItem(): ?ShipQueueItem
    {
        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditShipQueueType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->shipQueueRepository->save();
    }
}
