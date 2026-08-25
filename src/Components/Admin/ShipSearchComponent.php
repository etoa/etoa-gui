<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\ShipSearchRequest;
use EtoA\Form\Type\Admin\ShipSearchType;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipListSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_ship_search')]
class ShipSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private ShipSearchRequest $request;

    public function __construct(
        private readonly ShipListRepository $shipListRepository
    ) {
        $this->request = new ShipSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = ShipListSearch::create()->hasShips();
        if ($this->request->user) {
            $search->userId($this->request->user);
        }

        if ($this->request->entity) {
            $search->entityId($this->request->entity);
        }

        if ($this->request->ship) {
            $search->shipId($this->request->ship);
        }

        $total = $this->shipListRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->shipListRepository->search($search, $this->perPage, $limit);

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
