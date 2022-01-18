<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\DefaultItem\DefaultItem;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Form\Type\Admin\EditDefaultItemType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_default_item')]
class DefaultItemComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $name;
    private ?DefaultItem $item = null;

    public function __construct(
        private DefaultItemRepository $defaultItemRepository,
    ) {
    }

    public function mount(FormView $view = null, DefaultItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->id;
        }
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->defaultItemRepository->removeItem($this->itemId);
        $this->item = null;
    }

    protected function storeItem(): void
    {
        $this->defaultItemRepository->updateItemCount($this->itemId, $this->item->count);
    }

    public function getItem(): ?DefaultItem
    {
        if ($this->item === null) {
            $this->item = $this->defaultItemRepository->getItem($this->itemId);
        }

        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditDefaultItemType::class, $this->getItem());
    }

    protected function resetItem(): void
    {
        $this->item = null;
    }
}
