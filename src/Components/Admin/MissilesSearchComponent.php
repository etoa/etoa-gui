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

    private MissilesSearchRequest $request;

    public function __construct(
        private readonly MissileRepository $missileRepository,
    ) {
        $this->request = new MissilesSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = MissileListSearch::create()->hasMissiles();
        if ($this->request->user) {
            $search->userId($this->request->user);
        }

        if ($this->request->entity) {
            $search->entityId($this->request->entity);
        }

        if ($this->request->missile) {
            $search->missileId($this->request->missile);
        }

        $total = $this->missileRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->missileRepository->search($search, $this->perPage, $limit);

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
