<?php

namespace Framework\Database;

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
    protected $entity = \stdClass::class;

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

    public function makeQuery(): Query
    {
        return (new Query($this->pdo))
            ->from($this->table, $this->table[0])
            ->into($this->entity);
    }

    /**
     * Récupère tous les enregistrements
     *
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->makeQuery();
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
        return $this->makeQuery()
            ->where("$field = :field")
            ->params(['field' => $value])
            ->fetchOrFail();
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
        return $this->makeQuery()
            ->where("id = :id")
            ->params(['id' => $id])
            ->fetchOrFail();
    }

    /**
     * Récupère le nombre d'enregistrement
     *
     * @return int
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
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
}
