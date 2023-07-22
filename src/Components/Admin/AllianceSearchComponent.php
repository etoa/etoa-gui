<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSearch;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\AllianceSearchRequest;
use EtoA\Form\Type\Admin\AllianceSearchType;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('alliance_search')]
class AllianceSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var string[] */
    public array $users = [];
    private AllianceSearchRequest $request;

    public function __construct(
        private readonly UserRepository     $userRepository,
        private readonly AllianceRepository $allianceRepository
    )
    {
        $this->perPage = 99999;
        $this->request = new AllianceSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = AllianceSearch::create();
        if ($this->request->name !== null) {
            $search->nameLike($this->request->name);
        }

        if ($this->request->tag !== null) {
            $search->nameLike($this->request->tag);
        }

        if ($this->request->text !== null) {
            $search->nameLike($this->request->text);
        }

        $alliances = $this->allianceRepository->searchAlliances($search);
        if (count($alliances) > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
        }

        return new SearchResult($alliances, 0, count($alliances), $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(AllianceSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new AllianceSearchRequest();
    }
}
