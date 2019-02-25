<?php

namespace Mild\Cache;

use InvalidArgumentException;

class DatabaseHandler implements HandlerInterface
{
    /**
     * @var string
     */
    protected $prefix;
    /**
     * Set the table on the database
     *
     * @var string
     */
    protected $table = 'cache';
    /**
     * @var \Mild\Database\Database
     */
    protected $database;
    /**
     * @var array
     */
    protected $columns = [
        'key' => 'key',
        'payload' => 'payload',
        'expired' => 'expired'
    ];

    /**
     * DatabaseHandler constructor.
     * @param \Mild\Database\Database $database
     * @param array $config
     */
    public function __construct($database, $config = [])
    {
        $this->database = $database;
        if (isset($config['table'])) {
            $this->setTable($config['table']);
        }
        if (isset($config['prefix'])) {
            $this->setPrefix($config['prefix']);
        }
        if (isset($config['columns'])) {
            foreach ( (array) $config['columns'] as $key => $value) {
                if ($this->hasColumn($key)) {
                    $this->putColumn($key);
                    $this->setColumn($key, $value);
                } else {
                    $this->setColumn($key, $value);
                }
            }
        }
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
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
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
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasColumn($key)
    {
        return isset($this->columns[$key]);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getColumn($key)
    {
        if (!$this->hasColumn($key)) {
            throw new InvalidArgumentException('Column '.$key.' does not exist.');
        }
        return $this->columns[$key];
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function setColumn($key, $value)
    {
        $this->columns[$key] = $value;
    }

    /**
     * @param $key
     * @return void
     */
    public function putColumn($key)
    {
        unset($this->columns[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->database->table($this->table)->where($this->getColumn('key'), '=', $this->getName($key))->exists();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $cache = $this->database->table($this->table)->where($this->getColumn('key'), '=', $this->getName($key))->first();
        if (empty($cache)) {
            return false;
        }
        if ($cache->{$this->getColumn('expired')} <= time()) {
            return $this->put($key);
        }
        return unserialize($cache->{$this->getColumn('payload')});

    }

    /**
     * @param $key
     * @param $value
     * @param int $expired
     * @return bool
     */
    public function set($key, $value, int $expired = 0)
    {
        $expired = time() + ($expired * 60);
        $value = serialize($value);
        if ($this->has($key)) {
          return $this->database->table($this->table)->where($this->getColumn('key'), '=', $this->getName($key))->update([
              $this->getColumn('payload') => $value,
              $this->getColumn('expired') => $expired
          ]);
        }
        return $this->database->table($this->table)->insert([
            $this->getColumn('key') => $this->getName($key),
            $this->getColumn('payload') => $value,
            $this->getColumn('expired') => $expired
        ]);
    }

    /**
     * @param $key
     * @param int $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->incrementOrDecrement($key, $value, function ($current, $value) {
            return $current + $value;
        });
    }

    /**
     * @param $key
     * @param int $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->incrementOrDecrement($key, $value, function ($current, $value) {
            return $current - $value;
        });
    }

    /**
     * @param $key
     * @param $value
     * @param callable $callback
     * @return bool|int
     */
    protected function incrementOrDecrement($key, $value, callable $callback)
    {
        return $this->database->table($this->table)->where($this->getColumn('key'), '=', $this->getName($key))->update([
           $this->getColumn('payload') => serialize($callback($this->get($key), $value))
        ]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function put($key)
    {
        return $this->database->table($this->table)->where($this->getColumn('key'), '=', $this->getName($key))->delete();
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->database->table($this->table)->delete();
    }

    /**
     * @param $name
     * @return string
     */
    public function getName($name)
    {
        return $this->prefix.$name;
    }
}