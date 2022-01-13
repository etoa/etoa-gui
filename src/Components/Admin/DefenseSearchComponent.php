<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseListItem;
use EtoA\Defense\DefenseListSearch;
use EtoA\Defense\DefenseRepository;
use EtoA\Form\Type\Admin\DefenseSearchType;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_defense_search')]
class DefenseSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $defenseNames;
    /** @var array<int, string> */
    public array $entities;

    public function __construct(
        private DefenseRepository $defenseRepository,
        private DefenseDataRepository $defenseDataRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = DefenseListSearch::create();
        if ($this->getFormValues()['userId'] !== '') {
            $search->userId((int) $this->getFormValues()['userId']);
        }

        if ($this->getFormValues()['entityId'] !== '') {
            $search->entityId((int) $this->getFormValues()['entityId']);
        }

        if ($this->getFormValues()['defenseId'] !== '') {
            $search->defenseId((int) $this->getFormValues()['defenseId']);
        }

        $total = $this->defenseRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->defenseRepository->search($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->defenseNames = $this->defenseDataRepository->getDefenseNames(true);
            $this->entities = $this->getEntityLabels(array_map(fn (DefenseListItem $item) => $item->entityId, $entries));
        }

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(DefenseSearchType::class, $this->getFormValues());
    }
}
