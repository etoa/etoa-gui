<?php

declare(strict_types=1);

namespace EtoA\Pagination;

/**
 * An interface that defines methods needed to implement a paginator, i.e. an object that handles
 * a set of items and returns a sub set of items, given by a configuration.
 */
interface PaginatorInterface
{
    /**
     * Sets the amount of paginated items per page
     *
     * Must return a new instance of the Paginator with an updated internal state
     */
    public function withItemsPerPage(int $itemsPerPage): PaginatorInterface;

    /**
     * Sets the current page to calculate paginated items for
     *
     * Must return a new instance of the Paginator with an updated internal state
     */
    public function withCurrentPageNumber(int $currentPageNumber): PaginatorInterface;

    /**
     * Returns an iterable, sub set of the original set of items
     */
    public function getPaginatedItems(): iterable;

    /**
     * Returns the total number of pages, given the total number of non paginated items and the
     * items per page configuration
     */
    public function getNumberOfPages(): int;

    /**
     * Returns the current page number
     */
    public function getCurrentPageNumber(): int;

    /**
     * Returns the key of the first paginated item
     *
     * This is useful to display the exact range of
     * items that are available via getPaginatedItems
     */
    public function getKeyOfFirstPaginatedItem(): int;

    /**
     * Returns the key of the last paginated item
     *
     * This is useful to display the exact range of
     * items that are available via getPaginatedItems
     */
    public function getKeyOfLastPaginatedItem(): int;
}
