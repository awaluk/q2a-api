<?php

declare(strict_types=1);

namespace Q2aApi\Base;

class Paginator
{
    private $itemsCount;
    private $perPage;
    private $currentPage;

    public function __construct(int $itemsCount, int $perPage, int $currentPage = 1)
    {
        $this->itemsCount = $itemsCount;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    public function getCurrentPage(): int
    {
        if ($this->currentPage > 1) {
            return $this->currentPage;
        }

        return 1;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getItemsCount(): int
    {
        return $this->itemsCount;
    }

    public function getFirstPage(): int
    {
        return 1;
    }

    public function getLastPage(): int
    {
        return (int)ceil($this->itemsCount / $this->perPage);
    }

    public function getFirstItem(): int
    {
        return (($this->getCurrentPage() - 1) * $this->perPage) + 1;
    }

    public function getLastItem(): int
    {
        return $this->getCurrentPage() * $this->perPage;
    }

    /**
     * @return int|null
     */
    public function getPreviousPage()
    {
        if ($this->getCurrentPage() > $this->getFirstPage()) {
            return $this->getCurrentPage() - 1;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getNextPage()
    {
        if ($this->getCurrentPage() < $this->getLastPage()) {
            return $this->getCurrentPage() + 1;
        }

        return null;
    }
}
