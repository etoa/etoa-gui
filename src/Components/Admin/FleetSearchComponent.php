<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Form\Request\Admin\FleetSearchRequest;
use EtoA\Form\Type\Admin\FleetSearchType;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_fleet_search')]
class FleetSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $entities = [];
    /** @var array<int, string> */
    public array $fleetStatusCode;
    /** @var array<string, string> */
    public array $fleetActions;
    private FleetSearchRequest $request;

    public function __construct(
        private readonly FleetRepository $fleetRepository,
        private readonly EntityRepository $entityRepository
    ) {
        $this->request = new FleetSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = FleetSearch::create();
        if ($this->request->entityFrom !== null) {
            $search->entityFrom($this->request->entityFrom);
        }

        if ($this->request->entityTo !== null) {
            $search->entityTo($this->request->entityTo);
        }

        if ($this->request->action !== null) {
            $search->actionIn([$this->request->action]);
        }

        if ($this->request->status !== null) {
            $search->status($this->request->status);
        }

        if ($this->request->user !== null) {
            $search->user($this->request->user);
        }

        $fleets = $this->fleetRepository->search($search);
        if (count($fleets) > 0) {
            $this->fleetStatusCode = FleetAction::$statusCode;
            $this->fleetActions = FleetAction::getAll();
            $entityIds = [];
            foreach ($fleets as $fleet) {
                $entityIds[] = $fleet->getEntityFrom()->getId();
                $entityIds[] = $fleet->getEntityTo()->getId();
            }

            $entities = $this->entityRepository->searchEntityLabels(EntityLabelSearch::create()->ids($entityIds));
            foreach ($entities as $entity) {
                $this->entities[$entity->getId()] = $entity->codeString() . ' ' . $entity->toString();
            }
        }

        return new SearchResult($fleets, 0, count($fleets), count($fleets));
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(FleetSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new FleetSearchRequest();
    }
}
