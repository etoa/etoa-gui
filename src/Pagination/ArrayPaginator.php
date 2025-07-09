<?php

declare(strict_types=1);

namespace EtoA\Pagination;

final class ArrayPaginator extends AbstractPaginator
{
    /**
     * @var array
     */
    private array $items;

    /**
     * @var array
     */
    private array $paginatedItems = [];

    public function __construct(
        array $items,
        int $currentPageNumber = 1,
        int $itemsPerPage = 10
    ) {
        $this->items = $items;
        $this->setCurrentPageNumber($currentPageNumber);
        $this->setItemsPerPage($itemsPerPage);

        $this->updateInternalState();
    }

    /**
     * @return iterable
     */
    public function getPaginatedItems(): iterable
    {
        return $this->paginatedItems;
    }

    protected function updatePaginatedItems(int $itemsPerPage, int $offset): void
    {
        $this->paginatedItems = array_slice($this->items, $offset, $itemsPerPage);
    }

    protected function getTotalAmountOfItems(): int
    {
        return count($this->items);
    }

    protected function getAmountOfItemsOnCurrentPage(): int
    {
        return count($this->paginatedItems);
    }
}
