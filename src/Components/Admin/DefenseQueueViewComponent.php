<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\DefenseQueueItem;
use EtoA\Form\Type\Admin\EditDefenseQueueType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_defense_queue_view')]
class DefenseQueueViewComponent extends AbstractEditComponent
{
    #[LiveProp(writable: true)]
    public ?DefenseQueueItem $item = null;

    public function __construct(
        private readonly DefenseQueueRepository $defenseQueueRepository,
        private readonly DefenseRepository      $defenseRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->defenseQueueRepository->remove($this->item);
        $this->defenseQueueRepository->save();
        $this->item = null;
    }

    #[LiveAction]
    public function finish(): void
    {
        $item = $this->getItem();
        if ($item !== null) {
            $this->defenseRepository->addDefense($item->getDefense(), $item->getCount(), $item->getUser(), $item->getEntity());
            $this->delete();
        }
    }

    public function getItem(): ?DefenseQueueItem
    {
        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditDefenseQueueType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->defenseQueueRepository->save();
    }
}
