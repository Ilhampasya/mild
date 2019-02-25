<?php

namespace Mild\Session;

use SessionHandlerInterface;
use InvalidArgumentException;

class DatabaseSessionHandler implements SessionHandlerInterface
{
    /**
     * @var \Mild\Database\Database
     */
    protected $database;
    /**
     * Determine if the session is exists
     *
     * @var bool
     */
    protected $exists = false;
    /**
     * @var string
     */
    protected $prefix;
    /**
     * @var string
     */
    protected $table = 'sessions';
    /**
     * @var array
     */
    protected $columns = [
        'id' => 'id',
        'payload' => 'payload',
        'last_activity' => 'last_activity'
    ];

    /**
     * DatabaseSessionHandler constructor.
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
            foreach ((array) $config['columns'] as $key => $value) {
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
     * @param $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param $columns
     * @return void
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return \Mild\Database\Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $key
     * @return string
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
    public function hasColumn($key)
    {
        return isset($this->columns[$key]);
    }

    /**
     * @return bool
     */
    public function isExists()
    {
        return $this->exists;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * @param string $session_id
     * @return bool
     */
    public function destroy($session_id)
    {
        $this->database->table($this->table)->where($this->getColumn('id'), '=', $this->getName($session_id))->delete();
        return true;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        $this->database->table($this->table)->where($this->getColumn('last_activity'), '<=', $maxlifetime)->delete();
        return true;
    }

    /**
     * @param string $save_path
     * @param string $name
     * @return bool
     */
    public function open($save_path, $name)
    {
        return true;
    }

    /**
     * @param string $session_id
     * @return string
     */
    public function read($session_id)
    {
        $session = $this->database->table($this->table)->where($this->getColumn('id'), '=', $this->getName($session_id))->first();
        if (!empty($session)) {
            $this->exists = true;
            return base64_decode($session->{$this->getColumn('payload')});
        }
        return '';
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool
     */
    public function write($session_id, $session_data)
    {
        $last_activity = time();
        $session_id = $this->getName($session_id);
        $session_data = base64_encode($session_data);
        if ($this->exists) {
            $this->database->table($this->table)->where($this->getColumn('id'), '=', $session_id)->update([
               $this->getColumn('payload') => $session_data,
               $this->getColumn('last_activity') => $last_activity
            ]);
        } else {
            $this->database->table($this->table)->insert([
               $this->getColumn('id') => $session_id,
               $this->getColumn('payload') => $session_data,
               $this->getColumn('last_activity') => $last_activity
            ]);
        }
        return $this->exists = true;
    }

    /**
     * @param $session_id
     * @return string
     */
    protected function getName($session_id)
    {
        return $this->prefix.$session_id;
    }
}