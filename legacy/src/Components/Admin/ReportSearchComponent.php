<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\ReportSearchRequest;
use EtoA\Form\Type\Admin\ReportSearchType;
use EtoA\Message\ReportAggregator;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use EtoA\Message\ReportTypes;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_report_search')]
class ReportSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<string, string> */
    public array $types;
    /** @var array<int, string> */
    public array $users;
    private ReportSearchRequest $request;

    public function __construct(
        private ReportRepository $reportRepository,
        private UserRepository $userRepository,
        private ReportAggregator $reportAggregator
    ) {
        $this->request = new ReportSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = ReportSearch::create();
        if ($this->request->type !== null) {
            $search->type($this->request->type);
        }

        if ($this->request->userId !== null) {
            $search->userId($this->request->userId);
        }

        if ($this->request->opponentId !== null) {
            $search->opponentId($this->request->opponentId);
        }

        if ($this->request->entityId !== null) {
            $search->entityId($this->request->entityId);
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

        $total = $this->reportRepository->count($search);

        $limit = $this->getLimit($total);

        $reports = $this->reportRepository->searchReports($search, $this->perPage, $limit);

        if (count($reports) > 0) {
            $reports = $this->reportAggregator->aggregate($reports);
            $this->types = ReportTypes::TYPES;
            $this->users = $this->userRepository->searchUserNicknames();
        }

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
