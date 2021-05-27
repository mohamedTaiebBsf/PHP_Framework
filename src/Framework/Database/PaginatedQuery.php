<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $queryCount;

    /**
     * @var string|null
     */
    private $entity;

    /**
     * @var array
     */
    private array $params;

    /**
     * PaginatedQuery constructor.
     *
     * @param \PDO $pdo
     * @param string $query
     * @param string $queryCount
     * @param string|null $entity
     * @param array $params
     */
    public function __construct(
        \PDO $pdo,
        string $query,
        string $queryCount,
        ?string $entity,
        array $params = []
    ) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->queryCount = $queryCount;
        $this->entity = $entity;
        $this->params = $params;
    }

    public function getNbResults(): int
    {
        if (!empty($this->params)) {
            $query = $this->pdo->prepare($this->queryCount);
            $query->execute($this->params);
            return $query->fetchColumn();
        }
        return $this->pdo->query($this->queryCount)->fetchColumn();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        foreach ($this->params as $key => $param) {
            $statement->bindParam($key, $param);
        }
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);
        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $statement->execute();

        return $statement->fetchAll();
    }
}
