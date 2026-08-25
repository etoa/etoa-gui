<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\ReportSearchRequest;
use EtoA\Form\Type\Admin\ReportSearchType;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_report_search')]
class ReportSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private ReportSearchRequest $request;

    public function __construct(
        private readonly ReportRepository $reportRepository,
    ) {
        $this->request = new ReportSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = ReportSearch::create();
        if ($this->request->type !== null) {
            $search->type($this->request->type);
        }

        if ($this->request->user) {
            $search->userId($this->request->user);
        }

        if ($this->request->opponent) {
            $search->opponentId($this->request->opponent);
        }

        if ($this->request->entity) {
            $search->entityId($this->request->entity);
        }

        if ($this->request->read !== null) {
            $search->read($this->request->read);
        }

        if ($this->request->deleted !== null) {
            $search->deleted($this->request->deleted);
        }

        if ($this->request->archived !== null) {
            $search->archived($this->request->archived);
        }

        $total = $this->reportRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $reports = $this->reportRepository->searchReports($search, $this->perPage, $limit);

        return new SearchResult($reports, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ReportSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new ReportSearchRequest();
    }
}
