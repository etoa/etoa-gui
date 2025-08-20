<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingListItemSearch;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\BuildingSearchRequest;
use EtoA\Form\Type\Admin\BuildingSearchType;
use EtoA\Universe\Entity\EntityRepository;
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
        if ($this->request->user) {
            $search->user($this->request->user);
        }

        if ($this->request->entity) {
            $search->entity($this->request->entity);
        }

        if ($this->request->building) {
            $search->building($this->request->building);
        }

        if ($this->request->buildType !== null) {
            $search->buildType($this->request->buildType);
        }

        $total = $this->buildingRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->buildingRepository->search($search, $this->perPage, $limit);

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
