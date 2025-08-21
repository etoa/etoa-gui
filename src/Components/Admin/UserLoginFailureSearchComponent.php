<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\UserLoginFailureRequest;
use EtoA\Form\Type\Admin\UserLoginFailureType;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserLoginFailureSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('user_login_failures')]
class UserLoginFailureSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private UserLoginFailureRequest $request;

    public function __construct(
        private readonly UserLoginFailureRepository $userLoginFailureRepository,
    ) {
        $this->perPage = 99;
        $this->request = new UserLoginFailureRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = UserLoginFailureSearch::create();
        if ($this->request->user) {
            $search->userId($this->request->user);
        }

        if ($this->request->ip) {
            $search->likeIp($this->request->ip);
        }

        if ($this->request->host) {
            $search->likeHost($this->request->host);
        }

        if ($this->request->client) {
            $search->likeClient($this->request->client);
        }

        $total = $this->userLoginFailureRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->userLoginFailureRepository->search($search, $this->perPage, $limit);

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UserLoginFailureType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new UserLoginFailureRequest();
    }
}
