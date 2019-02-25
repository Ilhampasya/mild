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

use Exception;
use SessionHandlerInterface;

class FileSessionHandler implements SessionHandlerInterface
{
    /**
     * @var string
     */
    protected $path;
    /**
     * @var string
     */
    protected $prefix;

    /**
     * FileSessionHandler constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        if (isset($config['path'])) {
            $this->setPath(rtrim($config['path'], '/') .'/');
        }
        if (isset($config['prefix'])) {
            $this->setPrefix($config['prefix']);
        }
    }

    /**
     * @param $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed|string
     */
    public function getPrefix()
    {
        return $this->prefix;
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
        $file = $this->getName($session_id);
        if (file_exists($file)) {
            unlink($file);
        }
        return true;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        foreach (glob("$this->path*") as $file) {
            if (file_exists($file) && filemtime($file) + $maxlifetime < time()) {
                unlink($file);
            }
        }
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
     * @return false|string
     */
    public function read($session_id)
    {
        return (string)@file_get_contents($this->getName($session_id));
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool|void
     */
    public function write($session_id, $session_data)
    {
        return file_put_contents($this->getName($session_id), $session_data) === false ? false : true;
    }

    /**
     * @param $session_id
     * @return string
     */
    protected function getName($session_id)
    {
        return $this->path.$this->prefix.$session_id;
    }
}

