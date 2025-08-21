<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseListSearch;
use EtoA\Defense\DefenseRepository;
use EtoA\Form\Request\Admin\DefenseSearchRequest;
use EtoA\Form\Type\Admin\DefenseSearchType;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_defense_search')]
class DefenseSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private DefenseSearchRequest $request;

    public function __construct(
        private readonly DefenseRepository     $defenseRepository,
    ) {
        $this->request = new DefenseSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = DefenseListSearch::create();
        if ($this->request->user) {
            $search->userId($this->request->user);
        }

        if ($this->request->entity) {
            $search->entityId($this->request->entity);
        }

        if ($this->request->defense) {
            $search->defenseId($this->request->defense);
        }

        $total = $this->defenseRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $entries = $this->defenseRepository->search($search, $this->perPage, $limit);

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
