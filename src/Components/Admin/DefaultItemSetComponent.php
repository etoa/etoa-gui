<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\DefaultItem\DefaultItem;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Form\Type\Admin\NewDefaultItemType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;
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
    public int $setId;
    public string $error = '';
    /** @var array<int, string> */
    public array $buildings = [];
    /** @var array<int, string> */
    public array $technologies = [];
    /** @var array<int, string> */
    public array $ships = [];
    /** @var array<int, string> */
    public array $defense = [];
    /** @var array<int, string> */
    public array $missiles = [];

    public function __construct(
        private DefaultItemRepository $defaultItemRepository,
        private BuildingDataRepository $buildingDataRepository,
        private TechnologyDataRepository $technologyDataRepository,
        private ShipDataRepository $shipDataRepository,
        private DefenseDataRepository $defenseDataRepository,
        private MissileDataRepository $missileDataRepository,
    ) {
    }

    /**
     * @return DefaultItem[][]
     */
    public function getItems(): array
    {
        $defaultItems = $this->defaultItemRepository->getItemsGroupedByCategory($this->setId);
        if (isset($defaultItems['b'])) {
            $this->buildings = $this->buildingDataRepository->getBuildingNames(true);
        }
        if (isset($defaultItems['t'])) {
            $this->technologies = $this->technologyDataRepository->getTechnologyNames(true);
        }
        if (isset($defaultItems['s'])) {
            $this->ships = $this->shipDataRepository->getShipNames(true);
        }
        if (isset($defaultItems['d'])) {
            $this->defense = $this->defenseDataRepository->getDefenseNames(true);
        }
        if (isset($defaultItems['m'])) {
            $this->missiles = $this->missileDataRepository->getMissileNames(true);
        }

        return $defaultItems;
    }

    #[LiveAction]
    public function submit(): void
    {
        $this->submitForm();

        /** @var DefaultItem $item */
        $item = $this->getFormInstance()->getData();
        $success = $this->defaultItemRepository->addItemToSet($this->setId, $item->cat, $item->objectId, $item->count);
        if (!$success) {
            $this->error = 'Existiert bereits';
        }
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(NewDefaultItemType::class, DefaultItem::empty());
    }
}
