<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Defense\DefenseListItem;
use EtoA\Defense\DefenseRepository;
use EtoA\Form\Type\Admin\EditDefenseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_defense_view')]
class DefenseViewComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $defense;
    #[LiveProp]
    public string $entity;
    #[LiveProp]
    public bool $isEdit = false;
    private ?DefenseListItem $item = null;

    public function mount(DefenseListItem $item = null): void
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

    #[LiveAction]
    public function showEdit(): void
    {
        $this->isEdit = true;
    }

    #[LiveAction]
    public function abortEdit(): void
    {
        $this->isEdit = false;
    }

    #[LiveAction]
    public function submit(): void
    {
        $this->submitForm();

        $this->defenseRepository->setDefenseCount($this->item->id, $this->item->count);
        $this->isEdit = false;
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
}
