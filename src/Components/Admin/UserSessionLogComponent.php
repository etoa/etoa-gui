<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\UserSessionLogRequest;
use EtoA\Form\Type\Admin\UserSessionLogType;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionLogRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('user_session_log')]
class UserSessionLogComponent extends AbstractController
{
    use SearchComponentTrait;

    private UserSessionLogRequest $request;
    /** @var string[] */
    public array $users;

    public function __construct(
        private UserSessionRepository $userSessionRepository,
        private UserRepository $userRepository,
        private readonly UserSessionLogRepository $userSessionLogRepository
    ) {
        $this->request = new UserSessionLogRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = UserSessionSearch::create();
        if ($this->request->user) {
            $search->userId($this->request->user);
        }

        if ($this->request->ip) {
            $search->ipLike($this->request->ip);
        }

        if ($this->request->client) {
            $search->userAgentLike($this->request->client);
        }

        $total = $this->userSessionLogRepository->countLogs($search);

        $limit = $this->getLimit($total);

        $entries = $this->userSessionLogRepository->getSessionLogs($search, $this->perPage, $limit);
        $this->users = $this->userRepository->searchUserNicknames();

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UserSessionLogType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new UserSessionLogRequest();
    }
}
