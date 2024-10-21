<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Entity\TechnologyListItem;
use EtoA\Form\Type\Admin\EditTechnologyItemType;
use EtoA\Technology\TechnologyBuildType;
use EtoA\Technology\TechnologyRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_technology_view')]
class TechnologyViewComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $technology;
    #[LiveProp]
    public string $entity;
    private ?TechnologyListItem $item = null;
    /** @var array<int, string> */
    public array $buildTypes;

    public function mount(FormView $view = null, TechnologyListItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->getId();
        }
    }

    public function __construct(
        private TechnologyRepository $technologyRepository,
    ) {
        $this->buildTypes = TechnologyBuildType::all();
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->technologyRepository->removeEntry($this->itemId);
        $this->item = null;
    }

    public function getItem(): ?TechnologyListItem
    {
        if ($this->item === null) {
            $this->item = $this->technologyRepository->getEntry($this->itemId);
        }

        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditTechnologyItemType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->technologyRepository->save($this->item);
    }
}
