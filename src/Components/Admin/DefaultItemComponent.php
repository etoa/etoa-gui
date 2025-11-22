<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Entity\DefaultItem;
use EtoA\Form\Type\Admin\EditDefaultItemType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_default_item')]
class DefaultItemComponent extends AbstractEditComponent
{
    #[LiveProp(writable: true)]
    public ?DefaultItem $item = null;

    public function __construct(
        private readonly DefaultItemRepository $defaultItemRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->defaultItemRepository->remove($this->item);
        $this->defaultItemRepository->save();
        $this->item = null;
    }

    protected function storeItem(): void
    {
        $this->defaultItemRepository->updateItemCount($this->item->getId(), $this->item->getCount());
    }

    public function getItem(): ?DefaultItem
    {
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
