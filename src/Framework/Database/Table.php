<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;

class Table
{
    /**
     * @var \PDO
     */
    protected $pdo;


    /**
     * Nom de la table en BDD
     *
     * @var string
     */
    protected $table;

    /**
     * Entité à utilisée
     *
     * @var string|null
     */
    protected $entity;

    /**
     * PostTable constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Paginate des éléments
     *
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    protected function paginationQuery()
    {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * Récupère une liste $key => $value de nos enregistrement
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Récupère tous les enregistrements
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table}");

        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(\PDO::FETCH_OBJ);
        }

        return $query->fetchAll();
    }

    /**
     * Récupère une ligne par rapport à un champs
     *
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }

    /**
     * Récupère un élément à partir son ID
     *
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Récupère le nombre d'enregistrement
     *
     * @return int
     */
    public function count(): int
    {
        return $this->fetchColumn("SELECT count(id) FROM {$this->table}");
    }

    /**
     * Mise à jour d'un enregistrement au niveau de la BDD
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * Insérer un nouvel enregistrement
     *
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $statement = $this->pdo->prepare("INSERT INTO $this->table ($fields) VALUES ($values)");
        return $statement->execute($params);
    }

    /**
     * Supprimer un enregistrement
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {

        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Vérifie si n enregistrement existe
     *
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }

    /**
     * Permet d'executer la requête et de récupérer le 1er résultat
     *
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }

        $record = $query->fetch();

        if ($record === false) {
            throw new NoRecordException();
        }

        return $record;
    }

    /**
     * Récupère la 1ère colonne
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    private function fetchColumn(string $query, array $params = [])
    {
        $query = $this->pdo
            ->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }

        return $query->fetchColumn();
    }
}
