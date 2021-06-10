<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * PaginatedQuery constructor.
     *
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getNbResults(): int
    {
        return $this->query->count();
    }

    public function getSlice(int $offset, int $length): QueryResult
    {
        $query = clone $this->query;

        return $query->limit($length, $offset)->fetchAll();
    }
}
