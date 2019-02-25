<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Cache;

class FileHandler implements HandlerInterface
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
     * FileHandler constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
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
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return file_exists($this->getName($key));
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->getPayload($key)['data'];
    }

    /**
     * @return array
     */
    protected function emptyPayload()
    {
        return ['data' => null, 'expired' => null];
    }

    /**
     * @param $key
     * @return array
     */
    protected function getPayload($key)
    {
        if (!$this->has($key)) {
            return $this->emptyPayload();
        }
        $raw = unserialize(file_get_contents($this->getName($key)));
        if ($raw['expired'] <= time()) {
            $this->put($key);
            return $this->emptyPayload();
        }
        return $raw;
    }

    /**
     * @param $key
     * @param $value
     * @param int $expired
     * @return bool|int|void
     */
    public function set($key, $value, int $expired = 0)
    {
        if ($expired === 0 || $expired > 9999999999) {
            $expired = 9999999999;
        }
        return file_put_contents($this->getName($key), serialize([
            'data' => $value,
            'expired' => $expired
        ]));
    }

    /**
     * @param $key
     * @param int $value
     * @return bool|int
     */
    public function increment($key, $value = 1)
    {
        $raw = $this->getPayload($key);
        if (isset($raw['expired'])) {
            $expired = $raw['expired'];
        } else {
            $expired = 0;
        }
        return $this->set($key, (int) $raw['data'] + (int) $value, $expired);
    }

    /**
     * @param $key
     * @param int $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
       return $this->increment($key, (int) $value * -1);
    }

    /**
     * @param $key
     * @return void|bool
     */
    public function put($key)
    {
        if ($this->has($key)) {
            return unlink($this->getName($key));
        }
        return false;
    }

    /**
     * @return void|bool
     */
    public function flush()
    {
        foreach (glob($this->path .'*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
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
     * @param $key
     * @return string
     */
    protected function getName($key)
    {
        return $this->path.$this->prefix.sha1($key);
    }
}