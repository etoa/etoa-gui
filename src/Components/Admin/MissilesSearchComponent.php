<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Entity\MissileListItem;
use EtoA\Form\Request\Admin\MissilesSearchRequest;
use EtoA\Form\Type\Admin\MissileSearchType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileListSearch;
use EtoA\Missile\MissileRepository;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_missiles_search')]
class MissilesSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $missileNames;
    /** @var array<int, string> */
    public array $entities;
    private MissilesSearchRequest $request;

    public function __construct(
        private MissileRepository $missileRepository,
        private MissileDataRepository $missileDataRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
    ) {
        $this->request = new MissilesSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = MissileListSearch::create()->hasMissiles();
        if ($this->request->userId !== null) {
            $search->userId($this->request->userId);
        }

        if ($this->request->entityId !== null) {
            $search->entityId($this->request->entityId);
        }

        if ($this->request->missileId !== null) {
            $search->missileId($this->request->missileId);
        }

        $total = $this->missileRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->missileRepository->search($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->missileNames = $this->missileDataRepository->getMissileNames(true);
            $entityIds = array_map(fn (MissileListItem $item) => $item->entityId, $entries);
            $this->entities = array_map(fn (EntityLabel $label) => $label->toString(), $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids($entityIds)));
        }

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MissileSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new MissilesSearchRequest();
    }
}
