<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\AbstractEditComponent;
use EtoA\Entity\MissileListItem;
use EtoA\Form\Type\Admin\EditMissileListType;
use EtoA\Missile\MissileListSearch;
use EtoA\Missile\MissileRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_missile_view')]
class MissileViewComponent extends AbstractEditComponent
{
    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $missile;
    #[LiveProp]
    public string $entity;
    private ?MissileListItem $item = null;

    public function mount(FormView $view = null, MissileListItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->getId();
        }
    }

    public function __construct(
        private MissileRepository $missileRepository
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->missileRepository->remove($this->itemId);
        $this->item = null;
    }

    public function getItem(): ?MissileListItem
    {
        if ($this->item === null) {
            $this->item = $this->missileRepository->searchOne(MissileListSearch::create()->id($this->itemId));
        }

        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditMissileListType::class, $this->getItem());
    }

    protected function storeItem(): void
    {
        $this->missileRepository->setMissileCount($this->itemId, $this->item->getCount());
    }
}
