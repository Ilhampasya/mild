<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Database\Relations;

abstract class Relation
{
    /**
     * @var \Mild\Database\Queries\Query
     */
    protected $query;

    /**
     * Relation constructor.
     * @param \Mild\Database\Queries\Query $query
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return \Mild\Database\Queries\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $this->query->$name(...$arguments);
        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function get();
}