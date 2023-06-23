<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Form\Type\Admin\EditShipQueueType;
use EtoA\Ship\ShipQueueItem;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_ship_queue_view')]
class ShipQueueViewComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $ship;
    #[LiveProp]
    public string $entity;
    private ?ShipQueueItem $item = null;

    public function mount(FormView $view = null, ShipQueueItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->id;
        }
    }

    public function __construct(
        private ShipQueueRepository $shipQueueRepository,
        private ShipRepository $shipRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->shipQueueRepository->deleteQueueItem($this->itemId);
        $this->item = null;
    }

    #[LiveAction]
    public function finish(): void
    {
        $item = $this->getItem();
        if ($item !== null) {
            $this->shipRepository->addShip($item->shipId, $item->count, $item->userId, $item->entityId);
            $this->shipQueueRepository->deleteQueueItem($item->id);
        }

        $this->item = null;
    }

    public function getItem(): ?ShipQueueItem
    {
        if ($this->item === null) {
            $this->item = $this->shipQueueRepository->getQueueItem($this->itemId);
        }

        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditShipQueueType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->shipQueueRepository->saveQueueItem($this->item);
    }
}
