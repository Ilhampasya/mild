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

use PDO;
use InvalidArgumentException;
use Mild\Database\Queries\MysqlQuery;
use Mild\Database\Queries\SqliteQuery;
use Mild\Database\Queries\PostgresQuery;
use Mild\Database\Queries\SqlServerQuery;

class Database extends PDO
{
    /**
     * Determine whether the last insert ID did not return the last id due to being affected by the commit
     *
     * @var string
     */
    protected $lastId;
    /**
     * Set if the table prefix
     *
     * @var string
     */
    protected $prefix;
    /**
     * Set the table suffix
     *
     * @var string
     */
    protected $suffix;
    /**
     * Lists of the supports query
     *
     * @var array
     */
    protected $queries = [
        'mysql' => MysqlQuery::class,
        'sqlite' => SqliteQuery::class,
        'pgsql' => PostgresQuery::class,
        'sqlsrv' => SqlServerQuery::class
    ];

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $dsn = '';
        $username = '';
        $password = '';
        $options = [];
        if (isset($config['dsn'])) {
            $dsn = $config['dsn'];
        }
        if (isset($config['prefix'])) {
            $this->prefix = $config['prefix'];
        }
        if (isset($config['suffix'])) {
            $this->suffix = $config['suffix'];
        }
        if (isset($config['username'])) {
            $username = $config['username'];
        }
        if (isset($config['password'])) {
            $password = $config['password'];
        }
        if (isset($config['options'])) {
            $options = $config['options'];
        }
        $options[parent::ATTR_ERRMODE] = parent::ERRMODE_EXCEPTION;
        parent::__construct($dsn, $username, $password, $options);
        if (isset($config['resolver'])) {
            $config['resolver']($this);
        }
    }

    /**
     * @param $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param $suffix
     * @return void
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * @param $lastId
     * @return void
     */
    public function setLastId($lastId)
    {
        $this->lastId = $lastId;
    }

    /**
     * Determine whether the last insert ID did not return
     * the last id due to being affected by the commit
     * usually, the method will be useful if the driver is mysql.
     *
     * @param null $name
     * @return string
     */
    public function lastInsertId($name = null)
    {
        if (!empty($this->lastId)) {
            return $this->lastId;
        }
        return parent::lastInsertId($name);
    }

    /**
     * Create a query builder
     *
     * @param $table
     * @return Queries\Query
     */
    public function table($table)
    {
        $driver = $this->getDriver();
        if (!isset($this->queries[$driver])) {
            throw new InvalidArgumentException('Unsupported driver: '.$driver.'');
        }
        return new $this->queries[$driver]($this, $this->prefix.$table.$this->suffix);
    }

    /**
     * @return string
     */
    public function getLastId()
    {
        return $this->lastId;
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->getAttribute(parent::ATTR_DRIVER_NAME);
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @return int
     */
    public function getFetchMode()
    {
        return parent::FETCH_OBJ;
    }

    /**
     * @param \PDOStatement $stmt
     * @param $bindings
     * @return void
     */
    public function bindValues($stmt, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $stmt->bindValue(
                is_string($key) ? $key : $key + 1, $value,
                is_int($value) ? parent::PARAM_INT : parent::PARAM_STR
            );
        }
    }
}