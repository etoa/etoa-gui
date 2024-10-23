<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\DefenseQueueItem;
use EtoA\Form\Type\Admin\EditDefenseQueueType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_defense_queue_view')]
class DefenseQueueViewComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $defense;
    #[LiveProp]
    public string $entity;
    private ?DefenseQueueItem $item = null;

    public function mount(FormView $view = null, DefenseQueueItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->id;
        }
    }

    public function __construct(
        private DefenseQueueRepository $defenseQueueRepository,
        private DefenseRepository $defenseRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->defenseQueueRepository->deleteQueueItem($this->itemId);
        $this->item = null;
    }

    #[LiveAction]
    public function finish(): void
    {
        $item = $this->getItem();
        if ($item !== null) {
            $this->defenseRepository->addDefense($item->defenseId, $item->count, $item->userId, $item->entityId);
            $this->defenseQueueRepository->deleteQueueItem($item->id);
        }

        $this->item = null;
    }

    public function getItem(): ?DefenseQueueItem
    {
        if ($this->item === null) {
            $this->item = $this->defenseQueueRepository->getQueueItem($this->itemId);
        }

        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditDefenseQueueType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->defenseQueueRepository->saveQueueItem($this->item);
    }
}
