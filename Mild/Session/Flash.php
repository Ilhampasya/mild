<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Session;

use ArrayAccess;
use Mild\Supports\Traits\Dot;

class Flash implements ArrayAccess
{
    use Dot;
    /**
     * @var SessionManager
     */
    protected $session;

    /**
     * Flash constructor.
     * @param SessionManager $session
     */
    public function __construct($session)
    {
        $this->session = $session;
        if (!is_null($this->items = $session->get('_flash'))) {
            $session->put('_flash');
        }
    }

    /**
     * @return SessionManager
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->session->set('_flash.' .$key, $value);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return mixed|void
     */
    public function offsetUnset($offset)
    {
        $this->put($offset);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        $this->put($name);
    }
}

