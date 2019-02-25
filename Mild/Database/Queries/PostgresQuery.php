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

class PostgresQuery extends Query
{
    /**
     * @var array
     */
    protected $wrappers = [
        'start' => '"',
        'end' => '"'
    ];

    /**
     * @param $union
     * @param bool $all
     * @return $this
     */
    public function union($union, $all = false)
    {
        if (!empty($this->unionClause)) {
            $this->unionClause .= ' ';
        }
        $type = 'union ';
        if ($all === true) {
            $type .= 'all ';
        }
        $this->unionClause .= $type.'(select * from '.$this->wrap($union->getTable()).$union->resolveClause($union->joinClause).$union->resolveClause($union->whereClause).$union->resolveClause($union->havingClause).$union->resolveClause($union->groupClause).$union->resolveClause($union->orderClause).$union->resolveClause($union->limitClause).$union->resolveClause($union->offsetClause).')';
        return $this->setBinding('union', $union->getBindings());
    }

    /**
     * @param int $max
     * @return $this
     */
    public function limit($max)
    {
        if (empty($this->limitClause)) {
            $this->limitClause = 'limit '.$max;
        } else {
            $this->limitClause .= ', '.$max;
        }
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        if (empty($this->offsetClause)) {
            $this->offsetClause = 'offset '.$offset;
        } else {
            $this->offsetClause .= ', '.$offset;
        }
        return $this;
    }

    /**
     * @param $type
     * @param array $columns
     * @return mixed
     */
    public function aggregate($type, $columns = ['*'])
    {
        $table = $this->wrap($this->table);
        $columns = $this->wrap($columns);
        $joinClause = $this->resolveClause($this->joinClause);
        $whereClause = $this->resolveClause($this->whereClause);
        $havingClause = $this->resolveClause($this->havingClause);
        $groupClause = $this->resolveClause($this->groupClause);
        $orderClause = $this->resolveClause($this->orderClause);
        $limitClause = $this->resolveClause($this->limitClause);
        $offsetClause = $this->resolveClause($this->offsetClause);
        $sql = 'select '.$type.'('.$columns.') as aggregate from ';
        if (!empty($this->unionClause)) {
            $sql .= '((select '.$columns.' from '.$table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.')'.$this->resolveClause($this->unionClause).$limitClause.$offsetClause.') as '.$this->wrap('temp_table');
        } else {
            $sql .= $table.$joinClause.$whereClause.$havingClause.$groupClause.$orderClause.$limitClause.$offsetClause;
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
        $sql = 'select '.$this->wrap($columns).' from '.$this->wrap($this->table).$this->resolveClause($this->joinClause).$this->resolveClause($this->whereClause).$this->resolveClause($this->havingClause).$this->resolveClause($this->groupClause).$this->resolveClause($this->orderClause).$this->resolveClause($this->limitClause).$this->resolveClause($this->offsetClause);
        if (!empty($this->unionClause)) {
            $sql = '('.$sql.')'.$this->resolveClause($this->unionClause);
        }
        $sql = 'select exists ('.$sql.') as '.$this->wrap('exists');
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
        $sql = 'select '.$this->wrap($columns).' from '.$this->wrap($this->table).$this->resolveClause($this->joinClause).$this->resolveClause($this->whereClause).$this->resolveClause($this->havingClause).$this->resolveClause($this->groupClause).$this->resolveClause($this->orderClause).$this->resolveClause($this->limitClause).$this->resolveClause($this->offsetClause);
        if (!empty($this->unionClause)) {
            $sql = '('.$sql.')'.$this->resolveClause($this->unionClause);
        }
        $this->database->bindValues($stmt = $this->database->prepare($sql), $this->getBindings());
        $stmt->execute();
        return new Collection($this->fetch($stmt));
    }
}