<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Entity\TechnologyListItem;
use EtoA\Form\Type\Admin\EditTechnologyItemType;
use EtoA\Technology\TechnologyBuildType;
use EtoA\Technology\TechnologyListItemRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_technology_view')]
class TechnologyViewComponent extends AbstractEditComponent
{
    #[LiveProp(writable: true)]
    public ?TechnologyListItem $item = null;

    /** @var array<int, string> */
    public array $buildTypes;

    public function __construct(
        private readonly TechnologyListItemRepository $technologyRepository,
    ) {
        $this->buildTypes = TechnologyBuildType::all();
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->technologyRepository->remove($this->item);
        $this->technologyRepository->save();
        $this->item = null;
    }

    public function getItem(): ?TechnologyListItem
    {
        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditTechnologyItemType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->technologyRepository->save();
    }
}
