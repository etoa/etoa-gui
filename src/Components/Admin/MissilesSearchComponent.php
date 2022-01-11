<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\MissileSearchType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileListItem;
use EtoA\Missile\MissileListSearch;
use EtoA\Missile\MissileRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_missiles_search')]
class MissilesSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $missileNames;
    /** @var array<int, string> */
    public array $entities;

    public function __construct(
        private MissileRepository $missileRepository,
        private MissileDataRepository $missileDataRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = MissileListSearch::create()->hasMissiles();
        if ($this->getFormValues()['userId'] !== '') {
            $search->userId((int) $this->getFormValues()['userId']);
        }

        if ($this->getFormValues()['entityId'] !== '') {
            $search->entityId((int) $this->getFormValues()['entityId']);
        }

        if ($this->getFormValues()['missileId'] !== '') {
            $search->missileId((int) $this->getFormValues()['missileId']);
        }

        $total = $this->missileRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->missileRepository->search($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->missileNames = $this->missileDataRepository->getMissileNames(true);
            $this->entities = $this->getEntityLabels(array_map(fn (MissileListItem $item) => $item->entityId, $entries));
        }

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MissileSearchType::class, $this->getFormValues());
    }
}
