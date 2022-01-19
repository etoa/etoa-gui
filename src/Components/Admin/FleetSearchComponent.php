<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Form\Type\Admin\FleetSearchType;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_fleet_search')]
class FleetSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $entities = [];
    /** @var array<int, string> */
    public array $fleetStatusCode;
    /** @var array<string, string> */
    public array $fleetActions;

    public function __construct(
        private FleetRepository $fleetRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = FleetSearch::create();
        if ($this->getFormValues()['entityFrom'] !== '') {
            $search->entityFrom((int) $this->getFormValues()['entityFrom']);
        }

        if ($this->getFormValues()['entityTo'] !== '') {
            $search->entityTo((int) $this->getFormValues()['entityTo']);
        }

        if ($this->getFormValues()['action'] !== '') {
            $search->actionIn([$this->getFormValues()['action']]);
        }

        if ($this->getFormValues()['status'] !== '') {
            $search->status((int) $this->getFormValues()['status']);
        }

        if ($this->getFormValues()['user'] !== '') {
            $search->user((int) $this->getFormValues()['user']);
        }

        $fleets = $this->fleetRepository->search($search);
        if (count($fleets) > 0) {
            $this->fleetStatusCode = \FleetAction::$statusCode;
            $this->fleetActions = \FleetAction::getAll();
            $this->users = $this->userRepository->searchUserNicknames();
            $entityIds = [];
            foreach ($fleets as $fleet) {
                $entityIds[] = $fleet->entityFrom;
                $entityIds[] = $fleet->entityTo;
            }

            $entities = $this->entityRepository->searchEntityLabels(EntityLabelSearch::create()->ids($entityIds));
            foreach ($entities as $entity) {
                $this->entities[$entity->id] = $entity->codeString() . ' ' . $entity->toStringWithOwner();
            }
        }

        return new SearchResult($fleets, 0, count($fleets), count($fleets));
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(FleetSearchType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [
            'entityTo' => '',
            'entityFrom' => '',
            'user' => '',
            'status' => '',
            'action' => '',
        ];
    }
}
