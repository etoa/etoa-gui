<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\LogDebrisType;
use EtoA\Log\DebrisLogRepository;
use EtoA\Log\DebrisLogSearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('log_debris_search')]
class LogDebrisSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var string[] */
    public array $admins;
    /** @var string[] */
    public array $users;

    public function __construct(
        private DebrisLogRepository $debrisLogRepository,
        private UserRepository $userRepository,
        private AdminUserRepository $adminUserRepository
    ) {
        $this->perPage = 50;
    }

    public function getSearch(): SearchResult
    {
        $search = DebrisLogSearch::create();

        if ($this->getFormValues()['date'] !== '') {
            $search->timeBefore(strtotime($this->getFormValues()['date']));
        }

        if ($this->getFormValues()['user'] !== '') {
            $search->userId((int) $this->getFormValues()['user']);
        }

        if ($this->getFormValues()['admin'] !== '') {
            $search->adminId((int) $this->getFormValues()['admin']);
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
        return $this->createForm(LogDebrisType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [
            'user' => 0,
            'admin' => 0,
            'date' => (new \DateTime())->format('Y-m-d\TH:i:s'),
        ];
    }
}
