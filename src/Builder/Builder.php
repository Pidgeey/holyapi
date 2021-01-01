<?php

namespace Src\Builder;

use PDO;
use PDOException;
use Exception;
use Src\Model\Model;
use Src\Connections\Mysql;

/**
 * Class Builder
 * @package App
 */
class Builder
{
    /** @var string Builder */
    protected $builder;

    /** @var Model Model */
    protected $model;

    /** @var string Select */
    protected $select;

    /** @var string $statement */
    protected $statement;

    /** @var Mysql $connection */
    protected $connection;

    /**
     * Builder constructor.
     *
     * @param \Src\Model\Model $model
     */
    public function __construct(Model $model)
    {
        $this->select = '*';
        $this->model = $model;
        $this->connection = new Mysql();
    }

    /**
     * Add where clause
     *
     * @param string $column
     * @param string $operateur
     * @param string $value
     *
     * @return $this
     */
    public function where(string $column, string $operateur, string $value): self
    {
        $this->builder = $this->builder." WHERE $column $operateur '$value'";
        return $this;
    }

    /**
     * Add where in clause
     *
     * @param string $column
     * @param array $array
     *
     * @return $this
     */
    public function whereIn(string $column, array $array): self
    {
        $values = implode(',', $array);
        $this->builder = $this->builder." WHERE $column IN ( $values )";
        return $this;
    }

    /**
     * Add where and clause
     *
     * @param string $column
     * @param string $operator
     * @param string $value
     *
     * @return $this
     */
    public function andWhere(string $column, string $operator, string $value): self
    {
        $this->builder = $this->builder." AND $column $operator '$value'";
        return $this;
    }

    /**
     * Get entities
     *
     * @return array
     */
    public function get(): array
    {
        $modelsCollection = [];
        $attributesArray = $this->builderFetchObject();

        foreach ($attributesArray as $attributes) {
            $modelsCollection[] = new $this->model((array)$attributes, true);
        }
        return $modelsCollection;
    }

    public function first(): ?Model
    {
        $model = null;
        $this->limit(1);
        if (!empty($results = $this->get())) {
            $model = $results[0];
        }
        return $model;
    }

    /**
     * Get last entities
     *
     * @return \Src\Model\Model|null
     */
    public function last(): ?Model
    {
        $this->orderBy($this->model->getPrimaryKey(), 'DESC');
        return $this->first();
    }

    /**
     * Add an order by clause
     *
     * @param string $column
     * @param string $order
     *
     * @return $this
     */
    public function orderBy(string $column, string $order = 'ASC'): self
    {
        $this->builder = $this->builder." ORDER BY $column $order";
        return $this;
    }

    /**
     * Set a limit
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->builder = $this->builder." LIMIT $limit";
        return $this;
    }

    /**
     * Get count results
     *
     * @return int
     */
    public function count(): int
    {
        $this->select = 'count(*)';
        $result = $this->builderFetchObject();
        return (int) $result[0]->{'count(*)'};
    }

    /**
     * Execute sql request
     *
     * @return bool|\PDOStatement
     */
    protected function prepare()
    {
        $mysql = new Mysql();
        $this->prepareBuilder();
        $builder = $mysql->getConnection()->prepare($this->builder);
        $builder->execute();
        return $builder;
    }

    /**
     * Prepare sql request
     *
     * @return array
     */
    protected function builderFetchObject(): array
    {
        $builder = $this->prepare();
        return $builder->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Prepare builder
     *
     * @return $this
     */
    protected function prepareBuilder(): self
    {
        $this->builder = "SELECT {$this->select} FROM {$this->model->getModelTable()}$this->builder";
        return $this;
    }

    /**
     * Define list of selected attributes
     *
     * @param $select
     *
     * @return $this
     */
    public function select($select): self
    {
        if (is_array($select)) {
            $selectQuery = '';
            foreach ($select as $index => $column) {
                if ($index === 0) {
                    $selectQuery = "$column";
                } else {
                    $selectQuery = $selectQuery.", $column";
                }

            }
            $select = $selectQuery;
        }
        $this->select = $select;

        return $this;
    }

    /**
     * Left join clause
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $primaryKey
     *
     * @return $this
     */
    public function leftJoin(string $table, string $foreignKey, string $primaryKey): self
    {
        $this->builder = $this->builder." LEFT JOIN $table ON $foreignKey = $primaryKey";
        return $this;
    }


    /**
     * Make an update request
     *
     * @param array $attributes
     * @param Model $model
     *
     * @return bool
     */
    public function update(array $attributes, Model $model): bool
    {
        $data = [];
        $this->builder = "UPDATE {$this->model->getModelTable()} SET";
        foreach ($attributes as $column => $value) {
            $data[$column] = $value;
            $this->builder = $this->builder." $column = :$column,";
        }

        $primaryKey = $this->model->getPrimaryKey();

        $this->builder = substr($this->builder, 0, -1);

        if (is_array($primaryKey)) {
            foreach ($primaryKey as $index => $value) {
                $data[$value] = $model->{$value};
                if ($index === 0) {
                    $this->builder = $this->builder." WHERE $value = :$value";
                } else {
                    $this->builder = $this->builder." AND $value = :$value";
                }
            }
        }

        try {
            $mysql = new Mysql();
            $builder = $mysql->getConnection()->prepare($this->builder);
            $builder->execute($data);
        } catch (PDOException $e) {
            throw new $e($e->getMessage());
        }

        return true;
    }

    /**
     * Make a delete request
     *
     * @param Model|null $model
     *
     * @return bool
     */
    public function delete(?Model $model = null): bool
    {
        $this->statement = "DELETE";
        $primaryKey = $this->model->getPrimaryKey();

        if ($model) {
            if (is_array($primaryKey)) {
                foreach ($primaryKey as $index => $value) {
                    if ($index === 0) {
                        $this->builder = $this->builder." WHERE $value = '{$model->{$value}}'";
                    } else {
                        $this->builder = $this->builder." AND $value = '{$model->{$value}}'";
                    }
                }
            }
        }

        try {
            $this->connection
                ->getConnection()
                ->exec(sprintf('%s FROM %s %s', $this->statement, $this->model->getModelTable(), $this->builder));
        } catch (PDOException $e) {
            throw new $e($e->getMessage());
        }

        return true;
    }

    /**
     * Make an insert request
     *
     * @return int
     * @throws \Exception
     */
    public function insert(): int
    {
        $sql = $this->makeInsertRequest($this->model->getModelTable(), $this->model->getAttributes());
        $db = $this->connection->getConnection()->prepare($sql);

        try{
            $db->execute();
        } catch (PDOException $exception) {
            throw new Exception($db->errorInfo()[2]);
        }

        return $this->connection->getLastInsertId();
    }

    /**
     * Prepare an insert request
     *
     * @param array $attributes
     * @param string $table
     * @return string
     */
    private function makeInsertRequest(string $table, array $attributes): string
    {
        $values = [];
        foreach ($attributes as $attribute) {
            $values[] = "'$attribute'";
        }
        return sprintf(
            "INSERT INTO $table (%s) VALUES (%s);",
            implode(',',array_keys($attributes)),
            implode(',',array_values($values))
        );
    }
}
