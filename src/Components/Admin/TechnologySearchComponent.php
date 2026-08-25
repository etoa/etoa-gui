<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\TechnologySearchRequest;
use EtoA\Form\Type\Admin\TechnologySearchType;
use EtoA\Technology\TechnologyBuildType;
use EtoA\Technology\TechnologyListItemSearch;
use EtoA\Technology\TechnologyListItemRepository;
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
        private readonly TechnologyListItemRepository $technologyRepository,
    ) {
        $this->buildTypes = TechnologyBuildType::all();
        $this->request = new TechnologySearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = TechnologyListItemSearch::create();
        if ($this->request->user) {
            $search->userId($this->request->user);
        }

        if ($this->request->technology) {
            $search->technologyId($this->request->technology);
        }

        if ($this->request->entity) {
            $search->entityId($this->request->entity);
        }

        if ($this->request->buildType > 0) {
            $search->buildType($this->request->buildType);
        }

        $total = $this->technologyRepository->countSearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->technologyRepository->search($search, $this->perPage, $limit);

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
