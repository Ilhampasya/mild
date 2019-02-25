<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Database;

use ArrayAccess;
use Carbon\Carbon;
use JsonSerializable;
use DateTimeInterface;
use InvalidArgumentException;
use Mild\Database\Queries\Query;
use Mild\Database\Relations\HasOne;
use Mild\Database\Relations\HasMany;
use Mild\Database\Relations\BelongsTo;

abstract class Model implements ArrayAccess, JsonSerializable
{
    /**
     * Set table name
     *
     * @var string
     */
    protected $table;
    /**
     * Set primary key on the relation
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * Set per page for pagination
     *
     * @var int
     */
    protected $perPage = 15;
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var Query
     */
    protected $query;
    /**
     * Set date format on the time
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';
    /**
     * We need dependency on the application for get a model instance through the constructor
     * and get a connection after register the database
     *
     * @var \Mild\App
     */
    protected static $app;

    /**
     * @param $model
     * @param string $relation
     * @param string $primaryKey
     * @param string $foreignKey
     * @return BelongsTo
     */
    public function belongsTo($model, $relation = '', $primaryKey = '', $foreignKey = '')
    {
        if ($model instanceof Model === false) {
            $model = $model::instance();
        }
        if (empty($relation)) {
            $relation = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['function'];
        }
        if (empty($primaryKey)) {
            $primaryKey = $model->getPrimaryKey();
        }
        if (empty($foreignKey)) {
            $foreignKey = $relation.'_'.$primaryKey;
        }
        return new BelongsTo($model->where($primaryKey, '=', $this->{$foreignKey}));
    }

    /**
     * @param $model
     * @param string $foreignKey
     * @param string $primaryKey
     * @return HasMany
     */
    public function hasMany($model, $foreignKey = '', $primaryKey = '')
    {
        if ($model instanceof Model === false) {
            $model = $model::instance();
        }
        if (empty($foreignKey)) {
            $segments = explode('\\', static::class);
            $foreignKey = strtolower(end($segments)).'_'.$model->getPrimaryKey();
        }
        if (empty($primaryKey)) {
            $primaryKey = $this->getPrimaryKey();
        }
        return new HasMany($model->where($foreignKey, $this->{$primaryKey}));
    }

    /**
     * @param $model
     * @param string $foreignKey
     * @param string $primaryKey
     * @return HasOne
     */
    public function hasOne($model, $foreignKey = '', $primaryKey = '')
    {
        if ($model instanceof Model === false) {
            $model = $model::instance();
        }
        if (empty($foreignKey)) {
            $class = explode('\\', static::class);
            $foreignKey = strtolower(end($class)).'_'.$model->getPrimaryKey();
        }
        if (empty($primaryKey)) {
            $primaryKey = $this->getPrimaryKey();
        }
        return new HasOne($model->where($foreignKey, $this->{$primaryKey}));
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param $attributes
     * @return void
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (!$this->hasAttribute($name)) {
            if (method_exists($this, $name)) {
                return $this->$name()->get();
            }
            throw new InvalidArgumentException('Attribute ['.$name.'] does not exist');
        }
        $value = $this->attributes[$name];
        if (strtotime($value) !== false) {
            $parsers = date_parse($value);
            if (checkdate($parsers['month'], $parsers['day'], $parsers['year'])) {
                $value = Carbon::create($value);
            }
        }
        return $value;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function setAttribute($name, $value)
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format($this->dateFormat);
        }
        $this->attributes[$name] = $value;
    }

    /**
     * @param $name
     * @return void
     */
    public function putAttribute($name)
    {
        if ($this->hasAttribute($name)) {
            unset($this->attributes[$name]);
        }
    }

    /**
     * @param \Mild\App $app
     * @return void
     */
    public static function setApp($app)
    {
        static::$app = $app;
    }

    /**
     * @return \Mild\App
     */
    public static function getApp()
    {
        return static::$app;
    }

    /**
     * @param $table
     * @return void
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @param int $perPage
     * @return void
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * If empty the table name, we will make plural on the class name
     *
     * @return string
     */
    public function getTable()
    {
        if (empty($this->table)) {
            $table = explode('\\', strtolower(static::class));
            $table = end($table);
            if ($table[-1] !== 's') {
                $table .= 's';
            }
            return $table;
        }
        return $this->table;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @return Database
     * @throws \ReflectionException
     */
    public static function getConnection()
    {
        return static::$app->get('db');
    }

    /**
     * @return static
     * @throws \ReflectionException
     */
    public static function instance()
    {
        return static::$app->instance(static::class);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function lastInsertId()
    {
        return static::getConnection()->lastInsertId();
    }

    /**
     * @return bool
     */
    public function save()
    {
        return $this->insert($this->getAttributes());
    }

    /**
     * @param array $columns
     * @param string $pageName
     * @param int $page
     * @return mixed
     * @throws \ReflectionException
     */
    public function paginate($columns = ['*'], $pageName = 'page', $page = 0)
    {
        return $this->__call('paginate', [static::$app->get('request'), $this->perPage, $columns, $pageName, $page]);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public function __call($name, $arguments)
    {
        if (!empty($this->query)) {
            $result = $this->query->$name(...$arguments);
        } else {
            $result = static::getConnection()->table($this->getTable())->setModel(static::class)->$name(...$arguments);
        }
        if ($result instanceof Query) {
            $this->query = $result;
            return $this;
        }
        return $result;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public static function __callStatic($name, $arguments)
    {
        return static::$app->instance(static::class)->$name(...$arguments);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasAttribute($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        return $this->setAttribute($name, $value);
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        return $this->putAttribute($name);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasAttribute($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->setAttribute($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        return $this->putAttribute($offset);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getAttributes();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}