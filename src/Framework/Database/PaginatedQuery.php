<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    private $pdo;
    private $query;
    private $queryCount;
    private string $entity;

    /**
     * PaginatedQuery constructor.
     * @param \PDO $pdo
     * @param string $query
     * @param string $queryCount
     */
    public function __construct(\PDO $pdo, string $query, string $queryCount, string $entity)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->queryCount = $queryCount;
        $this->entity = $entity;
    }

    public function getNbResults(): int
    {
        return $this->pdo->query($this->queryCount)->fetchColumn();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        $statement->execute();

        return $statement->fetchAll();
    }
}
