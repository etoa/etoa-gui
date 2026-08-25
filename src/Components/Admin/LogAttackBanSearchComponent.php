<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Fleet\Attack\Ban;
use EtoA\Fleet\Attack\BanFinder;
use EtoA\Fleet\FleetAction;
use EtoA\Form\Request\Admin\LogAttackBanSearchRequest;
use EtoA\Form\Type\Admin\LogAttackBanType;
use EtoA\Log\BattleLogRepository;
use EtoA\Log\BattleLogSearch;
use EtoA\Universe\Entity\EntityLabel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('log_attack_ban_search')]
class LogAttackBanSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var string[] */
    public array $users = [];
    /** @var EntityLabel[] */
    public array $entities = [];
    /** @var \FleetAction[] */
    public array $fleetActions = [];
    private LogAttackBanSearchRequest $request;

    public function __construct(
        private readonly BattleLogRepository $battleLogRepository,
        private readonly BanFinder           $attackBanFinder,
    ) {
        $this->perPage = 99999;
        $this->request = new LogAttackBanSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = BattleLogSearch::create();
        if ($this->request->date !== null) {
            $landtime = $this->request->date;
            $search->attackingBetween($landtime, $landtime - 3600 * 24);
        }

        if ($this->request->action !== null) {
            $search->action($this->request->action);
        }

        if ($this->request->attacker !== null) {
            $search->fleetUserId($this->request->attacker);
        }
        if ($this->request->defender !== null) {
            $search->entityUserId($this->request->defender);
        }

        $logs = $this->battleLogRepository->searchLogs($search);
        $bans = $this->attackBanFinder->find($logs);

        $total = count($bans);
        $limit = $this->getLimit($total);

        if ($total > 0) {
            $actions = array_unique(array_map(fn (Ban $ban) => $ban->action, $bans));
            foreach ($actions as $action) {
                $this->fleetActions[$action] = FleetAction::createFactory($action);
            }
        }

        return new SearchResult($bans, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogAttackBanType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new LogAttackBanSearchRequest();
    }
}
