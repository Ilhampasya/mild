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

use PDOException;
use RuntimeException;
use Mild\Supports\Collection;
use Mild\Database\Pagination;

abstract class Query
{
    protected $model;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var \Mild\Database\Database
     */
    protected $database;
    /**
     * @var string
     */
    public $joinClause;
    /**
     * @var string
     */
    public $whereClause;
    /**
     * @var string
     */
    public $havingClause;
    /**
     * @var string
     */
    public $groupClause;
    /**
     * @var string
     */
    public $orderClause;
    /**
     * @var string
     */
    public $limitClause;
    /**
     * @var string
     */
    public $offsetClause;
    /**
     * @var string
     */
    public $unionClause;
    /**
     * @var array
     */
    protected $bindings = [
        'where' => [],
        'having' => [],
        'union' => []
    ];
    /**
     * @var array
     */
    protected $wrappers = [
        'start' => '"',
        'end' => '"'
    ];
    /**
     * @var array
     */
    protected $operators = [
        '=' => true,
        '<' => true,
        '>' => true,
        '<=' => true,
        '>=' => true,
        '<>' => true,
        '!=' => true,
        '<=>' => true,
        'like' => true,
        'like binary' => true,
        'not like' => true,
        'ilike' => true,
        '&' => true,
        '|' => true,
        '^' => true,
        '<<' => true,
        '>>' => true,
        'rlike' => true,
        'regexp' => true,
        'not regexp' => true,
        '~' => true,
        '~*' => true,
        '!~' => true,
        '!~*' => true,
        'similar to' => true,
        'not similar to' => true,
        'not ilike' => true,
        '~~*' => true,
        '!~~*' => true
    ];

    /**
     * Builder constructor.
     * @param Database $database
     * @param string $table
     */
    public function __construct($database, $table)
    {
        $this->table = $table;
        $this->database = $database;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param array|string $value
     * @return string
     */
    public function wrap($value)
    {
        if (empty($value)) {
            return $value;
        }
        if (is_array($value)) {
            $wrapped = '';
            foreach ($value as $val) {
                $wrapped .= $this->wrap($val).', ';
            }
            return substr($wrapped, 0, -2);
        }
        if (stripos($value, ' as ') !== false) {
            $segments = preg_split('/\s+as\s+/i', $value);
            return $this->wrap($segments[0]).' as '.$this->wrap($segments[1]);
        }
        if (strpos($value, '.') !== false) {
            $segments = explode('.', $value);
            foreach ($segments as $key => $value) {
                $segments[$key] = $this->wrap($value);
            }
            return implode('.', $segments);
        }
        if ($value !== '*') {
            if ($value[0] !== $this->wrappers['start']) {
                $value = $this->wrappers['start'].$value;
            }
            if ($value[-1] !== $this->wrappers['end']) {
                $value .= $this->wrappers['end'];
            }
        }
        return $value;
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @param string $type
     * @param bool $where
     * @return $this
     */
    public function join($table, $first, $operator, $second, $type = 'inner', $where = false)
    {
        $this->joinClause .= ' '.$type.' join '.$this->wrap($table).' on '.$this->wrap($first).' '.$operator.' ';
        if ($where === true) {
            $this->joinClause .= '?';
            return $this->setBinding('where', $second);
        } else {
            $this->joinClause .= $this->wrap($second);
        }
        return $this;
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return Query
     */
    public function leftJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return Query
     */
    public function rightJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'right');
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return Query
     */
    public function crossJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'cross');
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @param string $type
     * @return Query
     */
    public function joinWhere($table, $first, $operator, $second, $type = 'inner')
    {
        return $this->join($table, $first, $operator, $second, $type, true);
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return Query
     */
    public function leftJoinWhere($table, $first, $operator, $second)
    {
        return $this->joinWhere($table, $first, $operator, $second, 'left');
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return Query
     */
    public function rightJoinWhere($table, $first, $operator, $second)
    {
        return $this->joinWhere($table, $first, $operator, $second, 'right');
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return Query
     */
    public function crossJoinWhere($table, $first, $operator, $second)
    {
        return $this->joinWhere($table, $first, $operator, $second, 'cross');
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @param string $boolean
     * @return Query
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (!isset($this->operators[$operator])) {
            $value = $operator;
            $operator = '=';
        }
        if (empty($this->whereClause)) {
            $this->whereClause = 'where ';
        } else {
            $this->whereClause .= ' '.$boolean.' ';
        }
        $this->whereClause .= $this->wrap($column).' '.$operator.' ?';
        return $this->setBinding('where', $value);
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return Query
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * @param $column
     * @param string $booelan
     * @param bool $not
     * @return $this
     */
    public function whereNull($column, $booelan = 'and', $not = false)
    {
        $type = 'is null';
        if ($not === true) {
            $type = 'is not null';
        }
        if (empty($this->whereClause)) {
            $this->whereClause = 'where ';
        } else {
            $this->whereClause .= ' '.$booelan.' ';
        }
        $this->whereClause .= $this->wrap($column).' '.$type;
        return $this;
    }

    /**
     * @param $column
     * @return Query
     */
    public function orWhereNull($column)
    {
        return $this->whereNull($column, 'or');
    }

    /**
     * @param $column
     * @param string $booelan
     * @return Query
     */
    public function whereNotNull($column, $booelan = 'and')
    {
        return $this->whereNull($column, $booelan, true);
    }

    /**
     * @param $column
     * @return Query
     */
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'or');
    }

    /**
     * @param $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return Query
     */
    public function whereIn($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'in';
        if ($not === true) {
            $type = 'not in';
        }
        if (empty($this->whereClause)) {
            $this->whereClause = 'where ';
        } else {
            $this->whereClause .= ' '.$boolean.' ';
        }
        $this->whereClause .= $this->wrap($column).' '.$type.' (';
        foreach ($values as $value) {
            $this->whereClause .= '?, ';
        }
        $this->whereClause = substr($this->whereClause, 0, -2).')';
        return $this->setBinding('where', $values);
    }

    /**
     * @param $column
     * @param array $values
     * @return Query
     */
    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    /**
     * @param $column
     * @param array $values
     * @param string $boolean
     * @return Query
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * @param $column
     * @param $values
     * @return Query
     */
    public function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @param string $boolean
     * @return Query
     */
    public function having($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (!isset($this->operators[$operator])) {
            $value = $operator;
            $operator = '=';
        }
        if (empty($this->havingClause)) {
            $this->havingClause = 'having ';
        } else {
            $this->havingClause .= ' '.$boolean.' ';
        }
        $this->havingClause .= $this->wrap($column).' '.$operator.' ?';
        return $this->setBinding('having', $value);
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return Query
     */
    public function orHaving($column, $operator = null, $value = null)
    {
        return $this->having($column, $operator, $value, 'or');
    }

    /**
     * @param $union
     * @param bool $all
     * @return $this
     */
    public function union($union, $all = false)
    {
        throw new RuntimeException('Query does not implement union method.');
    }

    /**
     * @param $column
     * @return $this
     */
    public function group($column)
    {
        if (empty($this->groupClause)) {
            $this->groupClause = 'group by ';
        } else {
            $this->groupClause .= ', ';
        }
        $this->groupClause .= $this->wrap($column);
        return $this;
    }

    /**
     * @param $column
     * @param string $type
     * @return $this
     */
    public function order($column, $type = 'asc')
    {
        if (empty($this->orderClause)) {
            $this->orderClause = 'order by ';
        } else {
            $this->orderClause .= ', ';
        }
        $this->orderClause .= $this->wrap($column).' '.$type;
        return $this;
    }

    /**
     * @param int $max
     * @return $this
     */
    public function limit($max)
    {
        throw new RuntimeException('Query does not implement limit method.');
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        throw new RuntimeException('Query does not implement offset method.');
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function count($columns = ['*'])
    {
        return (int) $this->aggregate('count', $columns);
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function min($columns)
    {
        return $this->aggregate('min', $columns);
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function max($columns)
    {
        return $this->aggregate('max', $columns);
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function avg($columns)
    {
        return $this->aggregate('avg', $columns);
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function average($columns)
    {
        return $this->avg($columns);
    }

    /**
     * @param $type
     * @param array $columns
     * @return mixed
     */
    public function aggregate($type, $columns = ['*'])
    {
        throw new RuntimeException('Query does not implement aggregate method.');
    }

    /**
     * @param array $columns
     * @return bool
     */
    public function exists($columns = ['*'])
    {
        throw new RuntimeException('Query does not implement exists method.');
    }

    /**
     * @param array $columns
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        throw new RuntimeException('Query does not implement get method.');
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        return $this->limit(1)->get($columns)->first();
    }

    /**
     * @param $stmt
     * @return mixed
     */
    protected function fetch($stmt)
    {
        $stmt->setFetchMode($this->database->getFetchMode());
        $results = $stmt->fetchAll();
        if (!empty($this->model)) {
            foreach ($results as $key => $value) {
                $model = $this->model::instance();
                $model->setAttributes((array) $value);
                $results[$key] = $model;
            }
        }
        return $results;
    }

    /**
     * @param \Mild\Http\Request $request
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int $page
     * @return Pagination
     */
    public function paginate($request, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = 0)
    {
        $this->limitClause = null;
        $this->offsetClause = null;
        $params = $request->getQueryParams();
        if (isset($params[$pageName])) {
            $page = $params[$pageName];
        }
        if ($page < 1) {
            $page = 1;
        }
        $builder = clone $this;
        $builder->limitClause = null;
        $builder->offsetClause = null;
        $total = $builder->count($columns);
        $collection = $total ? $this->offset(($page - 1) * $perPage)->limit($perPage)->get($columns) : new Collection;
        return new Pagination($request, $collection, ['perPage' => $perPage, 'currentPage' => $page, 'pageName' => $pageName, 'total' => $total]);
    }

    /**
     * @param array $values
     * @return int
     */
    public function insert(array $values)
    {
        $bindings = [];
        $sql = 'insert into '.$this->wrap($this->table).' (';
        $sub = ' values (';
        foreach ($values as $key => $value) {
            $sub .= '?, ';
            $bindings[] = $value;
            $sql .= $this->wrap($key).', ';
        }
        try {
            $this->database->beginTransaction();
            $this->database->bindValues($stmt = $this->database->prepare(substr($sql, 0, -2).')'.substr($sub, 0, -2).')'), $bindings);
            $stmt->execute();
            $this->database->setLastId($this->database->lastInsertId());
            $this->database->commit();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    /**
     * @param array $values
     * @return int
     */
    public function update(array $values)
    {
        $bindings = [];
        $sql = 'update '.$this->wrap($this->table).' set ';
        foreach ($values as $key => $value) {
            $bindings[] = $value;
            $sql .= $this->wrap($key).' = ?, ';
        }
        foreach ($this->getBindings() as $binding) {
            $bindings[] = $binding;
        }
        try {
            $this->database->beginTransaction();
            $this->database->bindValues($stmt = $this->database->prepare(substr($sql, 0, -2).$this->resolveClause($this->joinClause).$this->resolveClause($this->whereClause).$this->resolveClause($this->havingClause).$this->resolveClause($this->groupClause).$this->resolveClause($this->orderClause).$this->resolveClause($this->limitClause).$this->resolveClause($this->offsetClause)), $bindings);
            $stmt->execute();
            $this->database->commit();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    /**
     * @return int
     */
    public function delete()
    {
        try {
            $this->database->beginTransaction();
            $this->database->bindValues($stmt = $this->database->prepare('delete from '.$this->wrap($this->table).$this->resolveClause($this->joinClause).$this->resolveClause($this->whereClause).$this->resolveClause($this->havingClause).$this->resolveClause($this->groupClause).$this->resolveClause($this->orderClause).$this->resolveClause($this->limitClause).$this->resolveClause($this->offsetClause)), $this->getBindings());
            $stmt->execute();
            $this->database->commit();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    /**
     * @param $clause
     * @return string
     */
    public function resolveClause($clause)
    {
        $clause = trim($clause, ' ');
        if (!empty($clause)) {
            $clause = ' '.$clause;
        }
        return $clause;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setBinding($key, $value)
    {
        if (!isset($this->bindings[$key])) {
            throw new InvalidArgumentException('Invalid binding type: '.$key.'');
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        if (empty($this->bindings[$key])) {
            $this->bindings[$key] = $value;
        } else {
            foreach ($value as $val) {
                $this->bindings[$key][] = $val;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        $bindings = $this->bindings['where'];
        foreach ($this->bindings['having'] as $binding) {
            $bindings[] = $binding;
        }
        foreach ($this->bindings['union'] as $binding) {
            $bindings[] = $binding;
        }
        return $bindings;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return \Mild\Database\Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return array
     */
    public function getOperators()
    {
        return $this->operators;
    }

    /**
     * @return array
     */
    public function getWrappers()
    {
        return $this->wrappers;
    }
}