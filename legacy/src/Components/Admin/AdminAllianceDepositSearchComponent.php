<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Alliance\AllianceSpendRepository;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\AdminAllianceDepositSearchRequest;
use EtoA\Form\Type\Admin\AllianceDepositSearchType;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_alliance_deposit_search')]
class AdminAllianceDepositSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    #[LiveProp()]
    public int $allianceId;
    public ?int $userId = null;
    /** @var string[] */
    public array $users;
    public bool $sum = false;
    private AdminAllianceDepositSearchRequest $request;

    public function __construct(
        private AllianceSpendRepository $allianceSpendRepository,
        private UserRepository $userRepository
    ) {
        $this->request = new AdminAllianceDepositSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $this->sum = (bool) $this->request->display;
        if ($this->sum) {
            $entries = [
                $this->allianceSpendRepository->getTotalSpent($this->allianceId, $this->request->user),
            ];
        } else {
            $entries = $this->allianceSpendRepository->getSpent($this->allianceId, $this->request->user, 0);
        }

        $total = count($entries);
        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames(UserSearch::create()->allianceId($this->allianceId));
        }

        return new SearchResult($entries, 0, count($entries), $total);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(AllianceDepositSearchType::class, $this->request, ['allianceId' => $this->allianceId]);
    }

    private function resetFormRequest(): void
    {
        $this->request = new AdminAllianceDepositSearchRequest();
    }
}
