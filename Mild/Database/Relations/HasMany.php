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

class HasMany extends Relation
{
    /**
     * @return \Mild\Supports\Collection
     */
    public function get()
    {
        return $this->query->get();
    }
}