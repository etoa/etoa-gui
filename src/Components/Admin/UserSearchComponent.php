<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\UserSearchRequest;
use EtoA\Form\Type\Admin\UserSearchType;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('user_search')]
class UserSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    public int $time;
    private UserSearchRequest $request;

    public function __construct(
        private UserRepository $userRepository,
    ) {
        $this->perPage = 99999;
        $this->request = new UserSearchRequest();
        $this->time = time();
    }

    public function getSearch(): SearchResult
    {
        $search = UserSearch::create();
        if ($this->request->nickname !== null) {
            $search->nickLike($this->request->nickname);
        }

        if ($this->request->name !== null) {
            $search->nameLike($this->request->name);
        }

        if ($this->request->email !== null) {
            $search->emailLike($this->request->email);
        }

        if ($this->request->emailFix !== null) {
            $search->emailFixLike($this->request->emailFix);
        }

        if ($this->request->emailFix !== null) {
            $search->emailFixLike($this->request->emailFix);
        }

        if ($this->request->allianceId !== null) {
            $search->allianceId($this->request->allianceId);
        }

        if ($this->request->raceId !== null) {
            $search->raceId($this->request->raceId);
        }

        if ($this->request->hmod !== null) {
            $this->request->hmod ? $search->inHmode() : $search->notInHmode();
        }

        if ($this->request->blocked !== null) {
            $this->request->blocked ? $search->blocked() : $search->notBlocked();
        }

        if ($this->request->ghost !== null) {
            $search->ghost($this->request->ghost);
        }

        if ($this->request->chatAdmin !== null) {
            $search->chatadmin($this->request->chatAdmin);
        }

        $users = $this->userRepository->searchUsers($search);

        return new SearchResult($users, 0, count($users), $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UserSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new UserSearchRequest();
    }
}
