<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Entity\ShipListItem;
use EtoA\Form\Request\Admin\ShipSearchRequest;
use EtoA\Form\Type\Admin\ShipSearchType;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListSearch;
use EtoA\Ship\ShipRepository;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_ship_search')]
class ShipSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $shipNames;
    /** @var array<int, string> */
    public array $entities;
    private ShipSearchRequest $request;

    public function __construct(
        private ShipRepository $shipRepository,
        private ShipDataRepository $shipDataRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
    ) {
        $this->request = new ShipSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = ShipListSearch::create()->hasShips();
        if ($this->request->userId !== null) {
            $search->userId($this->request->userId);
        }

        if ($this->request->entityId !== null) {
            $search->entityId($this->request->entityId);
        }

        if ($this->request->shipId !== null) {
            $search->shipId($this->request->shipId);
        }

        $total = $this->shipRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->shipRepository->search($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->shipNames = $this->shipDataRepository->getShipNames(true);
            $entityIds = array_map(fn (ShipListItem $item) => $item->entityId, $entries);
            $this->entities = array_map(fn (EntityLabel $label) => $label->toString(), $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids($entityIds)));
        }

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ShipSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new ShipSearchRequest();
    }
}
