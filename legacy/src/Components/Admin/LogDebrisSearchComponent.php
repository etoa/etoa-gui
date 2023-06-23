<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\LogDebrisSearchRequest;
use EtoA\Form\Type\Admin\LogDebrisType;
use EtoA\Log\DebrisLogRepository;
use EtoA\Log\DebrisLogSearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('log_debris_search')]
class LogDebrisSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var string[] */
    public array $admins;
    /** @var string[] */
    public array $users;
    private LogDebrisSearchRequest $request;

    public function __construct(
        private DebrisLogRepository $debrisLogRepository,
        private UserRepository $userRepository,
        private AdminUserRepository $adminUserRepository
    ) {
        $this->perPage = 50;
        $this->request = new LogDebrisSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = DebrisLogSearch::create();

        if ($this->request->date !== null) {
            $search->timeBefore($this->request->date);
        }

        if ($this->request->user !== null) {
            $search->userId($this->request->user);
        }

        if ($this->request->admin !== null) {
            $search->adminId($this->request->admin);
        }

        $total = $this->debrisLogRepository->count($search);

        $limit = $this->getLimit($total);

        $logs = [];
        if ($total > 0) {
            $logs = $this->debrisLogRepository->searchLogs($search, $this->perPage, $limit);
            $this->admins = $this->adminUserRepository->searchNicknames();
            $this->users = $this->userRepository->searchUserNicknames();
        }

        return new SearchResult($logs, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogDebrisType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new LogDebrisSearchRequest();
    }
}
