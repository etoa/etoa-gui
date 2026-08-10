<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Entity\DefaultItem;
use EtoA\Entity\DefaultItemSet;
use EtoA\Form\Type\Admin\NewDefaultItemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_default_item_set')]
class DefaultItemSetComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public DefaultItemSet $set;
    public string $error = '';

    public function __construct(
        private readonly DefaultItemRepository $defaultItemRepository,
    ) {
    }

    /**
     * @return array<string, array<DefaultItem>>
     */
    public function getItems(): array
    {
        return $this->defaultItemRepository->getItemsGroupedByCategory($this->set);
    }

    #[LiveAction]
    public function submit(): void
    {
        $this->error = '';
        $this->submitForm();

        /** @var DefaultItem $item */
        $item = $this->getForm()->getData();
        if ($item->getCat() === '') {
            $this->error = 'Kein Objekt gewählt';

            return;
        }

        $success = $this->defaultItemRepository->addItemToSet($this->set, $item->getCat(), $item->getObjectId(), $item->getCount());
        if (!$success) {
            $this->error = 'Existiert bereits';
        }

        // No resetForm() here: the choice widget is excluded from the live re-render
        // (see SearchableChoiceType), so clearing the form server-side would leave the
        // dropdown visibly showing an object the component no longer knows about.
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(NewDefaultItemType::class, new DefaultItem());
    }
}
