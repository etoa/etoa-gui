<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\DefenseListItem;
use EtoA\Form\Type\Admin\EditDefenseType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_defense_view')]
class DefenseViewComponent extends AbstractEditComponent
{
    #[LiveProp(writable: true)]
    public ?DefenseListItem $item = null;

    public function __construct(
        private readonly DefenseRepository $defenseRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->defenseRepository->remove($this->item);
        $this->defenseRepository->save();
        $this->item = null;
    }

    public function getItem(): ?DefenseListItem
    {
        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditDefenseType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->defenseRepository->save();
    }
}
