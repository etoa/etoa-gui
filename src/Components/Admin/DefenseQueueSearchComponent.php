<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Form\Request\Admin\DefenseQueueSearchRequest;
use EtoA\Form\Type\Admin\DefenseSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_defense_queue_search')]
class DefenseQueueSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private DefenseQueueSearchRequest $request;

    public function __construct(
        private DefenseQueueRepository $defenseQueueRepository,
    ) {
        $this->request = new DefenseQueueSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = DefenseQueueSearch::create();
        if ($this->request->user !== null) {
            $search->user($this->request->user);
        }

        if ($this->request->entity !== null) {
            $search->entity($this->request->entity);
        }

        if ($this->request->defense !== null) {
            $search->defenseId($this->request->defense);
        }

        $total = $this->defenseQueueRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->defenseQueueRepository->searchQueueItems($search, $this->perPage, $limit);

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(DefenseSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new DefenseQueueSearchRequest();
    }
}
