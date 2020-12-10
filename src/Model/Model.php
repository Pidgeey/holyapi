<?php

namespace Src\Model;

use Src\Builder\Builder;
use Src\Resources\Resource;

/**
 * Class BaseModel
 *
 * @package App\Models
 */
class Model implements ModelInterface
{
    /**
     * Table
     *
     * @var string
     */
    protected $table;

    /**
     * Primary key
     *
     * @var string|array
     */
    protected $primaryKey = 'id';

    /**
     * Model attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Hidden attributes
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Resource class
     *
     * @var string
     */
    public $resource = Resource::class;

    /**
     * AbstractModel constructor.
     *
     * @param array $attributes
     * @param bool $get
     */
    public function __construct(array $attributes = [], bool $get = false)
    {
        if ($get) {
            foreach ($attributes as $attribute => $value) {
                if (!in_array($attribute, $this->hidden)) {
                    $this->attributes[$attribute] = $value;
                }
            }
        } else {
            foreach ($attributes as $attribute => $value) {
                if (in_array($attribute, $this->fillable)) {
                    $this->attributes[$attribute] = $value;
                }
            }
        }
    }

    /**
     * Get model table
     *
     * @return string
     */
    public function getModelTable(): string
    {
        return $this->table;
    }

    /**
     * Get model primary key
     *
     * @return string|array
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Define model attributes
     *
     * @param $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get model attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Create new instance of current model
     *
     * @param array $attributes
     *
     * @return int
     * @throws \Exception
     */
    public static function create(array $attributes): int
    {
        $model = new static($attributes);
        return $model->save();
    }

    /**
     * Save a model in base
     *
     * @return int
     * @throws \Exception
     */
    public function save(): int
    {
        $builder = new Builder($this);
        return $builder->insert();

    }

    /**
     * Get model by param id
     *
     * @param int $id
     *
     * @return Model
     */
    public static function find(int $id): Model
    {
        $builder = new Builder(new static());
        $builder->where('id', '=', $id);
        return $builder->first();
    }

    /**
     * Get last entities
     *
     * @return Model|null
     */
    public static function last(): ?Model
    {
        $builder = new Builder(new static());
        return $builder->last();
    }

    /**
     * Update current model
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function update(array $attributes): bool
    {
        $builder = self::getQueryBuilder();
        return $builder->update($attributes, $this);
    }

    /**
     * Delete current model
     *
     * @return bool
     */
    public function delete(): bool
    {
        $builder = self::getQueryBuilder();
        return $builder->delete($this);
    }

    /**
     * Create model builder
     *
     * @param array $filters
     * @return Builder
     */
    public static function getQueryBuilder(array $filters = []): Builder
    {
        return new Builder(new static());
    }

    /**
     * Setter magic
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Getter magic
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->attributes[$name];
    }
}
