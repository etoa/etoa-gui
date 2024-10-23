<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingListItemSearch;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Entity\BuildingListItem;
use EtoA\Form\Request\Admin\BuildingSearchRequest;
use EtoA\Form\Type\Admin\BuildingSearchType;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_building_search')]
class BuildingSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $buildingNames;
    /** @var array<int, string> */
    public array $entities;
    private BuildingSearchRequest $request;

    public function __construct(
        private BuildingListItemRepository $buildingRepository,
        private BuildingDataRepository     $buildingDataRepository,
        private UserRepository             $userRepository,
        private EntityRepository           $entityRepository,
    ) {
        $this->request = new BuildingSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = BuildingListItemSearch::create();
        if ($this->request->userId !== null) {
            $search->userId($this->request->userId);
        }

        if ($this->request->entityId !== null) {
            $search->entityId($this->request->entityId);
        }

        if ($this->request->buildingId !== null) {
            $search->buildingId($this->request->buildingId);
        }

        if ($this->request->buildType !== null) {
            $search->buildType($this->request->buildType);
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
        return $this->createForm(BuildingSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new BuildingSearchRequest();
    }
}
