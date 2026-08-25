<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\EntitySearchRequest;
use EtoA\Form\Type\Admin\EntitySearchType;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_entity_search')]
class EntitySearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private EntitySearchRequest $request;

    public function __construct(
        private EntityRepository $entityRepository,
    ) {
        $this->request = new EntitySearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = EntityLabelSearch::create();
        if ($this->request->name !== null) {
            $search->likePlanetName($this->request->name);
        }

        if ($this->request->entity !== null) {
            $search->id($this->request->entity);
        }

        if ($this->request->cell !== null) {
            $search->cellId($this->request->cell);
        }

        if ($this->request->user !== null) {
            $search->planetUserId($this->request->user);
        }

        if ($this->request->code !== null) {
            $search->codeIn([$this->request->code]);
        }

        if ($this->request->isMainPlanet !== null) {
            $search->planetUserMain($this->request->isMainPlanet);
        }

        if ($this->request->planetDebris !== null) {
            $search->planetDebris($this->request->planetDebris);
        }

        $total = $this->entityRepository->countEntityLabels($search);

        $limit = $this->getLimit($total);

        $entities = $this->entityRepository->searchEntityLabels($search, null, $this->perPage, $limit);

        return new SearchResult($entities, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EntitySearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new EntitySearchRequest();
    }
}
