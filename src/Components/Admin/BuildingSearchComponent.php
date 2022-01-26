<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItem;
use EtoA\Building\BuildingListItemSearch;
use EtoA\Building\BuildingRepository;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\BuildingSearchType;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_building_search')]
class BuildingSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $buildingNames;
    /** @var array<int, string> */
    public array $entities;

    public function __construct(
        private BuildingRepository $buildingRepository,
        private BuildingDataRepository $buildingDataRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = BuildingListItemSearch::create();
        if ($this->getFormValues()['userId'] !== '') {
            $search->userId((int) $this->getFormValues()['userId']);
        }

        if ($this->getFormValues()['entityId'] !== '') {
            $search->entityId((int) $this->getFormValues()['entityId']);
        }

        if ($this->getFormValues()['buildingId'] !== '') {
            $search->buildingId((int) $this->getFormValues()['buildingId']);
        }

        if ($this->getFormValues()['buildType'] !== '') {
            $search->buildType((int) $this->getFormValues()['buildType']);
        }

        $total = $this->buildingRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->buildingRepository->search($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->buildingNames = $this->buildingDataRepository->getBuildingNames(true);
            $entityIds = array_map(fn (BuildingListItem $item) => $item->entityId, $entries);
            $this->entities = array_map(fn (EntityLabel $label) => $label->toString(), $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids($entityIds)));
        }

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(BuildingSearchType::class, $this->getFormValues());
    }
}
