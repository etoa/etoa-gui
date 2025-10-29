<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Entity\ShipQueueItem;
use EtoA\Form\Request\Admin\ShipQueueSearchRequest;
use EtoA\Form\Type\Admin\ShipSearchType;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_ship_queue_search')]
class ShipQueueSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $shipNames;
    /** @var array<int, string> */
    public array $entities;
    private ShipQueueSearchRequest $request;

    public function __construct(
        private readonly ShipQueueRepository $shipQueueRepository,
    ) {
        $this->request = new ShipQueueSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = ShipQueueSearch::create();
        if ($this->request->user !== null) {
            $search->userId($this->request->user);
        }

        if ($this->request->entity !== null) {
            $search->entityId($this->request->entity);
        }

        if ($this->request->ship !== null) {
            $search->shipId($this->request->ship);
        }

        $total = $this->shipQueueRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->shipQueueRepository->searchQueueItems($search, $this->perPage, $limit);

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ShipSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new ShipQueueSearchRequest();
    }
}
