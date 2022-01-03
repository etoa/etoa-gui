<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\ReportSearchType;
use EtoA\Message\ReportAggregator;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use EtoA\Message\ReportTypes;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_report_search')]
class ReportSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var array<string, string> */
    public array $types;
    /** @var array<int, string> */
    public array $users;

    public function __construct(
        private ReportRepository $reportRepository,
        private UserRepository $userRepository,
        private ReportAggregator $reportAggregator
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = ReportSearch::create();
        if ($this->getFormValues()['type'] !== '') {
            $search->type($this->getFormValues()['type']);
        }

        if ($this->getFormValues()['userId'] !== '') {
            $search->userId((int) $this->getFormValues()['userId']);
        }

        if ($this->getFormValues()['opponentId'] !== '') {
            $search->opponentId((int) $this->getFormValues()['opponentId']);
        }

        if ($this->getFormValues()['entityId'] !== '') {
            $search->entityId((int) $this->getFormValues()['entityId']);
        }

        if (!is_array($this->getFormValues()['read']) && $this->getFormValues()['read'] !== '') {
            $search->read((bool) $this->getFormValues()['read']);
        }

        if (!is_array($this->getFormValues()['deleted']) && $this->getFormValues()['deleted'] !== '') {
            $search->deleted((bool) $this->getFormValues()['deleted']);
        }

        if (!is_array($this->getFormValues()['archived']) && $this->getFormValues()['archived'] !== '') {
            $search->archived((bool) $this->getFormValues()['archived']);
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
        return $this->createForm(ReportSearchType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [];
        foreach ($this->getFormInstance()->all() as $field) {
            $this->formValues[$field->getName()] = '';
        }
    }
}
