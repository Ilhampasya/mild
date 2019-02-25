<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Database\Queries;

use Mild\Supports\Collection;

class SqlServerQuery extends Query
{
    /**
     * @var array
     */
    protected $wrappers = [
        'start' => '[',
        'end' => ']'
    ];

    /**
     * @param $union
     * @param bool $all
     * @return $this
     */
    public function union($union, $all = false)
    {
        $table = $this->wrap($union->getTable());
        $joinClause = $this->resolveClause($union->joinClause);
        $whereClause = $this->resolveClause($union->whereClause);
        $havingClause = $this->resolveClause($union->havingClause);
        $groupClause = $this->resolveClause($union->groupClause);
        $orderClause = $this->resolveClause($union->orderClause);
        $type = 'union ';
        if ($all) {
            $type .= 'all ';
        }
        if ($union->limitClause <= 0 && $union->offsetClause <= 0) {
            $this->unionClause = $type.'select * from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause;
        } elseif ($union->limitClause > 0 & $union->offsetClause <= 0) {
            $this->unionClause = $type.'select top '.$union->limitClause.' * from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause;
        } elseif ($union->offsetClause > 0) {
            if (empty($orderClause)) {
                $orderClause = 'order by (select 0)';
            }
            $this->unionClause = $type.'select * from (select *, row_number() over ('.trim($orderClause, ' ').') as row_num from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.') as '.$this->wrap('temp_table').' where row_num';
            if ($union->limitClause > 0) {
                $this->unionClause .= $this->resolveClause('between '.($union->offsetClause + 1).' and '.($union->offsetClause + $union->limitClause));
            } else {
                $this->unionClause .= $this->resolveClause('>= '. $union->offsetClause);
            }
            $this->unionClause .= $this->resolveClause('order by row_num');
        }
        return $this->setBinding('union', $union->getBindings());
    }

    /**
     * @param int $max
     * @return $this
     */
    public function limit($max)
    {
        $this->limitClause = $max;
        return $this;
    }

    /**
     * @param int $offset
     * @return Query|void
     */
    public function offset($offset)
    {
        $this->offsetClause = $offset;
        return $this;
    }

    /**
     * @param $type
     * @param array $columns
     * @return mixed
     */
    public function aggregate($type, $columns = ['*'])
    {
        $columns = $type.'('.$this->wrap($columns).') as aggregate ';
        $table = $this->wrap($this->table);
        $joinClause = $this->resolveClause($this->joinClause);
        $whereClause = $this->resolveClause($this->whereClause);
        $havingClause = $this->resolveClause($this->havingClause);
        $groupClause = $this->resolveClause($this->groupClause);
        $orderClause = $this->resolveClause($this->orderClause);
        $unionClause = '';
        if (!empty($this->unionClause)) {
            $unionClause = $this->resolveClause($this->unionClause);
        }
        if ($this->limitClause <= 0 && $this->offsetClause <= 0) {
            $sql = 'select '.$columns.' from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.$unionClause;
        } elseif ($this->limitClause > 0 & $this->offsetClause <= 0) {
            $sql = 'select top '.$this->limitClause .' '.$columns.' from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.$unionClause;
        } elseif ($this->offsetClause > 0) {
            if (empty($orderClause)) {
                $orderClause = 'order by (select 0)';
            }
            $sql = 'select * from (select '.$columns.', row_number() over ('.trim($orderClause, ' ').') as row_num from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$unionClause.') as '.$this->wrap('temp_table').' where row_num';
            if ($this->limitClause > 0) {
                $sql .= $this->resolveClause('between '.($this->offsetClause + 1).' and '.($this->offsetClause + $this->limitClause));
            } else {
                $sql .= $this->resolveClause('>= '. $this->offsetClause);
            }
            $sql .= $this->resolveClause('order by row_num');
        }
        $this->database->bindValues($stmt = $this->database->prepare($sql), $this->getBindings());
        $stmt->execute();
        $results = $this->fetch($stmt);
        if (!empty($results)) {
            return $results[0]->aggregate;
        }
        return 0;
    }

    /**
     * @param array $columns
     * @return bool
     */
    public function exists($columns = ['*'])
    {
        $columns = '1 '.$this->wrap('exists').', '.$this->wrap($columns);
        $table = $this->wrap($this->table);
        $joinClause = $this->resolveClause($this->joinClause);
        $whereClause = $this->resolveClause($this->whereClause);
        $havingClause = $this->resolveClause($this->havingClause);
        $groupClause = $this->resolveClause($this->groupClause);
        $orderClause = $this->resolveClause($this->orderClause);
        $unionClause = '';
        if (!empty($this->unionClause)) {
            $unionClause = $this->resolveClause($this->unionClause);
        }
        if ($this->limitClause <= 0 && $this->offsetClause <= 0) {
            $sql = 'select '.$columns.' from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.$unionClause;
        } elseif ($this->limitClause > 0 & $this->offsetClause <= 0) {
            $sql = 'select top '.$this->limitClause .' '.$columns.' from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.$unionClause;
        } elseif ($this->offsetClause > 0) {
            if (empty($orderClause)) {
                $orderClause = 'order by (select 0)';
            }
            $sql = 'select * from (select '.$columns.', row_number() over ('.trim($orderClause, ' ').') as row_num from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$unionClause.') as '.$this->wrap('temp_table').' where row_num';
            if ($this->limitClause > 0) {
                $sql .= $this->resolveClause('between '.($this->offsetClause + 1).' and '.($this->offsetClause + $this->limitClause));
            } else {
                $sql .= $this->resolveClause('>= '. $this->offsetClause);
            }
            $sql .= $this->resolveClause('order by row_num');
        }
        $this->database->bindValues($stmt = $this->database->prepare($sql), $this->getBindings());
        $stmt->execute();
        $results = $this->fetch($stmt);
        if (!empty($results)) {
            return (bool) $results[0]->exists;
        }
        return false;
    }

    /**
     * @param array $columns
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        $columns = $this->wrap($columns);
        $table = $this->wrap($this->table);
        $joinClause = $this->resolveClause($this->joinClause);
        $whereClause = $this->resolveClause($this->whereClause);
        $havingClause = $this->resolveClause($this->havingClause);
        $groupClause = $this->resolveClause($this->groupClause);
        $orderClause = $this->resolveClause($this->orderClause);
        $unionClause = '';
        if (!empty($this->unionClause)) {
            $unionClause = $this->resolveClause($this->unionClause);
        }
        if ($this->limitClause <= 0 && $this->offsetClause <= 0) {
            $sql = 'select '.$columns.' from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.$unionClause;
        } elseif ($this->limitClause > 0 & $this->offsetClause <= 0) {
            $sql = 'select top '.$this->limitClause .' '.$columns.' from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.$unionClause;
        } elseif ($this->offsetClause > 0) {
            if (empty($orderClause)) {
                $orderClause = 'order by (select 0)';
            }
            $sql = 'select * from (select '.$columns.', row_number() over ('.trim($orderClause, ' ').') as row_num from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$unionClause.') as '.$this->wrap('temp_table').' where row_num';
            if ($this->limitClause > 0) {
                $sql .= $this->resolveClause('between '.($this->offsetClause + 1).' and '.($this->offsetClause + $this->limitClause));
            } else {
                $sql .= $this->resolveClause('>= '. $this->offsetClause);
            }
            $sql .= $this->resolveClause('order by row_num');
        }
        $this->database->bindValues($stmt = $this->database->prepare($sql), $this->getBindings());
        $stmt->execute();
        return new Collection($this->fetch($stmt));
    }
}