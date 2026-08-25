<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Alliance\AllianceSpendRepository;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Entity\Alliance;
use EtoA\Form\Request\Admin\AdminAllianceDepositSearchRequest;
use EtoA\Form\Type\Admin\AllianceDepositSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('admin_alliance_deposit_search')]
class AdminAllianceDepositSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    #[LiveProp]
    public Alliance $alliance;
    public bool $sum = false;
    private AdminAllianceDepositSearchRequest $request;

    public function __construct(
        private readonly AllianceSpendRepository $allianceSpendRepository,
    ) {
        $this->request = new AdminAllianceDepositSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $this->sum = (bool) $this->request->display;
        if ($this->sum) {
            $entries = [
                $this->allianceSpendRepository->getTotalSpent($this->alliance, $this->request->user),
            ];
        } else {
            $entries = $this->alliance->getSpends()->toArray();
        }

        return new SearchResult($entries, 0, count($entries), count($entries));
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(AllianceDepositSearchType::class, $this->request, ['allianceId' => $this->alliance]);
    }

    private function resetFormRequest(): void
    {
        $this->request = new AdminAllianceDepositSearchRequest();
    }
}
