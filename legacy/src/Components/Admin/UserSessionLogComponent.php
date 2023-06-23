<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\UserSessionLogRequest;
use EtoA\Form\Type\Admin\UserSessionLogType;
use EtoA\User\UserRepository;
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
    ) {
        $this->request = new UserSessionLogRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = UserSessionSearch::create();
        if ($this->request->userId !== null) {
            $search->userId($this->request->userId);
        }

        if ($this->request->ip !== null) {
            $search->ipLike($this->request->ip);
        }

        if ($this->request->client !== null) {
            $search->userAgentLike($this->request->client);
        }

        $total = $this->userSessionRepository->countLogs($search);

        $limit = $this->getLimit($total);

        $entries = $this->userSessionRepository->getSessionLogs($search, $this->perPage, $limit);
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
