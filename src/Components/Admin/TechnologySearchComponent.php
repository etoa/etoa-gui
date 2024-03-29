<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\TechnologySearchRequest;
use EtoA\Form\Type\Admin\TechnologySearchType;
use EtoA\Technology\TechnologyBuildType;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyListItem;
use EtoA\Technology\TechnologyListItemSearch;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_technology_search')]
class TechnologySearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private TechnologySearchRequest $request;

    /** @var array<int, string> */
    public array $buildTypes;
    /** @var array<int, string> */
    public array $entities = [];
    /** @var array<int, string> */
    public array $userNicks = [];
    /** @var array<int, string> */
    public array $technologyNames = [];

    public function __construct(
        private TechnologyRepository $technologyRepository,
        private TechnologyDataRepository $technologyDataRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
    ) {
        $this->buildTypes = TechnologyBuildType::all();
        $this->request = new TechnologySearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = TechnologyListItemSearch::create();
        if ($this->request->userId > 0) {
            $search->userId($this->request->userId);
        }

        if ($this->request->techId > 0) {
            $search->technologyId($this->request->techId);
        }

        if ($this->request->entityId > 0) {
            $search->entityId($this->request->entityId);
        }

        if ($this->request->buildType > 0) {
            $search->buildType($this->request->buildType);
        }

        $total = $this->technologyRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->technologyRepository->search($search, $this->perPage, $limit);
        if ($total > 0) {
            $this->userNicks = $this->userRepository->searchUserNicknames();
            $this->technologyNames = $this->technologyDataRepository->getTechnologyNames(true);
            $entityIds = array_map(fn (TechnologyListItem $item) => $item->entityId, $entries);
            $this->entities = array_map(fn (EntityLabel $label) => $label->toString(), $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids($entityIds)));
        }

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TechnologySearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new TechnologySearchRequest();
    }
}
