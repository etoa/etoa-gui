<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Defense\DefenseListItem;
use EtoA\Defense\DefenseRepository;
use EtoA\Form\Type\Admin\EditDefenseType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_defense_view')]
class DefenseViewComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $defense;
    #[LiveProp]
    public string $entity;
    private ?DefenseListItem $item = null;

    public function mount(FormView $view = null, DefenseListItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->id;
        }
    }

    public function __construct(
        private DefenseRepository $defenseRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->defenseRepository->removeEntry($this->itemId);
        $this->item = null;
    }

    public function getItem(): ?DefenseListItem
    {
        if ($this->item === null) {
            $this->item = $this->defenseRepository->getItem($this->itemId);
        }

        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditDefenseType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->defenseRepository->setDefenseCount($this->item->id, $this->item->count);
    }
}
