<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Form\Type\Admin\EditMissileListType;
use EtoA\Missile\MissileListItem;
use EtoA\Missile\MissileListSearch;
use EtoA\Missile\MissileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_missile_view')]
class MissileViewComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public int $itemId;
    #[LiveProp]
    public string $user;
    #[LiveProp]
    public string $missile;
    #[LiveProp]
    public string $entity;
    #[LiveProp]
    public bool $isEdit = false;
    private ?MissileListItem $item = null;

    public function mount(MissileListItem $item = null): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemId = $item->id;
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

    #[LiveAction]
    public function showEdit(): void
    {
        $this->isEdit = true;
    }

    #[LiveAction]
    public function submit(): void
    {
        $this->submitForm();

        $this->missileRepository->setMissileCount($this->itemId, $this->item->count);
        $this->isEdit = false;
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
}
