<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseListItem;
use EtoA\Defense\DefenseListSearch;
use EtoA\Defense\DefenseRepository;
use EtoA\Form\Request\Admin\DefenseSearchRequest;
use EtoA\Form\Type\Admin\DefenseSearchType;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_defense_search')]
class DefenseSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $defenseNames;
    /** @var array<int, string> */
    public array $entities;
    private DefenseSearchRequest $request;

    public function __construct(
        private DefenseRepository $defenseRepository,
        private DefenseDataRepository $defenseDataRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
    ) {
        $this->request = new DefenseSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = DefenseListSearch::create();
        if ($this->request->userId !== null) {
            $search->userId($this->request->userId);
        }

        if ($this->request->entityId !== null) {
            $search->entityId($this->request->entityId);
        }

        if ($this->request->defenseId !== null) {
            $search->defenseId($this->request->defenseId);
        }

        $total = $this->defenseRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->defenseRepository->search($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->defenseNames = $this->defenseDataRepository->getDefenseNames(true);
            $entityIds = array_map(fn (DefenseListItem $item) => $item->entityId, $entries);
            $this->entities = array_map(fn (EntityLabel $label) => $label->toString(), $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids($entityIds)));
        }

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(DefenseSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new DefenseSearchRequest();
    }
}
