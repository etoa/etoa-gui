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
    #[LiveProp(writable: true)]
    public ?MissileListItem $item = null;

    public function __construct(
        private readonly MissileRepository $missileRepository
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->missileRepository->remove($this->item);
        $this->missileRepository->save();
        $this->item = null;
    }

    public function getItem(): ?MissileListItem
    {
        return $this->item;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditMissileListType::class, $this->getItem());
    }

    protected function storeItem(): void
    {}
}
