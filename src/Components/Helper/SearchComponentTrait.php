<?php declare(strict_types=1);

namespace EtoA\Components\Helper;

use EtoA\Universe\Entity\EntitySearch;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait SearchComponentTrait
{
    private int $perPage = 100;

    #[LiveProp(writable: true)]
    public int $limit = 0;

    /**
     * New search terms need to reset the current pagination progress
     */
    public function __invoke(): void
    {
        $this->limit = 0;
    }


    #[LiveAction]
    public function firstPage(): void
    {
        $this->limit = 0;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        $this->limit += $this->perPage;
    }

    #[LiveAction]
    public function previousPage(): void
    {
        $this->limit -= $this->perPage;
    }

    #[LiveAction]
    public function lastPage(): void
    {
        $this->limit = PHP_INT_MAX;
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->limit = 0;
        $this->resetFormValues();
        $this->instantiateForm();
    }

    private function getLimit(int $total): int
    {
        $limit = max(0, $this->limit);
        $limit = min($total, $limit);
        $limit -= $limit % $this->perPage;

        $this->limit = $limit;

        return $limit;
    }

    private function resetFormValues(): void
    {
        $this->formValues = [];
        foreach ($this->getFormInstance()->all() as $field) {
            $this->formValues[$field->getName()] = '';
        }
    }

    /**
     * @param int[] $entityIds
     * @return array<int, string>
     */
    private function getEntityLabels(array $entityIds): array
    {
        if (isset($this->entityRepository)) {
            $entityLabels = $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids($entityIds));

            $entities = [];
            foreach ($entityLabels as $entity) {
                $entities[$entity->id] = $entity->toString();
            }

            return $entities;
        }

        throw new \RuntimeException('EntityRepository must be set');
    }

    abstract public function getSearch(): SearchResult;
}